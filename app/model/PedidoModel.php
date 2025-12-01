<?php
/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";
require_once __DIR__ . "/EstoqueModel.php";

/* CRIAÇÃO DA CLASSE PEDIDO MODEL */
class PedidoModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;
    private $estoqueModel;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
        $this->estoqueModel = new EstoqueModel();
    }

    /* FUNÇÃO QUE LISTA TODOS OS PEDIDOS E RETORNA UM ARRAY CONTENDO OS DADOS */
    public function listarTodosPedidos()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.id_pedido,
                    p.data_pedido,
                    p.status_pedido,
                    p.valor_total,
                    u.nome as nome_cliente,
                    u.id_usuario as id_cliente,
                    pg.status_pagamento,
                    pg.forma_pagamento
                FROM Pedido p
                LEFT JOIN Usuarios u ON p.id_cliente = u.id_usuario
                LEFT JOIN Pagamento pg ON p.id_pedido = pg.id_pedido
                ORDER BY p.data_pedido DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA PEDIDOS POR TERMO E FILTROS E RETORNA UM ARRAY COM OS DADOS DE USUÁRIO COMPLETO */
    public function buscarPedidosPorTermo($termo = '', $filtroStatusPedido = '', $filtroStatusPagamento = '', $dataInicial = '', $dataFinal = '', $valorMin = '', $valorMax = '')
    {
        try {
            /* Remove espaços do início e fim */
            $termo = trim($termo);
            $filtroStatusPedido = trim($filtroStatusPedido);
            $filtroStatusPagamento = trim($filtroStatusPagamento);

            /* CRIA UM CÓDIGO BASE DE PESQUISA SQL ESSE CÓDIGO SERÁ IMPLEMETADO DE ACORDO COM OS FILTROS QUE TIVER*/
            $sql = "
                SELECT 
                    p.id_pedido,
                    p.data_pedido,
                    p.status_pedido,
                    p.valor_total,
                    u.nome as nome_cliente,
                    u.id_usuario as id_cliente,
                    pg.status_pagamento,
                    pg.forma_pagamento
                FROM Pedido p
                LEFT JOIN Usuarios u ON p.id_cliente = u.id_usuario
                LEFT JOIN Pagamento pg ON p.id_pedido = pg.id_pedido
                WHERE 1=1
            ";

            $params = [];

            /* Adiciona filtro de pesquisa (ID do pedido ou nome do cliente) */
            if (!empty($termo)) {
                $termoBusca = '%' . $termo . '%';
                /* Busca tanto por ID do pedido quanto por nome do cliente */
                if (is_numeric($termo)) {
                    /* Se for numérico, busca pelo ID exato OU por nome */
                    $sql .= " AND (p.id_pedido = :termo_id OR u.nome LIKE :termo_nome)";
                    $params[':termo_id'] = (int)$termo;
                    $params[':termo_nome'] = $termoBusca;
                } else {
                    /* Se não for numérico, busca apenas por nome */
                    $sql .= " AND u.nome LIKE :termo_nome";
                    $params[':termo_nome'] = $termoBusca;
                }
            }

            /* Adiciona filtro de status do pedido */
            if (!empty($filtroStatusPedido) && in_array($filtroStatusPedido, ['PENDENTE', 'FINALIZADO', 'CANCELADO'])) {
                $sql .= " AND p.status_pedido = :status_pedido";
                $params[':status_pedido'] = $filtroStatusPedido;
            }

            /* Adiciona filtro de status do pagamento */
            if (!empty($filtroStatusPagamento) && in_array($filtroStatusPagamento, ['PENDENTE', 'CONFIRMADO', 'CANCELADO'])) {
                $sql .= " AND pg.status_pagamento = :status_pagamento";
                $params[':status_pagamento'] = $filtroStatusPagamento;
            }

            /* Adiciona filtro de data inicial */
            if (!empty($dataInicial)) {
                $sql .= " AND DATE(p.data_pedido) >= :data_inicial";
                $params[':data_inicial'] = $dataInicial;
            }

            /* Adiciona filtro de data final */
            if (!empty($dataFinal)) {
                $sql .= " AND DATE(p.data_pedido) <= :data_final";
                $params[':data_final'] = $dataFinal;
            }

            /* Adiciona filtro de valor mínimo - sempre calcula o valor total a partir dos itens (preco_unitario * quantidade) */
            if (!empty($valorMin)) {
                $sql .= " AND (
                    SELECT COALESCE(SUM(pp.preco_unitario * pp.quantidade), 0) 
                    FROM Produto_Pedido pp 
                    WHERE pp.id_pedido = p.id_pedido
                ) >= :valor_min";
                $params[':valor_min'] = floatval($valorMin);
            }

            /* Adiciona filtro de valor máximo - sempre calcula o valor total a partir dos itens (preco_unitario * quantidade) */
            if (!empty($valorMax)) {
                $sql .= " AND (
                    SELECT COALESCE(SUM(pp.preco_unitario * pp.quantidade), 0) 
                    FROM Produto_Pedido pp 
                    WHERE pp.id_pedido = p.id_pedido
                ) <= :valor_max";
                $params[':valor_max'] = floatval($valorMax);
            }

            $sql .= " ORDER BY p.data_pedido DESC";

            $stmt = $this->conn->prepare($sql);

            /* FOREACH RESPONSÁVEL POR NORMALIZAR TODOS OS PARÂMETROS PASSADOS */
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA UM PEDIDO ESPECÍFICO POR ID COM TODOS OS DETALHES E RETORNA UM ARRAY ASSOCIATIVO COM OS DADOS*/
    public function buscarPedidoPorId($idPedido)
    {
        try {
            /* Busca dados do pedido */
            $stmt = $this->conn->prepare("
                SELECT 
                    p.id_pedido,
                    p.data_pedido,
                    p.status_pedido,
                    p.valor_total,
                    u.nome as nome_cliente,
                    u.email as email_cliente,
                    u.id_usuario as id_cliente,
                    pg.status_pagamento,
                    pg.forma_pagamento,
                    pg.data_pagamento
                FROM Pedido p
                LEFT JOIN Usuarios u ON p.id_cliente = u.id_usuario
                LEFT JOIN Pagamento pg ON p.id_pedido = pg.id_pedido
                WHERE p.id_pedido = :id
            ");
            $stmt->bindParam(':id', $idPedido);
            $stmt->execute();
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pedido) {
                return false;
            }

            /* Busca itens do pedido */
            $stmt = $this->conn->prepare("
                SELECT 
                    pp.id_produto,
                    pp.quantidade,
                    pp.preco_unitario,
                    pr.nome as nome_produto
                FROM Produto_Pedido pp
                INNER JOIN Produto pr ON pp.id_produto = pr.id_produto
                WHERE pp.id_pedido = :id
            ");
            $stmt->bindParam(':id', $idPedido);
            $stmt->execute();
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pedido['itens'] = $itens;

            return $pedido;
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE BUSCA APENAS OS ITENS DE UM PEDIDO E RETORNA UM ARRAY ASSOCIATIVO COM OS DADOS*/
    public function buscarItensPedido($idPedido)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    pp.quantidade,
                    pp.preco_unitario
                FROM Produto_Pedido pp
                WHERE pp.id_pedido = :id
            ");
            $stmt->bindParam(':id', $idPedido);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE LISTA TODOS OS PEDIDOS DE UM CLIENTE ESPECÍFICO */
    public function listarPedidosPorCliente($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.id_pedido,
                    p.data_pedido,
                    p.status_pedido,
                    /* Calcula valor total sempre a partir dos itens */
                    COALESCE(SUM(pp.preco_unitario * pp.quantidade), 0) AS valor_total_calculado,
                    pg.status_pagamento
                FROM Pedido p
                LEFT JOIN Produto_Pedido pp ON pp.id_pedido = p.id_pedido
                LEFT JOIN Pagamento pg ON pg.id_pedido = p.id_pedido
                WHERE p.id_cliente = :id_cliente
                GROUP BY p.id_pedido, p.data_pedido, p.status_pedido, pg.status_pagamento
                ORDER BY p.data_pedido DESC
            ");
            $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE ATUALIZA O STATUS DO PEDIDO E DO PAGAMENTO */
    public function atualizarStatusPedido($idPedido, $statusPedido, $statusPagamento = null)
    {
        try {
            /* Busca status anterior do pedido para verificar se precisa restaurar estoque */
            $stmt = $this->conn->prepare("SELECT status_pedido FROM Pedido WHERE id_pedido = :id");
            $stmt->bindParam(':id', $idPedido);
            $stmt->execute();
            $statusAnterior = $stmt->fetchColumn();
            
            $this->conn->beginTransaction();/* INICIA TRANSAÇÃO */

            /* Atualiza status do pedido */
            $stmt = $this->conn->prepare("
                UPDATE Pedido 
                SET status_pedido = :status_pedido 
                WHERE id_pedido = :id
            ");
            $stmt->bindParam(':status_pedido', $statusPedido);
            $stmt->bindParam(':id', $idPedido);
            $stmt->execute();

            /* Se status de pagamento foi fornecido, atualiza */
            if ($statusPagamento !== null) {
                /* Verifica se já existe pagamento para este pedido */
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Pagamento WHERE id_pedido = :id");
                $stmt->bindParam(':id', $idPedido);
                $stmt->execute();
                $existePagamento = $stmt->fetchColumn() > 0;

                if ($existePagamento) {
                    /* Atualiza pagamento existente */
                    $stmt = $this->conn->prepare("
                        UPDATE Pagamento 
                        SET status_pagamento = :status_pagamento 
                        WHERE id_pedido = :id
                    ");
                    $stmt->bindParam(':status_pagamento', $statusPagamento);
                    $stmt->bindParam(':id', $idPedido);
                    $stmt->execute();
                } else {
                    /* Cria novo pagamento se não existir */
                    $stmt = $this->conn->prepare("
                        INSERT INTO Pagamento (id_pedido, status_pagamento) 
                        VALUES (:id_pedido, :status_pagamento)
                    ");
                    $stmt->bindParam(':id_pedido', $idPedido);
                    $stmt->bindParam(':status_pagamento', $statusPagamento);
                    $stmt->execute();
                }
            }

            /* Se o pedido foi cancelado e estava PENDENTE, restaura o estoque */
            if ($statusPedido === 'CANCELADO' && $statusAnterior === 'PENDENTE') {
                /* Busca itens do pedido para restaurar estoque */
                $stmt = $this->conn->prepare("
                    SELECT id_produto, quantidade, cor, tamanho
                    FROM Produto_Pedido
                    WHERE id_pedido = :id_pedido
                ");
                $stmt->bindParam(':id_pedido', $idPedido);
                $stmt->execute();
                $itensPedido = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                /* Restaura estoque de cada item (dentro da mesma transação) */
                foreach ($itensPedido as $item) {
                    $this->restaurarEstoqueNaTransacao(
                        $item['id_produto'],
                        $item['cor'],
                        $item['tamanho'],
                        $item['quantidade']
                    );
                }
            }

            $this->conn->commit();/* FINALIZA TRANSAÇÃO */
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();/* SE ALGO DER ERRADO, NENHUM DADO SERÁ INSERIDO */
            error_log("Erro ao atualizar status do pedido: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE CRIA UM PEDIDO A PARTIR DO CARRINHO */
    public function criarPedidoDoCarrinho($idCliente, $itensCarrinho, $formaPagamento, $valorTotal)
    {
        try {
            /* Verifica se o cliente existe na tabela Cliente */
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Cliente WHERE id_usuario = :id_cliente");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            $clienteExiste = $stmt->fetchColumn() > 0;
            
            if (!$clienteExiste) {
                error_log("Erro: Cliente com ID $idCliente não existe na tabela Cliente");
                throw new Exception("Cliente não encontrado na base de dados. Verifique se o usuário está cadastrado corretamente.");
            }

            $this->conn->beginTransaction();/* INICIA TRANSAÇÃO */

            /* Cria o pedido */
            $stmt = $this->conn->prepare("
                INSERT INTO Pedido (id_cliente, status_pedido, valor_total)
                VALUES (:id_cliente, 'PENDENTE', :valor_total)
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':valor_total', $valorTotal);
            
            $stmt->execute();
            
            $idPedido = $this->conn->lastInsertId();

            if (!$idPedido || $idPedido === '0') {
                error_log("Erro: lastInsertId retornou false ou 0 para Pedido");
                throw new Exception("Erro ao obter ID do pedido criado");
            }

            /* Agrupa itens por produto (mesmo id_produto) para evitar erro de PRIMARY KEY */
            $itensAgrupados = [];
            foreach ($itensCarrinho as $item) {
                /* Valida dados do item */
                if (!isset($item['id_produto']) || !isset($item['quantidade']) || !isset($item['preco_unitario'])) {
                    error_log("Item com dados incompletos: " . print_r($item, true));
                    throw new Exception("Item do carrinho com dados incompletos");
                }

                $idProduto = (int)$item['id_produto'];
                
                /* Se já existe este produto, soma a quantidade */
                if (isset($itensAgrupados[$idProduto])) {
                    $itensAgrupados[$idProduto]['quantidade'] += (int)$item['quantidade'];
                    /* Mantém a primeira cor/tamanho encontrada (ou concatena se necessário) */
                    if (empty($itensAgrupados[$idProduto]['cor']) && !empty($item['cor'])) {
                        $itensAgrupados[$idProduto]['cor'] = trim($item['cor']);
                    }
                    if (empty($itensAgrupados[$idProduto]['tamanho']) && !empty($item['tamanho'])) {
                        $itensAgrupados[$idProduto]['tamanho'] = trim($item['tamanho']);
                    }
                } else {
                    /* Primeira ocorrência deste produto */
                    $itensAgrupados[$idProduto] = [
                        'id_produto' => $idProduto,
                        'quantidade' => (int)$item['quantidade'],
                        'preco_unitario' => (float)$item['preco_unitario'],
                        'cor' => !empty($item['cor']) ? trim($item['cor']) : null,
                        'tamanho' => !empty($item['tamanho']) ? trim($item['tamanho']) : null
                    ];
                }
            }

            /* Insere itens agrupados em Produto_Pedido e reduz estoque */
            foreach ($itensAgrupados as $item) {
                /* Verifica se o produto existe */
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Produto WHERE id_produto = :id_produto");
                $stmt->bindValue(':id_produto', $item['id_produto'], PDO::PARAM_INT);
                $stmt->execute();
                $produtoExiste = $stmt->fetchColumn() > 0;
                
                if (!$produtoExiste) {
                    error_log("Erro: Produto com ID {$item['id_produto']} não existe");
                    throw new Exception("Produto não encontrado na base de dados");
                }
                
                /* Verifica estoque antes de inserir */
                $estoqueDisponivel = $this->estoqueModel->obterQuantidadeDisponivel(
                    $item['id_produto'], 
                    $item['cor'], 
                    $item['tamanho']
                );
                
                if ($estoqueDisponivel < $item['quantidade']) {
                    throw new Exception("Estoque insuficiente para o produto ID {$item['id_produto']}. Disponível: $estoqueDisponivel, Solicitado: {$item['quantidade']}");
                }
                
                $stmt = $this->conn->prepare("
                    INSERT INTO Produto_Pedido (id_pedido, id_produto, quantidade, preco_unitario, cor, tamanho)
                    VALUES (:id_pedido, :id_produto, :quantidade, :preco_unitario, :cor, :tamanho)
                ");
                $stmt->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
                $stmt->bindValue(':id_produto', $item['id_produto'], PDO::PARAM_INT);
                $stmt->bindValue(':quantidade', $item['quantidade'], PDO::PARAM_INT);
                $stmt->bindValue(':preco_unitario', $item['preco_unitario']);
                $stmt->bindValue(':cor', $item['cor'], $item['cor'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(':tamanho', $item['tamanho'], $item['tamanho'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                
                $stmt->execute();
                
                /* Reduz estoque após inserir o item no pedido (dentro da mesma transação) */
                $this->reduzirEstoqueNaTransacao(
                    $item['id_produto'],
                    $item['cor'],
                    $item['tamanho'],
                    $item['quantidade']
                );
            }

            /* Cria registro de pagamento */
            $stmt = $this->conn->prepare("
                INSERT INTO Pagamento (id_pedido, status_pagamento, forma_pagamento)
                VALUES (:id_pedido, 'PENDENTE', :forma_pagamento)
            ");
            $stmt->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindValue(':forma_pagamento', $formaPagamento);
            
            $stmt->execute();

            $this->conn->commit();/* FINALIZA TRANSAÇÃO */
            return $idPedido;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();/* SE ALGO DER ERRADO, NENHUM DADO SERÁ INSERIDO */
            }
            $errorInfo = $e->errorInfo ?? [];
            $errorMsg = "Erro PDO ao criar pedido: " . $e->getMessage();
            $errorMsg .= " | Código: " . $e->getCode();
            $errorMsg .= " | SQL State: " . ($errorInfo[0] ?? 'N/A');
            $errorMsg .= " | Driver Error: " . ($errorInfo[1] ?? 'N/A');
            $errorMsg .= " | Driver Message: " . ($errorInfo[2] ?? 'N/A');
            error_log($errorMsg);
            error_log("Trace: " . $e->getTraceAsString());
            /* Retorna array com erro para debug */
            return ['erro' => true, 'mensagem' => $errorInfo[2] ?? $e->getMessage()];
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $errorMsg = "Erro geral ao criar pedido do carrinho: " . $e->getMessage();
            error_log($errorMsg);
            error_log("Trace: " . $e->getTraceAsString());
            /* Retorna array com erro para debug */
            return ['erro' => true, 'mensagem' => $e->getMessage()];
        }
    }

    /* FUNÇÃO AUXILIAR PARA REDUZIR ESTOQUE DENTRO DE UMA TRANSAÇÃO EXISTENTE */
    private function reduzirEstoqueNaTransacao($idProduto, $cor, $tamanho, $quantidade)
    {
        try {
            /* Busca entradas de estoque que contenham a cor e tamanho */
            $stmt = $this->conn->prepare("
                SELECT id_estoque, quantidade, cores_disponiveis, tamanhos_disponiveis
                FROM Estoque
                WHERE id_produto = :id_produto
                AND quantidade > 0
                ORDER BY quantidade DESC
            ");
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            $entradasEstoque = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $quantidadeRestante = (int)$quantidade;

            foreach ($entradasEstoque as $entrada) {
                if ($quantidadeRestante <= 0) {
                    break;
                }

                $temCor = false;
                $temTamanho = false;

                if (empty($cor)) {
                    $temCor = true;
                } elseif (!empty($entrada['cores_disponiveis'])) {
                    $coresArray = array_map('trim', explode(',', $entrada['cores_disponiveis']));
                    $temCor = in_array(trim($cor), $coresArray);
                }

                if (empty($tamanho)) {
                    $temTamanho = true;
                } elseif (!empty($entrada['tamanhos_disponiveis'])) {
                    $tamanhosArray = array_map('trim', explode(',', $entrada['tamanhos_disponiveis']));
                    $temTamanho = in_array(trim($tamanho), $tamanhosArray);
                }

                if ($temCor && $temTamanho) {
                    $quantidadeDisponivel = (int)$entrada['quantidade'];
                    $quantidadeAReduzir = min($quantidadeRestante, $quantidadeDisponivel);
                    $novaQuantidade = $quantidadeDisponivel - $quantidadeAReduzir;

                    $stmt = $this->conn->prepare("
                        UPDATE Estoque 
                        SET quantidade = :quantidade 
                        WHERE id_estoque = :id_estoque
                    ");
                    $stmt->bindValue(':quantidade', $novaQuantidade, PDO::PARAM_INT);
                    $stmt->bindValue(':id_estoque', $entrada['id_estoque'], PDO::PARAM_INT);
                    $stmt->execute();

                    $quantidadeRestante -= $quantidadeAReduzir;
                }
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao reduzir estoque na transação: " . $e->getMessage());
            throw $e; /* Propaga o erro para fazer rollback da transação principal */
        }
    }

    /* FUNÇÃO AUXILIAR PARA RESTAURAR ESTOQUE DENTRO DE UMA TRANSAÇÃO EXISTENTE */
    private function restaurarEstoqueNaTransacao($idProduto, $cor, $tamanho, $quantidade)
    {
        try {
            /* Busca entradas de estoque que contenham a cor e tamanho */
            $stmt = $this->conn->prepare("
                SELECT id_estoque, quantidade, cores_disponiveis, tamanhos_disponiveis
                FROM Estoque
                WHERE id_produto = :id_produto
                ORDER BY quantidade ASC
            ");
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            $entradasEstoque = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $quantidadeRestante = (int)$quantidade;

            /* Tenta restaurar em entradas que já têm a cor/tamanho */
            foreach ($entradasEstoque as $entrada) {
                if ($quantidadeRestante <= 0) {
                    break;
                }

                $temCor = false;
                $temTamanho = false;

                if (empty($cor)) {
                    $temCor = true;
                } elseif (!empty($entrada['cores_disponiveis'])) {
                    $coresArray = array_map('trim', explode(',', $entrada['cores_disponiveis']));
                    $temCor = in_array(trim($cor), $coresArray);
                }

                if (empty($tamanho)) {
                    $temTamanho = true;
                } elseif (!empty($entrada['tamanhos_disponiveis'])) {
                    $tamanhosArray = array_map('trim', explode(',', $entrada['tamanhos_disponiveis']));
                    $temTamanho = in_array(trim($tamanho), $tamanhosArray);
                }

                if ($temCor && $temTamanho) {
                    $quantidadeAtual = (int)$entrada['quantidade'];
                    $quantidadeARestaurar = $quantidadeRestante;
                    $novaQuantidade = $quantidadeAtual + $quantidadeARestaurar;

                    $stmt = $this->conn->prepare("
                        UPDATE Estoque 
                        SET quantidade = :quantidade 
                        WHERE id_estoque = :id_estoque
                    ");
                    $stmt->bindValue(':quantidade', $novaQuantidade, PDO::PARAM_INT);
                    $stmt->bindValue(':id_estoque', $entrada['id_estoque'], PDO::PARAM_INT);
                    $stmt->execute();

                    $quantidadeRestante = 0;
                    break;
                }
            }

            /* Se ainda há quantidade para restaurar e não encontrou entrada compatível, cria nova entrada */
            if ($quantidadeRestante > 0) {
                $stmt = $this->conn->prepare("
                    INSERT INTO Estoque (id_produto, quantidade, cores_disponiveis, tamanhos_disponiveis, data_cadastro)
                    VALUES (:id_produto, :quantidade, :cor, :tamanho, CURDATE())
                ");
                $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
                $stmt->bindValue(':quantidade', $quantidadeRestante, PDO::PARAM_INT);
                $stmt->bindValue(':cor', $cor, $cor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(':tamanho', $tamanho, $tamanho === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao restaurar estoque na transação: " . $e->getMessage());
            throw $e; /* Propaga o erro para fazer rollback da transação principal */
        }
    }
}

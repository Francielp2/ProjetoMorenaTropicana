<?php
/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* CRIAÇÃO DA CLASSE PEDIDO MODEL */
class PedidoModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
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

    /* FUNÇÃO QUE BUSCA PEDIDOS POR TERMO E FILTROS */
    public function buscarPedidosPorTermo($termo = '', $filtroStatusPedido = '', $filtroStatusPagamento = '', $dataInicial = '', $dataFinal = '', $valorMin = '', $valorMax = '')
    {
        try {
            /* Remove espaços do início e fim */
            $termo = trim($termo);
            $filtroStatusPedido = trim($filtroStatusPedido);
            $filtroStatusPagamento = trim($filtroStatusPagamento);

            /* CRIA UM CÓDIGO BASE DE PESQUISA SQL */
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

    /* FUNÇÃO QUE BUSCA UM PEDIDO ESPECÍFICO POR ID COM TODOS OS DETALHES */
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

    /* FUNÇÃO QUE BUSCA APENAS OS ITENS DE UM PEDIDO */
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

    /* FUNÇÃO QUE ATUALIZA O STATUS DO PEDIDO E DO PAGAMENTO */
    public function atualizarStatusPedido($idPedido, $statusPedido, $statusPagamento = null)
    {
        try {
            $this->conn->beginTransaction();

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

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}

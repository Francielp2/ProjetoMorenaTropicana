<?php
/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* CRIAÇÃO DA CLASSE ESTOQUE MODEL */
class EstoqueModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /* FUNÇÃO QUE LISTA TODOS OS REGISTROS DE ESTOQUE E RETORNA UM ARRAY CONTENDO OS DADOS */
    public function listarTodosEstoques()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT 
                e.id_estoque,
                e.quantidade,
                COALESCE(e.tamanhos_disponiveis, 'Não informado') as tamanhos_disponiveis,
                COALESCE(e.cores_disponiveis, 'Não informado') as cores_disponiveis,
                COALESCE(e.modelo_produto, '') as modelo_produto,
                e.data_cadastro,
                p.id_produto,
                p.nome as nome_produto
            FROM Estoque e
            INNER JOIN Produto p ON e.id_produto = p.id_produto
            ORDER BY e.data_cadastro DESC, p.nome ASC
        ");
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug
            error_log("Query executada com sucesso. Total de registros: " . count($resultado));

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao listar estoques: " . $e->getMessage());
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA ESTOQUES POR TERMO E FILTROS */
    public function buscarEstoquesPorTermo($termo = '', $filtroStatus = '')
    {
        try {
            /* Remove espaços do início e fim */
            $termo = trim($termo);
            $filtroStatus = trim($filtroStatus);

            /* CRIA UM CÓDIGO BASE DE PESQUISA SQL */
            $sql = "
                SELECT 
                    e.id_estoque,
                    e.quantidade,
                    COALESCE(e.tamanhos_disponiveis, '') as tamanhos_disponiveis,
                    COALESCE(e.cores_disponiveis, '') as cores_disponiveis,
                    COALESCE(e.modelo_produto, '') as modelo_produto,
                    e.data_cadastro,
                    p.id_produto,
                    p.nome as nome_produto
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                WHERE 1=1
            ";

            $params = [];

            /* Adiciona filtro de pesquisa (nome do produto, tamanho ou cor) */
            if (!empty($termo)) {
                $termoBusca = '%' . $termo . '%';
                $sql .= " AND (p.nome LIKE :termo OR COALESCE(e.tamanhos_disponiveis, '') LIKE :termo OR COALESCE(e.cores_disponiveis, '') LIKE :termo OR COALESCE(e.modelo_produto, '') LIKE :termo)";
                $params[':termo'] = $termoBusca;
            }

            /* Adiciona filtro de status do estoque */
            if (!empty($filtroStatus)) {
                if ($filtroStatus === 'zerado') {
                    $sql .= " AND e.quantidade = 0";
                } elseif ($filtroStatus === 'critico') {
                    $sql .= " AND e.quantidade > 0 AND e.quantidade < 3";
                } elseif ($filtroStatus === 'baixo') {
                    $sql .= " AND e.quantidade >= 3 AND e.quantidade < 10";
                } elseif ($filtroStatus === 'disponivel') {
                    $sql .= " AND e.quantidade >= 10";
                }
            }

            $sql .= " ORDER BY e.data_cadastro DESC, p.nome ASC";

            $stmt = $this->conn->prepare($sql);

            /* FOREACH RESPONSÁVEL POR NORMALIZAR TODOS OS PARÂMETROS PASSADOS */
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Estoques encontrados na busca: " . count($resultado));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar estoques por termo: " . $e->getMessage());
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA UM REGISTRO DE ESTOQUE ESPECÍFICO POR ID */
    public function buscarEstoquePorId($idEstoque)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    e.id_estoque,
                    e.quantidade,
                    COALESCE(e.tamanhos_disponiveis, '') as tamanhos_disponiveis,
                    COALESCE(e.cores_disponiveis, '') as cores_disponiveis,
                    COALESCE(e.modelo_produto, '') as modelo_produto,
                    e.data_cadastro,
                    p.id_produto,
                    p.nome as nome_produto
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                WHERE e.id_estoque = :id
            ");
            $stmt->bindValue(':id', $idEstoque, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao buscar estoque por ID: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE CRIA UMA NOVA ENTRADA DE ESTOQUE */
    public function criarEntradaEstoque($idProduto, $quantidade, $tamanhosDisponiveis, $coresDisponiveis, $modeloProduto = null, $dataCadastro = null)
    {
        try {
            if ($dataCadastro === null) {
                $dataCadastro = date('Y-m-d');
            }

            // Trata valores vazios como NULL
            if (empty($modeloProduto)) {
                $modeloProduto = null;
            }
            if (empty($tamanhosDisponiveis)) {
                $tamanhosDisponiveis = null;
            }
            if (empty($coresDisponiveis)) {
                $coresDisponiveis = null;
            }

            $stmt = $this->conn->prepare("
                INSERT INTO Estoque (id_produto, quantidade, tamanhos_disponiveis, cores_disponiveis, modelo_produto, data_cadastro)
                VALUES (:id_produto, :quantidade, :tamanhos_disponiveis, :cores_disponiveis, :modelo_produto, :data_cadastro)
            ");

            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);

            // Bind condicional para tamanhos
            if ($tamanhosDisponiveis === null) {
                $stmt->bindValue(':tamanhos_disponiveis', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':tamanhos_disponiveis', $tamanhosDisponiveis, PDO::PARAM_STR);
            }

            // Bind condicional para cores
            if ($coresDisponiveis === null) {
                $stmt->bindValue(':cores_disponiveis', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':cores_disponiveis', $coresDisponiveis, PDO::PARAM_STR);
            }

            // Bind condicional para modelo
            if ($modeloProduto === null) {
                $stmt->bindValue(':modelo_produto', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':modelo_produto', $modeloProduto, PDO::PARAM_STR);
            }

            $stmt->bindValue(':data_cadastro', $dataCadastro, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                error_log("Erro ao executar INSERT: " . print_r($stmt->errorInfo(), true));
                return false;
            }

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar estoque: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE ATUALIZA UM REGISTRO DE ESTOQUE */
    public function atualizarEstoque($idEstoque, $quantidade, $tamanhosDisponiveis, $coresDisponiveis, $modeloProduto = null, $dataCadastro = null)
    {
        try {
            if ($dataCadastro === null) {
                $dataCadastro = date('Y-m-d');
            }

            $stmt = $this->conn->prepare("
                UPDATE Estoque 
                SET quantidade = :quantidade, 
                    tamanhos_disponiveis = :tamanhos_disponiveis,
                    cores_disponiveis = :cores_disponiveis,
                    modelo_produto = :modelo_produto, 
                    data_cadastro = :data_cadastro
                WHERE id_estoque = :id
            ");

            // Trata valores vazios como NULL para modelo_produto
            if (empty($modeloProduto)) {
                $modeloProduto = null;
            }

            $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            $stmt->bindValue(':tamanhos_disponiveis', $tamanhosDisponiveis);
            $stmt->bindValue(':cores_disponiveis', $coresDisponiveis);
            $stmt->bindValue(':modelo_produto', $modeloProduto, $modeloProduto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':data_cadastro', $dataCadastro);
            $stmt->bindValue(':id', $idEstoque, PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE ADICIONA QUANTIDADE A UM REGISTRO DE ESTOQUE EXISTENTE */
    public function adicionarQuantidadeEstoque($idEstoque, $quantidadeAdicionar, $dataCadastro = null)
    {
        try {
            /* Busca o estoque atual */
            $estoqueAtual = $this->buscarEstoquePorId($idEstoque);
            if (!$estoqueAtual) {
                return false;
            }

            /* Calcula nova quantidade */
            $novaQuantidade = (int)$estoqueAtual['quantidade'] + (int)$quantidadeAdicionar;

            /* Se não foi informada data, usa a data atual */
            if ($dataCadastro === null) {
                $dataCadastro = date('Y-m-d');
            }

            /* Atualiza a quantidade */
            $stmt = $this->conn->prepare("
                UPDATE Estoque 
                SET quantidade = :quantidade,
                    data_cadastro = :data_cadastro
                WHERE id_estoque = :id
            ");

            $stmt->bindParam(':quantidade', $novaQuantidade);
            $stmt->bindParam(':data_cadastro', $dataCadastro);
            $stmt->bindParam(':id', $idEstoque);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE CALCULA O RESUMO DO ESTOQUE */
    public function calcularResumoEstoque()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_itens,
                    SUM(CASE WHEN quantidade >= 10 THEN 1 ELSE 0 END) as disponiveis,
                    SUM(CASE WHEN quantidade >= 3 AND quantidade < 10 THEN 1 ELSE 0 END) as estoque_baixo,
                    SUM(CASE WHEN quantidade < 3 THEN 1 ELSE 0 END) as criticos_sem_estoque
                FROM Estoque
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'total_itens' => 0,
                'disponiveis' => 0,
                'estoque_baixo' => 0,
                'criticos_sem_estoque' => 0
            ];
        }
    }

    /* FUNÇÃO QUE LISTA TODOS OS PRODUTOS PARA SEREM USADOS EM SELECTS */
    public function listarProdutos()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT id_produto, nome
                FROM Produto
                ORDER BY nome ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA CORES E TAMANHOS DISPONÍVEIS PARA UM PRODUTO */
    public function buscarCoresETamanhosPorProduto($idProduto)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT
                    COALESCE(cores_disponiveis, '') as cores_disponiveis,
                    COALESCE(tamanhos_disponiveis, '') as tamanhos_disponiveis,
                    quantidade
                FROM Estoque
                WHERE id_produto = :id_produto
                AND quantidade > 0
                AND (
                    (cores_disponiveis IS NOT NULL AND cores_disponiveis != '') OR
                    (tamanhos_disponiveis IS NOT NULL AND tamanhos_disponiveis != '')
                )
            ");
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            /* Processa os resultados para extrair cores e tamanhos únicos */
            $cores = [];
            $tamanhos = [];

            foreach ($resultados as $row) {
                /* Processa cores */
                if (!empty($row['cores_disponiveis'])) {
                    $coresArray = array_map('trim', explode(',', $row['cores_disponiveis']));
                    foreach ($coresArray as $cor) {
                        if (!empty($cor) && !in_array($cor, $cores)) {
                            $cores[] = $cor;
                        }
                    }
                }

                /* Processa tamanhos */
                if (!empty($row['tamanhos_disponiveis'])) {
                    $tamanhosArray = array_map('trim', explode(',', $row['tamanhos_disponiveis']));
                    foreach ($tamanhosArray as $tamanho) {
                        if (!empty($tamanho) && !in_array($tamanho, $tamanhos)) {
                            $tamanhos[] = $tamanho;
                        }
                    }
                }
            }

            return [
                'cores' => $cores,
                'tamanhos' => $tamanhos
            ];
        } catch (PDOException $e) {
            error_log("Erro ao buscar cores e tamanhos por produto: " . $e->getMessage());
            return [
                'cores' => [],
                'tamanhos' => []
            ];
        }
    }

    /* FUNÇÃO QUE VERIFICA SE HÁ ESTOQUE DISPONÍVEL PARA UM PRODUTO COM COR E TAMANHO ESPECÍFICOS */
    public function verificarEstoqueDisponivel($idProduto, $cor, $tamanho, $quantidadeNecessaria)
    {
        try {
            /* Busca entradas de estoque que contenham a cor e tamanho desejados */
            $stmt = $this->conn->prepare("
                SELECT id_estoque, quantidade, cores_disponiveis, tamanhos_disponiveis
                FROM Estoque
                WHERE id_produto = :id_produto
                AND quantidade > 0
            ");
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            $entradasEstoque = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $estoqueTotal = 0;

            foreach ($entradasEstoque as $entrada) {
                $temCor = false;
                $temTamanho = false;

                /* Verifica se a cor está disponível nesta entrada */
                if (empty($cor)) {
                    $temCor = true; /* Se não especificou cor, aceita qualquer */
                } elseif (!empty($entrada['cores_disponiveis'])) {
                    $coresArray = array_map('trim', explode(',', $entrada['cores_disponiveis']));
                    $temCor = in_array(trim($cor), $coresArray);
                }

                /* Verifica se o tamanho está disponível nesta entrada */
                if (empty($tamanho)) {
                    $temTamanho = true; /* Se não especificou tamanho, aceita qualquer */
                } elseif (!empty($entrada['tamanhos_disponiveis'])) {
                    $tamanhosArray = array_map('trim', explode(',', $entrada['tamanhos_disponiveis']));
                    $temTamanho = in_array(trim($tamanho), $tamanhosArray);
                }

                /* Se esta entrada tem a cor e tamanho desejados, soma a quantidade */
                if ($temCor && $temTamanho) {
                    $estoqueTotal += (int)$entrada['quantidade'];
                }
            }

            return $estoqueTotal >= $quantidadeNecessaria;
        } catch (PDOException $e) {
            error_log("Erro ao verificar estoque disponível: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE RETORNA A QUANTIDADE DISPONÍVEL DE ESTOQUE */
    public function obterQuantidadeDisponivel($idProduto, $cor, $tamanho)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT id_estoque, quantidade, cores_disponiveis, tamanhos_disponiveis
                FROM Estoque
                WHERE id_produto = :id_produto
                AND quantidade > 0
            ");
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            $entradasEstoque = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $estoqueTotal = 0;

            foreach ($entradasEstoque as $entrada) {
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
                    $estoqueTotal += (int)$entrada['quantidade'];
                }
            }

            return $estoqueTotal;
        } catch (PDOException $e) {
            error_log("Erro ao obter quantidade disponível: " . $e->getMessage());
            return 0;
        }
    }

    /* FUNÇÃO QUE REDUZ O ESTOQUE DE UM PRODUTO COM COR E TAMANHO ESPECÍFICOS */
    public function reduzirEstoque($idProduto, $cor, $tamanho, $quantidade)
    {
        try {
            $this->conn->beginTransaction();

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

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro ao reduzir estoque: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE RESTAURA O ESTOQUE DE UM PRODUTO (QUANDO PEDIDO É CANCELADO) */
    public function restaurarEstoque($idProduto, $cor, $tamanho, $quantidade)
    {
        try {
            $this->conn->beginTransaction();

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

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro ao restaurar estoque: " . $e->getMessage());
            return false;
        }
    }
}

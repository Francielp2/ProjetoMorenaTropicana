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
}

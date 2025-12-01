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
                    e.modelo_produto,
                    e.data_cadastro,
                    p.id_produto,
                    p.nome as nome_produto
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                ORDER BY e.data_cadastro DESC, p.nome ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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
                    e.modelo_produto,
                    e.data_cadastro,
                    p.id_produto,
                    p.nome as nome_produto
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                WHERE 1=1
            ";

            $params = [];

            /* Adiciona filtro de pesquisa (nome do produto ou modelo) */
            if (!empty($termo)) {
                $termoBusca = '%' . $termo . '%';
                $sql .= " AND (p.nome LIKE :termo OR e.modelo_produto LIKE :termo)";
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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
                    e.modelo_produto,
                    e.data_cadastro,
                    p.id_produto,
                    p.nome as nome_produto
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                WHERE e.id_estoque = :id
            ");
            $stmt->bindParam(':id', $idEstoque);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE CRIA UMA NOVA ENTRADA DE ESTOQUE */
    public function criarEntradaEstoque($idProduto, $quantidade, $modeloProduto, $dataCadastro = null)
    {
        try {
            if ($dataCadastro === null) {
                $dataCadastro = date('Y-m-d');
            }

            $stmt = $this->conn->prepare("
                INSERT INTO Estoque (id_produto, quantidade, modelo_produto, data_cadastro)
                VALUES (:id_produto, :quantidade, :modelo_produto, :data_cadastro)
            ");

            $stmt->bindParam(':id_produto', $idProduto);
            $stmt->bindParam(':quantidade', $quantidade);
            $stmt->bindParam(':modelo_produto', $modeloProduto);
            $stmt->bindParam(':data_cadastro', $dataCadastro);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE ATUALIZA UM REGISTRO DE ESTOQUE */
    public function atualizarEstoque($idEstoque, $quantidade, $modeloProduto, $dataCadastro)
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE Estoque 
                SET quantidade = :quantidade, 
                    modelo_produto = :modelo_produto, 
                    data_cadastro = :data_cadastro
                WHERE id_estoque = :id
            ");

            $stmt->bindParam(':quantidade', $quantidade);
            $stmt->bindParam(':modelo_produto', $modeloProduto);
            $stmt->bindParam(':data_cadastro', $dataCadastro);
            $stmt->bindParam(':id', $idEstoque);

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
}


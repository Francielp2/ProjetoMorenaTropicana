<?php
/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* CRIAÇÃO DA CLASSE PRODUTO MODEL */
class ProdutoModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /* FUNÇÃO QUE LISTA TODOS OS PRODUTOS E RETORNA UM ARRAY CONTENDO OS DADOS*/
    public function listarTodosProdutos()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.categoria,
                    p.preco,
                    p.descricao,
                    p.tamanhos_disponiveis,
                    p.cores_disponiveis,
                    p.imagens,
                    COALESCE(SUM(e.quantidade), 0) as estoque_total
                FROM Produto p
                LEFT JOIN Estoque e ON p.id_produto = e.id_produto
                GROUP BY p.id_produto
                ORDER BY p.id_produto ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA PRODUTOS POR TERMO E FILTROS */
    public function buscarProdutosPorTermo($termo = '', $filtroCategoria = '')
    {
        try {
            /* Remove espaços do início e fim */
            $termo = trim($termo);
            $filtroCategoria = trim($filtroCategoria);

            /* CRIA UM CÓDIGO BASE DE PESQUISA SQL */
            $sql = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.categoria,
                    p.preco,
                    p.descricao,
                    p.tamanhos_disponiveis,
                    p.cores_disponiveis,
                    p.imagens,
                    COALESCE(SUM(e.quantidade), 0) as estoque_total
                FROM Produto p
                LEFT JOIN Estoque e ON p.id_produto = e.id_produto
                WHERE 1=1
            ";

            $params = [];

            /* Adiciona filtro de pesquisa (nome ou categoria) */
            if (!empty($termo)) {
                $termoBusca = '%' . $termo . '%';
                $sql .= " AND (p.nome LIKE :termo OR p.categoria LIKE :termo)";
                $params[':termo'] = $termoBusca;
            }

            /* Adiciona filtro de categoria específica */
            if (!empty($filtroCategoria)) {
                $sql .= " AND p.categoria = :categoria";
                $params[':categoria'] = $filtroCategoria;
            }

            $sql .= " GROUP BY p.id_produto ORDER BY p.id_produto ASC";/* PEGA O ARQUIVO A CONSULTA FINAL SALVA EM SQL, E ADICIONA UMA ORDANAÇÃO EM ORDEM CRESCENTE DE ACORDO COM O ID */

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

    /* FUNÇÃO QUE BUSCA UM PRODUTO ESPECÍFICO POR ID */
    public function buscarProdutoPorId($idProduto)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.*,
                    COALESCE(SUM(e.quantidade), 0) as estoque_total
                FROM Produto p
                LEFT JOIN Estoque e ON p.id_produto = e.id_produto
                WHERE p.id_produto = :id
                GROUP BY p.id_produto
            ");
            $stmt->bindParam(':id', $idProduto);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE CRIA UM NOVO PRODUTO AS VALIDAÇÕES SÃO FEITAS NO CONTROLER, ESSA FUNÇÃO SÓ TENTA INSERIR OS VALORES NO BANCOD E DADOS*/
    public function criarProduto($nome, $descricao, $categoria, $preco, $tamanhos, $cores, $imagem = null)
    {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO Produto (nome, descricao, categoria, preco, tamanhos_disponiveis, cores_disponiveis, imagens)
                VALUES (:nome, :descricao, :categoria, :preco, :tamanhos, :cores, :imagem)
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':tamanhos', $tamanhos);
            $stmt->bindParam(':cores', $cores);
            $stmt->bindParam(':imagem', $imagem);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE ATUALIZA UM PRODUTO AS VALIDAÇÕES SÃO FEITAS NO CONTROLER, ESSA FUNÇÃO SÓ TENTA INSERIR OS VALORES NO BANCOD E DADOS*/
    public function atualizarProduto($idProduto, $nome, $descricao, $categoria, $preco, $tamanhos, $cores, $imagem = null)
    {
        try {
            /* Se imagem foi fornecida, atualiza também */
            if (!empty($imagem)) {
                $stmt = $this->conn->prepare("
                    UPDATE Produto 
                    SET nome = :nome, 
                        descricao = :descricao, 
                        categoria = :categoria, 
                        preco = :preco, 
                        tamanhos_disponiveis = :tamanhos, 
                        cores_disponiveis = :cores,
                        imagens = :imagem
                    WHERE id_produto = :id
                ");
                $stmt->bindParam(':imagem', $imagem);
            } else {
                $stmt = $this->conn->prepare("
                    UPDATE Produto 
                    SET nome = :nome, 
                        descricao = :descricao, 
                        categoria = :categoria, 
                        preco = :preco, 
                        tamanhos_disponiveis = :tamanhos, 
                        cores_disponiveis = :cores
                    WHERE id_produto = :id
                ");
            }

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':tamanhos', $tamanhos);
            $stmt->bindParam(':cores', $cores);
            $stmt->bindParam(':id', $idProduto);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE EXCLUI UM PRODUTO AS VALIDAÇÕES SÃO FEITAS NO CONTROLER, ESSA FUNÇÃO SÓ TENTA EXCLUITR VALORES DO BANCO DE DADOS */
    public function excluirProduto($idProduto)
    {
        try {
            /* O CASCADE já cuida de excluir os registros relacionados em Estoque e Administrador_Produto */
            $stmt = $this->conn->prepare("DELETE FROM Produto WHERE id_produto = :id");
            $stmt->bindParam(':id', $idProduto);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE BUSCA TODAS AS CATEGORIAS EXISTENTES PARA SEREM EXIBIDAS NA HORA DE FILTRAR*/
    public function listarCategorias()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT categoria 
                FROM Produto 
                WHERE categoria IS NOT NULL 
                ORDER BY categoria ASC
            ");
            $stmt->execute();
            $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $categorias;
        } catch (PDOException $e) {
            return [];
        }
    }
}

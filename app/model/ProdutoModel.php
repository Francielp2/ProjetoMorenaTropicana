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
    public function criarProduto($nome, $descricao, $categoria, $preco, $imagem = null)
    {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO Produto (nome, descricao, categoria, preco, imagens)
                VALUES (:nome, :descricao, :categoria, :preco, :imagem)
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':imagem', $imagem);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO QUE ATUALIZA UM PRODUTO AS VALIDAÇÕES SÃO FEITAS NO CONTROLER, ESSA FUNÇÃO SÓ TENTA INSERIR OS VALORES NO BANCOD E DADOS*/
    public function atualizarProduto($idProduto, $nome, $descricao, $categoria, $preco, $imagem = null)
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
                        preco = :preco
                    WHERE id_produto = :id
                ");
            }

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':preco', $preco);
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

    /* FUNÇÃO QUE BUSCA TODAS AS CATEGORIAS EXISTENTES COM PRODUTOS EM ESTOQUE */
    public function listarCategorias()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT p.categoria 
                FROM Produto p
                INNER JOIN Estoque e ON p.id_produto = e.id_produto
                WHERE p.categoria IS NOT NULL 
                AND p.categoria != ''
                AND e.quantidade > 0
                ORDER BY p.categoria ASC
            ");
            $stmt->execute();
            $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $categorias;
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA TODAS AS CORES ÚNICAS DO BANCO DE DADOS */
    public function listarCores()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT cores_disponiveis
                FROM Estoque
                WHERE cores_disponiveis IS NOT NULL 
                AND cores_disponiveis != ''
                AND quantidade > 0
            ");
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $cores = [];
            foreach ($resultados as $coresString) {
                $coresArray = array_map('trim', explode(',', $coresString));
                foreach ($coresArray as $cor) {
                    if (!empty($cor) && !in_array($cor, $cores)) {
                        $cores[] = $cor;
                    }
                }
            }
            
            sort($cores);
            return $cores;
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA TAMANHOS DISPONÍVEIS BASEADO NA CATEGORIA */
    public function listarTamanhosPorCategoria($categoria = '')
    {
        try {
            $sql = "
                SELECT DISTINCT e.tamanhos_disponiveis
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                WHERE e.tamanhos_disponiveis IS NOT NULL 
                AND e.tamanhos_disponiveis != ''
                AND e.quantidade > 0
            ";

            $params = [];
            if (!empty($categoria)) {
                /* Se categoria contém vírgula, são múltiplas categorias */
                if (strpos($categoria, ',') !== false) {
                    $categoriasArray = array_map('trim', explode(',', $categoria));
                    $placeholders = [];
                    foreach ($categoriasArray as $index => $cat) {
                        $key = ':categoria' . $index;
                        $placeholders[] = $key;
                        $params[$key] = $cat;
                    }
                    $sql .= " AND p.categoria IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND p.categoria = :categoria";
                    $params[':categoria'] = $categoria;
                }
            }

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $tamanhos = [];
            foreach ($resultados as $tamanhosString) {
                $tamanhosArray = array_map('trim', explode(',', $tamanhosString));
                foreach ($tamanhosArray as $tamanho) {
                    if (!empty($tamanho) && !in_array($tamanho, $tamanhos)) {
                        $tamanhos[] = $tamanho;
                    }
                }
            }
            
            sort($tamanhos);
            return $tamanhos;
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA CORES DISPONÍVEIS BASEADO NA CATEGORIA E TAMANHO */
    public function listarCoresPorCategoriaETamanho($categoria = '', $tamanho = '')
    {
        try {
            $sql = "
                SELECT DISTINCT e.cores_disponiveis
                FROM Estoque e
                INNER JOIN Produto p ON e.id_produto = p.id_produto
                WHERE e.cores_disponiveis IS NOT NULL 
                AND e.cores_disponiveis != ''
                AND e.quantidade > 0
            ";

            $params = [];
            if (!empty($categoria)) {
                /* Se categoria contém vírgula, são múltiplas categorias */
                if (strpos($categoria, ',') !== false) {
                    $categoriasArray = array_map('trim', explode(',', $categoria));
                    $placeholders = [];
                    foreach ($categoriasArray as $index => $cat) {
                        $key = ':categoria' . $index;
                        $placeholders[] = $key;
                        $params[$key] = $cat;
                    }
                    $sql .= " AND p.categoria IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND p.categoria = :categoria";
                    $params[':categoria'] = $categoria;
                }
            }

            if (!empty($tamanho)) {
                /* Se tamanho contém vírgula, são múltiplos tamanhos */
                if (strpos($tamanho, ',') !== false) {
                    $tamanhosArray = array_map('trim', explode(',', $tamanho));
                    $condicoes = [];
                    foreach ($tamanhosArray as $index => $tam) {
                        $key = ':tamanho' . $index;
                        $condicoes[] = "e.tamanhos_disponiveis LIKE " . $key;
                        $params[$key] = '%' . $tam . '%';
                    }
                    $sql .= " AND (" . implode(' OR ', $condicoes) . ")";
                } else {
                    $sql .= " AND e.tamanhos_disponiveis LIKE :tamanho";
                    $params[':tamanho'] = '%' . $tamanho . '%';
                }
            }

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $cores = [];
            foreach ($resultados as $coresString) {
                $coresArray = array_map('trim', explode(',', $coresString));
                foreach ($coresArray as $cor) {
                    if (!empty($cor) && !in_array($cor, $cores)) {
                        $cores[] = $cor;
                    }
                }
            }
            
            sort($cores);
            return $cores;
        } catch (PDOException $e) {
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA PRODUTOS COM FILTROS DE CATEGORIA, TAMANHO E COR */
    public function buscarProdutosComFiltros($categoria = '', $tamanho = '', $cor = '')
    {
        try {
            $categoria = trim($categoria);
            $tamanho = trim($tamanho);
            $cor = trim($cor);

            $temFiltroEstoque = !empty($tamanho) || !empty($cor);
            $joinType = $temFiltroEstoque ? 'INNER JOIN' : 'LEFT JOIN';

            $sql = "
                SELECT DISTINCT
                    p.id_produto,
                    p.nome,
                    p.categoria,
                    p.preco,
                    p.descricao,
                    p.imagens,
                    COALESCE(SUM(e.quantidade), 0) as estoque_total
                FROM Produto p
                $joinType Estoque e ON p.id_produto = e.id_produto
                WHERE 1=1
            ";

            $params = [];

            if (!empty($categoria)) {
                /* Se categoria contém vírgula, são múltiplas categorias */
                if (strpos($categoria, ',') !== false) {
                    $categoriasArray = array_map('trim', explode(',', $categoria));
                    $placeholders = [];
                    foreach ($categoriasArray as $index => $cat) {
                        $key = ':categoria' . $index;
                        $placeholders[] = $key;
                        $params[$key] = $cat;
                    }
                    $sql .= " AND p.categoria IN (" . implode(',', $placeholders) . ")";
                } else {
                    $sql .= " AND p.categoria = :categoria";
                    $params[':categoria'] = $categoria;
                }
            }

            if (!empty($tamanho)) {
                /* Se tamanho contém vírgula, são múltiplos tamanhos */
                if (strpos($tamanho, ',') !== false) {
                    $tamanhosArray = array_map('trim', explode(',', $tamanho));
                    $condicoes = [];
                    foreach ($tamanhosArray as $index => $tam) {
                        $key = ':tamanho' . $index;
                        $condicoes[] = "e.tamanhos_disponiveis LIKE " . $key;
                        $params[$key] = '%' . $tam . '%';
                    }
                    $sql .= " AND (" . implode(' OR ', $condicoes) . ")";
                } else {
                    $sql .= " AND e.tamanhos_disponiveis LIKE :tamanho";
                    $params[':tamanho'] = '%' . $tamanho . '%';
                }
            }

            if (!empty($cor)) {
                /* Se cor contém vírgula, são múltiplas cores */
                if (strpos($cor, ',') !== false) {
                    $coresArray = array_map('trim', explode(',', $cor));
                    $condicoes = [];
                    foreach ($coresArray as $index => $c) {
                        $key = ':cor' . $index;
                        $condicoes[] = "e.cores_disponiveis LIKE " . $key;
                        $params[$key] = '%' . $c . '%';
                    }
                    $sql .= " AND (" . implode(' OR ', $condicoes) . ")";
                } else {
                    $sql .= " AND e.cores_disponiveis LIKE :cor";
                    $params[':cor'] = '%' . $cor . '%';
                }
            }

            if ($temFiltroEstoque) {
                $sql .= " AND e.quantidade > 0";
            }

            $sql .= " GROUP BY p.id_produto ORDER BY p.id_produto ASC";

            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

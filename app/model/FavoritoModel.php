<?php
/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* CRIAÇÃO DA CLASSE FAVORITO MODEL */
class FavoritoModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /* FUNÇÃO QUE ADICIONA UM PRODUTO AOS FAVORITOS */
    public function adicionarFavorito($idCliente, $idProduto)
    {
        try {
            /* Verifica se já está nos favoritos */
            $stmt = $this->conn->prepare("
                SELECT id_favorito 
                FROM Favoritos 
                WHERE id_cliente = :id_cliente AND id_produto = :id_produto
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                return false; /* Já está nos favoritos */
            }

            /* Adiciona aos favoritos */
            $stmt = $this->conn->prepare("
                INSERT INTO Favoritos (id_cliente, id_produto)
                VALUES (:id_cliente, :id_produto)
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao adicionar favorito: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE REMOVE UM PRODUTO DOS FAVORITOS */
    public function removerFavorito($idCliente, $idProduto)
    {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM Favoritos 
                WHERE id_cliente = :id_cliente AND id_produto = :id_produto
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao remover favorito: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE REMOVE UM FAVORITO POR ID */
    public function removerFavoritoPorId($idFavorito, $idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM Favoritos 
                WHERE id_favorito = :id_favorito AND id_cliente = :id_cliente
            ");
            $stmt->bindValue(':id_favorito', $idFavorito, PDO::PARAM_INT);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao remover favorito por ID: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE VERIFICA SE UM PRODUTO ESTÁ NOS FAVORITOS */
    public function verificarFavorito($idCliente, $idProduto)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT id_favorito 
                FROM Favoritos 
                WHERE id_cliente = :id_cliente AND id_produto = :id_produto
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar favorito: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE LISTA TODOS OS FAVORITOS DE UM CLIENTE */
    public function listarFavoritos($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    f.id_favorito,
                    f.id_produto,
                    f.data_adicionado,
                    p.nome as nome_produto,
                    p.categoria,
                    p.preco,
                    p.descricao,
                    p.imagens,
                    COALESCE(SUM(e.quantidade), 0) as estoque_total
                FROM Favoritos f
                INNER JOIN Produto p ON f.id_produto = p.id_produto
                LEFT JOIN Estoque e ON p.id_produto = e.id_produto
                WHERE f.id_cliente = :id_cliente
                GROUP BY f.id_favorito, f.id_produto, p.nome, p.categoria, p.preco, p.descricao, p.imagens, f.data_adicionado
                ORDER BY f.data_adicionado DESC
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar favoritos: " . $e->getMessage());
            return [];
        }
    }

    /* FUNÇÃO QUE BUSCA TODOS OS IDs DE PRODUTOS FAVORITOS DE UM CLIENTE */
    public function listarIdsFavoritos($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT id_produto 
                FROM Favoritos 
                WHERE id_cliente = :id_cliente
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Erro ao listar IDs de favoritos: " . $e->getMessage());
            return [];
        }
    }
}


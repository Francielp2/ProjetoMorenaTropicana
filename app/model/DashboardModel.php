<?php
require_once __DIR__ . "/../../Database/conexaodb.php";

class DashboardModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /**
     * Busca o total de usuários cadastrados
     */
    public function getTotalUsuarios()
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Usuarios");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca o total de produtos cadastrados
     */
    public function getTotalProdutos()
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Produto");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca o total de pedidos pendentes
     */
    public function getPedidosPendentes()
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Pedido WHERE status_pedido = 'PENDENTE'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca o total de itens em estoque
     */
    public function getTotalEstoque()
    {
        try {
            $stmt = $this->conn->prepare("SELECT SUM(quantidade) FROM Estoque");
            $stmt->execute();
            $total = $stmt->fetchColumn();
            return $total ? $total : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca a receita do mês atual
     * Considera pedidos FINALIZADOS ou ENTREGUE
     */
    public function getReceitaMesAtual()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT COALESCE(SUM(valor_total), 0) 
                FROM Pedido 
                WHERE status_pedido IN ('FINALIZADO', 'ENTREGUE')
                AND YEAR(data_pedido) = YEAR(CURRENT_DATE)
            ");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca o total de vendas realizadas
     * Considera pedidos FINALIZADOS ou ENTREGUE
     */
    public function getTotalVendas()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM Pedido 
                WHERE status_pedido IN ('FINALIZADO', 'ENTREGUE')
            ");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca os 5 últimos pedidos realizados
     * Retorna array com dados dos pedidos incluindo nome do cliente
     */
    public function getUltimosPedidos($limite = 5)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.id_pedido,
                    p.data_pedido,
                    p.valor_total,
                    p.status_pedido,
                    u.nome as nome_cliente
                FROM Pedido p
                LEFT JOIN Usuarios u ON p.id_cliente = u.id_usuario
                ORDER BY p.data_pedido DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Busca produtos com estoque abaixo de 10
     * Retorna array com dados do produto e estoque
     */
    public function getProdutosEstoqueBaixo()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    pr.id_produto,
                    pr.nome as nome_produto,
                    pr.categoria,
                    e.quantidade,
                    e.id_estoque
                FROM Estoque e
                INNER JOIN Produto pr ON e.id_produto = pr.id_produto
                WHERE e.quantidade < 10
                ORDER BY e.quantidade ASC, pr.nome ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

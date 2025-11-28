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
}

<?php
/* ---INCLUI NO MODEL A CONEXÃO COM O BANCO DE DADOS--- */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* ---CRAÇÃO DA CLASSE DASHBOARD DO MODEL--- */

class DashboardModel
{
    private $conn;

    /* ---FUNÇÃO CONSTRUCT EXECULTA A CONEXÃO TODA VEZ QUE O OBJETO DE DASHBOARD FOR INSTÂNCIADO--- */
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /* ---FUNÇÃO DE PEGAR A QUANTIDADE DE USUÁRIOS DO BANCO--- */
    public function getTotalUsuarios()
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Usuarios");
            $stmt->execute();
            return $stmt->fetchColumn();/* O fetchColumn() NESSE USO PEGA OS VALORES EM ARRAY DA VARIÁVEL $STMT E TRANSFORMA EM UMA ÚNICA LINHA DE DADOS
            DO TIPO QUE O COUNT() RETORNA (INT)*/
        } catch (PDOException $e) {
            return 0;
        }
    }

    /*---FUNÇÃO DE PEGAR A QUANTIDADE DE PRODUTOS DO BANCO---*/
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

    /* ---FUNÇÃO DE PEGAR A QUANTIDADE DE PEDIDOS DO BANCO (SÓ RETORNA OS PEDIDOS COM STATUS = "PENDENTE")--- */
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

    /* ---FUNÇÃO DE PEGAR A QUANTIDADE DE PRODUTOS EM ESTOQUE NO BANCO, RETORNA A SOMA DO ESTOQUE DE TODOS OS PRODUTOS")--- */
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

    /* ---FUNÇÃO DE PEGAR O VALOR DE TODOS OS PEDIDOS QUE FORAM VENDIDOS DO BANCO NO NO ANO ATUAL. SE ESSE VALOR FOR NULO RETORNA 0")--- */
    public function getReceitaAnoAtual()
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

    /* ---FUNÇÃO DE PEGAR A QUANTIDADE DE TODOS OS PEDIDOS VENDIDOS DO BANCO (ENTENDE-SE POR VENDIDO PEDIDOS QUE TEM STATUS FINALIZADO OU ENTREGUE)*/
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

    /* ---FUNÇÃO DE PEGAR OS ÚLTIMOS PEDIDOS QUE FORAM FEITOS---*/
    public function getUltimosPedidos($limite)
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
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);/* O $LIMITE DECIDE QUANTOS DADOS SERAM PUXADOS */
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /*---FUNÇÃO QUE PEGA OS PRODUTOS COM ESTOQUE BAIXO*/
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

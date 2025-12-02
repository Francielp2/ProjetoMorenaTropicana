<?php
/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* CRIAÇÃO DA CLASSE CARRINHO MODEL */
class CarrinhoModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /* FUNÇÃO QUE ADICIONA UM ITEM AO CARRINHO */
    public function adicionarAoCarrinho($idCliente, $idProduto, $quantidade, $cor, $tamanho, $precoUnitario, $estoqueModel = null)
    {
        try {
            /* Verifica estoque disponível antes de adicionar */
            if ($estoqueModel !== null) {
                /* Verifica quantidade atual no carrinho para este item */
                $stmt = $this->conn->prepare("
                    SELECT quantidade 
                    FROM Carrinho 
                    WHERE id_cliente = :id_cliente 
                    AND id_produto = :id_produto 
                    AND (cor = :cor OR (cor IS NULL AND :cor IS NULL))
                    AND (tamanho = :tamanho OR (tamanho IS NULL AND :tamanho IS NULL))
                ");
                $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
                $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
                $stmt->bindValue(':cor', $cor, $cor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(':tamanho', $tamanho, $tamanho === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->execute();
                $itemExistente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $quantidadeAtualNoCarrinho = $itemExistente ? (int)$itemExistente['quantidade'] : 0;
                $quantidadeTotalNecessaria = $quantidadeAtualNoCarrinho + (int)$quantidade;
                
                /* Verifica se há estoque suficiente */
                $estoqueDisponivel = $estoqueModel->obterQuantidadeDisponivel($idProduto, $cor, $tamanho);
                
                if ($estoqueDisponivel < $quantidadeTotalNecessaria) {
                    return ['erro' => true, 'mensagem' => "Estoque insuficiente. Disponível: $estoqueDisponivel unidades. Solicitado: $quantidadeTotalNecessaria unidades."];
                }
            }

            /* Verifica se já existe um item igual no carrinho (mesmo produto, cor e tamanho) */
            $stmt = $this->conn->prepare("
                SELECT id_carrinho, quantidade 
                FROM Carrinho 
                WHERE id_cliente = :id_cliente 
                AND id_produto = :id_produto 
                AND (cor = :cor OR (cor IS NULL AND :cor IS NULL))
                AND (tamanho = :tamanho OR (tamanho IS NULL AND :tamanho IS NULL))
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->bindValue(':cor', $cor, $cor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':tamanho', $tamanho, $tamanho === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
            $itemExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($itemExistente) {
                /* Se já existe, atualiza a quantidade */
                $novaQuantidade = (int)$itemExistente['quantidade'] + (int)$quantidade;
                $stmt = $this->conn->prepare("
                    UPDATE Carrinho 
                    SET quantidade = :quantidade 
                    WHERE id_carrinho = :id
                ");
                $stmt->bindValue(':quantidade', $novaQuantidade, PDO::PARAM_INT);
                $stmt->bindValue(':id', $itemExistente['id_carrinho'], PDO::PARAM_INT);
                $stmt->execute();
                return $itemExistente['id_carrinho'];
            } else {
                /* Se não existe, cria novo item */
                $stmt = $this->conn->prepare("
                    INSERT INTO Carrinho (id_cliente, id_produto, quantidade, cor, tamanho, preco_unitario)
                    VALUES (:id_cliente, :id_produto, :quantidade, :cor, :tamanho, :preco_unitario)
                ");
                $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
                $stmt->bindValue(':id_produto', $idProduto, PDO::PARAM_INT);
                $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
                $stmt->bindValue(':cor', $cor, $cor === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(':tamanho', $tamanho, $tamanho === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(':preco_unitario', $precoUnitario);
                $stmt->execute();
                return $this->conn->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE BUSCA OS ÚLTIMOS 4 PRODUTOS ÚNICOS ADICIONADOS AO CARRINHO (DE TODOS OS CLIENTES) */
    public function listarUltimosProdutosAdicionados($limite = 4)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT 
                    p.id_produto,
                    p.nome,
                    p.categoria,
                    p.preco,
                    p.descricao,
                    p.imagens,
                    MAX(c.data_adicionado) as ultima_adicao
                FROM Carrinho c
                INNER JOIN Produto p ON c.id_produto = p.id_produto
                GROUP BY p.id_produto, p.nome, p.categoria, p.preco, p.descricao, p.imagens
                ORDER BY ultima_adicao DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar últimos produtos adicionados: " . $e->getMessage());
            return [];
        }
    }

    /* FUNÇÃO QUE LISTA TODOS OS ITENS DO CARRINHO DE UM CLIENTE */
    public function listarItensCarrinho($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    c.id_carrinho,
                    c.id_produto,
                    c.quantidade,
                    c.cor,
                    c.tamanho,
                    c.preco_unitario,
                    c.data_adicionado,
                    p.nome as nome_produto,
                    p.imagens,
                    p.categoria
                FROM Carrinho c
                INNER JOIN Produto p ON c.id_produto = p.id_produto
                WHERE c.id_cliente = :id_cliente
                ORDER BY c.data_adicionado DESC
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar itens do carrinho: " . $e->getMessage());
            return [];
        }
    }

    /* FUNÇÃO QUE ATUALIZA A QUANTIDADE DE UM ITEM DO CARRINHO */
    public function atualizarQuantidade($idCarrinho, $quantidade, $idCliente, $estoqueModel = null)
    {
        try {
            /* Verifica se o item pertence ao cliente e busca dados do item */
            $stmt = $this->conn->prepare("
                SELECT id_carrinho, id_produto, cor, tamanho
                FROM Carrinho 
                WHERE id_carrinho = :id_carrinho AND id_cliente = :id_cliente
            ");
            $stmt->bindValue(':id_carrinho', $idCarrinho, PDO::PARAM_INT);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$item) {
                return false; /* Item não pertence ao cliente */
            }

            if ($quantidade <= 0) {
                /* Se quantidade for 0 ou menor, remove o item */
                return $this->removerItem($idCarrinho, $idCliente);
            }

            /* Verifica estoque disponível antes de atualizar */
            if ($estoqueModel !== null) {
                $estoqueDisponivel = $estoqueModel->obterQuantidadeDisponivel(
                    $item['id_produto'], 
                    $item['cor'], 
                    $item['tamanho']
                );
                
                if ($estoqueDisponivel < $quantidade) {
                    return ['erro' => true, 'mensagem' => "Estoque insuficiente. Disponível: $estoqueDisponivel unidades."];
                }
            }

            $stmt = $this->conn->prepare("
                UPDATE Carrinho 
                SET quantidade = :quantidade 
                WHERE id_carrinho = :id_carrinho AND id_cliente = :id_cliente
            ");
            $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            $stmt->bindValue(':id_carrinho', $idCarrinho, PDO::PARAM_INT);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar quantidade do carrinho: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE REMOVE UM ITEM DO CARRINHO */
    public function removerItem($idCarrinho, $idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM Carrinho 
                WHERE id_carrinho = :id_carrinho AND id_cliente = :id_cliente
            ");
            $stmt->bindValue(':id_carrinho', $idCarrinho, PDO::PARAM_INT);
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao remover item do carrinho: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE LIMPA TODO O CARRINHO DE UM CLIENTE */
    public function limparCarrinho($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM Carrinho WHERE id_cliente = :id_cliente");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao limpar carrinho: " . $e->getMessage());
            return false;
        }
    }

    /* FUNÇÃO QUE CALCULA O TOTAL DO CARRINHO */
    public function calcularTotalCarrinho($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT SUM(quantidade * preco_unitario) as total
                FROM Carrinho
                WHERE id_cliente = :id_cliente
            ");
            $stmt->bindValue(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro ao calcular total do carrinho: " . $e->getMessage());
            return 0;
        }
    }
}


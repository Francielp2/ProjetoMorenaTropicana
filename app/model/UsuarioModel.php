<?php
require_once __DIR__ . "/../../Database/conexaodb.php";


//Classe de usuario

class UsuarioModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /**
     * Busca um usuário pelo email
     */
    public function buscarPorEmail($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Busca um usuário pelo ID
     */
    public function buscarPorId($id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Usuarios WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verifica se o email já existe
     */
    public function emailExiste($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verifica se o CPF já existe
     */
    public function cpfExiste($cpf)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Usuarios WHERE cpf = :cpf");
            $stmt->bindParam(':cpf', $cpf);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Cria um novo usuário (cliente)
     */
    public function criarCliente($nome, $cpf, $email, $senha, $telefone = null)
    {
        try {
            // Inicia transação
            $this->conn->beginTransaction();

            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere na tabela Usuarios
            $stmt = $this->conn->prepare("
                INSERT INTO Usuarios (nome, cpf, email, senha, permissao) 
                VALUES (:nome, :cpf, :email, :senha, 'CLIENTE')
            ");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->execute();

            // Pega o ID do usuário criado
            $idUsuario = $this->conn->lastInsertId();

            // Insere na tabela Cliente
            $stmt = $this->conn->prepare("
                INSERT INTO Cliente (id_usuario, telefone, status) 
                VALUES (:id_usuario, :telefone, 'Ativo')
            ");
            $stmt->bindParam(':id_usuario', $idUsuario);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->execute();

            // Confirma transação
            $this->conn->commit();
            return $idUsuario;
        } catch (PDOException $e) {
            // Desfaz transação em caso de erro
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Verifica login (email e senha)
     */
    public function verificarLogin($email, $senha)
    {
        try {
            $usuario = $this->buscarPorEmail($email);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                return $usuario;
            }

            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Busca dados completos do cliente (com informações da tabela Cliente)
     */
    public function buscarDadosCliente($idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, c.telefone, c.status 
                FROM Usuarios u 
                INNER JOIN Cliente c ON u.id_usuario = c.id_usuario 
                WHERE u.id_usuario = :id
            ");
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Exclui a conta do cliente
     * Como há ON DELETE CASCADE no DDL, ao deletar da tabela Usuarios,
     * automaticamente deleta de Cliente e outras tabelas relacionadas
     */
    public function excluirConta($idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM Usuarios WHERE id_usuario = :id");
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Atualiza dados pessoais do cliente (nome e telefone)
     */
    public function atualizarDadosPessoais($idUsuario, $nome, $telefone)
    {
        try {
            $this->conn->beginTransaction();

            // Atualiza nome na tabela Usuarios
            $stmt = $this->conn->prepare("UPDATE Usuarios SET nome = :nome WHERE id_usuario = :id");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();

            // Atualiza telefone na tabela Cliente
            $stmt = $this->conn->prepare("UPDATE Cliente SET telefone = :telefone WHERE id_usuario = :id");
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Busca endereço do cliente
     */
    public function buscarEndereco($idCliente)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM Endereco 
                WHERE id_cliente = :id_cliente 
                ORDER BY id_endereco DESC 
                LIMIT 1
            ");
            $stmt->bindParam(':id_cliente', $idCliente);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Salva ou atualiza endereço do cliente
     */
    public function salvarEndereco($idCliente, $rua, $numero, $bairro, $cep, $estado, $complemento = null, $tipoEndereco = 'ENTREGA')
    {
        try {
            // Remove caracteres não numéricos do CEP
            $cep = preg_replace('/[^0-9]/', '', $cep);

            // Valida CEP (deve ter 8 dígitos)
            if (strlen($cep) !== 8) {
                return false;
            }

            // Limpa e valida campos
            $rua = trim($rua);
            $numero = !empty(trim($numero)) ? trim($numero) : null;
            $bairro = !empty(trim($bairro)) ? trim($bairro) : null;
            $estado = !empty(trim($estado)) ? strtoupper(trim($estado)) : null;
            $complemento = !empty(trim($complemento)) ? trim($complemento) : null;

            // Verifica se já existe endereço para este cliente
            $enderecoExistente = $this->buscarEndereco($idCliente);

            if ($enderecoExistente) {
                // Atualiza endereço existente
                $stmt = $this->conn->prepare("
                    UPDATE Endereco 
                    SET rua = :rua, numero = :numero, bairro = :bairro, 
                        cep = :cep, estado = :estado, complemento = :complemento,
                        tipo_endereco = :tipo_endereco
                    WHERE id_endereco = :id_endereco
                ");
                $stmt->bindParam(':rua', $rua);
                $stmt->bindParam(':numero', $numero);
                $stmt->bindParam(':bairro', $bairro);
                $stmt->bindParam(':cep', $cep);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':complemento', $complemento);
                $stmt->bindParam(':tipo_endereco', $tipoEndereco);
                $stmt->bindParam(':id_endereco', $enderecoExistente['id_endereco']);
                $stmt->execute();
            } else {
                // Cria novo endereço
                $stmt = $this->conn->prepare("
                    INSERT INTO Endereco (id_cliente, rua, numero, bairro, cep, estado, complemento, tipo_endereco)
                    VALUES (:id_cliente, :rua, :numero, :bairro, :cep, :estado, :complemento, :tipo_endereco)
                ");
                $stmt->bindParam(':id_cliente', $idCliente);
                $stmt->bindParam(':rua', $rua);
                $stmt->bindParam(':numero', $numero);
                $stmt->bindParam(':bairro', $bairro);
                $stmt->bindParam(':cep', $cep);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':complemento', $complemento);
                $stmt->bindParam(':tipo_endereco', $tipoEndereco);
                $stmt->execute();
            }

            return true;
        } catch (PDOException $e) {
            // Log do erro para debug
            error_log("Erro ao salvar endereço: " . $e->getMessage());
            // Retorna a mensagem de erro para debug (remova em produção)
            $_SESSION['erro_sql'] = $e->getMessage();
            return false;
        }
    }
}

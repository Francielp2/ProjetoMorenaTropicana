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
        return $this->criarUsuario($nome, $cpf, $email, $senha, 'CLIENTE', 'Ativo', $telefone);
    }

    /**
     * Cria um novo usuário (cliente ou admin)
     * Reutiliza a lógica do criarCliente para manter consistência
     */
    public function criarUsuario($nome, $cpf, $email, $senha, $tipo, $status = 'Ativo', $telefone = null)
    {
        try {
            // Valida tipo
            if (!in_array($tipo, ['ADMIN', 'CLIENTE'])) {
                return false;
            }

            // Valida status (apenas para clientes)
            if ($tipo === 'CLIENTE' && !in_array($status, ['Ativo', 'Suspenso'])) {
                return false;
            }

            // Inicia transação
            $this->conn->beginTransaction();

            // Hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere na tabela Usuarios
            $stmt = $this->conn->prepare("
                INSERT INTO Usuarios (nome, cpf, email, senha, permissao) 
                VALUES (:nome, :cpf, :email, :senha, :permissao)
            ");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':permissao', $tipo);
            $stmt->execute();

            // Pega o ID do usuário criado
            $idUsuario = $this->conn->lastInsertId();

            // Se for cliente, insere na tabela Cliente
            if ($tipo === 'CLIENTE') {
                $stmt = $this->conn->prepare("
                    INSERT INTO Cliente (id_usuario, telefone, status) 
                    VALUES (:id_usuario, :telefone, :status)
                ");
                $stmt->bindParam(':id_usuario', $idUsuario);
                $stmt->bindParam(':telefone', $telefone);
                $stmt->bindParam(':status', $status);
                $stmt->execute();
            }

            // Se for admin, insere na tabela Administrador
            if ($tipo === 'ADMIN') {
                $stmt = $this->conn->prepare("
                    INSERT INTO Administrador (id_usuario, data_contratacao) 
                    VALUES (:id_usuario, CURDATE())
                ");
                $stmt->bindParam(':id_usuario', $idUsuario);
                $stmt->execute();
            }

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
     * Retorna o usuário se login estiver correto e status for Ativo
     */
    public function verificarLogin($email, $senha)
    {
        try {
            $usuario = $this->buscarPorEmail($email);

            // Verifica se usuário existe e senha está correta
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Se for cliente, verifica o status
                if ($usuario['permissao'] === 'CLIENTE') {
                    // Busca status do cliente
                    $stmt = $this->conn->prepare("SELECT status FROM Cliente WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $usuario['id_usuario']);
                    $stmt->execute();
                    $statusCliente = $stmt->fetchColumn();

                    // Se status não for Ativo, retorna false
                    if ($statusCliente !== 'Ativo') {
                        return false;
                    }
                }

                // Se chegou aqui, login está correto e status é válido
                return $usuario;
            }

            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verifica se o cliente está ativo
     * Retorna true se status for 'Ativo', false caso contrário
     */
    public function verificarClienteAtivo($idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("SELECT status FROM Cliente WHERE id_usuario = :id");
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();
            $status = $stmt->fetchColumn();

            return $status === 'Ativo';
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

    /**
     * Busca todos os usuários com seus dados completos
     * Retorna array com dados de Usuarios, Cliente e Administrador
     */
    public function listarTodosUsuarios()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    u.id_usuario,
                    u.nome,
                    u.email,
                    u.cpf,
                    u.permissao,
                    c.status as status_cliente,
                    c.telefone,
                    a.data_contratacao
                FROM Usuarios u
                LEFT JOIN Cliente c ON u.id_usuario = c.id_usuario
                LEFT JOIN Administrador a ON u.id_usuario = a.id_usuario
                ORDER BY u.id_usuario ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Busca usuários por termo de pesquisa (nome ou email) com filtros opcionais
     * Busca case-insensitive (não diferencia maiúsculas/minúsculas)
     */
    public function buscarUsuariosPorTermo($termo = '', $filtroTipo = '', $filtroStatus = '')
    {
        try {
            // Remove espaços do início e fim
            $termo = trim($termo);
            $filtroTipo = trim($filtroTipo);
            $filtroStatus = trim($filtroStatus);

            // Monta a query SQL base
            $sql = "SELECT
                        u.id_usuario,
                        u.nome,
                        u.email,
                        u.cpf,
                        u.permissao,
                        c.status as status_cliente,
                        c.telefone,
                        a.data_contratacao
                    FROM Usuarios u
                    LEFT JOIN Cliente c ON u.id_usuario = c.id_usuario
                    LEFT JOIN Administrador a ON u.id_usuario = a.id_usuario
                    WHERE 1=1";

            $params = [];

            // Adiciona filtro de pesquisa (nome ou email)
            if (!empty($termo)) {
                $termoBusca = '%' . $termo . '%';
                $sql .= " AND (u.nome LIKE :termo OR u.email LIKE :termo)";
                $params[':termo'] = $termoBusca;
            }

            // Adiciona filtro de tipo (CLIENTE ou ADMIN)
            if (!empty($filtroTipo) && in_array($filtroTipo, ['CLIENTE', 'ADMIN'])) {
                $sql .= " AND u.permissao = :tipo";
                $params[':tipo'] = $filtroTipo;
            }

            // Adiciona filtro de status
            if (!empty($filtroStatus) && in_array($filtroStatus, ['Ativo', 'Suspenso'])) {
                // Se o filtro for "Ativo", mostra clientes ativos E todos os administradores
                if ($filtroStatus === 'Ativo') {
                    $sql .= " AND (c.status = :status OR u.permissao = 'ADMIN')";
                    $params[':status'] = $filtroStatus;
                } else {
                    // Se for "Suspenso", mostra apenas clientes suspensos
                    $sql .= " AND c.status = :status";
                    $params[':status'] = $filtroStatus;
                }
            }

            $sql .= " ORDER BY u.id_usuario ASC";

            $stmt = $this->conn->prepare($sql);

            // Bind dos parâmetros
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;
        } catch (PDOException $e) {
            // Retorna array vazio em caso de erro
            return [];
        }
    }

    /**
     * Busca dados completos de um usuário específico
     * Retorna array com dados de Usuarios, Cliente e Administrador
     */
    public function buscarUsuarioCompleto($idUsuario)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    u.id_usuario,
                    u.nome,
                    u.email,
                    u.cpf,
                    u.permissao,
                    c.status as status_cliente,
                    c.telefone,
                    a.data_contratacao
                FROM Usuarios u
                LEFT JOIN Cliente c ON u.id_usuario = c.id_usuario
                LEFT JOIN Administrador a ON u.id_usuario = a.id_usuario
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
     * Atualiza dados completos de um usuário
     * Atualiza nome, email, senha (se fornecida), tipo e status
     */
    public function atualizarUsuario($idUsuario, $nome, $email, $senha = null, $tipo, $status = null, $telefone = null)
    {
        try {
            $this->conn->beginTransaction();

            // Busca dados atuais do usuário
            $usuarioAtual = $this->buscarUsuarioCompleto($idUsuario);
            if (!$usuarioAtual) {
                $this->conn->rollBack();
                return false;
            }

            // Atualiza nome e email na tabela Usuarios
            $sql = "UPDATE Usuarios SET nome = :nome, email = :email, permissao = :permissao WHERE id_usuario = :id";
            $params = [
                ':nome' => $nome,
                ':email' => $email,
                ':permissao' => $tipo,
                ':id' => $idUsuario
            ];

            // Se senha foi fornecida, atualiza também
            if (!empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE Usuarios SET nome = :nome, email = :email, senha = :senha, permissao = :permissao WHERE id_usuario = :id";
                $params[':senha'] = $senhaHash;
            }

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            // Se mudou de tipo, precisa ajustar tabelas Cliente e Administrador
            if ($usuarioAtual['permissao'] !== $tipo) {
                if ($tipo === 'ADMIN') {
                    // Remove de Cliente se existir
                    $stmt = $this->conn->prepare("DELETE FROM Cliente WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();

                    // Adiciona em Administrador se não existir
                    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Administrador WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $stmt = $this->conn->prepare("INSERT INTO Administrador (id_usuario, data_contratacao) VALUES (:id, CURDATE())");
                        $stmt->bindParam(':id', $idUsuario);
                        $stmt->execute();
                    }
                } else {
                    // Remove de Administrador se existir
                    $stmt = $this->conn->prepare("DELETE FROM Administrador WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();

                    // Adiciona em Cliente se não existir
                    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Cliente WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $stmt = $this->conn->prepare("INSERT INTO Cliente (id_usuario, telefone, status) VALUES (:id, :telefone, :status)");
                        $stmt->bindParam(':id', $idUsuario);
                        $stmt->bindValue(':telefone', $telefone);
                        $stmt->bindValue(':status', $status ?? 'Ativo');
                        $stmt->execute();
                    } else {
                        // Atualiza status e telefone do cliente existente
                        $stmt = $this->conn->prepare("UPDATE Cliente SET status = :status, telefone = :telefone WHERE id_usuario = :id");
                        $stmt->bindValue(':status', $status ?? 'Ativo');
                        $stmt->bindValue(':telefone', $telefone);
                        $stmt->bindParam(':id', $idUsuario);
                        $stmt->execute();
                    }
                }
            } else {
                // Tipo não mudou
                if ($tipo === 'CLIENTE') {
                    // Atualiza status e telefone do cliente
                    $stmt = $this->conn->prepare("UPDATE Cliente SET status = :status, telefone = :telefone WHERE id_usuario = :id");
                    $stmt->bindValue(':status', $status ?? 'Ativo');
                    $stmt->bindValue(':telefone', $telefone);
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();
                }
                // Se for ADMIN, não precisa atualizar nada nas tabelas relacionadas
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}

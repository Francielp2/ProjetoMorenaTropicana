<?php

/* CHAMA A CONEXÃO COM O BANCO DE DADOS */
require_once __DIR__ . "/../../Database/conexaodb.php";

/* CRIAÇÃO DA CLASSE USUÁRIO */

class UsuarioModel
{
    /* INICIA O OBJETO DE CONEXÃO E ARMAZENA SEU VALOR NA VARIÁVEL CONN */
    private $conn;
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conexao();
    }

    /* BUSCA USUÁRIO NO BANCO DE DADOS USANDO O EMAIL E RETORNA UM ARRAY COM OS DADOS DO USUÁRIO */
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

    /* FUNÇÃO QUE BUSCA O USUÁRIO USANDO ID E RETORNA UM ARRAY COM OS DADOS DE USUÁRIO*/
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

    /* FUNÇÃO QUE VERIFICA SE O EMAIL JÁ EXISTE NO BANCO DE DADOS E RETORNA O VALOR DO COUNT SE O EMAIL JÁ EXISTIR */
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

    /* FUNÇÃO QUE VERIFICA SE O CPF JÁ EXISTE NO BANCO DE DADOS E RETORNA O VALOR DO COUNT SE O CPF JÁ EXISTIR */
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

    /* FUNÇÃO USADA PARA CRIAR UM CLIENTE QUE RETORNA  A FUNÇÃO CRIAR USUÁRIO PASSANDO CLIENTE E ATIVO COMO PERMISSÃO E STATUS RESPECTIVAMENTE*/
    public function criarCliente($nome, $cpf, $email, $senha, $telefone = null)/* DADOS DO USUÁRIO, SE NÃO TIVER TELEFONE ESTE FICA NULL */
    {
        return $this->criarUsuario($nome, $cpf, $email, $senha, 'CLIENTE', 'Ativo', $telefone);
    }

    /* FUNÇÃO USADA PARA CRIAR UM USUÁRIO QUE PODE SER CLIENTE OU ADM */
    public function criarUsuario($nome, $cpf, $email, $senha, $tipo, $status = 'Ativo', $telefone = null)
    {
        try {
            /* VERIFICA SE O TIPO ADMIN OU CLIENTE EXISTE E SE NÃO TIVER RETORNA FALSO */
            if (!in_array($tipo, ['ADMIN', 'CLIENTE'])) {
                return false;
            }

            /* SE FOR CLIENTE, VERIFICA SE TEM O STATUS DE ATIVO OU SUSPENSO E SE NÃO TIVER RETORNA FALSO */
            if ($tipo === 'CLIENTE' && !in_array($status, ['Ativo', 'Suspenso'])) {
                return false;
            }

            $this->conn->beginTransaction();/* ESSA FUNÇÃO INICIA UM BLOCO DE INSERÇÕES NO BANCO DE DADOS ONDE TODOS OS VALORES DÃO CERTO OU TODOS DÃO ERRADO PARA EVITAR ERROS. SÓ ENVIA PARA O BANCO DEPOIS QUE FOR COMITADO */

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);/* CRIA UM HASH DA SENHA, QUE É UMA FORMA DE CRIPTOGRAFIA */

            /* INSERE NA TABELA DO BANCO DE DADOS OS VALORES QUE FORAM PASSADOS COMO PARÂMENTROS */
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

            $idUsuario = $this->conn->lastInsertId();/* PEGA O ID DO ULTIMO USUÁRIO CRIADO */

            /* SE ESSE USUÁRIO FOR CLIENTE ADICIONA NA TABELA DE CLIENTE */
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

            /* SE ESSE USUÁRIO FOR ADM ADICIONA NA TABELA DE ADM */
            if ($tipo === 'ADMIN') {
                $stmt = $this->conn->prepare("
                    INSERT INTO Administrador (id_usuario, data_contratacao) 
                    VALUES (:id_usuario, CURDATE())
                ");
                $stmt->bindParam(':id_usuario', $idUsuario);
                $stmt->execute();
            }


            $this->conn->commit();/* PERMITE QUE OS VALORES SEJAM ENVIADOS */
            return $idUsuario;/* SE TUDO SER CERTO A FUNÇÃO RETORNA O ID USUÁRIO CRIADO */
        } catch (PDOException $e) {
            $this->conn->rollBack();/* SE ALGO DER ERRADO DESFAZ A TRANSAÇÃO COM O BANCO DE DADOS */
            return false;
        }
    }

    /* FUNÇÃO DE VERIFICAR O LOGIN POR MEIO DO EMAIL E DA SENHA */
    public function verificarLogin($email, $senha)
    {
        try {
            $usuario = $this->buscarPorEmail($email); /* PASSA À VARIÁVEL USUÁRIO O RESULTADO DA FUNÇAO DE BUSCAR EMAIL QUE RETORNA OS DADOS DO USUÁRIO EM  UM ARRAY */

            /* VERIFICA SE A VARIÁVEL DE USUÁRIO TEM VALOR !NULL E SE A SENHA BATE COM O HASH DO BANCO DE DADOS */
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                /* SE FOR CLINTE VERIFICA O STATUS, AFNAL SE ESTE NÃO FOR ATIVO O LOGIN NÃO PODE ACONTECER */
                if ($usuario['permissao'] === 'CLIENTE') {
                    /*  Busca status do cliente */
                    $stmt = $this->conn->prepare("SELECT status FROM Cliente WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $usuario['id_usuario']);
                    $stmt->execute();
                    $statusCliente = $stmt->fetchColumn();

                    /* Se status não for Ativo, retorna false */
                    if ($statusCliente !== 'Ativo') {
                        return false;
                    }
                }

                return $usuario;/* SE OS DADOS INSERIDOS (EMAIL E SENHA) PASSARAM POR TODAS AS VERIFICAÇÕES E NÃO PARARAM O CÓDIGO EM NENHUMA DELAS, O LOGIN É VALIDO E A FUNÇÃO RETORNA O USUÁRIO */
            }
            return false;/*  SE DER ALGUM ERRO OU NÃO PASSAR POR ALGUMA VERIFICAÇÃO ESSA FUNÇÃO RETORNA FALSO */
        } catch (PDOException $e) {
            return false;
        }
    }

    /* FUNÇÃO DE VERIFICAR SE O CLIENTE ESTÁ ATIVO */
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

    /* BUSCA DADOS COMPLETO DO CLIENTE COM INFORMAÇÕES DA TABELA USUÁRIO E DA TABELA CLIENTE E RETORNA UM ARRAY DE DADOS*/
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

    /* EXCLUIR CONTA DE CLIENTE SE DER CERTO RETORNA VERDADEIRO E SE NÃO RETORNA FALSO */
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

    /* ATUALISA DADOS DO CLIENTE */
    public function atualizarDadosPessoais($idUsuario, $nome, $telefone)
    {
        try {
            $this->conn->beginTransaction();/* INICIA O BLOCO DE INSERÇÕES */

            /* Atualiza nome na tabela Usuarios */
            $stmt = $this->conn->prepare("UPDATE Usuarios SET nome = :nome WHERE id_usuario = :id");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();

            /* Atualiza telefone na tabela Cliente */
            $stmt = $this->conn->prepare("UPDATE Cliente SET telefone = :telefone WHERE id_usuario = :id");
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':id', $idUsuario);
            $stmt->execute();

            $this->conn->commit();/* PERMITE O ENVIO DOS DADOS PARA O BANCO */
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();/* SE DER ERROS TODAS AS INSERÇÕES FALHAM */
            return false;
        }
    }

    /* BUSCA ENDEREÇO DO CLIENTE E RETORNA UM ARRAY SE ACHA RENDEREÇO SALVO E SE NÃO RETORNA FALSO */
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

    /* SALVA OU ATUALIZA O ENDEREÇO DO CLIENTE  E RETORNA VERDADEIRO OU FALSO */
    public function salvarEndereco($idCliente, $rua, $numero, $bairro, $cep, $estado, $complemento = null, $tipoEndereco = 'ENTREGA')/* PARÂMETROS PARA SEREM SALVOS */
    {
        try {
            $cep = preg_replace('/[^0-9]/', '', $cep); /* Remove caracteres não numéricos do CEP */

            /*    Valida CEP (deve ter 8 dígitos) */
            if (strlen($cep) !== 8) {
                return false;
            }

            /* LIMPA E VALIDA OD CAMPOS VERIFICANDO SE OS OBRIGATÓRIOS ESTÃO PREENCHIDOS E RETIRA OS ESPAÇOS DO INICIO E DO FINAL */
            $rua = trim($rua);
            $numero = !empty(trim($numero)) ? trim($numero) : null;
            $bairro = !empty(trim($bairro)) ? trim($bairro) : null;
            $estado = !empty(trim($estado)) ? strtoupper(trim($estado)) : null;
            $complemento = !empty(trim($complemento)) ? trim($complemento) : null;

            $enderecoExistente = $this->buscarEndereco($idCliente);  /* Verifica se já existe endereço para este cliente */

            /* SE EXISTIR ENDEREÇO ATUALIZA OS DADOS E SE NÃO EXISTIR ADICIONA OS DADOS */
            if ($enderecoExistente) {
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
                /* Cria novo endereço */
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

    /* BUSCA TODOS OS USUÁRIOS E SEUS DADOS COMPLETOS E RETORNA UM ARRAY CONTENDO OS RESULTADOS */
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

    /* REALIZA AS BUSCAS DA TABELA DE USUÁRIO */
    public function buscarUsuariosPorTermo($termo = '', $filtroTipo = '', $filtroStatus = '')/* SE NÃO FOR DEFINIDO OS VALORES, ESSE SERAM VAZIOS */
    {
        try {
            /* Remove espaços do início e fim */
            $termo = trim($termo);
            $filtroTipo = trim($filtroTipo);
            $filtroStatus = trim($filtroStatus);

            /* CRIA UM CÓDIGO BASE DE PESQUISA SQL E GUARDA NA VÁRIÁVEL SQL */
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

            $sql .= " ORDER BY u.id_usuario ASC";/* PEGA O SQL FINAL E ADICIONA A FUNÇÃO DE ORDENAR POR ID EM ORDEM CRESCENTE */

            $stmt = $this->conn->prepare($sql);/* AO PASSAR PELA VERIFICAÇÃO O CÓDIGO SQL É COMPLEMENTADO E É PREPARADO PARA SER CONSULTADO NO BANCO DE DADOS */

            /* Bind dos parâmetros */
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;/* RETORNA UMA VARIÁVEL COM TODOS OS DADOS DA PESQUISA */
        } catch (PDOException $e) {
            /* Retorna array vazio em caso de erro */
            return [];
        }
    }

    /* BUSCA DADOS COMPLETOS DE UM USUÁRIO ESPERCÍFICO */
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

    /* ATUALIZA DADOS DE UM USUÁRIO */
    public function atualizarUsuario($idUsuario, $nome, $email, $tipo, $senha = null, $status = null, $telefone = null)
    {
        try {
            $this->conn->beginTransaction();

            /* Busca dados atuais do usuário */
            $usuarioAtual = $this->buscarUsuarioCompleto($idUsuario);


            /* SE NÃO ENCONTRAR DADOS DO USUÁRIO DESFAZ A TRANSAÇÃO */
            if (!$usuarioAtual) {
                $this->conn->rollBack();
                return false;
            }

            /* ATUALIZA NOME EMAIL  E TIPO NA TABELA USUARIO */
            $sql = "UPDATE Usuarios SET nome = :nome, email = :email, permissao = :permissao WHERE id_usuario = :id";
            $params = [
                ':nome' => $nome,
                ':email' => $email,
                ':permissao' => $tipo,
                ':id' => $idUsuario
            ];

            /* SE A SENHA FOI FORNECIDA ELA É ATUALIZADA TAMBÉM */
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

            /* SE MUDOU A PERMISSÃO DE USUÁRIO PRECISA MUDAR AS TABELAS DE ADM E CLIENTE */
            if ($usuarioAtual['permissao'] !== $tipo) {
                if ($tipo === 'ADMIN') {
                    /* Remove de Cliente se existir */
                    $stmt = $this->conn->prepare("DELETE FROM Cliente WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();

                    /* Adiciona em Administrador se não existir */
                    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Administrador WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $stmt = $this->conn->prepare("INSERT INTO Administrador (id_usuario, data_contratacao) VALUES (:id, CURDATE())");
                        $stmt->bindParam(':id', $idUsuario);
                        $stmt->execute();
                    }
                } else {
                    /* Remove de Administrador se existir */
                    $stmt = $this->conn->prepare("DELETE FROM Administrador WHERE id_usuario = :id");
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();

                    /* Adiciona em Cliente se não existir */
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
                        /* Atualiza status e telefone do cliente existente */
                        $stmt = $this->conn->prepare("UPDATE Cliente SET status = :status, telefone = :telefone WHERE id_usuario = :id");
                        $stmt->bindValue(':status', $status ?? 'Ativo');
                        $stmt->bindValue(':telefone', $telefone);
                        $stmt->bindParam(':id', $idUsuario);
                        $stmt->execute();
                    }
                }
            } else {
                /* Tipo não mudou */
                if ($tipo === 'CLIENTE') {
                    /* Atualiza status e telefone do cliente */
                    $stmt = $this->conn->prepare("UPDATE Cliente SET status = :status, telefone = :telefone WHERE id_usuario = :id");
                    $stmt->bindValue(':status', $status ?? 'Ativo');
                    $stmt->bindValue(':telefone', $telefone);
                    $stmt->bindParam(':id', $idUsuario);
                    $stmt->execute();
                }
                /* Se for ADMIN, não precisa atualizar nada nas tabelas relacionadas */
            }

            $this->conn->commit();/* PERMITE AS MUDANÇAS NO BD */
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();/* SE DER ERRO CANCELA TODAS AS MUDANÇAS */
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
}

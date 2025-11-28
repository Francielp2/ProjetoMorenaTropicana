<?php
require_once __DIR__ . "/../model/UsuarioModel.php";
require_once __DIR__ . "/../config/config.php";

class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Inicia a sessão se ainda não estiver iniciada
     */
    private function iniciarSessao()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Processa o login
     */
    public function login()
    {
        $this->iniciarSessao();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if (empty($email) || empty($senha)) {
                $_SESSION['erro'] = "Por favor, preencha todos os campos.";
                header("Location: " . BASE_URL . "/app/view/login.php");
                exit;
            }

            $usuario = $this->usuarioModel->verificarLogin($email, $senha);

            if ($usuario) {
                // Salva dados na sessão
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_permissao'] = $usuario['permissao'];

                // Redireciona conforme a permissão
                if ($usuario['permissao'] === 'ADMIN') {
                    header("Location: " . BASE_URL . "/app/view/admin/index.php");
                } else {
                    header("Location: " . BASE_URL . "/app/view/cliente/tela_inicial.php");
                }
                exit;
            } else {
                $_SESSION['erro'] = "Email ou senha incorretos.";
                header("Location: " . BASE_URL . "/app/view/login.php");
                exit;
            }
        }
    }

    /**
     * Processa o cadastro
     */
    public function cadastro()
    {
        $this->iniciarSessao();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? ''); // Remove caracteres não numéricos
            $email = trim($_POST['email'] ?? '');
            $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
            $senha = $_POST['senha'] ?? '';

            // Validações básicas
            if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
                $_SESSION['erro'] = "Por favor, preencha todos os campos obrigatórios.";
                header("Location: " . BASE_URL . "/app/view/cadastro.php");
                exit;
            }

            // Valida CPF (deve ter 11 dígitos)
            if (strlen($cpf) !== 11) {
                $_SESSION['erro'] = "CPF deve conter 11 dígitos.";
                header("Location: " . BASE_URL . "/app/view/cadastro.php");
                exit;
            }

            // Valida senha (mínimo 6 caracteres)
            if (strlen($senha) < 6) {
                $_SESSION['erro'] = "A senha deve ter no mínimo 6 caracteres.";
                header("Location: " . BASE_URL . "/app/view/cadastro.php");
                exit;
            }

            // Verifica se email já existe
            if ($this->usuarioModel->emailExiste($email)) {
                $_SESSION['erro'] = "Este email já está cadastrado.";
                header("Location: " . BASE_URL . "/app/view/cadastro.php");
                exit;
            }

            // Verifica se CPF já existe
            if ($this->usuarioModel->cpfExiste($cpf)) {
                $_SESSION['erro'] = "Este CPF já está cadastrado.";
                header("Location: " . BASE_URL . "/app/view/cadastro.php");
                exit;
            }

            // Cria o cliente
            $idUsuario = $this->usuarioModel->criarCliente($nome, $cpf, $email, $senha, $telefone);

            if ($idUsuario) {
                $_SESSION['sucesso'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                header("Location: " . BASE_URL . "/app/view/login.php");
                exit;
            } else {
                $_SESSION['erro'] = "Erro ao realizar cadastro. Tente novamente.";
                header("Location: " . BASE_URL . "/app/view/cadastro.php");
                exit;
            }
        }
    }

    /**
     * Faz logout
     */
    public function logout()
    {
        $this->iniciarSessao();

        // Destroi a sessão
        session_unset();
        session_destroy();

        // Redireciona para login
        header("Location: " . BASE_URL . "/app/view/login.php");
        exit;
    }

    /**
     * Verifica se o usuário está logado
     */
    public static function verificarLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario_id']);
    }

    /**
     * Verifica se o usuário é admin
     */
    public static function verificarAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] === 'ADMIN';
    }

    /**
     * Verifica se o usuário é cliente
     */
    public static function verificarCliente()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] === 'CLIENTE';
    }

    /**
     * Protege rotas de admin (só admin pode acessar)
     */
    public static function protegerAdmin()
    {
        if (!self::verificarLogin()) {
            header("Location: " . BASE_URL . "/app/view/login.php");
            exit;
        }

        if (!self::verificarAdmin()) {
            header("Location: " . BASE_URL . "/app/view/cliente/tela_inicial.php");
            exit;
        }
    }

    /**
     * Protege rotas de cliente (só cliente pode acessar)
     */
    public static function protegerCliente()
    {
        if (!self::verificarLogin()) {
            header("Location: " . BASE_URL . "/app/view/login.php");
            exit;
        }

        if (!self::verificarCliente()) {
            header("Location: " . BASE_URL . "/app/view/admin/index.php");
            exit;
        }
    }

    /**
     * Processa a exclusão da conta do cliente
     */
    public function excluirConta()
    {
        $this->iniciarSessao();

        // Verifica se o usuário está logado e é cliente
        if (!self::verificarLogin() || !self::verificarCliente()) {
            $_SESSION['erro'] = "Acesso negado.";
            header("Location: " . BASE_URL . "/app/view/login.php");
            exit;
        }

        $idUsuario = $_SESSION['usuario_id'] ?? null;

        if (!$idUsuario) {
            $_SESSION['erro'] = "Erro ao identificar usuário.";
            header("Location: " . BASE_URL . "/app/view/cliente/conta.php");
            exit;
        }

        // Exclui a conta
        $resultado = $this->usuarioModel->excluirConta($idUsuario);

        if ($resultado) {
            // Destroi a sessão
            session_unset();
            session_destroy();

            // Redireciona para tela inicial como solicitado
            // Como a tela inicial está protegida, o sistema redirecionará automaticamente para login
            header("Location: " . BASE_URL . "/app/view/cliente/tela_inicial.php");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao excluir conta. Tente novamente.";
            header("Location: " . BASE_URL . "/app/view/cliente/conta.php");
            exit;
        }
    }
}

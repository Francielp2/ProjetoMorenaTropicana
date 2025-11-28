<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class LoginController
{
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    /**
     * Exibe a página de login
     * Se for POST, processa o login
     */
    public function index()
    {
        // Inicia sessão
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se for POST, processa o login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authController->login();
            return; // O AuthController já faz o redirecionamento
        }

        // Pega mensagens de erro/sucesso da sessão
        $erro = $_SESSION['erro'] ?? '';
        $sucesso = $_SESSION['sucesso'] ?? '';
        
        // Limpa as mensagens da sessão após pegar
        unset($_SESSION['erro']);
        unset($_SESSION['sucesso']);

        // Inclui a view passando as variáveis prontas
        require_once __DIR__ . "/../view/login.php";
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'LoginController.php') {
    $controller = new LoginController();
    $controller->index();
}

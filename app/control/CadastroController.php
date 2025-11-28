<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class CadastroController
{
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    /**
     * Exibe a página de cadastro
     * Se for POST, processa o cadastro
     */
    public function index()
    {
        // Inicia sessão
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se for POST, processa o cadastro
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authController->cadastro();
            return; // O AuthController já faz o redirecionamento
        }

        // Pega mensagens de erro/sucesso da sessão
        $erro = $_SESSION['erro'] ?? '';
        $sucesso = $_SESSION['sucesso'] ?? '';
        
        // Limpa as mensagens da sessão após pegar
        unset($_SESSION['erro']);
        unset($_SESSION['sucesso']);

        // Inclui a view passando as variáveis prontas
        require_once __DIR__ . "/../view/cadastro.php";
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'CadastroController.php') {
    $controller = new CadastroController();
    $controller->index();
}

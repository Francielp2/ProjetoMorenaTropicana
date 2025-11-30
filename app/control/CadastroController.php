<?php
/* CHAMA COMO BASE O CONTROLER DE AUTENTICAÇÃO */
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class CadastroController
{
    /* INSTANCIA O OBJETO DE AUTH CONTROLER AO INSTANCIAR ESSA CLASSE */
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    /* EXIBE A PÁGINA DE CADASTRO E SE FOR POST, PROCESSA OS DADOS DO POST */
    public function index()
    {
        /*  Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* Se for POST, processa o cadastro */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authController->cadastro();
            return; /* O AuthController já faz o redirecionamento */
        }

        /*  Pega mensagens de erro/sucesso da sessão */
        $erro = $_SESSION['erro'] ?? '';
        $sucesso = $_SESSION['sucesso'] ?? '';

        /* Limpa as mensagens da sessão após pegar */
        unset($_SESSION['erro']);
        unset($_SESSION['sucesso']);

        require_once __DIR__ . "/../view/cadastro.php";/*   Inclui a view passando as variáveis prontas */
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'CadastroController.php') {
    $controller = new CadastroController();
    $controller->index();
}

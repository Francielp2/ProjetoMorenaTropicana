<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class LogoutController
{
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    /**
     * Processa o logout
     */
    public function index()
    {
        $this->authController->logout();
        // O AuthController já faz o redirecionamento
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'LogoutController.php') {
    $controller = new LogoutController();
    $controller->index();
}

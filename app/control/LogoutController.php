<?php
/* CHAMA COMO BASE O CONTROLER DE AUTENTICAÇÃO */
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class LogoutController
{
    /* INSTANCIA O OBJETO DE AUTH CONTROLER AO INSTANCIAR ESSA CLASSE */
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }


    /* A FUNÇÃO PRINCIPAL DESSE ARQUIVO CHAMA A FUNÇÃO DE LOGOUT */
    public function index()
    {
        $this->authController->logout();        /* O AuthController já faz o redirecionamento */
    }
}

/* Se o arquivo foi chamado diretamente, executa o controller */
if (basename($_SERVER['PHP_SELF']) === 'LogoutController.php') {
    $controller = new LogoutController();
    $controller->index();
}

<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class AdminController
{
    /**
     * Roteia para a ação solicitada
     */
    public function index()
    {
        // Pega a ação da URL (ex: ?acao=usuarios)
        $acao = $_GET['acao'] ?? 'usuarios';

        // Chama o método correspondente
        switch ($acao) {
            case 'usuarios':
                $this->usuarios();
                break;
            case 'produtos':
                $this->produtos();
                break;
            case 'pedidos':
                $this->pedidos();
                break;
            case 'estoque':
                $this->estoque();
                break;
            default:
                $this->usuarios();
                break;
        }
    }

    /**
     * Exibe a página de usuários
     */
    private function usuarios()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        $titulo_pagina = "Usuários";
        $_GET['acao'] = 'usuarios';

        // Inclui a view
        require_once __DIR__ . "/../view/admin/usuarios.php";
    }

    /**
     * Exibe a página de produtos
     */
    private function produtos()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        $titulo_pagina = "Produtos";
        $_GET['acao'] = 'produtos';

        // Inclui a view
        require_once __DIR__ . "/../view/admin/produtos.php";
    }

    /**
     * Exibe a página de pedidos
     */
    private function pedidos()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        $titulo_pagina = "Pedidos";
        $_GET['acao'] = 'pedidos';

        // Inclui a view
        require_once __DIR__ . "/../view/admin/pedidos.php";
    }

    /**
     * Exibe a página de estoque
     */
    private function estoque()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        $titulo_pagina = "Estoque";
        $_GET['acao'] = 'estoque';

        // Inclui a view
        require_once __DIR__ . "/../view/admin/estoque.php";
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'AdminController.php') {
    $controller = new AdminController();
    $controller->index();
}


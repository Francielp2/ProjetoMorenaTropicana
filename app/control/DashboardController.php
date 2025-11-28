<?php
require_once __DIR__ . "/../model/DashboardModel.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";

class DashboardController
{
    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Exibe a página do dashboard
     * Busca todas as estatísticas e inclui a view
     */
    public function index()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        // Busca todas as estatísticas do banco
        $totalUsuarios = $this->dashboardModel->getTotalUsuarios();
        $totalProdutos = $this->dashboardModel->getTotalProdutos();
        $pedidosPendentes = $this->dashboardModel->getPedidosPendentes();
        $totalEstoque = $this->dashboardModel->getTotalEstoque();
        $receitaMesAtual = $this->dashboardModel->getReceitaMesAtual();
        $totalVendas = $this->dashboardModel->getTotalVendas();

        // Formata a receita para exibição
        $receitaFormatada = "R$ " . number_format($receitaMesAtual, 2, ',', '.');

        // Define o título da página
        $titulo_pagina = "Dashboard";

        // Define a página atual para o menu
        $_GET['acao'] = 'dashboard';

        // Inclui a view passando todas as variáveis prontas
        require_once __DIR__ . "/../view/admin/index.php";
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'DashboardController.php') {
    $controller = new DashboardController();
    $controller->index();
}

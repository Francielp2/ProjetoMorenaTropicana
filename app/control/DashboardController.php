<?php
require_once __DIR__ . "/../model/DashboardModel.php";
require_once __DIR__ . "/../config/config.php";

class DashboardController
{
    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Busca todas as estatísticas do dashboard
     * Retorna um array com todos os dados formatados
     */
    public function getEstatisticas()
    {
        // Busca todas as estatísticas do banco
        $totalUsuarios = $this->dashboardModel->getTotalUsuarios();
        $totalProdutos = $this->dashboardModel->getTotalProdutos();
        $pedidosPendentes = $this->dashboardModel->getPedidosPendentes();
        $totalEstoque = $this->dashboardModel->getTotalEstoque();
        $receitaMesAtual = $this->dashboardModel->getReceitaMesAtual();
        $totalVendas = $this->dashboardModel->getTotalVendas();

        // Formata a receita para exibição
        $receitaFormatada = "R$ " . number_format($receitaMesAtual, 2, ',', '.');

        // Retorna um array com todos os dados
        return [
            'totalUsuarios' => $totalUsuarios,
            'totalProdutos' => $totalProdutos,
            'pedidosPendentes' => $pedidosPendentes,
            'totalEstoque' => $totalEstoque,
            'receitaMesAtual' => $receitaMesAtual,
            'receitaFormatada' => $receitaFormatada,
            'totalVendas' => $totalVendas
        ];
    }
}

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

        // Busca os 5 últimos pedidos
        $ultimosPedidos = $this->dashboardModel->getUltimosPedidos(5);

        // Formata os pedidos para exibição
        $pedidosFormatados = [];
        foreach ($ultimosPedidos as $pedido) {
            // Formata ID do pedido (com zeros à esquerda)
            $idFormatado = '#' . str_pad($pedido['id_pedido'], 6, '0', STR_PAD_LEFT);

            // Formata data (DD/MM/YYYY)
            $dataFormatada = '';
            if (!empty($pedido['data_pedido'])) {
                $dataObj = new DateTime($pedido['data_pedido']);
                $dataFormatada = $dataObj->format('d/m/Y');
            }

            // Formata valor
            $valorFormatado = 'R$ ' . number_format($pedido['valor_total'] ?? 0, 2, ',', '.');

            // Formata status (traduz e define classe CSS)
            $statusFormatado = $this->formatarStatusPedido($pedido['status_pedido']);

            $pedidosFormatados[] = [
                'id' => $idFormatado,
                'id_original' => $pedido['id_pedido'],
                'cliente' => $pedido['nome_cliente'] ?? 'Cliente não encontrado',
                'data' => $dataFormatada,
                'valor' => $valorFormatado,
                'status' => $statusFormatado['texto'],
                'status_classe' => $statusFormatado['classe']
            ];
        }

        // Busca produtos com estoque baixo
        $produtosEstoqueBaixo = $this->dashboardModel->getProdutosEstoqueBaixo();

        // Formata os produtos para exibição
        $produtosFormatados = [];
        foreach ($produtosEstoqueBaixo as $produto) {
            $quantidade = (int)$produto['quantidade'];

            // Determina status baseado na quantidade
            $statusFormatado = $this->formatarStatusEstoque($quantidade);

            $produtosFormatados[] = [
                'id_produto' => $produto['id_produto'],
                'id_estoque' => $produto['id_estoque'],
                'nome' => $produto['nome_produto'],
                'categoria' => $produto['categoria'] ?? 'Sem categoria',
                'quantidade' => $quantidade,
                'status' => $statusFormatado['texto'],
                'status_classe' => $statusFormatado['classe']
            ];
        }

        // Define o título da página
        $titulo_pagina = "Dashboard";

        // Define a página atual para o menu
        $_GET['acao'] = 'dashboard';

        // Inclui a view passando todas as variáveis prontas
        require_once __DIR__ . "/../view/admin/index.php";
    }

    /**
     * Formata o status do pedido para exibição
     * Retorna array com texto formatado e classe CSS
     */
    private function formatarStatusPedido($status)
    {
        $statusMap = [
            'PENDENTE' => ['texto' => 'Pendente', 'classe' => 'admin-badge-warning'],
            'FINALIZADO' => ['texto' => 'Finalizado', 'classe' => 'admin-badge-success'],
            'ENTREGUE' => ['texto' => 'Entregue', 'classe' => 'admin-badge-success'],
            'CANCELADO' => ['texto' => 'Cancelado', 'classe' => 'admin-badge-danger']
        ];

        return $statusMap[$status] ?? ['texto' => ucfirst(strtolower($status)), 'classe' => 'admin-badge-info'];
    }

    /**
     * Formata o status do estoque baseado na quantidade
     * Retorna array com texto formatado e classe CSS
     */
    private function formatarStatusEstoque($quantidade)
    {
        if ($quantidade == 0) {
            return ['texto' => 'Esgotado', 'classe' => 'admin-badge-danger'];
        } elseif ($quantidade < 3) {
            return ['texto' => 'Crítico', 'classe' => 'admin-badge-danger'];
        } elseif ($quantidade < 10) {
            return ['texto' => 'Baixo', 'classe' => 'admin-badge-warning'];
        } else {
            return ['texto' => 'Disponível', 'classe' => 'admin-badge-success'];
        }
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'DashboardController.php') {
    $controller = new DashboardController();
    $controller->index();
}

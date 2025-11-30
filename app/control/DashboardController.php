<?php

/* FAZ AS REQUISIÇÕES NECESSÁRIAS PARA QUE FUNCIONE O DASHBOARD*/
require_once __DIR__ . "/../model/DashboardModel.php";/*CHAMA O MODEL DO DASHBOARD*/
require_once __DIR__ . "/../config/config.php";/*CHAMA O ARQUIVO QUE CONFIGURA OS CAMINHOS DE OUTROS ARQUIVOS*/
require_once __DIR__ . "/AuthController.php";/*CHAMA O CONTROLADOR DE LOGIN E AÇÕES RELACIONADAS*/

/* ---CLASSE QUE CONTROLA A DANHBOARD--- */

class DashboardController
{

    /* CRIA UMA VÁRIAVEL E INICIA NESTA O OBJETO DE DASHBOARD MODEL PARA CONSEGUIR USAR AS FUNÇÕES DECLARADAS NO MODEL */

    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /* ---FUNÇÃO DE INCLUIR A VIEW E TRATAR AS VARIÁVEIS PARA SEREM USADAS */
    public function index()
    {
        /* ---CHAMA A FUNÇÃO DE PROTEGER A ROTA PARA QUE USUÁRIOS SEM AUTORIZAÇÃO NÃO ACESSEM---*/
        /* ---ESSA FUNÇÃO É ESTATICA POR ISSO NÃO PRECISA INSTÂNCIAR O OBJETO---*/
        AuthController::protegerAdmin();

        /* CHAMA AS FUNÇÕES DE CONSULTA DO DASHBOARD MODEL E PASSA O QUE ELAS RETORNAM PARA AS RESPECTIVAS VARIÁVEIS */
        $totalUsuarios = $this->dashboardModel->getTotalUsuarios();
        $totalProdutos = $this->dashboardModel->getTotalProdutos();
        $pedidosPendentes = $this->dashboardModel->getPedidosPendentes();
        $totalEstoque = $this->dashboardModel->getTotalEstoque();
        $receitaAnoAtual = $this->dashboardModel->getReceitaAnoAtual();
        $totalVendas = $this->dashboardModel->getTotalVendas();
        $receitaFormatada = "R$ " . number_format($receitaAnoAtual, 2, ',', '.');/* FORMATA O VALOR DA RECEITA PARA SER EXIBIDO */

        $ultimosPedidos = $this->dashboardModel->getUltimosPedidos(5);/* BUSCA OS ULTIMOS 5 PEDIDOS REALIZADOS */

        /* FORMATAÇÃO DOS DADOS DOS ULTIMOS PEDIDOS PARA SEREM MOSTRADOS NA TABELA */
        $pedidosFormatados = [];
        foreach ($ultimosPedidos as $pedido) {
            $idFormatado = '#' . str_pad($pedido['id_pedido'], 6, '0', STR_PAD_LEFT);/* Formata o ID */
            $dataFormatada = '';/* Formata data*/
            if (!empty($pedido['data_pedido'])) {
                $dataObj = new DateTime($pedido['data_pedido']);
                $dataFormatada = $dataObj->format('d/m/Y');
            }

            $valorFormatado = 'R$ ' . number_format($pedido['valor_total'] ?? 0, 2, ',', '.'); // Formata valor

            /* FUNÇÃO DE FORMATAR O STATUS DO PEDIDO ESSA FUNÇÃO SERÁ DECLARADA POSTERIORMENTE NESSE CÓDIGO */
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
        $produtosEstoqueBaixo = $this->dashboardModel->getProdutosEstoqueBaixo();/* Busca produtos com estoque baixo */

        /* FORMATAÇÃO DOS DADOS DOS PRODUTOS COM ESTOQUE BAIXO PARA SEREM MOSTRADOS NA TABELA */
        $produtosFormatados = [];
        foreach ($produtosEstoqueBaixo as $produto) {
            $quantidade = (int)$produto['quantidade'];

            $statusFormatado = $this->formatarStatusEstoque($quantidade); /* função de formatar e cassificar o estoque pela quantidade. Será implemntada masis a frente no código */

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

        $titulo_pagina = "Dashboard"; /* Define o título da página */

        $_GET['acao'] = 'dashboard'; /* Define a página atual para o menu */

        require_once __DIR__ . "/../view/admin/index.php";/* INCLUI A VIEW */
    }

    /* FUNÇÃO DE FORMATAR O STATUS DO PEDIDO, REBEBE UMA STRING COM O STATUS */
    private function formatarStatusPedido($status)
    {
        $statusMap = [
            'PENDENTE' => ['texto' => 'Pendente', 'classe' => 'admin-badge-warning'],
            'FINALIZADO' => ['texto' => 'Finalizado', 'classe' => 'admin-badge-success'],
            'ENTREGUE' => ['texto' => 'Entregue', 'classe' => 'admin-badge-success'],
            'CANCELADO' => ['texto' => 'Cancelado', 'classe' => 'admin-badge-danger']
        ];

        return $statusMap[$status] ?? ['texto' => ucfirst(strtolower($status)), 'classe' => 'admin-badge-info'];/* RETORNA UM ARRAY QUE CONTÉM O STATUS E A CLASSE CSS DESSE STATUS PARA SER FORMATADO NA TABELA */
    }

    /* FUNÇÃO DE FORMATAR O STATUS DO ESTOQUE, REBEBE UM INTEIRO COM A QUANTIDADE DE ITENS NO ESTOQUE */
    /* RETORNA O O STATUS E A CLASSE */
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

/* PEGA O ARQUIVO QUE ESTA SENDO EXECULTADO E SE ESSE ARQUIVO FOR O ATUAL INSTANCIA O OBJETO CHAMA A A FUNÇÃO DE MOSTRAR O INDEX */
if (basename($_SERVER['PHP_SELF']) === 'DashboardController.php') {
    $controller = new DashboardController();
    $controller->index();
}

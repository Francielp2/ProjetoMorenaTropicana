<?php
/* chama os arquivos necessários de config e de model usuario, produto e de controler de autenticação */
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/../model/UsuarioModel.php";
require_once __DIR__ . "/../model/ProdutoModel.php";
require_once __DIR__ . "/../model/PedidoModel.php";
require_once __DIR__ . "/../model/EstoqueModel.php";

class AdminController
{
    /* instância o usuario model, produto model, pedido model e estoque model quando o controler for instânciado */
    private $usuarioModel;
    private $produtoModel;
    private $pedidoModel;
    private $estoqueModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->produtoModel = new ProdutoModel();
        $this->pedidoModel = new PedidoModel();
        $this->estoqueModel = new EstoqueModel();
    }
    /* ROTEADOR QUE CHAMA PÁGINA E SEUS MÉTODOS ESPERCIFICOS DE ACORDO COM A AÇÃO SOLICITADA */
    public function index()
    {
        /* PEGA A AÇÃO DA URL POR MEIO DO GET */
        $acao = $_GET['acao'] ?? 'usuarios';

        /* CHAMA O MÉTODO CORRESPONDENTE A AÇÃO */
        switch ($acao) {
            case 'usuarios':
                $this->usuarios();
                break;
            case 'atualizarUsuario':
                $this->atualizarUsuario();
                break;
            case 'excluirUsuario':
                $this->excluirUsuario();
                break;
            case 'cadastrarUsuario':
                $this->cadastrarUsuario();
                break;
            case 'produtos':
                $this->produtos();
                break;
            case 'atualizarProduto':
                $this->atualizarProduto();
                break;
            case 'excluirProduto':
                $this->excluirProduto();
                break;
            case 'cadastrarProduto':
                $this->cadastrarProduto();
                break;
            case 'pedidos':
                $this->pedidos();
                break;
            case 'atualizarStatusPedido':
                $this->atualizarStatusPedido();
                break;
            case 'estoque':
                $this->estoque();
                break;
            case 'cadastrarEntradaEstoque':
                $this->cadastrarEntradaEstoque();
                break;
            case 'atualizarEstoque':
                $this->atualizarEstoque();
                break;
            case 'adicionarQuantidadeEstoque':
                $this->adicionarQuantidadeEstoque();
                break;
            default:
                $this->usuarios();
                break;
        }
    }

    /* MÉTODO DE EXIBIR A PÁGINA DE USUÁRIO */
    private function usuarios()
    {
        /* PROTEGE PARA QUE SÓ ADM POSSA ACESSAR USANDO A FUNÇÃO ESTÁTICA DO CONTROLADOR DE AUTENTICAÇÃO */
        AuthController::protegerAdmin();

        /* PEGA POR MEIO DO GET OS PARÂMETROS QUE FORAM USADOS PARA FAZER A PESQUISA NA TABELA DE USUÁRIOS E SE NÃO TIVER NADA PASSA VALOR VAZIO */
        $termoPesquisa = $_GET['termo'] ?? $_GET['pesquisa'] ?? '';
        $filtroTipo = $_GET['tipo'] ?? '';
        $filtroStatus = $_GET['status'] ?? '';

        /* SE UM DOS PARÂMETROS DE PESQUISA NÃO FOR VAZIO, CHAMA A FUNÇÃO DE PESQUISA USUÁRIO E SE TODOS OS PARÂMETROS FOREM VAZIOS (SE O USUÁRIO NÃO PESUISOU NADA) RETORNA TODOS OS USUÁRIOS */
        if (!empty($termoPesquisa) || !empty($filtroTipo) || !empty($filtroStatus)) {
            $usuarios = $this->usuarioModel->buscarUsuariosPorTermo($termoPesquisa, $filtroTipo, $filtroStatus);
        } else {
            $usuarios = $this->usuarioModel->listarTodosUsuarios();
        }

        /* PEGA O RESULTADO DA PESQUISA DE USUÁRIOS COM OU SEM FILTRO FORMATA OS VALORES */
        $usuariosFormatados = [];
        foreach ($usuarios as $usuario) {
            /*  Formata CPF (XXX.XXX.XXX-XX) */
            $cpfFormatado = '';
            if (!empty($usuario['cpf']) && strlen($usuario['cpf']) == 11) {
                $cpfFormatado = substr($usuario['cpf'], 0, 3) . '.' .
                    substr($usuario['cpf'], 3, 3) . '.' .
                    substr($usuario['cpf'], 6, 3) . '-' .
                    substr($usuario['cpf'], 9, 2);
            }

            /* VER SE O USUÁRIO É ADM OU CLIENTE E APLICA A CLASSE NECESSÁRIA PARA MOSTRAR NA TABELA */
            $tipo = $usuario['permissao'] === 'ADMIN' ? 'Admin' : 'Cliente';
            $tipoClasse = $usuario['permissao'] === 'ADMIN' ? 'admin-badge-primary' : 'admin-badge-info';

            /* DETERMINA O STATUS PARA SER MOSTRADO NA TABELA SE FOR CLIENTE PASSA NA VERIFICAÇÃO E SE FOR ADM É SEMPRE ATIVO */
            $status = 'Ativo';
            $statusClasse = 'admin-badge-success';

            if ($usuario['permissao'] === 'CLIENTE' && !empty($usuario['status_cliente'])) {
                $status = $usuario['status_cliente'];
                /*   Define classe CSS baseada no status */
                switch ($status) {
                    case 'Ativo':
                        $statusClasse = 'admin-badge-success';
                        break;
                    case 'Suspenso':
                        $statusClasse = 'admin-badge-warning';
                        break;
                    default:
                        $statusClasse = 'admin-badge-success';
                }
            }

            /* CRIA UM ARRAY COM OS DADOS DE USUÁRIOS PRONTOS PARA A TABELA */
            $usuariosFormatados[] = [
                'id' => $usuario['id_usuario'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'cpf' => $cpfFormatado,
                'tipo' => $tipo,
                'tipo_classe' => $tipoClasse,
                'status' => $status,
                'status_classe' => $statusClasse
            ];
        }

        /* DEFINE O TITULO DAPÁGINA COMO USUÁRIO PARA SER USADO NO HEADER DE ADMIN E DECLARA A AÇÃO DA URL COMO USUÁRIO PARA QUE A PÁGINA DE USUÁRIO SEJA EXIBIDA*/
        $titulo_pagina = "Usuários";
        $_GET['acao'] = 'usuarios';

        /* DECLARA OS FILTROS PARA QUE ELES FIQUEM SELECIONADOS NA VIEW */
        $filtros = [
            'termo' => $_GET['termo'] ?? $termoPesquisa,
            'tipo' => $_GET['tipo'] ?? $filtroTipo,
            'status' => $_GET['status'] ?? $filtroStatus
        ];

        /* SE FOR ENVIADO NA URL PEGA O EDITAR QUE CONTÉM O ID DO USUÁRIO */
        $usuarioEdicao = null;
        if (isset($_GET['editar']) && !empty($_GET['editar'])) {
            $usuarioCompleto = $this->usuarioModel->buscarUsuarioCompleto($_GET['editar']);
            if ($usuarioCompleto) {
                /* Formata dados do usuário para edição */
                $cpfFormatado = '';
                if (!empty($usuarioCompleto['cpf']) && strlen($usuarioCompleto['cpf']) == 11) {
                    $cpfFormatado = substr($usuarioCompleto['cpf'], 0, 3) . '.' .
                        substr($usuarioCompleto['cpf'], 3, 3) . '.' .
                        substr($usuarioCompleto['cpf'], 6, 3) . '-' .
                        substr($usuarioCompleto['cpf'], 9, 2);
                }
                /* DADOS QUE VÃO APARECER NO MODAL DE EDIÇÃO */
                $usuarioEdicao = [
                    'id' => $usuarioCompleto['id_usuario'],
                    'nome' => $usuarioCompleto['nome'],
                    'email' => $usuarioCompleto['email'],
                    'cpf' => $cpfFormatado,
                    'tipo' => $usuarioCompleto['permissao'],
                    'status' => $usuarioCompleto['status_cliente'] ?? 'Ativo',
                    'telefone' => $usuarioCompleto['telefone'] ?? ''
                ];
            }
        }

        /* SE FOR ENVIADO NA URL PEGA O VISUALIZAR QUE CONTÉM O ID DO USUÁRIO */
        $usuarioVisualizacao = null;
        if (isset($_GET['visualizar']) && !empty($_GET['visualizar'])) {
            $usuarioCompleto = $this->usuarioModel->buscarUsuarioCompleto($_GET['visualizar']);
            if ($usuarioCompleto) {
                /* Formata dados do usuário para visualização */
                $cpfFormatado = '';
                if (!empty($usuarioCompleto['cpf']) && strlen($usuarioCompleto['cpf']) == 11) {
                    $cpfFormatado = substr($usuarioCompleto['cpf'], 0, 3) . '.' .
                        substr($usuarioCompleto['cpf'], 3, 3) . '.' .
                        substr($usuarioCompleto['cpf'], 6, 3) . '-' .
                        substr($usuarioCompleto['cpf'], 9, 2);
                }

                $telefoneFormatado = '';
                if (!empty($usuarioCompleto['telefone'])) {
                    $telefone = $usuarioCompleto['telefone'];
                    if (strlen($telefone) == 11) {
                        $telefoneFormatado = '(' . substr($telefone, 0, 2) . ') ' .
                            substr($telefone, 2, 5) . '-' .
                            substr($telefone, 7, 4);
                    } else {
                        $telefoneFormatado = $telefone;
                    }
                }

                $tipo = $usuarioCompleto['permissao'] === 'ADMIN' ? 'Administrador' : 'Cliente';
                $status = 'Ativo';
                if ($usuarioCompleto['permissao'] === 'CLIENTE' && !empty($usuarioCompleto['status_cliente'])) {
                    $status = $usuarioCompleto['status_cliente'];
                }

                $dataContratacaoFormatada = '';
                if (!empty($usuarioCompleto['data_contratacao'])) {
                    $dataObj = new DateTime($usuarioCompleto['data_contratacao']);
                    $dataContratacaoFormatada = $dataObj->format('d/m/Y');
                }
                /* DADOS QUE VÃO APARECER NO MODEL DE VISUALIZAÇÃO */
                $usuarioVisualizacao = [
                    'id' => $usuarioCompleto['id_usuario'],
                    'nome' => $usuarioCompleto['nome'],
                    'email' => $usuarioCompleto['email'],
                    'cpf' => $cpfFormatado,
                    'telefone' => $telefoneFormatado,
                    'tipo' => $tipo,
                    'status' => $status,
                    'data_contratacao' => $dataContratacaoFormatada
                ];
            }
        }

        /* INCLUI A VIEW DE USUÁRIO */
        require_once __DIR__ . "/../view/admin/usuarios.php";
    }

    /* EXIBE A PÁGINA DE PRODUTOS */
    private function produtos()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* Pega parâmetros de pesquisa e filtros via GET */
        $termoPesquisa = $_GET['termo'] ?? $_GET['pesquisa'] ?? '';
        $filtroCategoria = $_GET['categoria'] ?? '';

        /* VALIDA SE FOI PASSADO FILTROS DE PESQUISA E PESQUISA OS PRODUTOS DE ACORDO COM OS FILTROS OU SEM ELES */
        if (!empty($termoPesquisa) || !empty($filtroCategoria)) {
            /* SE EXISTIR TERMO PASSA OS TERMOS COMO PARÂMETRO DE PESQUISA */
            $produtos = $this->produtoModel->buscarProdutosPorTermo($termoPesquisa, $filtroCategoria);
        } else {
            $produtos = $this->produtoModel->listarTodosProdutos();
        }

        /* BUSCA AS CATEGORIAS EXISTENTES PARA SER EXIBIDA NO FILTRO*/
        $categorias = $this->produtoModel->listarCategorias();

        /* FORMATA OS PRODUTOS PARA SEREM EXIBIDOS */
        $produtosFormatados = [];
        foreach ($produtos as $produto) {
            /* Formata preço (R$ XXX,XX) */
            $precoFormatado = 'R$ ' . number_format($produto['preco'], 2, ',', '.');

            /* Determina status do estoque */
            $estoqueTotal = (int)$produto['estoque_total'];
            $statusEstoque = $this->formatarStatusEstoque($estoqueTotal);/* CHAMA A FUNÇÃO DE FORMATAR E PASSAR AS CLASSES PARA OS PRODUTOS */

            /* ARRAY COM OS RESULTADOS FORMATADOS */
            $produtosFormatados[] = [
                'id' => $produto['id_produto'],
                'nome' => $produto['nome'],
                'categoria' => $produto['categoria'] ?? 'Sem categoria',
                'preco' => $precoFormatado,
                'preco_numerico' => $produto['preco'],
                'estoque' => $estoqueTotal,
                'status_estoque' => $statusEstoque['texto'],
                'status_classe' => $statusEstoque['classe'],
                'descricao' => $produto['descricao'],
                'imagem' => $produto['imagens']
            ];
        }


        /* PASSA O TITULO DA PÁGINA E AÇÃO QUE DEVE SER EXECUTADA PELO ROTEADOR */
        $titulo_pagina = "Produtos";
        $_GET['acao'] = 'produtos';

        /* PASSA OS FILTROS QUE A VIEW DEVE MANTER SELECIONADOS ATÉ QUE SEJAM LIMPOS */
        $filtros = [
            'termo' => $termoPesquisa,
            'categoria' => $filtroCategoria
        ];

        /* Busca dados do produto para edição (se houver ID na URL) */
        $produtoEdicao = null;
        if (isset($_GET['editar']) && !empty($_GET['editar'])) {
            $produtoCompleto = $this->produtoModel->buscarProdutoPorId($_GET['editar']);
            if ($produtoCompleto) {
                $produtoEdicao = [
                    'id' => $produtoCompleto['id_produto'],
                    'nome' => $produtoCompleto['nome'],
                    'descricao' => $produtoCompleto['descricao'],
                    'categoria' => $produtoCompleto['categoria'],
                    'preco' => number_format($produtoCompleto['preco'], 2, '.', ''),
                    'imagem' => $produtoCompleto['imagens']
                ];
            }
        }

        /* Busca dados do produto para visualização (se houver ID na URL) */
        $produtoVisualizacao = null;
        if (isset($_GET['visualizar']) && !empty($_GET['visualizar'])) {
            $produtoCompleto = $this->produtoModel->buscarProdutoPorId($_GET['visualizar']);
            if ($produtoCompleto) {
                $precoFormatado = 'R$ ' . number_format($produtoCompleto['preco'], 2, ',', '.');
                $estoqueTotal = (int)$produtoCompleto['estoque_total'];

                $produtoVisualizacao = [
                    'id' => $produtoCompleto['id_produto'],
                    'nome' => $produtoCompleto['nome'],
                    'descricao' => $produtoCompleto['descricao'],
                    'categoria' => $produtoCompleto['categoria'] ?? 'Sem categoria',
                    'preco' => $precoFormatado,
                    'estoque_total' => $estoqueTotal,
                    'imagem' => $produtoCompleto['imagens']
                ];
            }
        }

        /* REQUIRE A VIEW PARA MOSTRAR O FRONT END */
        require_once __DIR__ . "/../view/admin/produtos.php";
    }

    /* PROCESSA DADOS DE UM NOVO PRODUTO */
    public function cadastrarProduto()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* PEGA OS DADOS QUE FORAM DECLARADOS NO POST */
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $preco = $_POST['preco'] ?? 0;
        $imagem = trim($_POST['imagem'] ?? '');

        /* VALIDAÇÕES BÁSICAS */
        if (empty($nome)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Nome é obrigatório'));
            exit;
        }

        if (empty($descricao)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Descrição é obrigatória'));
            exit;
        }

        if (empty($categoria)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Categoria é obrigatória'));
            exit;
        }

        if ($preco <= 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Preço deve ser maior que zero'));
            exit;
        }

        /* SE PASSAR POR TODAS AS VALIDAÇÕES  ATÉ AQUI CHAMA A FUNÇÃO DE INSERIR DADOS NO BANCO */
        $idProduto = $this->produtoModel->criarProduto($nome, $descricao, $categoria, $preco, $imagem);

        if ($idProduto) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&sucesso=' . urlencode('Produto cadastrado com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Erro ao cadastrar produto. Tente novamente.'));
            exit;
        }
    }

    /* PROCESSA A ATUALIZAÇÃO DO PRODUTO */
    public function atualizarProduto()
    {
        /* PROTEGE A ROTA PARA SOMENTE O ADMIN PODER ACESSAR */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* Pega dados do POST */
        $idProduto = $_POST['id'] ?? null;
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $preco = $_POST['preco'] ?? 0;
        $imagem = trim($_POST['imagem'] ?? '');

        /* VALIDAÇÕES BÁSICAS */
        if (!$idProduto) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('ID do produto não informado'));
            exit;
        }

        if (empty($nome)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Nome é obrigatório'));
            exit;
        }

        if ($preco <= 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Preço deve ser maior que zero'));
            exit;
        }

        /* SE PASSAR POR TODAS AS VALIDAÇÕES ATÉ AGORA CHAMA A FUNÇÃO DE ATUALIZAR OS DADOS NO BANCO */
        $resultado = $this->produtoModel->atualizarProduto($idProduto, $nome, $descricao, $categoria, $preco, $imagem);

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&sucesso=' . urlencode('Produto atualizado com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Erro ao atualizar produto. Tente novamente.'));
            exit;
        }
    }

    /* PROCESSA A EXCLUSÃO DE UM PRODUTO */
    public function excluirProduto()
    {
        /* PROTEGE A ROTA PARA SOMENTE O ADMIN PODER EXECUTAR A AÇÃO */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* Pega ID do POST */
        $idProduto = $_POST['id'] ?? null;

        /* VALIDAÇÃO BÁSICA DAS INFORMAÇÕES */
        if (!$idProduto) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('ID do produto não informado'));
            exit;
        }

        /* SE PASSAR PELA VALIDAÇÃO, CHAMA A FUNÇÃO DE EXCLUIR O PRODUTO E PASSA O ID DO MESMO COMO PARÂMETRO */
        $resultado = $this->produtoModel->excluirProduto($idProduto);

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&sucesso=' . urlencode('Produto excluído com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=produtos&erro=' . urlencode('Erro ao excluir produto. Tente novamente.'));
            exit;
        }
    }

    /* FUNÇÃO DE FORMATAR E PASSAR A CLASSE REFERENTE A QUANTRIDADE DE ESTOQUE */
    private function formatarStatusEstoque($quantidade)
    {
        if ($quantidade == 0) {
            return ['texto' => 'Sem Estoque', 'classe' => 'admin-badge-danger'];
        } elseif ($quantidade < 3) {
            return ['texto' => 'Estoque Crítico', 'classe' => 'admin-badge-danger'];
        } elseif ($quantidade < 10) {
            return ['texto' => 'Estoque Baixo', 'classe' => 'admin-badge-warning'];
        } else {
            return ['texto' => 'Disponível', 'classe' => 'admin-badge-success'];
        }
    }

    /* EXIBE A PÁGINA DE PEDIDOS */
    private function pedidos()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* PEGA POR MEIO DO GET OS PARÂMETROS QUE FORAM USADOS PARA FAZER A PESQUISA NA TABELA DE PEDIDOS E SE NÃO TIVER NADA PASSA VALOR VAZIO */
        $termoPesquisa = $_GET['termo'] ?? $_GET['pesquisa'] ?? '';
        $filtroStatusPedido = $_GET['status_pedido'] ?? '';
        $filtroStatusPagamento = $_GET['status_pagamento'] ?? '';
        $dataInicial = $_GET['data_inicial'] ?? '';
        $dataFinal = $_GET['data_final'] ?? '';
        $valorMin = $_GET['valor_min'] ?? '';
        $valorMax = $_GET['valor_max'] ?? '';

        /* SE UM DOS PARÂMETROS DE PESQUISA NÃO FOR VAZIO, CHAMA A FUNÇÃO DE PESQUISA PEDIDO E SE TODOS OS PARÂMETROS FOREM VAZIOS (SE O USUÁRIO NÃO PESUISOU NADA) RETORNA TODOS OS PEDIDOS */
        if (!empty($termoPesquisa) || !empty($filtroStatusPedido) || !empty($filtroStatusPagamento) || !empty($dataInicial) || !empty($dataFinal) || !empty($valorMin) || !empty($valorMax)) {
            $pedidos = $this->pedidoModel->buscarPedidosPorTermo($termoPesquisa, $filtroStatusPedido, $filtroStatusPagamento, $dataInicial, $dataFinal, $valorMin, $valorMax);
        } else {
            $pedidos = $this->pedidoModel->listarTodosPedidos();
        }

        /* FORMATA OS PEDIDOS PARA SEREM EXIBIDOS */
        $pedidosFormatados = [];
        foreach ($pedidos as $pedido) {
            /* Formata data (dd/mm/yyyy hh:mm) */
            $dataFormatada = '';
            if (!empty($pedido['data_pedido'])) {
                $dataObj = new DateTime($pedido['data_pedido']);
                $dataFormatada = $dataObj->format('d/m/Y H:i');
            }

            /* Calcula valor total sempre a partir dos itens do pedido para garantir precisão */
            $itensPedido = $this->pedidoModel->buscarItensPedido($pedido['id_pedido']);
            $valorTotal = 0;
            if (!empty($itensPedido) && is_array($itensPedido)) {
                foreach ($itensPedido as $item) {
                    $precoUnitario = floatval($item['preco_unitario'] ?? 0);
                    $quantidade = intval($item['quantidade'] ?? 0);
                    $valorTotal += $precoUnitario * $quantidade;
                }
            }
            /* Se não houver itens ou o cálculo resultar em zero, usa o valor_total da tabela como fallback */
            if ($valorTotal == 0 && !empty($pedido['valor_total'])) {
                $valorTotal = floatval($pedido['valor_total']);
            }
            $valorFormatado = 'R$ ' . number_format($valorTotal, 2, ',', '.');

            /* Determina classe CSS baseada no status do pedido */
            $statusPedido = $pedido['status_pedido'] ?? 'PENDENTE';
            $statusPedidoClasse = '';
            $statusPedidoTexto = '';
            switch ($statusPedido) {
                case 'PENDENTE':
                    $statusPedidoClasse = 'admin-badge-warning';
                    $statusPedidoTexto = 'Pendente';
                    break;
                case 'FINALIZADO':
                    $statusPedidoClasse = 'admin-badge-info';
                    $statusPedidoTexto = 'Finalizado';
                    break;
                case 'CANCELADO':
                    $statusPedidoClasse = 'admin-badge-danger';
                    $statusPedidoTexto = 'Cancelado';
                    break;
                default:
                    $statusPedidoClasse = 'admin-badge-warning';
                    $statusPedidoTexto = $statusPedido;
            }

            /* Determina classe CSS baseada no status do pagamento */
            $statusPagamento = $pedido['status_pagamento'] ?? 'PENDENTE';
            $statusPagamentoClasse = '';
            $statusPagamentoTexto = '';
            switch ($statusPagamento) {
                case 'PENDENTE':
                    $statusPagamentoClasse = 'admin-badge-warning';
                    $statusPagamentoTexto = 'Pendente';
                    break;
                case 'CONFIRMADO':
                    $statusPagamentoClasse = 'admin-badge-success';
                    $statusPagamentoTexto = 'Confirmado';
                    break;
                case 'CANCELADO':
                    $statusPagamentoClasse = 'admin-badge-danger';
                    $statusPagamentoTexto = 'Cancelado';
                    break;
                default:
                    $statusPagamentoClasse = 'admin-badge-warning';
                    $statusPagamentoTexto = 'Pendente';
            }

            /* ARRAY COM OS RESULTADOS FORMATADOS */
            $pedidosFormatados[] = [
                'id' => $pedido['id_pedido'],
                'cliente' => $pedido['nome_cliente'] ?? 'Cliente não encontrado',
                'data' => $dataFormatada,
                'valor_total' => $valorFormatado,
                'status_pedido' => $statusPedidoTexto,
                'status_pedido_classe' => $statusPedidoClasse,
                'status_pagamento' => $statusPagamentoTexto,
                'status_pagamento_classe' => $statusPagamentoClasse
            ];
        }

        /* DEFINE O TITULO DA PÁGINA COMO PEDIDOS PARA SER USADO NO HEADER DE ADMIN E DECLARA A AÇÃO DA URL COMO PEDIDOS PARA QUE A PÁGINA DE PEDIDOS SEJA EXIBIDA*/
        $titulo_pagina = "Pedidos";
        $_GET['acao'] = 'pedidos';

        /* DECLARA OS FILTROS PARA QUE ELES FIQUEM SELECIONADOS NA VIEW */
        $filtros = [
            'termo' => $termoPesquisa,
            'status_pedido' => $filtroStatusPedido,
            'status_pagamento' => $filtroStatusPagamento,
            'data_inicial' => $dataInicial,
            'data_final' => $dataFinal,
            'valor_min' => $valorMin,
            'valor_max' => $valorMax
        ];

        /* SE FOR ENVIADO NA URL PEGA O VISUALIZAR QUE CONTÉM O ID DO PEDIDO */
        $pedidoVisualizacao = null;
        if (isset($_GET['visualizar']) && !empty($_GET['visualizar'])) {
            $pedidoCompleto = $this->pedidoModel->buscarPedidoPorId($_GET['visualizar']);
            if ($pedidoCompleto) {
                /* Formata dados do pedido para visualização */
                $dataFormatada = '';
                if (!empty($pedidoCompleto['data_pedido'])) {
                    $dataObj = new DateTime($pedidoCompleto['data_pedido']);
                    $dataFormatada = $dataObj->format('d/m/Y H:i');
                }

                /* Calcula valor total sempre a partir dos itens do pedido para garantir precisão */
                $valorTotalCompleto = 0;
                if (!empty($pedidoCompleto['itens']) && is_array($pedidoCompleto['itens'])) {
                    foreach ($pedidoCompleto['itens'] as $item) {
                        $precoUnitario = floatval($item['preco_unitario'] ?? 0);
                        $quantidade = intval($item['quantidade'] ?? 0);
                        $valorTotalCompleto += $precoUnitario * $quantidade;
                    }
                }
                /* Se não houver itens ou o cálculo resultar em zero, usa o valor_total da tabela como fallback */
                if ($valorTotalCompleto == 0 && !empty($pedidoCompleto['valor_total'])) {
                    $valorTotalCompleto = floatval($pedidoCompleto['valor_total']);
                }
                $valorFormatado = 'R$ ' . number_format($valorTotalCompleto, 2, ',', '.');

                $statusPedidoTexto = '';
                switch ($pedidoCompleto['status_pedido']) {
                    case 'PENDENTE':
                        $statusPedidoTexto = 'Pendente';
                        break;
                    case 'FINALIZADO':
                        $statusPedidoTexto = 'Finalizado';
                        break;
                    case 'CANCELADO':
                        $statusPedidoTexto = 'Cancelado';
                        break;
                    default:
                        $statusPedidoTexto = $pedidoCompleto['status_pedido'];
                }

                $statusPagamentoTexto = '';
                switch ($pedidoCompleto['status_pagamento']) {
                    case 'PENDENTE':
                        $statusPagamentoTexto = 'Pendente';
                        break;
                    case 'CONFIRMADO':
                        $statusPagamentoTexto = 'Confirmado';
                        break;
                    case 'CANCELADO':
                        $statusPagamentoTexto = 'Cancelado';
                        break;
                    default:
                        $statusPagamentoTexto = 'Pendente';
                }
                $formaPagamentoTexto = $pedidoCompleto['forma_pagamento'] ?? 'Não informado';

                /* Formata itens do pedido */
                $itensFormatados = [];
                foreach ($pedidoCompleto['itens'] as $item) {
                    $precoUnitarioFormatado = 'R$ ' . number_format($item['preco_unitario'], 2, ',', '.');
                    $subtotal = $item['preco_unitario'] * $item['quantidade'];
                    $subtotalFormatado = 'R$ ' . number_format($subtotal, 2, ',', '.');

                    $itensFormatados[] = [
                        'nome' => $item['nome_produto'],
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $precoUnitarioFormatado,
                        'subtotal' => $subtotalFormatado
                    ];
                }

                /* DADOS QUE VÃO APARECER NO MODAL DE VISUALIZAÇÃO */
                $pedidoVisualizacao = [
                    'id' => $pedidoCompleto['id_pedido'],
                    'cliente' => $pedidoCompleto['nome_cliente'] ?? 'Cliente não encontrado',
                    'email' => $pedidoCompleto['email_cliente'] ?? '',
                    'data' => $dataFormatada,
                    'status_pedido' => $statusPedidoTexto,
                    'valor_total' => $valorFormatado,
                    'status_pagamento' => $statusPagamentoTexto,
                    'forma_pagamento' => $formaPagamentoTexto,
                    'itens' => $itensFormatados
                ];
            }
        }

        /* SE FOR ENVIADO NA URL PEGA O EDITAR QUE CONTÉM O ID DO PEDIDO */
        $pedidoEdicao = null;
        if (isset($_GET['editar']) && !empty($_GET['editar'])) {
            $pedidoCompleto = $this->pedidoModel->buscarPedidoPorId($_GET['editar']);
            if ($pedidoCompleto) {
                /* DADOS QUE VÃO APARECER NO MODAL DE EDIÇÃO */
                $pedidoEdicao = [
                    'id' => $pedidoCompleto['id_pedido'],
                    'status_pedido' => $pedidoCompleto['status_pedido'],
                    'status_pagamento' => $pedidoCompleto['status_pagamento'] ?? 'PENDENTE'
                ];
            }
        }

        /* Inclui a view */
        require_once __DIR__ . "/../view/admin/pedidos.php";
    }

    /* PROCESSA A ATUALIZAÇÃO DO STATUS DO PEDIDO */
    public function atualizarStatusPedido()
    {
        /* PROTEGE A ROTA PARA SOMENTE O ADMIN PODER EXECUTAR A AÇÃO */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=pedidos&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* Pega dados do POST */
        $idPedido = $_POST['id'] ?? null;
        $statusPedido = $_POST['status_pedido'] ?? null;
        $statusPagamento = $_POST['status_pagamento'] ?? null;

        /* VALIDAÇÕES BÁSICAS */
        if (!$idPedido) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=pedidos&erro=' . urlencode('ID do pedido não informado'));
            exit;
        }

        if (!$statusPedido || !in_array($statusPedido, ['PENDENTE', 'FINALIZADO', 'CANCELADO'])) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=pedidos&erro=' . urlencode('Status do pedido inválido'));
            exit;
        }

        if ($statusPagamento && !in_array($statusPagamento, ['PENDENTE', 'CONFIRMADO', 'CANCELADO'])) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=pedidos&erro=' . urlencode('Status do pagamento inválido'));
            exit;
        }

        /* SE PASSAR POR TODAS AS VALIDAÇÕES ATÉ AGORA CHAMA A FUNÇÃO DE ATUALIZAR OS DADOS NO BANCO */
        $resultado = $this->pedidoModel->atualizarStatusPedido($idPedido, $statusPedido, $statusPagamento);

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=pedidos&sucesso=' . urlencode('Status do pedido atualizado com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=pedidos&erro=' . urlencode('Erro ao atualizar status do pedido. Tente novamente.'));
            exit;
        }
    }

    /* EXIBE A PÁGINA DE ESTOQUE */
    private function estoque()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* PEGA POR MEIO DO GET OS PARÂMETROS QUE FORAM USADOS PARA FAZER A PESQUISA NA TABELA DE ESTOQUE E SE NÃO TIVER NADA PASSA VALOR VAZIO */
        $termoPesquisa = $_GET['termo'] ?? $_GET['pesquisa'] ?? '';
        $filtroStatus = $_GET['status'] ?? '';

        /* SE UM DOS PARÂMETROS DE PESQUISA NÃO FOR VAZIO, CHAMA A FUNÇÃO DE PESQUISA ESTOQUE E SE TODOS OS PARÂMETROS FOREM VAZIOS (SE O USUÁRIO NÃO PESUISOU NADA) RETORNA TODOS OS ESTOQUES */
        if (!empty($termoPesquisa) || !empty($filtroStatus)) {
            $estoques = $this->estoqueModel->buscarEstoquesPorTermo($termoPesquisa, $filtroStatus);
        } else {
            $estoques = $this->estoqueModel->listarTodosEstoques();
        }

        // Debug: verifica se há estoques retornados
        if (empty($estoques)) {
            error_log("Nenhum estoque retornado do banco de dados");
        } else {
            error_log("Total de estoques retornados: " . count($estoques));
        }

        /* FORMATA OS ESTOQUES PARA SEREM EXIBIDOS */
        $estoquesFormatados = [];
        if (is_array($estoques) && count($estoques) > 0) {
            foreach ($estoques as $estoque) {
                // Verifica se o estoque tem os campos necessários
                if (!isset($estoque['id_estoque']) || !isset($estoque['nome_produto'])) {
                    error_log("Estoque com dados incompletos: " . print_r($estoque, true));
                    continue; // Pula estoques com dados incompletos
                }

                /* Formata data (dd/mm/yyyy) */
                $dataFormatada = '';
                if (!empty($estoque['data_cadastro'])) {
                    try {
                        $dataObj = new DateTime($estoque['data_cadastro']);
                        $dataFormatada = $dataObj->format('d/m/Y');
                    } catch (Exception $e) {
                        $dataFormatada = $estoque['data_cadastro'];
                    }
                }

                /* Determina status do estoque baseado na quantidade */
                $quantidade = isset($estoque['quantidade']) ? (int)$estoque['quantidade'] : 0;
                $statusEstoque = $this->formatarStatusEstoque($quantidade);

                /* ARRAY COM OS RESULTADOS FORMATADOS */
                $tamanho = isset($estoque['tamanhos_disponiveis']) && !empty($estoque['tamanhos_disponiveis'])
                    ? $estoque['tamanhos_disponiveis']
                    : 'Não informado';
                $cor = isset($estoque['cores_disponiveis']) && !empty($estoque['cores_disponiveis'])
                    ? $estoque['cores_disponiveis']
                    : 'Não informado';

                $estoquesFormatados[] = [
                    'id' => isset($estoque['id_estoque']) ? (int)$estoque['id_estoque'] : 0,
                    'id_produto' => isset($estoque['id_produto']) ? (int)$estoque['id_produto'] : 0,
                    'produto' => isset($estoque['nome_produto']) ? $estoque['nome_produto'] : 'Produto desconhecido',
                    'tamanho' => $tamanho,
                    'cor' => $cor,
                    'quantidade' => $quantidade,
                    'data_cadastro' => $dataFormatada,
                    'status' => $statusEstoque['texto'],
                    'status_classe' => $statusEstoque['classe']
                ];
            }
        }

        /* CALCULA O RESUMO DO ESTOQUE */
        $resumoEstoque = $this->estoqueModel->calcularResumoEstoque();

        /* BUSCA LISTA DE PRODUTOS PARA OS SELECTS */
        $produtos = $this->estoqueModel->listarProdutos();

        // Debug: verifica se estoquesFormatados foi criado
        error_log("Total de estoques formatados: " . count($estoquesFormatados));

        /* DEFINE O TITULO DA PÁGINA COMO ESTOQUE PARA SER USADO NO HEADER DE ADMIN E DECLARA A AÇÃO DA URL COMO ESTOQUE PARA QUE A PÁGINA DE ESTOQUE SEJA EXIBIDA*/
        $titulo_pagina = "Estoque";
        $_GET['acao'] = 'estoque';

        /* DECLARA OS FILTROS PARA QUE ELES FIQUEM SELECIONADOS NA VIEW */
        $filtros = [
            'termo' => $termoPesquisa,
            'status' => $filtroStatus
        ];

        /* SE FOR ENVIADO NA URL PEGA O EDITAR QUE CONTÉM O ID DO ESTOQUE */
        $estoqueEdicao = null;
        if (isset($_GET['editar']) && !empty($_GET['editar'])) {
            $estoqueCompleto = $this->estoqueModel->buscarEstoquePorId($_GET['editar']);
            if ($estoqueCompleto) {
                $dataFormatada = '';
                if (!empty($estoqueCompleto['data_cadastro'])) {
                    $dataObj = new DateTime($estoqueCompleto['data_cadastro']);
                    $dataFormatada = $dataObj->format('Y-m-d');
                }

                /* DADOS QUE VÃO APARECER NO MODAL DE EDIÇÃO */
                $estoqueEdicao = [
                    'id' => $estoqueCompleto['id_estoque'],
                    'id_produto' => $estoqueCompleto['id_produto'],
                    'produto' => $estoqueCompleto['nome_produto'],
                    'tamanhos' => !empty($estoqueCompleto['tamanhos_disponiveis']) ? $estoqueCompleto['tamanhos_disponiveis'] : '',
                    'cores' => !empty($estoqueCompleto['cores_disponiveis']) ? $estoqueCompleto['cores_disponiveis'] : '',
                    'modelo' => !empty($estoqueCompleto['modelo_produto']) ? $estoqueCompleto['modelo_produto'] : '',
                    'quantidade' => $estoqueCompleto['quantidade'],
                    'data_cadastro' => $dataFormatada
                ];
            }
        }

        /* SE FOR ENVIADO NA URL PEGA O ADICIONAR QUE CONTÉM O ID DO ESTOQUE */
        $estoqueAdicionar = null;
        if (isset($_GET['adicionar']) && !empty($_GET['adicionar'])) {
            $estoqueCompleto = $this->estoqueModel->buscarEstoquePorId($_GET['adicionar']);
            if ($estoqueCompleto) {
                /* DADOS QUE VÃO APARECER NO MODAL DE ADICIONAR */
                $estoqueAdicionar = [
                    'id' => $estoqueCompleto['id_estoque'],
                    'id_produto' => $estoqueCompleto['id_produto'],
                    'produto' => $estoqueCompleto['nome_produto'],
                    'quantidade_atual' => $estoqueCompleto['quantidade']
                ];
            }
        }

        /* Inclui a view */
        require_once __DIR__ . "/../view/admin/estoque.php";
    }

    /* PROCESSA O CADASTRO DE UMA NOVA ENTRADA DE ESTOQUE */
    public function cadastrarEntradaEstoque()
    {
        AuthController::protegerAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Método não permitido'));
            exit;
        }

        $idProduto = isset($_POST['id_produto']) ? intval($_POST['id_produto']) : null;
        $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
        $tamanhosDisponiveis = isset($_POST['tamanhos']) ? trim($_POST['tamanhos']) : null;
        $coresDisponiveis = isset($_POST['cores']) ? trim($_POST['cores']) : null;
        $modeloProduto = isset($_POST['modelo']) ? trim($_POST['modelo']) : null;
        $dataCadastro = isset($_POST['data']) ? trim($_POST['data']) : null;

        // Trata strings vazias como NULL
        if (empty($tamanhosDisponiveis)) $tamanhosDisponiveis = null;
        if (empty($coresDisponiveis)) $coresDisponiveis = null;
        if (empty($modeloProduto)) $modeloProduto = null;

        // VALIDAÇÕES BÁSICAS
        if (empty($idProduto) || $idProduto <= 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Produto é obrigatório'));
            exit;
        }

        if ($quantidade <= 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Quantidade deve ser maior que zero'));
            exit;
        }

        // Tamanhos e cores não são mais obrigatórios
        // (remova as validações de tamanhos e cores obrigatórios)

        $idEstoque = $this->estoqueModel->criarEntradaEstoque($idProduto, $quantidade, $tamanhosDisponiveis, $coresDisponiveis, $modeloProduto, $dataCadastro);

        if ($idEstoque && $idEstoque > 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&sucesso=' . urlencode('Entrada de estoque cadastrada com sucesso!'));
            exit;
        } else {
            error_log("Falha ao cadastrar estoque - valores: idProduto=$idProduto, quantidade=$quantidade");
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Erro ao cadastrar entrada de estoque. Verifique os logs.'));
            exit;
        }
    }

    /* PROCESSA A ATUALIZAÇÃO DE UM REGISTRO DE ESTOQUE */
    public function atualizarEstoque()
    {
        /* PROTEGE A ROTA PARA SOMENTE O ADMIN PODER EXECUTAR A AÇÃO */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* Pega dados do POST */
        $idEstoque = isset($_POST['id']) ? intval($_POST['id']) : null;
        $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
        $tamanhosDisponiveis = isset($_POST['tamanhos']) ? trim($_POST['tamanhos']) : '';
        $coresDisponiveis = isset($_POST['cores']) ? trim($_POST['cores']) : '';
        $modeloProduto = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
        $dataCadastro = isset($_POST['data']) ? trim($_POST['data']) : null;

        // Log dos valores recebidos para debug
        error_log("Dados de atualização recebidos - idEstoque: $idEstoque, quantidade: $quantidade, tamanhos: '$tamanhosDisponiveis', cores: '$coresDisponiveis'");

        // Trata modelo vazio como NULL
        if (empty($modeloProduto)) {
            $modeloProduto = null;
        }

        /* VALIDAÇÕES BÁSICAS */
        if (!$idEstoque) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('ID do estoque não informado'));
            exit;
        }

        if ($quantidade < 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Quantidade não pode ser negativa'));
            exit;
        }

        if (empty($tamanhosDisponiveis)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Tamanhos disponíveis é obrigatório'));
            exit;
        }

        if (empty($coresDisponiveis)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Cores disponíveis é obrigatório'));
            exit;
        }

        if (empty($dataCadastro)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Data de cadastro é obrigatória'));
            exit;
        }

        /* SE PASSAR POR TODAS AS VALIDAÇÕES ATÉ AGORA CHAMA A FUNÇÃO DE ATUALIZAR OS DADOS NO BANCO */
        $resultado = $this->estoqueModel->atualizarEstoque($idEstoque, $quantidade, $tamanhosDisponiveis, $coresDisponiveis, $modeloProduto, $dataCadastro);

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&sucesso=' . urlencode('Estoque atualizado com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Erro ao atualizar estoque. Tente novamente.'));
            exit;
        }
    }

    /* PROCESSA A ADIÇÃO DE QUANTIDADE A UM REGISTRO DE ESTOQUE EXISTENTE */
    public function adicionarQuantidadeEstoque()
    {
        /* PROTEGE A ROTA PARA SOMENTE O ADMIN PODER EXECUTAR A AÇÃO */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* Pega dados do POST */
        $idEstoque = $_POST['id'] ?? null;
        $quantidadeAdicionar = $_POST['quantidade'] ?? 0;
        $dataCadastro = $_POST['data'] ?? null;

        /* VALIDAÇÕES BÁSICAS */
        if (!$idEstoque) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('ID do estoque não informado'));
            exit;
        }

        if ($quantidadeAdicionar <= 0) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Quantidade a adicionar deve ser maior que zero'));
            exit;
        }

        /* SE PASSAR POR TODAS AS VALIDAÇÕES ATÉ AGORA CHAMA A FUNÇÃO DE ADICIONAR QUANTIDADE NO BANCO */
        $resultado = $this->estoqueModel->adicionarQuantidadeEstoque($idEstoque, $quantidadeAdicionar, $dataCadastro);

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&sucesso=' . urlencode('Quantidade adicionada ao estoque com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=estoque&erro=' . urlencode('Erro ao adicionar quantidade ao estoque. Tente novamente.'));
            exit;
        }
    }

    /* Processa a atualização de um usuário */
    public function atualizarUsuario()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* se não for post retorna erro */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* pega os dados mandados pelo formuário Post */
        $idUsuario = $_POST['id'] ?? null;
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? 'CLIENTE';
        $status = $_POST['status'] ?? null;
        $telefone = !empty($_POST['telefone']) ? preg_replace('/[^0-9]/', '', $_POST['telefone']) : null;

        /* faz as validações basicas para poder atualizar o usuário */
        if (!$idUsuario) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('ID do usuário não informado'));
            exit;
        }

        if (empty($nome)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Nome é obrigatório'));
            exit;
        }

        if (empty($email)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Email é obrigatório'));
            exit;
        }

        if (!in_array($tipo, ['ADMIN', 'CLIENTE'])) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Tipo de usuário inválido'));
            exit;
        }

        if ($tipo === 'CLIENTE' && $status && !in_array($status, ['Ativo', 'Suspenso'])) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Status inválido'));
            exit;
        }

        /* verifica se o email inserido já existe em outro usuário */
        $usuarioAtual = $this->usuarioModel->buscarUsuarioCompleto($idUsuario);
        if ($usuarioAtual && $usuarioAtual['email'] !== $email) {
            if ($this->usuarioModel->emailExiste($email)) {
                header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Este email já está cadastrado para outro usuário'));
                exit;
            }
        }

        /* verifica se o adm não está mudando sua propria permissão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $usuarioLogadoId = $_SESSION['usuario_id'] ?? null;
        if ($idUsuario == $usuarioLogadoId && $usuarioAtual) {
            /*  Se o usuário atual é ADMIN e está tentando mudar para CLIENTE, impede */
            if ($usuarioAtual['permissao'] === 'ADMIN' && $tipo === 'CLIENTE') {
                header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Você não pode alterar sua própria permissão de administrador'));
                exit;
            }
        }

        /* chama a função de atualizar usuário e manda os dados validados como parâmetros */
        $senhaParaAtualizar = !empty($senha) ? $senha : null;
        $resultado = $this->usuarioModel->atualizarUsuario(
            $idUsuario,
            $nome,
            $email,
            $senhaParaAtualizar,
            $tipo,
            $status,
            $telefone
        );

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&sucesso=' . urlencode('Usuário atualizado com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Erro ao atualizar usuário. Tente novamente.'));
            exit;
        }
    }

    /* processa a ação de excluir usuário */
    public function excluirUsuario()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* verifica se o metodo é post para ser mais seguro */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* Pega ID do POST */
        $idUsuario = $_POST['id'] ?? null;

        /* se o id de usuário não for recebido envia mensagem de erro*/
        if (!$idUsuario) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('ID do usuário não informado'));
            exit;
        }

        /* verifica se está tentando se excluir */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioLogadoId = $_SESSION['usuario_id'] ?? null;
        if ($idUsuario == $usuarioLogadoId) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Você não pode excluir sua própria conta'));
            exit;
        }

        /* chama a função de excluir usuário se passar pelas validações */
        $resultado = $this->usuarioModel->excluirConta($idUsuario);

        if ($resultado) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&sucesso=' . urlencode('Usuário excluído com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Erro ao excluir usuário. Tente novamente.'));
            exit;
        }
    }

    /* processa o cadastro de um novo usuário */
    public function cadastrarUsuario()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* Verifica se é POST */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Método não permitido'));
            exit;
        }

        /* pega os dados do novo usuário informados no formuário */
        $nome = trim($_POST['nome'] ?? '');
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? ''); // Remove caracteres não numéricos
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? 'CLIENTE';
        $status = $_POST['status'] ?? 'Ativo';
        $telefone = !empty($_POST['telefone']) ? preg_replace('/[^0-9]/', '', $_POST['telefone']) : null;

        /* faz as validações basicas dos dados */
        if (empty($nome)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Nome é obrigatório'));
            exit;
        }

        if (empty($email)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Email é obrigatório'));
            exit;
        }

        if (empty($senha)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Senha é obrigatória'));
            exit;
        }

        /* Valida CPF (deve ter 11 dígitos) */
        if (strlen($cpf) !== 11) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('CPF deve conter 11 dígitos'));
            exit;
        }

        /* Valida senha (mínimo 6 caracteres) */
        if (strlen($senha) < 6) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('A senha deve ter no mínimo 6 caracteres'));
            exit;
        }

        /* Valida tipo */
        if (!in_array($tipo, ['ADMIN', 'CLIENTE'])) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Tipo de usuário inválido'));
            exit;
        }

        /*  Valida status (apenas para clientes) */
        if ($tipo === 'CLIENTE' && !in_array($status, ['Ativo', 'Suspenso'])) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Status inválido'));
            exit;
        }

        /* Verifica se email já existe */
        if ($this->usuarioModel->emailExiste($email)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Este email já está cadastrado'));
            exit;
        }

        /* Verifica se CPF já existe */
        if ($this->usuarioModel->cpfExiste($cpf)) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Este CPF já está cadastrado'));
            exit;
        }

        /* se passar por todas as verificações chama a função de criar um novo usuário e passa como parêmetro os dados informados */
        $idUsuario = $this->usuarioModel->criarUsuario($nome, $cpf, $email, $senha, $tipo, $status, $telefone);

        if ($idUsuario) {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&sucesso=' . urlencode('Usuário cadastrado com sucesso!'));
            exit;
        } else {
            header('Location: ' . BASE_URL . '/app/control/AdminController.php?acao=usuarios&erro=' . urlencode('Erro ao cadastrar usuário. Tente novamente.'));
            exit;
        }
    }
}

/* Se o arquivo foi chamado diretamente, executa o controller */
if (basename($_SERVER['PHP_SELF']) === 'AdminController.php') {
    $controller = new AdminController();
    $controller->index();
}

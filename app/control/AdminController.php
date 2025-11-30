<?php
/* chama os arquivos necessários de config e de model usuario e de controler de autenticação */
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/../model/UsuarioModel.php";

class AdminController
{
    /* instância o usuario model quando o controler for instânciado */
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
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

    /* EXIBE A PÁGINA DE PRODUTOS E  */
    private function produtos()
    {
        /*  Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* DEFINE O TITULO DA PÁGINA COMO PRODUTOS PARA SER USADO NO HEADER DE ADMIN E DECLARA A AÇÃO DA URL COMO PRODUTOS PARA QUE A PÁGINA DE USUÁRIO SEJA EXIBIDA*/
        $titulo_pagina = "Produtos";
        $_GET['acao'] = 'produtos';

        /* Inclui a view de produtos */
        require_once __DIR__ . "/../view/admin/produtos.php";
    }

    /* EXIBE A PÁGINA DE PEDIDOS */
    private function pedidos()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* DEFINE O TITULO DA PÁGINA COMO PEDIDOS PARA SER USADO NO HEADER DE ADMIN E DECLARA A AÇÃO DA URL COMO PEDIDOS PARA QUE A PÁGINA DE USUÁRIO SEJA EXIBIDA*/
        $titulo_pagina = "Pedidos";
        $_GET['acao'] = 'pedidos';

        /* Inclui a view */
        require_once __DIR__ . "/../view/admin/pedidos.php";
    }

    /* EXIBE A PÁGINA DE ESTOQUE */
    private function estoque()
    {
        /* Protege a rota - só admin pode acessar */
        AuthController::protegerAdmin();

        /* DEFINE O TITULO DA PÁGINA COMO ESTOQUE PARA SER USADO NO HEADER DE ADMIN E DECLARA A AÇÃO DA URL COMO ESTOQUE PARA QUE A PÁGINA DE USUÁRIO SEJA EXIBIDA*/
        $titulo_pagina = "Estoque";
        $_GET['acao'] = 'estoque';

        /* Inclui a view */
        require_once __DIR__ . "/../view/admin/estoque.php";
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

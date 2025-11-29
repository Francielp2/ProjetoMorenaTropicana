<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/../model/UsuarioModel.php";

class AdminController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }
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
            case 'buscarUsuario':
                $this->buscarUsuario();
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
            case 'pesquisarUsuarios':
                $this->pesquisarUsuarios();
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

        // Verifica se há termo de pesquisa
        $termoPesquisa = $_GET['pesquisa'] ?? '';

        // Busca usuários (com ou sem filtro)
        if (!empty($termoPesquisa)) {
            $usuarios = $this->usuarioModel->buscarUsuariosPorTermo($termoPesquisa);
        } else {
            $usuarios = $this->usuarioModel->listarTodosUsuarios();
        }

        // Formata os usuários para exibição
        $usuariosFormatados = [];
        foreach ($usuarios as $usuario) {
            // Formata CPF (XXX.XXX.XXX-XX)
            $cpfFormatado = '';
            if (!empty($usuario['cpf']) && strlen($usuario['cpf']) == 11) {
                $cpfFormatado = substr($usuario['cpf'], 0, 3) . '.' .
                    substr($usuario['cpf'], 3, 3) . '.' .
                    substr($usuario['cpf'], 6, 3) . '-' .
                    substr($usuario['cpf'], 9, 2);
            }

            // Determina tipo de usuário
            $tipo = $usuario['permissao'] === 'ADMIN' ? 'Admin' : 'Cliente';
            $tipoClasse = $usuario['permissao'] === 'ADMIN' ? 'admin-badge-primary' : 'admin-badge-info';

            // Determina status (para clientes usa status_cliente, para admin sempre Ativo)
            $status = 'Ativo';
            $statusClasse = 'admin-badge-success';

            if ($usuario['permissao'] === 'CLIENTE' && !empty($usuario['status_cliente'])) {
                $status = $usuario['status_cliente'];
                // Define classe CSS baseada no status
                switch ($status) {
                    case 'Ativo':
                        $statusClasse = 'admin-badge-success';
                        break;
                    case 'Inativo':
                        $statusClasse = 'admin-badge-danger';
                        break;
                    case 'Suspenso':
                        $statusClasse = 'admin-badge-warning';
                        break;
                    case 'Cancelado':
                        $statusClasse = 'admin-badge-danger';
                        break;
                    case 'Pendente':
                        $statusClasse = 'admin-badge-info';
                        break;
                    default:
                        $statusClasse = 'admin-badge-info';
                }
            }

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

        $titulo_pagina = "Usuários";
        $_GET['acao'] = 'usuarios';

        // Inclui a view passando as variáveis prontas
        require_once __DIR__ . "/../view/admin/usuarios.php";
    }

    /**
     * Processa a pesquisa de usuários (endpoint AJAX)
     * Retorna JSON com os usuários encontrados
     */
    public function pesquisarUsuarios()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        // Verifica se é GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }

        // Pega termo de pesquisa e trata encoding
        $termo = isset($_GET['termo']) ? $_GET['termo'] : '';
        $termo = urldecode($termo);
        $termo = trim($termo);

        // Pega filtros
        $filtroTipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
        $filtroStatus = isset($_GET['status']) ? trim($_GET['status']) : '';

        // Busca usuários com filtros
        $usuarios = $this->usuarioModel->buscarUsuariosPorTermo($termo, $filtroTipo, $filtroStatus);

        // Formata os usuários para exibição (reutiliza lógica do método usuarios)
        $usuariosFormatados = [];
        foreach ($usuarios as $usuario) {
            // Formata CPF (XXX.XXX.XXX-XX)
            $cpfFormatado = '';
            if (!empty($usuario['cpf']) && strlen($usuario['cpf']) == 11) {
                $cpfFormatado = substr($usuario['cpf'], 0, 3) . '.' .
                    substr($usuario['cpf'], 3, 3) . '.' .
                    substr($usuario['cpf'], 6, 3) . '-' .
                    substr($usuario['cpf'], 9, 2);
            }

            // Determina tipo de usuário
            $tipo = $usuario['permissao'] === 'ADMIN' ? 'Admin' : 'Cliente';
            $tipoClasse = $usuario['permissao'] === 'ADMIN' ? 'admin-badge-primary' : 'admin-badge-info';

            // Determina status (para clientes usa status_cliente, para admin sempre Ativo)
            $status = 'Ativo';
            $statusClasse = 'admin-badge-success';

            if ($usuario['permissao'] === 'CLIENTE' && !empty($usuario['status_cliente'])) {
                $status = $usuario['status_cliente'];
                // Define classe CSS baseada no status
                switch ($status) {
                    case 'Ativo':
                        $statusClasse = 'admin-badge-success';
                        break;
                    case 'Suspenso':
                        $statusClasse = 'admin-badge-warning';
                        break;
                    default:
                        $statusClasse = 'admin-badge-info';
                }
            }

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

        // Retorna JSON
        header('Content-Type: application/json');
        echo json_encode(['usuarios' => $usuariosFormatados]);
        exit;
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

    /**
     * Retorna dados completos de um usuário em JSON
     * Usado para preencher o modal de visualização
     */
    public function buscarUsuario()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        // Verifica se foi passado o ID
        $idUsuario = $_GET['id'] ?? null;

        if (!$idUsuario) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'ID do usuário não informado']);
            exit;
        }

        // Busca dados do usuário
        $usuario = $this->usuarioModel->buscarUsuarioCompleto($idUsuario);

        if (!$usuario) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Usuário não encontrado']);
            exit;
        }

        // Formata CPF
        $cpfFormatado = '';
        if (!empty($usuario['cpf']) && strlen($usuario['cpf']) == 11) {
            $cpfFormatado = substr($usuario['cpf'], 0, 3) . '.' .
                substr($usuario['cpf'], 3, 3) . '.' .
                substr($usuario['cpf'], 6, 3) . '-' .
                substr($usuario['cpf'], 9, 2);
        }

        // Formata telefone
        $telefoneFormatado = '';
        if (!empty($usuario['telefone'])) {
            $telefone = $usuario['telefone'];
            if (strlen($telefone) == 11) {
                $telefoneFormatado = '(' . substr($telefone, 0, 2) . ') ' .
                    substr($telefone, 2, 5) . '-' .
                    substr($telefone, 7, 4);
            } else {
                $telefoneFormatado = $telefone;
            }
        }

        // Determina tipo
        $tipo = $usuario['permissao'] === 'ADMIN' ? 'Administrador' : 'Cliente';

        // Determina status
        $status = 'Ativo';
        if ($usuario['permissao'] === 'CLIENTE' && !empty($usuario['status_cliente'])) {
            $status = $usuario['status_cliente'];
        }

        // Formata data de contratação (se for admin)
        $dataContratacaoFormatada = '';
        if (!empty($usuario['data_contratacao'])) {
            $dataObj = new DateTime($usuario['data_contratacao']);
            $dataContratacaoFormatada = $dataObj->format('d/m/Y');
        }

        // Prepara resposta
        $resposta = [
            'id' => $usuario['id_usuario'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'cpf' => $cpfFormatado,
            'telefone' => $telefoneFormatado,
            'tipo' => $tipo,
            'status' => $status,
            'data_contratacao' => $dataContratacaoFormatada
        ];

        header('Content-Type: application/json');
        echo json_encode($resposta);
        exit;
    }

    /**
     * Processa a atualização de um usuário
     * Recebe dados via POST e atualiza no banco
     */
    public function atualizarUsuario()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        // Verifica se é POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }

        // Pega dados do POST
        $idUsuario = $_POST['id'] ?? null;
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? 'CLIENTE';
        $status = $_POST['status'] ?? null;
        $telefone = !empty($_POST['telefone']) ? preg_replace('/[^0-9]/', '', $_POST['telefone']) : null;

        // Validações básicas
        if (!$idUsuario) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'ID do usuário não informado']);
            exit;
        }

        if (empty($nome)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Nome é obrigatório']);
            exit;
        }

        if (empty($email)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Email é obrigatório']);
            exit;
        }

        // Valida tipo
        if (!in_array($tipo, ['ADMIN', 'CLIENTE'])) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Tipo de usuário inválido']);
            exit;
        }

        // Valida status se for cliente
        if ($tipo === 'CLIENTE' && $status && !in_array($status, ['Ativo', 'Suspenso'])) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Status inválido']);
            exit;
        }

        // Verifica se email já existe em outro usuário
        $usuarioAtual = $this->usuarioModel->buscarUsuarioCompleto($idUsuario);
        if ($usuarioAtual && $usuarioAtual['email'] !== $email) {
            if ($this->usuarioModel->emailExiste($email)) {
                header('Content-Type: application/json');
                echo json_encode(['erro' => 'Este email já está cadastrado para outro usuário']);
                exit;
            }
        }

        // Atualiza o usuário
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
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso!']);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Erro ao atualizar usuário. Tente novamente.']);
            exit;
        }
    }

    /**
     * Processa a exclusão de um usuário
     * Recebe ID via POST e exclui do banco
     */
    public function excluirUsuario()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        // Verifica se é POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }

        // Pega ID do POST
        $idUsuario = $_POST['id'] ?? null;

        // Validações
        if (!$idUsuario) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'ID do usuário não informado']);
            exit;
        }

        // Verifica se não está tentando excluir a si mesmo
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioLogadoId = $_SESSION['usuario_id'] ?? null;
        if ($idUsuario == $usuarioLogadoId) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Você não pode excluir sua própria conta']);
            exit;
        }

        // Exclui o usuário (reaproveita método do Model)
        $resultado = $this->usuarioModel->excluirConta($idUsuario);

        if ($resultado) {
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso!']);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Erro ao excluir usuário. Tente novamente.']);
            exit;
        }
    }

    /**
     * Processa o cadastro de um novo usuário
     * Recebe dados via POST e cria no banco
     */
    public function cadastrarUsuario()
    {
        // Protege a rota - só admin pode acessar
        AuthController::protegerAdmin();

        // Verifica se é POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Método não permitido']);
            exit;
        }

        // Pega dados do POST
        $nome = trim($_POST['nome'] ?? '');
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? ''); // Remove caracteres não numéricos
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? 'CLIENTE';
        $status = $_POST['status'] ?? 'Ativo';
        $telefone = !empty($_POST['telefone']) ? preg_replace('/[^0-9]/', '', $_POST['telefone']) : null;

        // Validações básicas
        if (empty($nome)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Nome é obrigatório']);
            exit;
        }

        if (empty($email)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Email é obrigatório']);
            exit;
        }

        if (empty($senha)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Senha é obrigatória']);
            exit;
        }

        // Valida CPF (deve ter 11 dígitos)
        if (strlen($cpf) !== 11) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'CPF deve conter 11 dígitos']);
            exit;
        }

        // Valida senha (mínimo 6 caracteres)
        if (strlen($senha) < 6) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'A senha deve ter no mínimo 6 caracteres']);
            exit;
        }

        // Valida tipo
        if (!in_array($tipo, ['ADMIN', 'CLIENTE'])) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Tipo de usuário inválido']);
            exit;
        }

        // Valida status (apenas para clientes)
        if ($tipo === 'CLIENTE' && !in_array($status, ['Ativo', 'Suspenso'])) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Status inválido']);
            exit;
        }

        // Verifica se email já existe
        if ($this->usuarioModel->emailExiste($email)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Este email já está cadastrado']);
            exit;
        }

        // Verifica se CPF já existe
        if ($this->usuarioModel->cpfExiste($cpf)) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Este CPF já está cadastrado']);
            exit;
        }

        // Cria o usuário
        $idUsuario = $this->usuarioModel->criarUsuario($nome, $cpf, $email, $senha, $tipo, $status, $telefone);

        if ($idUsuario) {
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => 'Usuário cadastrado com sucesso!']);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Erro ao cadastrar usuário. Tente novamente.']);
            exit;
        }
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'AdminController.php') {
    $controller = new AdminController();
    $controller->index();
}

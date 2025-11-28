<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/../model/UsuarioModel.php";

class ClienteController
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
        // Pega a ação da URL (ex: ?acao=tela_inicial)
        $acao = $_GET['acao'] ?? 'tela_inicial';

        // Chama o método correspondente
        switch ($acao) {
            case 'tela_inicial':
                $this->telaInicial();
                break;
            case 'conta':
                $this->conta();
                break;
            case 'produtos':
            case 'PaginaProdutos':
                $this->paginaProdutos();
                break;
            case 'carrinho':
                $this->carrinho();
                break;
            case 'checkout':
                $this->checkout();
                break;
            case 'detalhes_produtos':
                $this->detalhesProdutos();
                break;
            case 'favoritos':
            case 'Favoritos':
                $this->favoritos();
                break;
            case 'quiz':
                $this->quiz();
                break;
            case 'politicas':
            case 'PoliticasDaLoja':
                $this->politicasDaLoja();
                break;
            default:
                $this->telaInicial();
                break;
        }
    }

    /**
     * Exibe a tela inicial do cliente
     */
    private function telaInicial()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view (sem variáveis por enquanto, pois a view não precisa de lógica)
        require_once __DIR__ . "/../view/cliente/tela_inicial.php";
    }

    /**
     * Exibe a página de conta do cliente
     * Processa todas as ações: exclusão, atualização de dados e endereço
     */
    private function conta()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inicia sessão
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        // Processa exclusão de conta se solicitado
        if (isset($_GET['excluir']) && $_GET['excluir'] === 'confirmar') {
            $authController = new AuthController();
            $authController->excluirConta();
            return; // O método excluirConta já faz o redirecionamento
        }

        // Inicializa variáveis de mensagem
        $mensagem = '';
        $tipoMensagem = '';

        // Processa atualização de dados pessoais
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_dados_pessoais'])) {
            $nome = trim($_POST['nome_completo'] ?? '');
            $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');

            if (!empty($nome)) {
                if ($this->usuarioModel->atualizarDadosPessoais($usuarioId, $nome, $telefone)) {
                    $_SESSION['usuario_nome'] = $nome; // Atualiza sessão
                    $mensagem = "Dados pessoais atualizados com sucesso!";
                    $tipoMensagem = 'sucesso';
                } else {
                    $mensagem = "Erro ao atualizar dados pessoais. Tente novamente.";
                    $tipoMensagem = 'erro';
                }
            } else {
                $mensagem = "Nome é obrigatório.";
                $tipoMensagem = 'erro';
            }
        }

        // Processa atualização de endereço
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_endereco'])) {
            $rua = trim($_POST['rua'] ?? '');
            $numero = trim($_POST['numero'] ?? '');
            $bairro = trim($_POST['bairro'] ?? '');
            $cep = trim($_POST['cep'] ?? '');
            $estado = trim($_POST['estado'] ?? '');
            $complemento = trim($_POST['complemento'] ?? '');

            // Remove formatação do CEP para validação
            $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

            if (empty($rua)) {
                $mensagem = "O campo Rua é obrigatório.";
                $tipoMensagem = 'erro';
            } elseif (empty($cepLimpo) || strlen($cepLimpo) !== 8) {
                $mensagem = "CEP inválido. Deve conter 8 dígitos.";
                $tipoMensagem = 'erro';
            } elseif (!empty($estado) && strlen($estado) > 2) {
                $mensagem = "Estado deve conter no máximo 2 caracteres (UF).";
                $tipoMensagem = 'erro';
            } else {
                if ($this->usuarioModel->salvarEndereco($usuarioId, $rua, $numero, $bairro, $cep, $estado, $complemento)) {
                    $mensagem = "Endereço atualizado com sucesso!";
                    $tipoMensagem = 'sucesso';
                } else {
                    // Mostra mensagem de erro mais detalhada para debug
                    $erroDetalhado = $_SESSION['erro_sql'] ?? '';
                    unset($_SESSION['erro_sql']);

                    if (!empty($erroDetalhado)) {
                        $mensagem = "Erro ao atualizar endereço: " . htmlspecialchars($erroDetalhado);
                    } else {
                        $mensagem = "Erro ao atualizar endereço. Verifique os dados e tente novamente.";
                    }
                    $tipoMensagem = 'erro';
                }
            }
        }

        // Busca dados completos do cliente
        $dadosCliente = $this->usuarioModel->buscarDadosCliente($usuarioId);

        // Se não encontrou os dados, usa os dados da sessão
        $nome = $dadosCliente['nome'] ?? $_SESSION['usuario_nome'] ?? '';
        $email = $dadosCliente['email'] ?? $_SESSION['usuario_email'] ?? '';
        $cpf = $dadosCliente['cpf'] ?? '';
        $telefone = $dadosCliente['telefone'] ?? '';

        // Busca endereço do cliente
        $endereco = $this->usuarioModel->buscarEndereco($usuarioId);
        $rua = $endereco['rua'] ?? '';
        $numero = $endereco['numero'] ?? '';
        $bairro = $endereco['bairro'] ?? '';
        $cep = $endereco['cep'] ?? '';
        $estado = $endereco['estado'] ?? '';
        $complemento = $endereco['complemento'] ?? '';

        // Formata CEP para exibição (XXXXX-XXX)
        $cepFormatado = $cep;
        if (!empty($cep) && strlen($cep) == 8) {
            $cepFormatado = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        }

        // Formata CPF para exibição (XXX.XXX.XXX-XX)
        $cpfFormatado = '';
        if (!empty($cpf) && strlen($cpf) == 11) {
            $cpfFormatado = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }

        // Formata telefone para exibição
        $telefoneFormatado = '';
        if (!empty($telefone)) {
            if (strlen($telefone) == 11) {
                $telefoneFormatado = '+55 (' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
            } else {
                $telefoneFormatado = $telefone;
            }
        }

        // Inclui a view passando todas as variáveis prontas
        require_once __DIR__ . "/../view/cliente/conta.php";
    }

    /**
     * Exibe a página de produtos (PaginaProdutos)
     */
    private function paginaProdutos()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/PaginaProdutos.php";
    }

    /**
     * Exibe detalhes do produto
     */
    private function detalhesProdutos()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/detalhes_produtos.php";
    }

    /**
     * Exibe favoritos
     */
    private function favoritos()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/Favoritos.php";
    }

    /**
     * Exibe quiz
     */
    private function quiz()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/quiz.php";
    }

    /**
     * Exibe políticas da loja
     */
    private function politicasDaLoja()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/PoliticasDaLoja.php";
    }

    /**
     * Exibe o carrinho
     */
    private function carrinho()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/carrinho.php";
    }

    /**
     * Exibe o checkout
     */
    private function checkout()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inclui a view
        require_once __DIR__ . "/../view/cliente/checkout.php";
    }
}

// Se o arquivo foi chamado diretamente, executa o controller
if (basename($_SERVER['PHP_SELF']) === 'ClienteController.php') {
    $controller = new ClienteController();
    $controller->index();
}

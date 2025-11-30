<?php

/* CHAMA OS ARQUIVOS DE CONFIG O CONTROLADOR DE AUTENTICAÇÃO E O MODEL DE USUÁRIO */
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/../model/UsuarioModel.php";

class ClienteController
{
    /* Ao instanciar o objeto já instancia junto o model de usuário */
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }
    /* Roteador que leva para cada página de cliente */
    public function index()
    {
        /* Passa para a variável acao o valor da ação da url */
        $acao = $_GET['acao'] ?? 'tela_inicial';

        /* chama o método correspondente a  ação */
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

    /* ---EXIBE A PÁGINA DE TELA INICIAL--- */
    private function telaInicial()
    {
        /*  Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /*  Inclui a view (sem variáveis por enquanto, pois a view não precisa de lógica) */
        require_once __DIR__ . "/../view/cliente/tela_inicial.php";
    }

    /* Exibe na tela a tela de conta de usuário e faz o processamento das funções relacionadas a conta */
    private function conta()
    {
        /*  Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inicia a sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuarioId = $_SESSION['usuario_id'] ?? null;/* Pega o valor do id de usuário está na sessão depois de fazer o login */

        /* se o id usuário não existir leva para a página de login */
        if (!$usuarioId) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        /* se o usuário confirmar que quer excluir excliur vai ser igual confirmar */
        if (isset($_GET['excluir']) && $_GET['excluir'] === 'confirmar') {
            $authController = new AuthController();/* instancia o objeto controler para chamar a função de excluir conta */
            $authController->excluirConta();
            return; /* O método excluirConta já faz o redirecionamento */
        }

        /*  Inicializa variáveis de mensagem */
        $mensagem = '';
        $tipoMensagem = '';

        /* recebe o envio das informações do formuário de atualizar dados pessoais e processa a atualização */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_dados_pessoais'])) {
            $nome = trim($_POST['nome_completo'] ?? '');
            $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');

            /* se o nome foi mudado, tenta atualizar */
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

        /* se receber o formulário de editar endereço, faz o processamento da ação */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_endereco'])) {
            /* trata os dados removendo espaços em branco */
            $rua = trim($_POST['rua'] ?? '');
            $numero = trim($_POST['numero'] ?? '');
            $bairro = trim($_POST['bairro'] ?? '');
            $cep = trim($_POST['cep'] ?? '');
            $estado = trim($_POST['estado'] ?? '');
            $complemento = trim($_POST['complemento'] ?? '');

            /* Remove formatação do CEP para validação */
            $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

            if (empty($rua)) {
                $mensagem = "O campo Rua é obrigatório.";
                $tipoMensagem = 'erro';
                /* vê se o cep tem 8 digitos e se foi declarado */
            } elseif (empty($cepLimpo) || strlen($cepLimpo) !== 8) {
                $mensagem = "CEP inválido. Deve conter 8 dígitos.";
                $tipoMensagem = 'erro';
            } elseif (!empty($estado) && strlen($estado) > 2) {
                $mensagem = "Estado deve conter no máximo 2 caracteres (UF).";
                $tipoMensagem = 'erro';
            } else {
                /* se passar por essas validações, tenta salvar o endereço */
                if ($this->usuarioModel->salvarEndereco($usuarioId, $rua, $numero, $bairro, $cep, $estado, $complemento)) {
                    $mensagem = "Endereço atualizado com sucesso!";
                    $tipoMensagem = 'sucesso';
                } else {
                    /* Mostra mensagem de erro detalhada para debug */
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

        /*  Busca dados completos do cliente */
        $dadosCliente = $this->usuarioModel->buscarDadosCliente($usuarioId);

        /* Se não encontrou os dados, usa os dados da sessão */
        $nome = $dadosCliente['nome'] ?? $_SESSION['usuario_nome'] ?? '';
        $email = $dadosCliente['email'] ?? $_SESSION['usuario_email'] ?? '';
        $cpf = $dadosCliente['cpf'] ?? '';
        $telefone = $dadosCliente['telefone'] ?? '';

        /*   Busca endereço do cliente e se não encontrar passa nulo */
        $endereco = $this->usuarioModel->buscarEndereco($usuarioId);

        $rua = $endereco['rua'] ?? '';
        $numero = $endereco['numero'] ?? '';
        $bairro = $endereco['bairro'] ?? '';
        $cep = $endereco['cep'] ?? '';
        $estado = $endereco['estado'] ?? '';
        $complemento = $endereco['complemento'] ?? '';

        /*   Formata CEP para exibição (XXXXX-XXX) */
        $cepFormatado = $cep;
        if (!empty($cep) && strlen($cep) == 8) {
            $cepFormatado = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        }

        /* Formata CPF para exibição (XXX.XXX.XXX-XX) */
        $cpfFormatado = '';
        if (!empty($cpf) && strlen($cpf) == 11) {
            $cpfFormatado = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }
        /*   Formata telefone para exibição */
        $telefoneFormatado = '';
        if (!empty($telefone)) {
            if (strlen($telefone) == 11) {
                $telefoneFormatado = '+55 (' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
            } else {
                $telefoneFormatado = $telefone;
            }
        }

        /* Inclui a view passando todas as variáveis prontas */
        require_once __DIR__ . "/../view/cliente/conta.php";
    }

    /* ---EXIBE A PÁGINA DE PRODUTOS--- */
    private function paginaProdutos()
    {
        /* Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/PaginaProdutos.php";
    }

    /* ---EXIBE A PÁGINA DE DETALHES--- */
    private function detalhesProdutos()
    {
        /* Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/detalhes_produtos.php";
    }

    /* ---EXIBE A PÁGINA DE FAVORITOS--- */
    private function favoritos()
    {
        /* Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/Favoritos.php";
    }

    /* ---EXIBE A PÁGINA DE QUIZ--- */
    private function quiz()
    {
        /*   Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/quiz.php";
    }

    /* ---EXIBE A PÁGINA DE POLITICAS DA LOJA--- */
    private function politicasDaLoja()
    {
        /*   Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view  */
        require_once __DIR__ . "/../view/cliente/PoliticasDaLoja.php";
    }

    /* ---EXIBE A PÁGINA DE CARRINHO--- */
    private function carrinho()
    {
        /* Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/carrinho.php";
    }

    /* ---EXIBE A PÁGINA DE CHECKOUT--- */
    private function checkout()
    {
        /*  Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/checkout.php";
    }
}

/* Se o arquivo foi chamado diretamente, executa o controller */
if (basename($_SERVER['PHP_SELF']) === 'ClienteController.php') {
    $controller = new ClienteController();
    $controller->index();
}

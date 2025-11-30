<?php
/* ARQUIVO QUE CONTROLA O SISTEMA DE AUTENTICAÇÃO */

/* CHAMAMENTO DOS ARQUIVOS DE CONFIG E MODEL DE USUÁRIO */
require_once __DIR__ . "/../model/UsuarioModel.php";
require_once __DIR__ . "/../config/config.php";


/* CRIÇÃO DA CLASSE DE AUTHCONTROLER */
class AuthController
{

    /* FUNÇÃO CONSTRUCT INSTÂNCIA O OBJETO DE  MODEL USUÁRIOS*/
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /* FUNÇÃO QUE VERIFICA SE A SEÇÃO DO PHP ESTÁ INICIADA E SE NÃO TIVER ELA INICIA */
    private function iniciarSessao()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* FUNÇÃO DE VERIFICAR O LOGIN */
    public function login()
    {
        $this->iniciarSessao();

        /* PEGA O EMAIL E A SENHA DO FORMULÁRIO DE LOGIN POR MEIO DOS METODO DE ENVIO POST */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            /* SE A SENHA OU O EMAIL FOR VAZIO ADICIONA A MENSAGEM DE ERRO A VARIÁVEL DE ERRO */
            if (empty($email) || empty($senha)) {
                $_SESSION['erro'] = "Por favor, preencha todos os campos.";
                header("Location: " . BASE_URL . "/app/control/LoginController.php");
                exit;
            }

            $usuario = $this->usuarioModel->verificarLogin($email, $senha);/* CHAMA A FUNÇÃO DE VERIFICAR LOGIN E PASSA O EMAIL E A SENHA ADQUIRIDOS NO FORMULÁRIO. SE O LOGIN FOR VÁLIDO, ADICIONA OS DADOS DE USUÁRIO A VÁRIÁVEL DE USUÁRIO */

            if ($usuario) {
                /* SE O LOGIN FOI VALIDADO SALVA NA SESSÃO OS DADOS NECESSÁRIO PARA MENTER O LOGIN */
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_permissao'] = $usuario['permissao'];

                /* Redireciona conforme a permissão */
                if ($usuario['permissao'] === 'ADMIN') {
                    header("Location: " . BASE_URL . "/app/control/DashboardController.php");/* SE FOR ADM VAI PARA A DASHBOARD */
                } else {
                    header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=tela_inicial");/* SE FOR CLIENTE VAI PARA A TELA INICIAL DO SITE */
                }
                exit;
            } else {
                /* SE O LOGIN NÃO FOI VÁLIDO VERIFICA SE O EMAIL EXISTE E SE A CONTA ESTÁ SUSPENSA*/
                $usuarioPorEmail = $this->usuarioModel->buscarPorEmail($email);

                if ($usuarioPorEmail && $usuarioPorEmail['permissao'] === 'CLIENTE') {
                    $clienteAtivo = $this->usuarioModel->verificarClienteAtivo($usuarioPorEmail['id_usuario']);
                    /* SE O USUÁRIO FOR CLIENTE E ESTÁ SUSPENSO ARMAZENA A MENSAGEM DE ERRO, NÃO ARMAZENA ERRO DE SENHA OU EMAIL INCORRETOS */
                    if (!$clienteAtivo) {
                        $_SESSION['erro'] = "Sua conta está suspensa. Entre em contato com o suporte: morenatropicana.official@gmail.com .";
                    } else {
                        $_SESSION['erro'] = "Email ou senha incorretos.";
                    }
                } else {
                    $_SESSION['erro'] = "Email ou senha incorretos.";
                }

                header("Location: " . BASE_URL . "/app/control/LoginController.php");/* SE O LOGIN NÃO ESTIVER CORRETO VOLTA PARA A TELA DE LOGIN */
                exit;
            }
        }
    }

    /* PROCESSA O CADASTRO */
    public function cadastro()
    {
        $this->iniciarSessao();

        /* PEGA E LIMPA OS DADOS DE CADASTRO DO FORMULÁRIO */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? ''); // Remove caracteres não numéricos
            $email = trim($_POST['email'] ?? '');
            $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
            $senha = $_POST['senha'] ?? '';

            /* VERIFICA SE ESTÁ FALTANDO CAMPOS A SEREM PREENCHIDOS */
            if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
                $_SESSION['erro'] = "Por favor, preencha todos os campos obrigatórios.";
                header("Location: " . BASE_URL . "/app/control/CadastroController.php");
                exit;
            }

            /* VALIDA A QUANTIDADE DE DIGITOS DO CPF */
            if (strlen($cpf) !== 11) {
                $_SESSION['erro'] = "CPF deve conter 11 dígitos.";
                header("Location: " . BASE_URL . "/app/control/CadastroController.php");
                exit;
            }

            /* VALIDA A QUANTIDADE DE DIGITOS DA SENHA */
            if (strlen($senha) < 6) {
                $_SESSION['erro'] = "A senha deve ter no mínimo 6 caracteres.";
                header("Location: " . BASE_URL . "/app/control/CadastroController.php");
                exit;
            }

            /* VERIFICA SE O EMAIL JÁ EXISTE E EXISTIR DECLARA MENSAGEM DE ERRO */
            if ($this->usuarioModel->emailExiste($email)) {
                $_SESSION['erro'] = "Este email já está cadastrado.";
                header("Location: " . BASE_URL . "/app/control/CadastroController.php");
                exit;
            }

            /* VERIFICA SE O CPF JÁ EXISTE E SE SIM DECLARA MENSAGEM DE ERRO */
            if ($this->usuarioModel->cpfExiste($cpf)) {
                $_SESSION['erro'] = "Este CPF já está cadastrado.";
                header("Location: " . BASE_URL . "/app/control/CadastroController.php");
                exit;
            }

            /* CHAMA A FUNÇÃO DE CRIAR CLIENTE E SE ELA DER CERTO RETORNA O ID DE USUÁRIO QUE É ARMAZENADO */
            $idUsuario = $this->usuarioModel->criarCliente($nome, $cpf, $email, $senha, $telefone);

            /* SE O CADASTRO DEU CERTO MANDA PARA LOGIN SE NÃO MANDA TENTAAR NOVAMENTE */
            if ($idUsuario) {
                $_SESSION['sucesso'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                header("Location: " . BASE_URL . "/app/control/LoginController.php");
                exit;
            } else {
                $_SESSION['erro'] = "Erro ao realizar cadastro. Tente novamente.";
                header("Location: " . BASE_URL . "/app/control/CadastroController.php");
                exit;
            }
        }
    }

    /* FUNÇÃO DE FAZER LOGOUT */
    public function logout()
    {
        $this->iniciarSessao();

        /* DESTROI A SESSÃO QUE É REPONSÁVEL POR MANTER O USER LOGADO */
        session_unset();
        session_destroy();

        /* SEMPRE QUE FAZE LOGOUT RETORNA PARA TELA DE LOGIN */
        header("Location: " . BASE_URL . "/app/control/LoginController.php");
        exit;
    }

    /* FUNÇÃO QUE VERIFICA SE O USUÁRIO ESTÁ LOGADO */
    public static function verificarLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario_id']);/* SE ESTIVER LOGADO ESSA SESSÃO ESTÁ SETADA */
    }

    /* VERIFICA SE A PERMISSÃO É ADMIM */
    public static function verificarAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] === 'ADMIN';
    }

    /* VERIFICA SE A PERMISSÃO É CLIENTE */
    public static function verificarCliente()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] === 'CLIENTE';
    }

    /* PROTEGE AS ROTAS DE ADMIM, SÓ ADMIM CONSEGUE ACESSAR */
    public static function protegerAdmin()
    {
        /* chama a função de verificar login por meio do self, que permite fazer acessos de funções estáticas dentro da mesma classe */
        if (!self::verificarLogin()) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }
        /* se não for adm manda pra tela de usuário */
        if (!self::verificarAdmin()) {
            header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=tela_inicial");
            exit;
        }
    }

    /* FAZ  A MESMA COISA DO ADM MAS DESSA FEZ PROTEGE PARA O ADM NÃO ACESSAR */
    public static function protegerCliente()
    {
        if (!self::verificarLogin()) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        if (!self::verificarCliente()) {
            header("Location: " . BASE_URL . "/app/control/DashboardController.php");
            exit;
        }
    }

    /* PROCESSA A EXCLUSÃO DE CONTA PELO CLIENTE */
    public function excluirConta()
    {
        $this->iniciarSessao();

        /* Verifica se o usuário está logado e é cliente */
        if (!self::verificarLogin() || !self::verificarCliente()) {
            $_SESSION['erro'] = "Acesso negado.";
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        /* PEGA O ID DO USUÁRIO LOGADO PARA EXCLUIR A CONTA CERTA */
        $idUsuario = $_SESSION['usuario_id'] ?? null;

        if (!$idUsuario) {
            $_SESSION['erro'] = "Erro ao identificar usuário.";
            header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=conta");
            exit;
        }

        /* CHAMA A FUNÇÃO DE EXCLUIR CONTA E PASSA O ID DO USUÁRIO ATUAL */
        $resultado = $this->usuarioModel->excluirConta($idUsuario);

        /* SE O USUÁRIO FOR EXCLUIDO MANDA PARA A TELA INICIAL SE NÃO APARECE MSG DE ERRO*/
        if ($resultado) {
            $this->logout();
        } else {
            $_SESSION['erro'] = "Erro ao excluir conta. Tente novamente.";
            header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=conta");
            exit;
        }
    }
}

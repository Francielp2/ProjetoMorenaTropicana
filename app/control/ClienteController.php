<?php

/* CHAMA OS ARQUIVOS DE CONFIG O CONTROLADOR DE AUTENTICAÇÃO E OS MODELS NECESSÁRIOS */
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/../model/UsuarioModel.php";
require_once __DIR__ . "/../model/PedidoModel.php";
require_once __DIR__ . "/../model/ProdutoModel.php";
require_once __DIR__ . "/../model/CarrinhoModel.php";
require_once __DIR__ . "/../model/EstoqueModel.php";
require_once __DIR__ . "/../model/FavoritoModel.php";

class ClienteController
{
    /* Ao instanciar o objeto já instancia junto os models necessários */
    private $usuarioModel;
    private $pedidoModel;
    private $produtoModel;
    private $carrinhoModel;
    private $estoqueModel;
    private $favoritoModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new ProdutoModel();
        $this->carrinhoModel = new CarrinhoModel();
        $this->estoqueModel = new EstoqueModel();
        $this->favoritoModel = new FavoritoModel();
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
            case 'pedidos':
                $this->pedidos();
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

        /* Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idCliente = $_SESSION['usuario_id'] ?? null;

        /* Processa adicionar/remover favorito (se vier da seção de produtos) */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_favorito'])) {
            if ($idCliente) {
                $idProduto = isset($_POST['id_produto']) ? (int)$_POST['id_produto'] : 0;
                $acao = $_POST['acao_favorito'];

                if ($idProduto > 0) {
                    if ($acao === 'adicionar') {
                        $this->favoritoModel->adicionarFavorito($idCliente, $idProduto);
                    } elseif ($acao === 'remover') {
                        $this->favoritoModel->removerFavorito($idCliente, $idProduto);
                    }
                }
            }
            /* Redireciona para evitar reenvio do formulário */
            header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=tela_inicial");
            exit;
        }

        /* Busca os últimos 4 produtos adicionados ao carrinho */
        $produtosDestaque = $this->carrinhoModel->listarUltimosProdutosAdicionados(4);

        /* Se não houver produtos no carrinho, busca os 4 últimos produtos cadastrados */
        if (empty($produtosDestaque)) {
            $todosProdutos = $this->produtoModel->listarTodosProdutos();
            $produtosDestaque = array_slice($todosProdutos, -4, 4);
        }

        /* Busca IDs de produtos favoritos do cliente */
        $idsFavoritos = [];
        if ($idCliente) {
            $idsFavoritos = $this->favoritoModel->listarIdsFavoritos($idCliente);
        }

        /* Inclui a view passando os produtos e favoritos */
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

        /* Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idCliente = $_SESSION['usuario_id'] ?? null;

        /* Processa adicionar/remover favorito */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_favorito'])) {
            if ($idCliente) {
                $idProduto = isset($_POST['id_produto']) ? (int)$_POST['id_produto'] : 0;
                $acao = $_POST['acao_favorito'];

                if ($idProduto > 0) {
                    if ($acao === 'adicionar') {
                        $this->favoritoModel->adicionarFavorito($idCliente, $idProduto);
                    } elseif ($acao === 'remover') {
                        $this->favoritoModel->removerFavorito($idCliente, $idProduto);
                    }
                }
            }
            /* Redireciona para evitar reenvio do formulário */
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        /* Verifica se há busca na barra de pesquisa */
        $termoBusca = $_GET['busca'] ?? $_GET['termo'] ?? '';
        $termoBusca = trim($termoBusca);

        /* Verifica se há filtros do quiz */
        $categoria = $_GET['categoria'] ?? '';
        $tamanho = $_GET['tamanho'] ?? '';
        $cor = $_GET['cor'] ?? '';
        $isQuiz = isset($_GET['quiz']) && $_GET['quiz'] == '1';

        /* Se houver busca, busca produtos por termo */
        if (!empty($termoBusca)) {
            $produtos = $this->produtoModel->buscarProdutosPorTermo($termoBusca);
        } elseif (!empty($categoria) || !empty($tamanho) || !empty($cor)) {
            /* Se houver filtros do quiz, busca produtos filtrados */
            $produtos = $this->produtoModel->buscarProdutosComFiltros($categoria, $tamanho, $cor);
        } else {
            /* Caso contrário, lista todos os produtos */
            $produtos = $this->produtoModel->listarTodosProdutos();
        }

        /* Busca IDs de produtos favoritos do cliente */
        $idsFavoritos = [];
        if ($idCliente) {
            $idsFavoritos = $this->favoritoModel->listarIdsFavoritos($idCliente);
        }

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/PaginaProdutos.php";
    }

    /* ---EXIBE A PÁGINA DE DETALHES--- */
    private function detalhesProdutos()
    {
        /* Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* Inicializa variáveis de mensagem */
        $mensagem = '';
        $tipoMensagem = '';

        /* Pega o ID do produto da URL */
        $idProduto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$idProduto) {
            $mensagem = "Produto não encontrado.";
            $tipoMensagem = 'erro';
            $produto = null;
            $coresDisponiveis = [];
            $tamanhosDisponiveis = [];
        } else {
            /* Busca dados do produto */
            $produto = $this->produtoModel->buscarProdutoPorId($idProduto);

            if (!$produto) {
                $mensagem = "Produto não encontrado.";
                $tipoMensagem = 'erro';
                $coresDisponiveis = [];
                $tamanhosDisponiveis = [];
            } else {
                /* Busca cores e tamanhos disponíveis do estoque */
                $coresETamanhos = $this->estoqueModel->buscarCoresETamanhosPorProduto($idProduto);
                $coresDisponiveis = $coresETamanhos['cores'];
                $tamanhosDisponiveis = $coresETamanhos['tamanhos'];
            }
        }

        /* Verifica se usuário está logado */
        $idCliente = $_SESSION['usuario_id'] ?? null;

        /* Processa adicionar/remover favorito */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_favorito'])) {
            if ($idCliente && $idProduto > 0) {
                $acao = $_POST['acao_favorito'];
                if ($acao === 'adicionar') {
                    $this->favoritoModel->adicionarFavorito($idCliente, $idProduto);
                } elseif ($acao === 'remover') {
                    $this->favoritoModel->removerFavorito($idCliente, $idProduto);
                }
            }
            /* Redireciona para evitar reenvio do formulário */
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        /* Verifica se produto está nos favoritos */
        $ehFavorito = false;
        if ($idCliente && $idProduto > 0) {
            $ehFavorito = $this->favoritoModel->verificarFavorito($idCliente, $idProduto);
        }

        /* Processa adicionar ao carrinho (POST) */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho'])) {
            if (!$idCliente) {
                header("Location: " . BASE_URL . "/app/control/LoginController.php");
                exit;
            }

            if (!$produto) {
                $mensagem = "Produto não encontrado.";
                $tipoMensagem = 'erro';
            } else {
                $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
                $cor = !empty($_POST['cor']) ? trim($_POST['cor']) : null;
                $tamanho = !empty($_POST['tamanho']) ? trim($_POST['tamanho']) : null;
                $precoUnitario = (float)($produto['preco'] ?? 0);

                if ($quantidade <= 0) {
                    $mensagem = "Quantidade deve ser maior que zero.";
                    $tipoMensagem = 'erro';
                } elseif ($precoUnitario <= 0) {
                    $mensagem = "Erro ao obter preço do produto.";
                    $tipoMensagem = 'erro';
                } else {
                    $resultado = $this->carrinhoModel->adicionarAoCarrinho(
                        $idCliente,
                        $idProduto,
                        $quantidade,
                        $cor,
                        $tamanho,
                        $precoUnitario,
                        $this->estoqueModel
                    );

                    if (is_array($resultado) && isset($resultado['erro'])) {
                        $mensagem = $resultado['mensagem'];
                        $tipoMensagem = 'erro';
                    } elseif ($resultado) {
                        $mensagem = "Produto adicionado ao carrinho com sucesso!";
                        $tipoMensagem = 'sucesso';
                    } else {
                        $mensagem = "Erro ao adicionar produto ao carrinho. Tente novamente.";
                        $tipoMensagem = 'erro';
                    }
                }
            }
        }

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/detalhes_produtos.php";
    }

    /* ---EXIBE A PÁGINA DE FAVORITOS--- */
    private function favoritos()
    {
        /* Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* Verifica se usuário está logado */
        $idCliente = $_SESSION['usuario_id'] ?? null;
        if (!$idCliente) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        /* Inicializa variáveis de mensagem */
        $mensagem = '';
        $tipoMensagem = '';

        /* Processa remoção de favorito (POST) */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_favorito'])) {
            $idFavorito = isset($_POST['id_favorito']) ? (int)$_POST['id_favorito'] : 0;

            if ($idFavorito > 0) {
                $resultado = $this->favoritoModel->removerFavoritoPorId($idFavorito, $idCliente);
                if ($resultado) {
                    $mensagem = "Produto removido dos favoritos com sucesso!";
                    $tipoMensagem = 'sucesso';
                } else {
                    $mensagem = "Erro ao remover produto dos favoritos. Tente novamente.";
                    $tipoMensagem = 'erro';
                }
            }
        }

        /* Busca favoritos do cliente */
        $favoritos = $this->favoritoModel->listarFavoritos($idCliente);

        /* Formata favoritos para a view */
        $favoritosFormatados = [];
        foreach ($favoritos as $favorito) {
            $idProduto = (int)$favorito['id_produto'];
            $nomeProduto = htmlspecialchars($favorito['nome_produto'] ?? 'Produto sem nome');
            $precoProduto = isset($favorito['preco']) ? number_format((float)$favorito['preco'], 2, ',', '.') : '0,00';
            $imagemProduto = !empty($favorito['imagens']) ? BASE_URL . $favorito['imagens'] : '';
            $estoqueTotal = (int)($favorito['estoque_total'] ?? 0);
            
            /* Formata status de estoque igual ao admin */
            $statusEstoque = $this->formatarStatusEstoque($estoqueTotal);

            $favoritosFormatados[] = [
                'id_favorito' => (int)$favorito['id_favorito'],
                'id_produto' => $idProduto,
                'nome_produto' => $nomeProduto,
                'preco' => $precoProduto,
                'preco_numerico' => (float)$favorito['preco'],
                'imagem' => $imagemProduto,
                'categoria' => htmlspecialchars($favorito['categoria'] ?? ''),
                'estoque_total' => $estoqueTotal,
                'status_estoque' => $statusEstoque['texto'],
                'status_classe' => $statusEstoque['classe']
            ];
        }

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/Favoritos.php";
    }

    /* ---EXIBE A PÁGINA DE QUIZ--- */
    private function quiz()
    {
        /*   Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inicia sessão para armazenar respostas */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* Determina a etapa atual do quiz */
        $etapa = isset($_GET['etapa']) ? (int)$_GET['etapa'] : 1;

        /* Se voltar para etapa anterior, limpa seleções das etapas seguintes */
        if (isset($_GET['etapa'])) {
            if ($etapa == 1) {
                unset($_SESSION['quiz_categoria']);
                unset($_SESSION['quiz_tamanho']);
                unset($_SESSION['quiz_cor']);
            } elseif ($etapa == 2) {
                unset($_SESSION['quiz_tamanho']);
                unset($_SESSION['quiz_cor']);
            } elseif ($etapa == 3) {
                unset($_SESSION['quiz_cor']);
            }
        }

        /* Processa respostas do formulário */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['categoria']) && is_array($_POST['categoria']) && !empty($_POST['categoria'])) {
                $categoriasSelecionadas = array_filter(array_map('trim', $_POST['categoria']));
                if (!empty($categoriasSelecionadas)) {
                    /* Pega a primeira categoria para buscar tamanhos (simplificado) */
                    $primeiraCategoria = reset($categoriasSelecionadas);
                    $tamanhosDisponiveis = $this->produtoModel->listarTamanhosPorCategoria($primeiraCategoria);
                    if (!empty($tamanhosDisponiveis)) {
                        $_SESSION['quiz_categoria'] = implode(',', $categoriasSelecionadas);
                        $etapa = 2;
                    } else {
                        $etapa = 1;
                        $_SESSION['quiz_erro'] = 'A(s) categoria(s) selecionada(s) não possui(em) produtos disponíveis. Por favor, escolha outra(s) categoria(s).';
                    }
                }
            } elseif (isset($_POST['tamanho']) && is_array($_POST['tamanho']) && !empty($_POST['tamanho'])) {
                $tamanhosSelecionados = array_filter(array_map('trim', $_POST['tamanho']));
                if (!empty($tamanhosSelecionados)) {
                    $categoriaAtual = $_SESSION['quiz_categoria'] ?? '';
                    $primeiraCategoria = !empty($categoriaAtual) ? explode(',', $categoriaAtual)[0] : '';
                    $primeiroTamanho = reset($tamanhosSelecionados);
                    $coresDisponiveis = $this->produtoModel->listarCoresPorCategoriaETamanho($primeiraCategoria, $primeiroTamanho);
                    if (!empty($coresDisponiveis)) {
                        $_SESSION['quiz_tamanho'] = implode(',', $tamanhosSelecionados);
                        $etapa = 3;
                    } else {
                        $etapa = 2;
                        $_SESSION['quiz_erro'] = 'O(s) tamanho(s) selecionado(s) não possui(em) cores disponíveis. Por favor, escolha outro(s) tamanho(s).';
                    }
                }
            } elseif (isset($_POST['cor']) && is_array($_POST['cor']) && !empty($_POST['cor'])) {
                $coresSelecionadas = array_filter(array_map('trim', $_POST['cor']));
                if (!empty($coresSelecionadas)) {
                    $_SESSION['quiz_cor'] = implode(',', $coresSelecionadas);
                    
                    /* Redireciona para produtos com os filtros aplicados */
                    $url = BASE_URL . "/app/control/ClienteController.php?acao=produtos";
                    $filtros = [];
                    
                    if (!empty($_SESSION['quiz_categoria'])) {
                        $filtros[] = "categoria=" . urlencode($_SESSION['quiz_categoria']);
                    }
                    if (!empty($_SESSION['quiz_tamanho'])) {
                        $filtros[] = "tamanho=" . urlencode($_SESSION['quiz_tamanho']);
                    }
                    if (!empty($_SESSION['quiz_cor'])) {
                        $filtros[] = "cor=" . urlencode($_SESSION['quiz_cor']);
                    }
                    
                    if (!empty($filtros)) {
                        $url .= "&" . implode("&", $filtros);
                        $url .= "&quiz=1";
                    }
                    
                    /* Limpa sessão do quiz */
                    unset($_SESSION['quiz_categoria']);
                    unset($_SESSION['quiz_tamanho']);
                    unset($_SESSION['quiz_cor']);
                    
                    header("Location: " . $url);
                    exit;
                }
            }
        }

        /* Busca dados para a etapa atual */
        $categorias = [];
        $tamanhos = [];
        $cores = [];
        $categoriaSelecionada = $_SESSION['quiz_categoria'] ?? '';
        $tamanhoSelecionado = $_SESSION['quiz_tamanho'] ?? '';
        $mensagemErro = $_SESSION['quiz_erro'] ?? '';
        unset($_SESSION['quiz_erro']);

        if ($etapa == 1) {
            /* Etapa 1: Mostra categorias com produtos em estoque */
            $categorias = $this->produtoModel->listarCategorias();
            if (empty($categorias)) {
                $mensagemErro = 'Não há categorias disponíveis no momento.';
            }
        } elseif ($etapa == 2) {
            /* Etapa 2: Mostra tamanhos baseado na categoria */
            if (!empty($categoriaSelecionada)) {
                $tamanhos = $this->produtoModel->listarTamanhosPorCategoria($categoriaSelecionada);
                if (empty($tamanhos)) {
                    $mensagemErro = 'Não há tamanhos disponíveis para esta categoria. Por favor, escolha outra categoria.';
                    $etapa = 1;
                    $categorias = $this->produtoModel->listarCategorias();
                }
            } else {
                /* Se não tem categoria, volta para etapa 1 */
                $etapa = 1;
                $categorias = $this->produtoModel->listarCategorias();
            }
        } elseif ($etapa == 3) {
            /* Etapa 3: Mostra cores baseado na categoria e tamanho */
            if (!empty($categoriaSelecionada) && !empty($tamanhoSelecionado)) {
                $cores = $this->produtoModel->listarCoresPorCategoriaETamanho($categoriaSelecionada, $tamanhoSelecionado);
                if (empty($cores)) {
                    $mensagemErro = 'Não há cores disponíveis para esta combinação. Por favor, escolha outro tamanho.';
                    $etapa = 2;
                    $tamanhos = $this->produtoModel->listarTamanhosPorCategoria($categoriaSelecionada);
                }
            } else {
                /* Se não tem categoria ou tamanho, volta para etapa anterior */
                if (empty($categoriaSelecionada)) {
                    $etapa = 1;
                    $categorias = $this->produtoModel->listarCategorias();
                } else {
                    $etapa = 2;
                    $tamanhos = $this->produtoModel->listarTamanhosPorCategoria($categoriaSelecionada);
                }
            }
        }

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

        /* Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* Verifica se usuário está logado */
        $idCliente = $_SESSION['usuario_id'] ?? null;
        if (!$idCliente) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        /* Inicializa variáveis de mensagem */
        $mensagem = '';
        $tipoMensagem = '';

        /* Processa ações do carrinho (POST) */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['atualizar_quantidade'])) {
                /* Atualiza quantidade de um item */
                $idCarrinho = isset($_POST['id_carrinho']) ? (int)$_POST['id_carrinho'] : 0;
                $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;

                if ($idCarrinho > 0) {
                    $resultado = $this->carrinhoModel->atualizarQuantidade($idCarrinho, $quantidade, $idCliente, $this->estoqueModel);
                    if (is_array($resultado) && isset($resultado['erro'])) {
                        $mensagem = $resultado['mensagem'];
                        $tipoMensagem = 'erro';
                    } elseif ($resultado) {
                        $mensagem = "Quantidade atualizada com sucesso!";
                        $tipoMensagem = 'sucesso';
                    } else {
                        $mensagem = "Erro ao atualizar quantidade. Tente novamente.";
                        $tipoMensagem = 'erro';
                    }
                }
            } elseif (isset($_POST['remover_item'])) {
                /* Remove um item do carrinho */
                $idCarrinho = isset($_POST['id_carrinho']) ? (int)$_POST['id_carrinho'] : 0;

                if ($idCarrinho > 0) {
                    $resultado = $this->carrinhoModel->removerItem($idCarrinho, $idCliente);
                    if ($resultado) {
                        $mensagem = "Item removido do carrinho com sucesso!";
                        $tipoMensagem = 'sucesso';
                    } else {
                        $mensagem = "Erro ao remover item. Tente novamente.";
                        $tipoMensagem = 'erro';
                    }
                }
            }
        }

        /* Busca itens do carrinho */
        $itensCarrinho = $this->carrinhoModel->listarItensCarrinho($idCliente);

        /* Formata itens para a view */
        $itensFormatados = [];
        $subtotal = 0;

        foreach ($itensCarrinho as $item) {
            $quantidade = (int)($item['quantidade'] ?? 0);
            $precoUnitario = (float)($item['preco_unitario'] ?? 0);
            $precoTotal = $quantidade * $precoUnitario;
            $subtotal += $precoTotal;

            $imagemProduto = !empty($item['imagens']) ? BASE_URL . $item['imagens'] : '';

            $itensFormatados[] = [
                'id_carrinho' => (int)$item['id_carrinho'],
                'id_produto' => (int)$item['id_produto'],
                'nome_produto' => htmlspecialchars($item['nome_produto'] ?? ''),
                'quantidade' => $quantidade,
                'preco_unitario' => $precoUnitario,
                'preco_total' => $precoTotal,
                'cor' => htmlspecialchars($item['cor'] ?? ''),
                'tamanho' => htmlspecialchars($item['tamanho'] ?? ''),
                'imagem' => $imagemProduto
            ];
        }

        /* Calcula totais */
        $subtotalFormatado = 'R$ ' . number_format($subtotal, 2, ',', '.');
        $frete = 0; /* Por enquanto frete grátis ou pode ser calculado depois */
        $freteFormatado = 'R$ ' . number_format($frete, 2, ',', '.');
        $total = $subtotal + $frete;
        $totalFormatado = 'R$ ' . number_format($total, 2, ',', '.');

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/carrinho.php";
    }

    private function pedidos()
    {
        // Protege a rota - só cliente pode acessar
        AuthController::protegerCliente();

        // Inicia sessão
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Inicializa variáveis de mensagem
        $mensagem = '';
        $tipoMensagem = '';

        // Verifica mensagem de sucesso na URL
        if (isset($_GET['sucesso'])) {
            $mensagem = urldecode($_GET['sucesso']);
            $tipoMensagem = 'sucesso';
        }

        // Verifica se usuário está logado
        $idCliente = $_SESSION['usuario_id'] ?? null;
        if (!$idCliente) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        /* Processa ações de pagar ou cancelar pedido (POST) */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_pedido'], $_POST['id_pedido'])) {
            $acaoPedido = $_POST['acao_pedido'];
            $idPedido = (int)($_POST['id_pedido']);

            // Garante que o pedido pertence ao cliente logado
            $pedido = $this->pedidoModel->buscarPedidoPorId($idPedido);
            if (!$pedido || (int)($pedido['id_cliente'] ?? 0) !== (int)$idCliente) {
                $mensagem = "Pedido não encontrado para este usuário.";
                $tipoMensagem = 'erro';
            } else {
                if ($acaoPedido === 'pagar') {
                    // Se o usuário pagar: pedido FINALIZADO e pagamento CONFIRMADO
                    $ok = $this->pedidoModel->atualizarStatusPedido($idPedido, 'FINALIZADO', 'CONFIRMADO');
                    if ($ok) {
                        $mensagem = "Pagamento confirmado e pedido finalizado com sucesso.";
                        $tipoMensagem = 'sucesso';
                    } else {
                        $mensagem = "Erro ao atualizar o status do pedido. Tente novamente.";
                        $tipoMensagem = 'erro';
                    }
                } elseif ($acaoPedido === 'cancelar') {
                    // Se cancelar: pedido CANCELADO e pagamento CANCELADO
                    $ok = $this->pedidoModel->atualizarStatusPedido($idPedido, 'CANCELADO', 'CANCELADO');
                    if ($ok) {
                        $mensagem = "Pedido e pagamento cancelados com sucesso.";
                        $tipoMensagem = 'sucesso';
                    } else {
                        $mensagem = "Erro ao cancelar o pedido. Tente novamente.";
                        $tipoMensagem = 'erro';
                    }
                }
            }
        }

        // Busca pedidos reais do cliente
        $pedidosBrutos = $this->pedidoModel->listarPedidosPorCliente($idCliente);

        // Formata pedidos para a view
        $pedidosFormatados = [];
        foreach ($pedidosBrutos as $pedido) {
            $id = (int)$pedido['id_pedido'];

            // Formata ID com zeros à esquerda
            $idFormatado = str_pad((string)$id, 6, '0', STR_PAD_LEFT);

            // Separa data e hora
            $dataHora = $pedido['data_pedido'] ?? '';
            $data = '';
            $hora = '';
            if (!empty($dataHora)) {
                $dt = new DateTime($dataHora);
                $data = $dt->format('d/m/Y');
                $hora = $dt->format('H:i');
            }

            // Calcula valor total a partir do campo calculado e formata
            $valorTotalNumero = (float)($pedido['valor_total_calculado'] ?? 0);
            $valorTotalFormatado = 'R$ ' . number_format($valorTotalNumero, 2, ',', '.');

            // Status do pedido
            $statusPedido = strtoupper($pedido['status_pedido'] ?? 'PENDENTE');
            $statusTexto = '';
            $statusClasse = 'avaliacao';

            switch ($statusPedido) {
                case 'FINALIZADO':
                    $statusTexto = 'Finalizado';
                    break;
                case 'CANCELADO':
                    $statusTexto = 'Cancelado';
                    break;
                default:
                    $statusTexto = 'Pendente';
                    break;
            }

            // Status do pagamento
            $statusPagamento = strtoupper($pedido['status_pagamento'] ?? 'PENDENTE');
            $pagamentoTexto = '';
            $pagamentoClasse = 'avaliacao';

            switch ($statusPagamento) {
                case 'CONFIRMADO':
                    $pagamentoTexto = 'Confirmado';
                    break;
                case 'CANCELADO':
                    $pagamentoTexto = 'Cancelado';
                    break;
                default:
                    $pagamentoTexto = 'Pendente';
                    break;
            }

            // Define se pode pagar ou cancelar
            $podePagar = ($statusPedido === 'PENDENTE' && $statusPagamento === 'PENDENTE');
            $podeCancelar = ($statusPedido === 'PENDENTE');

            // Conta itens do pedido
            $itens = $this->pedidoModel->buscarItensPedido($id);
            $totalItens = 0;
            foreach ($itens as $item) {
                $totalItens += (int)($item['quantidade'] ?? 0);
            }

            $pedidosFormatados[] = [
                'id' => $id,
                'id_formatado' => $idFormatado,
                'data' => $data,
                'hora' => $hora,
                'status' => $statusTexto,
                'status_classe' => $statusClasse,
                'valor_total' => $valorTotalFormatado,
                'pagamento_status' => $pagamentoTexto,
                'pagamento_classe' => $pagamentoClasse,
                'total_itens' => $totalItens,
                'pode_pagar' => $podePagar,
                'pode_cancelar' => $podeCancelar
            ];
        }

        // Inclui a view passando as variáveis prontas
        require_once __DIR__ . "/../view/cliente/pedidos.php";
    }

    /* ---EXIBE A PÁGINA DE CHECKOUT--- */
    private function checkout()
    {
        /*  Protege a rota - só cliente pode acessar */
        AuthController::protegerCliente();

        /* Inicia sessão */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /* Verifica se usuário está logado */
        $idCliente = $_SESSION['usuario_id'] ?? null;
        if (!$idCliente) {
            header("Location: " . BASE_URL . "/app/control/LoginController.php");
            exit;
        }

        /* Inicializa variáveis de mensagem */
        $mensagem = '';
        $tipoMensagem = '';

        /* Verifica se o cliente existe na tabela Cliente */
        $dadosCliente = $this->usuarioModel->buscarDadosCliente($idCliente);
        if (!$dadosCliente) {
            $mensagem = "Erro: Seu cadastro não está completo. Por favor, atualize seus dados na página 'Minha Conta'.";
            $tipoMensagem = 'erro';
            /* Continua para mostrar a mensagem na view */
        }

        /* Busca itens do carrinho */
        $itensCarrinho = $this->carrinhoModel->listarItensCarrinho($idCliente);

        /* Se carrinho vazio, redireciona */
        if (empty($itensCarrinho)) {
            header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=carrinho");
            exit;
        }

        /* Busca endereço do cliente */
        $endereco = $this->usuarioModel->buscarEndereco($idCliente);

        /* Processa finalizar compra (POST) */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_pedido'])) {
            $formaPagamento = $_POST['forma_pagamento'] ?? '';

            if (empty($formaPagamento) || !in_array($formaPagamento, ['Pix', 'Cartão', 'Boleto'])) {
                $mensagem = "Selecione uma forma de pagamento válida.";
                $tipoMensagem = 'erro';
            } else {
                /* Verifica se há itens no carrinho */
                if (empty($itensCarrinho)) {
                    $mensagem = "Seu carrinho está vazio. Adicione produtos antes de finalizar o pedido.";
                    $tipoMensagem = 'erro';
                } else {
                    /* Prepara itens do carrinho para o pedido */
                    $itensParaPedido = [];
                    $valorTotal = 0;

                    foreach ($itensCarrinho as $item) {
                        if (!isset($item['id_produto']) || !isset($item['quantidade']) || !isset($item['preco_unitario'])) {
                            error_log("Item do carrinho com dados incompletos: " . print_r($item, true));
                            continue;
                        }

                        $itensParaPedido[] = [
                            'id_produto' => (int)$item['id_produto'],
                            'quantidade' => (int)$item['quantidade'],
                            'preco_unitario' => (float)$item['preco_unitario'],
                            'cor' => !empty($item['cor']) ? trim($item['cor']) : null,
                            'tamanho' => !empty($item['tamanho']) ? trim($item['tamanho']) : null
                        ];
                        $valorTotal += (float)$item['preco_unitario'] * (int)$item['quantidade'];
                    }

                    if (empty($itensParaPedido)) {
                        $mensagem = "Erro ao processar itens do carrinho. Tente novamente.";
                        $tipoMensagem = 'erro';
                    } elseif ($valorTotal <= 0) {
                        $mensagem = "Valor total inválido. Tente novamente.";
                        $tipoMensagem = 'erro';
                    } else {
                        /* Cria pedido a partir do carrinho */
                        error_log("Tentando criar pedido - Cliente: $idCliente, Itens: " . count($itensParaPedido) . ", Valor: $valorTotal, Forma: $formaPagamento");
                        $resultado = $this->pedidoModel->criarPedidoDoCarrinho($idCliente, $itensParaPedido, $formaPagamento, $valorTotal);

                        /* Verifica se retornou erro ou ID do pedido */
                        if (is_array($resultado) && isset($resultado['erro'])) {
                            /* Erro retornado do model */
                            $mensagem = "Erro ao finalizar pedido: " . htmlspecialchars($resultado['mensagem']);
                            $tipoMensagem = 'erro';
                            error_log("Falha ao criar pedido - Erro: " . $resultado['mensagem']);
                            error_log("Dados dos itens: " . print_r($itensParaPedido, true));
                        } elseif ($resultado && is_numeric($resultado)) {
                            /* Sucesso - retornou ID do pedido */
                            $idPedido = (int)$resultado;

                            /* Limpa o carrinho */
                            $this->carrinhoModel->limparCarrinho($idCliente);

                            /* Redireciona para pedidos com mensagem de sucesso */
                            header("Location: " . BASE_URL . "/app/control/ClienteController.php?acao=pedidos&sucesso=" . urlencode("Pedido criado com sucesso! ID: #" . str_pad($idPedido, 6, '0', STR_PAD_LEFT)));
                            exit;
                        } else {
                            /* Erro desconhecido */
                            $mensagem = "Erro desconhecido ao finalizar pedido. Verifique os logs do servidor.";
                            $tipoMensagem = 'erro';
                            error_log("Falha ao criar pedido - Retorno inesperado: " . print_r($resultado, true));
                            error_log("Dados dos itens: " . print_r($itensParaPedido, true));
                        }
                    }
                }
            }
        }

        /* Formata itens para a view */
        $itensFormatados = [];
        $subtotal = 0;

        foreach ($itensCarrinho as $item) {
            $quantidade = (int)($item['quantidade'] ?? 0);
            $precoUnitario = (float)($item['preco_unitario'] ?? 0);
            $precoTotal = $quantidade * $precoUnitario;
            $subtotal += $precoTotal;

            $imagemProduto = !empty($item['imagens']) ? BASE_URL . $item['imagens'] : '';

            $itensFormatados[] = [
                'id_produto' => (int)$item['id_produto'],
                'nome_produto' => htmlspecialchars($item['nome_produto'] ?? ''),
                'quantidade' => $quantidade,
                'preco_unitario' => $precoUnitario,
                'preco_total' => $precoTotal,
                'cor' => htmlspecialchars($item['cor'] ?? ''),
                'tamanho' => htmlspecialchars($item['tamanho'] ?? ''),
                'imagem' => $imagemProduto
            ];
        }

        /* Calcula totais */
        $subtotalFormatado = 'R$ ' . number_format($subtotal, 2, ',', '.');
        $frete = 0;
        $freteFormatado = 'R$ ' . number_format($frete, 2, ',', '.');
        $total = $subtotal + $frete;
        $totalFormatado = 'R$ ' . number_format($total, 2, ',', '.');

        /* Formata endereço para exibição */
        $rua = $endereco['rua'] ?? '';
        $numero = $endereco['numero'] ?? '';
        $bairro = $endereco['bairro'] ?? '';
        $cep = $endereco['cep'] ?? '';
        $estado = $endereco['estado'] ?? '';
        $complemento = $endereco['complemento'] ?? '';

        /* Formata CEP para exibição */
        $cepFormatado = $cep;
        if (!empty($cep) && strlen($cep) == 8) {
            $cepFormatado = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        }

        /* Inclui a view */
        require_once __DIR__ . "/../view/cliente/checkout.php";
    }

    /* FUNÇÃO DE FORMATAR E PASSAR A CLASSE REFERENTE A QUANTIDADE DE ESTOQUE */
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
}

/* Se o arquivo foi chamado diretamente, executa o controller */
if (basename($_SERVER['PHP_SELF']) === 'ClienteController.php') {
    $controller = new ClienteController();
    $controller->index();
}

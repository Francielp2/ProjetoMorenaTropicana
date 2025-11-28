<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../control/AuthController.php";

// Protege a rota - só cliente pode acessar
AuthController::protegerCliente();

// Inicia sessão e pega dados do usuário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nomeUsuario = $_SESSION['usuario_nome'] ?? '';
$emailUsuario = $_SESSION['usuario_email'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Morena Tropicana</title><!--título da aba-->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/image/logo.png"><!--icone da aba-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" /><!--link para swiper css-->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/nice-select2.css"><!--link para o nice select css-->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/principal.css"><!--link para o css da tela inicial-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet" /> <!--biblioteca de icones-->
</head>

<body>
    <!-- CABEÇALHO -->

    <header class="cabecalho" id="cabecalho">

        <!-- PARTE SUPERIOR DO CABEÇALHO -->

        <div class="topbar">
            <div class="container_topbar container">

                <ul class="lista_topbar">
                    <li class="item_topbar">
                        <a href="<?= BASE_URL ?>/app/view/cliente/PoliticasDaLoja.php" class="link_topbar">Políticas da Loja</a>
                    </li>

                    <li class="item_topbar">
                        <a href="<?= BASE_URL ?>/app/view/cliente/tela_inicial.php#newsletter" class="link_topbar">Boletim informativo</a>
                    </li>

                    <li class="item_topbar">
                        <a href="#rodape" class="link_topbar">Contato</a>
                    </li>
                </ul>

                <div class="social_topbar">
                    <a href="https://www.facebook.com/" class="social_topbar_link"><i class="ri-facebook-circle-fill"></i>
                    </a>

                    <a href="https://www.twitter.com/" class="social_topbar_link"><i class="ri-twitter-x-line"></i>
                    </a>

                    <a href="https://www.instagram.com/" class="social_topbar_link"><i class="ri-instagram-line"></i>
                    </a>

                    <a href="https://www.youtube.com/" class="social_topbar_link"><i class="ri-youtube-fill"></i>
                    </a>
                </div>

                <div class="msg_topbar">
                    <img src="<?= BASE_URL ?>/public/assets/image/caminhao.svg" alt="ícone de caminhão" class="icone_topbar">
                    <p class="texto_topbar">Frete grátis a partir de R$200</p>
                </div>

            </div>
        </div>

        <!-- PARTE INFERIOR DO CABEÇALHO -->

        <div class="bottom_bar container">
            <div class="logo">

                <a href="#">
                    <img src="<?= BASE_URL ?>/public/assets/image/logo.png" alt="Logo Morena Tropicana" class="logo_navbar">
                </a>
                <span class="nome_logo">Morena Tropicana</span>

            </div>

            <form action="#" class="formulario_navbar">
                <select name="" id="select_navbar" class="select_navbar" style="display: none;">
                    <option value="todascat" selected>Todas as categorias</option>
                    <option value="vessai">Vestidos e Saídas</option>
                    <option value="blucam">Blusas e Camisas</option>
                    <option value="saical">Saias e Calças</option>
                    <option value="acecom">Acessórios</option>
                </select>

                <input type="text" placeholder="Procurar..." class="input_navbar">

                <button type="submit" class="procurar_navbar">

                    <i class="ri-search-line"></i>

                </button>
            </form>

            <div class="btns_navbar">

                <a href="<?= BASE_URL ?>/app/view/cliente/conta.php" class="icone_btn"><i class="ri-user-line"></i></a>
                <a href="<?= BASE_URL ?>/app/view/cliente/Favoritos.php" class="icone_btn"><i class="ri-heart-line"></i></a>
                <a href="<?= BASE_URL ?>/app/view/cliente/carrinho.php" class="icone_btn"><i class="ri-shopping-cart-line"></i></a>

            </div>
        </div>

        <!--BARRA DA NAVEGAÇÃO-->

        <nav class="navbar container">

            <ul class="lista_navbar">
                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/view/cliente/tela_inicial.php" class="link_navbar">INÍCIO</a>
                </li>

                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/view/cliente/PaginaProdutos.php" class="link_navbar">PRODUTOS</a>
                </li>

                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/view/cliente/conta.php" class="link_navbar">MINHA CONTA</a>
                </li>

                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/view/cliente/quiz.php" class="link_navbar">QUIZ</a>
                </li>
            </ul>

            <div class="contato_navbar">
                <img src="<?= BASE_URL ?>/public/assets/image/email.svg" alt="Icone email" class="icone_navbar">
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=morenatropicana.official@gmail.com&su=Contato%20via%20site&body=Olá,%20tenho%20interesse%20em%20saber%20mais." target="_blank" class="contato_link">morenatropicana.official@gmail.com</a>
            </div>
        </nav>
    </header>
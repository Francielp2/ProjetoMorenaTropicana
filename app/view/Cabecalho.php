<?php
// Esta view recebe apenas variáveis prontas do controller
// $nomeUsuario - nome do usuário logado
// $emailUsuario - email do usuário logado

// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pega dados do usuário (já validado pelo controller)
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
    <!-- Iframe invisível para submissões de favoritos sem recarregar a página -->
    <iframe name="favorito_target" id="favorito_target" style="display:none;width:0;height:0;border:0;" aria-hidden="true"></iframe>
    <!-- CABEÇALHO -->

    <header class="cabecalho" id="cabecalho">

        <!-- PARTE SUPERIOR DO CABEÇALHO -->

        <div class="topbar">
            <div class="container_topbar container">

                <ul class="lista_topbar">
                    <li class="item_topbar">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=politicas" class="link_topbar">Políticas da Loja</a>
                    </li>

                    <li class="item_topbar">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial#newsletter" class="link_topbar">Boletim informativo</a>
                    </li>

                    <li class="item_topbar">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=pedidos" class="link_topbar">Meus pedidos</a>
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
                    <p class="texto_topbar">Frete grátis</p>
                </div>

            </div>
        </div>

        <!-- PARTE INFERIOR DO CABEÇALHO -->

        <div class="bottom_bar container">
            <div class="logo">

                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">
                    <img src="<?= BASE_URL ?>/public/assets/image/logo.png" alt="Logo Morena Tropicana" class="logo_navbar">
                </a>
                <span class="nome_logo">Morena Tropicana</span>

            </div>

            <form action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" method="GET" class="formulario_navbar">
                <input type="hidden" name="acao" value="produtos">
                <!-- <select name="" id="select_navbar" class="select_navbar" style="display: none;">
                    <option value="todascat" selected>Todas as categorias</option>
                    <option value="vessai">Vestidos e Saídas</option>
                    <option value="blucam">Blusas e Camisas</option>
                    <option value="saical">Saias e Calças</option>
                    <option value="acecom">Acessórios</option>
                </select> -->

                <input type="text" name="busca" placeholder="Procurar..." class="input_navbar" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">

                <button type="submit" class="procurar_navbar">

                    <i class="ri-search-line"></i>

                </button>
            </form>

            <div class="btns_navbar">

                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=conta" class="icone_btn"><i class="ri-user-line"></i></a>
                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=favoritos" class="icone_btn"><i class="ri-heart-line"></i></a>
                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=carrinho" class="icone_btn"><i class="ri-shopping-cart-line"></i></a>

            </div>
        </div>

        <!--BARRA DA NAVEGAÇÃO-->

        <nav class="navbar container">

            <ul class="lista_navbar">
                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial" class="link_navbar">INÍCIO</a>
                </li>

                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" class="link_navbar">PRODUTOS</a>
                </li>

                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=conta" class="link_navbar">MINHA CONTA</a>
                </li>

                <li class="item_navbar">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=quiz" class="link_navbar">QUIZ</a>
                </li>
            </ul>

            <div class="contato_navbar">
                <img src="<?= BASE_URL ?>/public/assets/image/email.svg" alt="Icone email" class="icone_navbar">
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=morenatropicana.official@gmail.com&su=Contato%20via%20site&body=Olá,%20tenho%20interesse%20em%20saber%20mais." target="_blank" class="contato_link">morenatropicana.official@gmail.com</a>
            </div>
        </nav>
    </header>
    <script>
        (function() {
            // Guarda o UID do último form submetido para favoritos
            window._lastFavoritoFormUid = null;

            // Delegation: intercepta submissões de forms de favorito
            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form.classList || !form.classList.contains('form-favorito')) return;

                // Identificador único do form (adicionado nos templates)
                var uid = form.dataset.favoritoUid || null;
                if (uid) window._lastFavoritoFormUid = uid;

                // Se for formulário que tem acao_favorito (produtos/descricao)
                var acaoInput = form.querySelector('input[name="acao_favorito"]');
                if (acaoInput) {
                    // Atualiza visual do ícone imediatamente (otimista)
                    var btn = form.querySelector('button[type="submit"]');
                    var icon = btn ? btn.querySelector('i') : null;
                    var original = acaoInput.value; // 'adicionar' ou 'remover'

                    if (icon) {
                        // toggle visual conforme ação que será executada
                        if (original === 'adicionar') {
                            icon.className = icon.className.replace('-line', '-fill');
                            icon.style.color = '#d32f2f';
                        } else {
                            icon.className = icon.className.replace('-fill', '-line');
                            icon.style.color = '';
                        }
                    }

                    // marca como pendente para controle posterior
                    form.dataset.pending = 'true';
                    // permite a submissão seguir para o iframe (não preventDefault)
                } else if (form.querySelector('input[name="remover_favorito"]')) {
                    // formulário da página Favoritos - removemos a linha imediatamente
                    var row = form.closest('tr');
                    if (row) row.remove();
                    // marca pendente
                    form.dataset.pending = 'true';
                }
            }, true);

            // Quando o iframe carrega, atualiza o estado do input acao_favorito
            var iframe = document.getElementById('favorito_target');
            if (iframe) {
                iframe.addEventListener('load', function() {
                    try {
                        var uid = window._lastFavoritoFormUid;
                        if (!uid) return;
                        var form = document.querySelector('.form-favorito[data-favorito-uid="' + uid + '"]');
                        if (!form) return;

                        // se tinha acao_favorito, inverte o valor para a próxima ação
                        var acaoInput = form.querySelector('input[name="acao_favorito"]');
                        if (acaoInput) {
                            acaoInput.value = (acaoInput.value === 'adicionar') ? 'remover' : 'adicionar';
                        }

                        // limpa pendente
                        delete form.dataset.pending;
                        window._lastFavoritoFormUid = null;
                    } catch (err) {
                        // falha silenciosa
                        console.error('Erro ao processar resposta do iframe de favoritos', err);
                    }
                });
            }
        })();
    </script>
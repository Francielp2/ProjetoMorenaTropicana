<?php require_once __DIR__ . "../../config/config.php";?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Morena Tropicana</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/login_cadastro.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/image/logo.png">

</head>

<body>
    <!-- <-- conteúdo principal --->
    <main class="container">
        <!-- Cabeçalho com logo -->
        <div class="header">
            <div class="logo">
                <img class="icone_logo" src="<?= BASE_URL ?>/public/assets/image/logo.png" alt="Logo Morena Tropicana">
                <h1 class="nome_logo">MORENA TROPICANA</h1>
            </div>
        </div>
        <!-- Formuário de login -->
        <form class="formulario_login_cadastro" method="POST" action="">
            <h2 class="titulo_formulario">Bem-vindo(a) de volta!</h2>
            <p class="subtitulo_formulario">Entre na sua conta para acessar o site</p>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-envelope icone_input"></i>
                    <input name="email" type="email" placeholder="Seu email" required>
                </div>
            </div>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-lock-alt icone_input"></i>
                    <input name="senha" type="password" placeholder="Sua senha" required>
                </div>
            </div>

            <div class="opcoes_formulario">
                <label class="lembre-me">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Lembrar de mim
                </label>
                <a href="#" class="esqueceu_senha">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="botao_submit">
                <span>Entrar</span>
                <i class="bx bx-right-arrow-alt"></i>
            </button>

            <p class="link_cadastro_login">
                Não tem conta?
                <a href="cadastro.php" class="botao_cadastro_login">Cadastre-se aqui</a>
            </p>
        </form>
    </main>
</body>

</html>
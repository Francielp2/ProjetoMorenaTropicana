<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../control/AuthController.php"; 

// Inicia sessão
session_start();

// Processa cadastro se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController();
    $authController->cadastro();
}

// Pega mensagens de erro/sucesso
$erro = $_SESSION['erro'] ?? '';
$sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['erro']);
unset($_SESSION['sucesso']);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Morena Tropicana</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/login_cadastro.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/image/logo.png">
</head>

<body>
    <!-- Conteúdo principal -->
    <main class="container">
        <!-- Cabeçalho com logo -->
        <div class="header">
            <div class="logo">
                <img class="icone_logo" src="<?= BASE_URL ?>/public/assets/image/logo.png" alt="Logo Morena Tropicana">
                <h1 class="nome_logo">MORENA TROPICANA</h1>
            </div>
        </div>

        <!-- Mensagens de erro/sucesso -->
        <?php if ($erro): ?>
            <div style="background-color: #fee; color: #c33; padding: 15px; margin: 20px auto; max-width: 400px; border-radius: 5px; text-align: center;">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div style="background-color: #efe; color: #3c3; padding: 15px; margin: 20px auto; max-width: 400px; border-radius: 5px; text-align: center;">
                <?= htmlspecialchars($sucesso) ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de cadastro -->
        <form class="formulario_login_cadastro" method="POST" action="">
            <h2 class="titulo_formulario">Crie sua conta</h2>
            <p class="subtitulo_formulario">Junte-se à comunidade Morena Tropicana</p>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-user icone_input"></i>
                    <input name="nome" type="text" placeholder="Nome completo" required>
                </div>
            </div>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-id-card icone_input"></i>
                    <input name="cpf" type="text" placeholder="CPF (apenas números)" maxlength="11" required>
                </div>
            </div>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-envelope icone_input"></i>
                    <input name="email" type="email" placeholder="Seu email" required>
                </div>
            </div>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-phone icone_input"></i>
                    <input name="telefone" type="text" placeholder="Telefone (opcional)" maxlength="11">
                </div>
            </div>

            <div class="inputs">
                <div class="input-container">
                    <i class="bx bxs-lock-alt icone_input"></i>
                    <input name="senha" type="password" placeholder="Sua senha" required minlength="6">
                </div>
            </div>

            <button type="submit" class="botao_submit">
                <span>Cadastrar</span>
                <i class="bx bx-right-arrow-alt"></i>
            </button>

            <p class="link_cadastro_login">
                Já tem conta?
                <a href="login.php" class="botao_cadastro_login">Faça login aqui</a>
            </p>
        </form>
    </main>
</body>

</html>
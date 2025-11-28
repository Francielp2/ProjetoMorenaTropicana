<?php
// Esta view recebe apenas variáveis prontas do controller
// $nomeUsuario - nome do usuário logado
// $pagina_atual - página atual para destacar no menu

// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pega dados do usuário logado (já validado pelo controller)
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Administrador';
$pagina_atual = $_GET['acao'] ?? 'dashboard';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Morena Tropicana</title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/image/logo.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/adm.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body class="admin-body">
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div>
                <div class="admin-logo">
                    <img src="<?= BASE_URL ?>/public/assets/image/logo.png" alt="Logo Morena Tropicana">
                    <span class="admin-logo-text">Morena Tropicana</span>
                </div>

                <nav>
                    <ul class="admin-menu">
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/control/DashboardController.php" class="admin-menu-link <?= $pagina_atual == 'dashboard' ? 'active' : '' ?>">
                                <i class="ri-dashboard-line"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios" class="admin-menu-link <?= $pagina_atual == 'usuarios' ? 'active' : '' ?>">
                                <i class="ri-user-line"></i>
                                <span>Usuários</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=produtos" class="admin-menu-link <?= $pagina_atual == 'produtos' ? 'active' : '' ?>">
                                <i class="ri-shopping-bag-line"></i>
                                <span>Produtos</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos" class="admin-menu-link <?= $pagina_atual == 'pedidos' ? 'active' : '' ?>">
                                <i class="ri-shopping-cart-line"></i>
                                <span>Pedidos</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque" class="admin-menu-link <?= $pagina_atual == 'estoque' ? 'active' : '' ?>">
                                <i class="ri-stack-line"></i>
                                <span>Estoque</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="admin-menu-logout">
                <a href="<?= BASE_URL ?>/app/control/LogoutController.php" class="admin-menu-link admin-menu-logout-link">
                    <i class="ri-logout-box-line"></i>
                    <span>Sair</span>
                </a>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title"><?= $titulo_pagina ?? 'Painel Administrativo' ?></h1>
                <div class="admin-user-info">
                    <span class="admin-user-name"><?= htmlspecialchars($nomeUsuario) ?></span>
                    <div class="admin-user-icon">
                        <i class="ri-user-fill"></i>
                    </div>
                </div>
            </div>
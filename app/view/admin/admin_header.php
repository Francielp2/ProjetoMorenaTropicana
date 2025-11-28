<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../control/AuthController.php";

// Protege a rota - só admin pode acessar
AuthController::protegerAdmin();

$pagina_atual = basename($_SERVER['PHP_SELF']);

// Pega dados do usuário logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$nomeUsuario = $_SESSION['usuario_nome'] ?? null;
if ($nomeUsuario === null) {
    $nomeUsuario = 'Administrador';
}

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
                            <a href="<?= BASE_URL ?>/app/view/admin/index.php" class="admin-menu-link <?= $pagina_atual == 'index.php' ? 'active' : '' ?>">
                                <i class="ri-dashboard-line"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/view/admin/usuarios.php" class="admin-menu-link <?= $pagina_atual == 'usuarios.php' ? 'active' : '' ?>">
                                <i class="ri-user-line"></i>
                                <span>Usuários</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/view/admin/produtos.php" class="admin-menu-link <?= $pagina_atual == 'produtos.php' ? 'active' : '' ?>">
                                <i class="ri-shopping-bag-line"></i>
                                <span>Produtos</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/view/admin/pedidos.php" class="admin-menu-link <?= $pagina_atual == 'pedidos.php' ? 'active' : '' ?>">
                                <i class="ri-shopping-cart-line"></i>
                                <span>Pedidos</span>
                            </a>
                        </li>
                        <li class="admin-menu-item">
                            <a href="<?= BASE_URL ?>/app/view/admin/estoque.php" class="admin-menu-link <?= $pagina_atual == 'estoque.php' ? 'active' : '' ?>">
                                <i class="ri-stack-line"></i>
                                <span>Estoque</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="admin-menu-logout">
                <a href="<?= BASE_URL ?>/app/view/logout.php" class="admin-menu-link admin-menu-logout-link">
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
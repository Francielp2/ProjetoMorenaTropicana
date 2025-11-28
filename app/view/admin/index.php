<?php
require_once __DIR__ . "/../../control/DashboardController.php";

$titulo_pagina = "Dashboard";
include_once "admin_header.php";

// Instancia o controller do dashboard
$dashboardController = new DashboardController();

// Busca todas as estatísticas através do controller
$estatisticas = $dashboardController->getEstatisticas();

// Extrai as variáveis do array para facilitar o uso na view
$totalUsuarios = $estatisticas['totalUsuarios'];
$totalProdutos = $estatisticas['totalProdutos'];
$pedidosPendentes = $estatisticas['pedidosPendentes'];
$totalEstoque = $estatisticas['totalEstoque'];
$receitaFormatada = $estatisticas['receitaFormatada'];
$totalVendas = $estatisticas['totalVendas'];
?>

<!-- Grade de Cards do Dashboard -->
<div class="admin-dashboard-grid-3x2">
    <a href="<?= BASE_URL ?>/app/view/admin/usuarios.php" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon primary">
            <i class="ri-user-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Usuários</h3>
            <div class="admin-dashboard-card-value"><?= number_format($totalUsuarios, 0, ',', '.') ?></div>
            <p class="admin-dashboard-card-label">Total de usuários cadastrados</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/app/view/admin/produtos.php" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon success">
            <i class="ri-shopping-bag-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Produtos</h3>
            <div class="admin-dashboard-card-value"><?= number_format($totalProdutos, 0, ',', '.') ?></div>
            <p class="admin-dashboard-card-label">Total de produtos cadastrados</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/app/view/admin/pedidos.php" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon warning">
            <i class="ri-shopping-cart-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Pedidos</h3>
            <div class="admin-dashboard-card-value"><?= number_format($pedidosPendentes, 0, ',', '.') ?></div>
            <p class="admin-dashboard-card-label">Pedidos pendentes</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/app/view/admin/estoque.php" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon info">
            <i class="ri-stack-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Estoque</h3>
            <div class="admin-dashboard-card-value"><?= number_format($totalEstoque, 0, ',', '.') ?></div>
            <p class="admin-dashboard-card-label">Itens em estoque</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/app/view/admin/pedidos.php" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon primary">
            <i class="ri-money-dollar-circle-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Receita</h3>
            <div class="admin-dashboard-card-value"><?= $receitaFormatada ?></div>
            <p class="admin-dashboard-card-label">Receita do mês atual</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/app/view/admin/pedidos.php" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon success">
            <i class="ri-line-chart-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Vendas</h3>
            <div class="admin-dashboard-card-value"><?= number_format($totalVendas, 0, ',', '.') ?></div>
            <p class="admin-dashboard-card-label">Total de vendas realizadas</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>
</div>

<!-- Pedidos Recentes -->
<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Pedidos Recentes</h2>
        <a href="<?= BASE_URL ?>/app/view/admin/pedidos.php" class="admin-btn admin-btn-secondary admin-btn-sm">
            Ver Todos
            <i class="ri-arrow-right-line"></i>
        </a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#001234</td>
                <td>Maria Silva</td>
                <td>15/01/2025</td>
                <td>R$ 299,90</td>
                <td><span class="admin-badge admin-badge-warning">Pendente</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001233</td>
                <td>Ana Costa</td>
                <td>14/01/2025</td>
                <td>R$ 450,00</td>
                <td><span class="admin-badge admin-badge-success">Entregue</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001232</td>
                <td>Joana Santos</td>
                <td>14/01/2025</td>
                <td>R$ 189,90</td>
                <td><span class="admin-badge admin-badge-info">Em Trânsito</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001231</td>
                <td>Paula Oliveira</td>
                <td>13/01/2025</td>
                <td>R$ 320,00</td>
                <td><span class="admin-badge admin-badge-success">Entregue</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Produtos com Baixo Estoque -->
<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Produtos com Baixo Estoque</h2>
        <a href="<?= BASE_URL ?>/app/view/admin/estoque.php" class="admin-btn admin-btn-secondary admin-btn-sm">
            Ver Estoque Completo
            <i class="ri-arrow-right-line"></i>
        </a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Quantidade</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Vestido Floral Verão</td>
                <td>Vestidos</td>
                <td>3</td>
                <td><span class="admin-badge admin-badge-danger">Crítico</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Repor Estoque">
                            <i class="ri-add-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Blusa Manga Longa</td>
                <td>Blusas</td>
                <td>5</td>
                <td><span class="admin-badge admin-badge-warning">Baixo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Repor Estoque">
                            <i class="ri-add-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Calça Jeans Skinny</td>
                <td>Calças</td>
                <td>7</td>
                <td><span class="admin-badge admin-badge-warning">Baixo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Repor Estoque">
                            <i class="ri-add-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php include_once "admin_footer.php"; ?>
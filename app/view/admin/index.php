<?php
// Esta view recebe apenas variáveis prontas do controller
// $titulo_pagina - título da página
// $totalUsuarios - total de usuários
// $totalProdutos - total de produtos
// $pedidosPendentes - total de pedidos pendentes
// $totalEstoque - total de itens em estoque
// $receitaFormatada - receita formatada (ex: "R$ 1.234,56")
// $totalVendas - total de vendas
// $pedidosFormatados - array com os últimos pedidos formatados
// $produtosFormatados - array com produtos de estoque baixo formatados

include_once "admin_header.php";
?>

<!-- Grade de Cards do Dashboard -->
<div class="admin-dashboard-grid-3x2">
    <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios" class="admin-dashboard-card">
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

    <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=produtos" class="admin-dashboard-card">
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

    <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos" class="admin-dashboard-card">
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

    <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque" class="admin-dashboard-card">
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

    <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos" class="admin-dashboard-card">
        <div class="admin-dashboard-card-icon primary">
            <i class="ri-money-dollar-circle-line"></i>
        </div>
        <div class="admin-dashboard-card-content">
            <h3 class="admin-dashboard-card-title">Receita</h3>
            <div class="admin-dashboard-card-value"><?= $receitaFormatada ?></div>
            <p class="admin-dashboard-card-label">Receita do ano atual</p>
            <div class="admin-dashboard-card-footer">
                <span>Ver detalhes</span>
                <i class="ri-arrow-right-line"></i>
            </div>
        </div>
    </a>

    <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos" class="admin-dashboard-card">
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
        <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos" class="admin-btn admin-btn-secondary admin-btn-sm">
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
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidosFormatados)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum pedido encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pedidosFormatados as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido['id']) ?></td>
                        <td><?= htmlspecialchars($pedido['cliente']) ?></td>
                        <td><?= htmlspecialchars($pedido['data']) ?></td>
                        <td><?= htmlspecialchars($pedido['valor']) ?></td>
                        <td><span class="admin-badge <?= htmlspecialchars($pedido['status_classe']) ?>"><?= htmlspecialchars($pedido['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Produtos com Baixo Estoque -->
<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Produtos com Baixo Estoque</h2>
        <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque" class="admin-btn admin-btn-secondary admin-btn-sm">
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
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produtosFormatados)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum produto com estoque baixo encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($produtosFormatados as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= htmlspecialchars($produto['categoria']) ?></td>
                        <td><?= htmlspecialchars($produto['quantidade']) ?></td>
                        <td><span class="admin-badge <?= htmlspecialchars($produto['status_classe']) ?>"><?= htmlspecialchars($produto['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once "admin_footer.php"; ?>
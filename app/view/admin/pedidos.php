<?php
// Esta view recebe apenas variáveis prontas do controller
// $titulo_pagina - título da página
// $pedidosFormatados - array com todos os pedidos formatados
// $filtros - array com os filtros aplicados

include_once "admin_header.php";

// Pega mensagens de sucesso/erro da URL
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
$mensagemErro = isset($_GET['erro']) ? $_GET['erro'] : '';
$filtros = $filtros ?? ['termo' => '', 'status_pedido' => '', 'status_pagamento' => '', 'data_inicial' => '', 'data_final' => '', 'valor_min' => '', 'valor_max' => ''];

// Variáveis $pedidoEdicao e $pedidoVisualizacao são passadas pelo controller
$pedidoEdicao = $pedidoEdicao ?? null;
$pedidoVisualizacao = $pedidoVisualizacao ?? null;
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Lista de Pedidos</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalFiltros()">
            <i class="ri-filter-line"></i>
            Filtros Avançados
        </button>
    </div>

    <!-- Mensagens de sucesso/erro -->
    <?php if (!empty($mensagemSucesso)): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 1rem; border-radius: 5px; text-align: center;">
            <?= htmlspecialchars($mensagemSucesso) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensagemErro)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 1rem; border-radius: 5px; text-align: center;">
            <?= htmlspecialchars($mensagemErro) ?>
        </div>
    <?php endif; ?>

    <!-- Barra de Busca e Filtros -->
    <form method="GET" action="<?= BASE_URL ?>/app/control/AdminController.php" class="admin-search-bar" style="display: flex; align-items: center; gap: 1rem; flex-wrap: nowrap;">
        <input type="hidden" name="acao" value="pedidos">
        <input type="text" name="termo" class="admin-search-input" placeholder="Buscar por ID do pedido, cliente..." value="<?= htmlspecialchars($filtros['termo']) ?>" style="flex: 1; min-width: 200px; max-width: 300px;">
        <button type="submit" class="admin-btn admin-btn-primary" style="min-width: 120px; flex-shrink: 0;">
            <i class="ri-search-line"></i>
            Pesquisar
        </button>
        <select class="admin-filter-select" name="status_pedido" onchange="this.form.submit()" style="min-width: 180px; max-width: 180px; flex-shrink: 0;">
            <option value="">Todos os Status do Pedido</option>
            <option value="PENDENTE" <?= $filtros['status_pedido'] === 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
            <option value="FINALIZADO" <?= $filtros['status_pedido'] === 'FINALIZADO' ? 'selected' : '' ?>>Finalizado</option>
            <option value="CANCELADO" <?= $filtros['status_pedido'] === 'CANCELADO' ? 'selected' : '' ?>>Cancelado</option>
        </select>
        <select class="admin-filter-select" name="status_pagamento" onchange="this.form.submit()" style="min-width: 200px; max-width: 200px; flex-shrink: 0;">
            <option value="">Todos os Status de Pagamento</option>
            <option value="PENDENTE" <?= $filtros['status_pagamento'] === 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
            <option value="CONFIRMADO" <?= $filtros['status_pagamento'] === 'CONFIRMADO' ? 'selected' : '' ?>>Confirmado</option>
            <option value="CANCELADO" <?= $filtros['status_pagamento'] === 'CANCELADO' ? 'selected' : '' ?>>Cancelado</option>
        </select>
        <?php if (!empty($filtros['termo']) || !empty($filtros['status_pedido']) || !empty($filtros['status_pagamento']) || !empty($filtros['data_inicial']) || !empty($filtros['data_final']) || !empty($filtros['valor_min']) || !empty($filtros['valor_max'])): ?>
            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos" class="admin-btn admin-btn-secondary" style="min-width: 120px;">
                Limpar Filtros
            </a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Pagamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidosFormatados)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum pedido encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pedidosFormatados as $pedido): ?>
                    <tr>
                        <td>#<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($pedido['cliente']) ?></td>
                        <td><?= htmlspecialchars($pedido['data']) ?></td>
                        <td><?= htmlspecialchars($pedido['valor_total']) ?></td>
                        <td><span class="admin-badge <?= htmlspecialchars($pedido['status_pedido_classe']) ?>"><?= htmlspecialchars($pedido['status_pedido']) ?></span></td>
                        <td><span class="admin-badge <?= htmlspecialchars($pedido['status_pagamento_classe']) ?>"><?= htmlspecialchars($pedido['status_pagamento']) ?></span></td>
                        <td>
                            <div class="admin-table-actions">
                                <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarPedido(<?= $pedido['id'] ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="admin-btn admin-btn-icon admin-btn-success" title="Atualizar Status" onclick="atualizarStatus(<?= $pedido['id'] ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Visualização -->
<?php if (isset($_GET['visualizar']) && !empty($pedidoVisualizacao)): ?>
    <div id="modalVisualizacao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 class="admin-modal-title">Detalhes do Pedido #<?= str_pad($pedidoVisualizacao['id'], 6, '0', STR_PAD_LEFT) ?></h2>
                <button class="admin-modal-close" onclick="fecharModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <div class="admin-form">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Cliente</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['cliente']) ?>" readonly>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Email</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['email']) ?>" readonly>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Data do Pedido</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['data']) ?>" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Status</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['status_pedido']) ?>" readonly>
                        </div>
                    </div>

                    <h4 style="margin: 1.5rem 0 1rem 0; color: var(--cor_titulo);">Itens do Pedido</h4>
                    <table class="admin-table" style="margin-bottom: 1.5rem;">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidoVisualizacao['itens'])): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">Nenhum item encontrado</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pedidoVisualizacao['itens'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nome']) ?></td>
                                        <td><?= htmlspecialchars($item['quantidade']) ?></td>
                                        <td><?= htmlspecialchars($item['preco_unitario']) ?></td>
                                        <td><?= htmlspecialchars($item['subtotal']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Valor Total</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['valor_total']) ?>" readonly style="font-weight: var(--espessura_700); font-size: var(--fonte_grande);">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Forma de Pagamento</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['forma_pagamento']) ?>" readonly>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Status do Pagamento</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($pedidoVisualizacao['status_pagamento']) ?>" readonly>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="width: 100%;">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Edição -->
<?php if (isset($_GET['editar']) && !empty($pedidoEdicao)): ?>
    <div id="modalEdicao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 class="admin-modal-title">Atualizar Status do Pedido</h2>
                <button class="admin-modal-close" onclick="fecharModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=atualizarStatusPedido" class="admin-form">
                    <input type="hidden" name="id" value="<?= $pedidoEdicao['id'] ?>">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Status do Pedido</label>
                        <select class="admin-form-select" name="status_pedido" required>
                            <option value="PENDENTE" <?= $pedidoEdicao['status_pedido'] === 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
                            <option value="FINALIZADO" <?= $pedidoEdicao['status_pedido'] === 'FINALIZADO' ? 'selected' : '' ?>>Finalizado</option>
                            <option value="CANCELADO" <?= $pedidoEdicao['status_pedido'] === 'CANCELADO' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Status do Pagamento</label>
                        <select class="admin-form-select" name="status_pagamento" required>
                            <option value="PENDENTE" <?= ($pedidoEdicao['status_pagamento'] ?? 'PENDENTE') === 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
                            <option value="CONFIRMADO" <?= ($pedidoEdicao['status_pagamento'] ?? '') === 'CONFIRMADO' ? 'selected' : '' ?>>Confirmado</option>
                            <option value="CANCELADO" <?= ($pedidoEdicao['status_pagamento'] ?? '') === 'CANCELADO' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                            <i class="ri-save-line"></i>
                            Atualizar Status
                        </button>
                        <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="flex: 1;">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include_once "admin_footer.php"; ?>

<script>
    // Função para fechar modal e limpar URL
    function fecharModal() {
        const modais = document.querySelectorAll('.admin-modal');
        modais.forEach(modal => {
            modal.classList.remove('active');
        });
        // Remove parâmetros da URL sem recarregar a página
        const url = new URL(window.location.href);
        url.searchParams.delete('visualizar');
        url.searchParams.delete('editar');
        window.history.replaceState({}, '', url.toString());
    }

    // Garante que a função seja acessível globalmente
    window.fecharModal = fecharModal;

    // Função para visualizar pedido
    function visualizarPedido(id) {
        window.location.href = '<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos&visualizar=' + id;
    }

    // Função para atualizar status
    function atualizarStatus(id) {
        window.location.href = '<?= BASE_URL ?>/app/control/AdminController.php?acao=pedidos&editar=' + id;
    }

    // Função para abrir modal de filtros
    function abrirModalFiltros() {
        const filtros = <?= json_encode($filtros) ?>;
        const conteudo = `
        <form class="admin-form" method="GET" action="<?= BASE_URL ?>/app/control/AdminController.php">
            <input type="hidden" name="acao" value="pedidos">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Data Inicial</label>
                    <input type="date" class="admin-form-input" name="data_inicial" value="${filtros.data_inicial || ''}">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Data Final</label>
                    <input type="date" class="admin-form-input" name="data_final" value="${filtros.data_final || ''}">
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pedido</label>
                <select class="admin-form-select" name="status_pedido">
                    <option value="">Todos</option>
                    <option value="PENDENTE" ${filtros.status_pedido === 'PENDENTE' ? 'selected' : ''}>Pendente</option>
                    <option value="FINALIZADO" ${filtros.status_pedido === 'FINALIZADO' ? 'selected' : ''}>Finalizado</option>
                    <option value="CANCELADO" ${filtros.status_pedido === 'CANCELADO' ? 'selected' : ''}>Cancelado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pagamento</label>
                <select class="admin-form-select" name="status_pagamento">
                    <option value="">Todos</option>
                    <option value="PENDENTE" ${filtros.status_pagamento === 'PENDENTE' ? 'selected' : ''}>Pendente</option>
                    <option value="CONFIRMADO" ${filtros.status_pagamento === 'CONFIRMADO' ? 'selected' : ''}>Confirmado</option>
                    <option value="CANCELADO" ${filtros.status_pagamento === 'CANCELADO' ? 'selected' : ''}>Cancelado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Valor Mínimo</label>
                <input type="number" step="0.01" class="admin-form-input" name="valor_min" placeholder="R$ 0,00" value="${filtros.valor_min || ''}">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Valor Máximo</label>
                <input type="number" step="0.01" class="admin-form-input" name="valor_max" placeholder="R$ 0,00" value="${filtros.valor_max || ''}">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                    <i class="ri-filter-line"></i>
                    Aplicar Filtros
                </button>
                <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="flex: 1;">
                    Cancelar
                </button>
            </div>
        </form>
    `;
        abrirModal('Filtros Avançados', conteudo);
    }

    // Inicialização quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        // Fecha modal ao clicar fora dele
        const modais = document.querySelectorAll('.admin-modal');
        modais.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    fecharModal();
                }
            });
        });

        // Fecha modal ao pressionar ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                fecharModal();
            }
        });
    });


    function abrirModalFiltros() {
        const filtros = <?= json_encode($filtros) ?>;
        const conteudo = `
        <form class="admin-form" method="GET" action="<?= BASE_URL ?>/app/control/AdminController.php">
            <input type="hidden" name="acao" value="pedidos">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Data Inicial</label>
                    <input type="date" class="admin-form-input" name="data_inicial" value="${filtros.data_inicial || ''}">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Data Final</label>
                    <input type="date" class="admin-form-input" name="data_final" value="${filtros.data_final || ''}">
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pedido</label>
                <select class="admin-form-select" name="status_pedido">
                    <option value="">Todos</option>
                    <option value="PENDENTE" ${filtros.status_pedido === 'PENDENTE' ? 'selected' : ''}>Pendente</option>
                    <option value="FINALIZADO" ${filtros.status_pedido === 'FINALIZADO' ? 'selected' : ''}>Finalizado</option>
                    <option value="CANCELADO" ${filtros.status_pedido === 'CANCELADO' ? 'selected' : ''}>Cancelado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pagamento</label>
                <select class="admin-form-select" name="status_pagamento">
                    <option value="">Todos</option>
                    <option value="PENDENTE" ${filtros.status_pagamento === 'PENDENTE' ? 'selected' : ''}>Pendente</option>
                    <option value="CONFIRMADO" ${filtros.status_pagamento === 'CONFIRMADO' ? 'selected' : ''}>Confirmado</option>
                    <option value="CANCELADO" ${filtros.status_pagamento === 'CANCELADO' ? 'selected' : ''}>Cancelado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Valor Mínimo</label>
                <input type="number" step="0.01" class="admin-form-input" name="valor_min" placeholder="R$ 0,00" value="${filtros.valor_min || ''}">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Valor Máximo</label>
                <input type="number" step="0.01" class="admin-form-input" name="valor_max" placeholder="R$ 0,00" value="${filtros.valor_max || ''}">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                    <i class="ri-filter-line"></i>
                    Aplicar Filtros
                </button>
                <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="flex: 1;">
                    Cancelar
                </button>
            </div>
        </form>
    `;
        abrirModal('Filtros Avançados', conteudo);
    }
</script>
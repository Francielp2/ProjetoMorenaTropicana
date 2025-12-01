<?php
// Esta view recebe apenas variáveis prontas do controller
// $titulo_pagina - título da página
// $estoquesFormatados - array com todos os estoques formatados
// $resumoEstoque - array com resumo do estoque
// $produtos - array com todos os produtos
// $filtros - array com os filtros aplicados

include_once "admin_header.php";

// Pega mensagens de sucesso/erro da URL
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
$mensagemErro = isset($_GET['erro']) ? $_GET['erro'] : '';
$filtros = $filtros ?? ['termo' => '', 'status' => ''];

// Variáveis $estoqueEdicao e $estoqueAdicionar são passadas pelo controller
$estoqueEdicao = $estoqueEdicao ?? null;
$estoqueAdicionar = $estoqueAdicionar ?? null;

// Garante que estoquesFormatados existe e é um array
if (!isset($estoquesFormatados) || !is_array($estoquesFormatados)) {
    $estoquesFormatados = [];
}
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Controle de Estoque</h2>
        <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque&novo=1" class="admin-btn admin-btn-primary">
            <i class="ri-add-line"></i>
            Nova Entrada
        </a>
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
        <input type="hidden" name="acao" value="estoque">
        <input type="text" name="termo" class="admin-search-input" placeholder="Buscar por produto, tamanho ou cor..." value="<?= htmlspecialchars($filtros['termo']) ?>" style="flex: 1; min-width: 200px; max-width: 300px;">
        <button type="submit" class="admin-btn admin-btn-primary" style="min-width: 120px; flex-shrink: 0;">
            <i class="ri-search-line"></i>
            Pesquisar
        </button>
        <select class="admin-filter-select" name="status" onchange="this.form.submit()" style="min-width: 180px; max-width: 180px; flex-shrink: 0;">
            <option value="">Todos os Status</option>
            <option value="disponivel" <?= $filtros['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
            <option value="baixo" <?= $filtros['status'] === 'baixo' ? 'selected' : '' ?>>Estoque Baixo</option>
            <option value="critico" <?= $filtros['status'] === 'critico' ? 'selected' : '' ?>>Estoque Crítico</option>
            <option value="zerado" <?= $filtros['status'] === 'zerado' ? 'selected' : '' ?>>Sem Estoque</option>
        </select>
        <?php if (!empty($filtros['termo']) || !empty($filtros['status'])): ?>
            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque" class="admin-btn admin-btn-secondary" style="min-width: 120px;">
                Limpar Filtros
            </a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Tamanho</th>
                <th>Cor</th>
                <th>Quantidade</th>
                <th>Data Cadastro</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($estoquesFormatados)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum registro de estoque encontrado
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($estoquesFormatados as $estoque): ?>
                    <tr>
                        <td><?= htmlspecialchars($estoque['id']) ?></td>
                        <td><?= htmlspecialchars($estoque['produto']) ?></td>
                        <td><?= htmlspecialchars($estoque['tamanho']) ?></td>
                        <td><?= htmlspecialchars($estoque['cor']) ?></td>
                        <td><?= htmlspecialchars($estoque['quantidade']) ?></td>
                        <td><?= htmlspecialchars($estoque['data_cadastro']) ?></td>
                        <td><span class="admin-badge <?= htmlspecialchars($estoque['status_classe']) ?>"><?= htmlspecialchars($estoque['status']) ?></span></td>
                <td>
                    <div class="admin-table-actions">
                                <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque&adicionar=<?= $estoque['id'] ?>" class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque">
                            <i class="ri-add-line"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=estoque&editar=<?= $estoque['id'] ?>" class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar">
                            <i class="ri-edit-line"></i>
                                </a>
                    </div>
                </td>
            </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Resumo de Estoque -->
<div class="admin-content-card" style="margin-top: 2rem;">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Resumo do Estoque</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: var(--cor_primaria); margin-bottom: 0.5rem;"><?= htmlspecialchars($resumoEstoque['total_itens'] ?? 0) ?></div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Total de Itens</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: hsl(151, 87%, 36%); margin-bottom: 0.5rem;"><?= htmlspecialchars($resumoEstoque['disponiveis'] ?? 0) ?></div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Disponíveis</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: hsl(51, 100%, 67%); margin-bottom: 0.5rem;"><?= htmlspecialchars($resumoEstoque['estoque_baixo'] ?? 0) ?></div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Estoque Baixo</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: #d32f2f; margin-bottom: 0.5rem;"><?= htmlspecialchars($resumoEstoque['criticos_sem_estoque'] ?? 0) ?></div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Críticos/Sem Estoque</div>
        </div>
    </div>
</div>

<!-- Modal de Nova Entrada -->
<?php if (isset($_GET['novo'])): ?>
    <div id="modalNovaEntrada" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 class="admin-modal-title">Nova Entrada de Estoque</h2>
                <button class="admin-modal-close" onclick="fecharModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=cadastrarEntradaEstoque" class="admin-form">
            <div class="admin-form-group">
                <label class="admin-form-label">Produto</label>
                        <select class="admin-form-select" name="id_produto" required>
                    <option value="">Selecione um produto...</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?= htmlspecialchars($produto['id_produto']) ?>">
                                    <?= htmlspecialchars($produto['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                </select>
            </div>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Tamanhos Disponíveis</label>
                            <input type="text" class="admin-form-input" name="tamanhos" placeholder="Ex: P, M, G, GG" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Cores Disponíveis</label>
                            <input type="text" class="admin-form-input" name="cores" placeholder="Ex: Branco, Preto, Rosa" required>
                        </div>
                    </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Modelo do Produto</label>
                        <input type="text" class="admin-form-input" name="modelo" placeholder="Ex: Modelo específico (opcional)">
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Quantidade</label>
                    <input type="number" class="admin-form-input" name="quantidade" min="1" required>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Data de Entrada</label>
                    <input type="date" class="admin-form-input" name="data" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                    <i class="ri-save-line"></i>
                    Registrar Entrada
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

<!-- Modal de Adicionar Quantidade -->
<?php if (isset($_GET['adicionar']) && !empty($estoqueAdicionar)): ?>
    <div id="modalAdicionar" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 class="admin-modal-title">Adicionar Estoque</h2>
                <button class="admin-modal-close" onclick="fecharModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=adicionarQuantidadeEstoque" class="admin-form">
                    <input type="hidden" name="id" value="<?= $estoqueAdicionar['id'] ?>">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Produto</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($estoqueAdicionar['produto']) ?>" readonly>
                    </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Quantidade Atual</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($estoqueAdicionar['quantidade_atual']) ?>" readonly>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Quantidade a Adicionar</label>
                <input type="number" class="admin-form-input" name="quantidade" min="1" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Data da Entrada</label>
                <input type="date" class="admin-form-input" name="data" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                    <i class="ri-add-line"></i>
                    Adicionar ao Estoque
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

<!-- Modal de Edição -->
<?php if (isset($_GET['editar']) && !empty($estoqueEdicao)): ?>
    <div id="modalEdicao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 class="admin-modal-title">Editar Estoque</h2>
                <button class="admin-modal-close" onclick="fecharModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=atualizarEstoque" class="admin-form">
                    <input type="hidden" name="id" value="<?= $estoqueEdicao['id'] ?>">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Produto</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($estoqueEdicao['produto']) ?>" readonly>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Tamanhos Disponíveis</label>
                            <input type="text" class="admin-form-input" name="tamanhos" value="<?= htmlspecialchars($estoqueEdicao['tamanhos']) ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Cores Disponíveis</label>
                            <input type="text" class="admin-form-input" name="cores" value="<?= htmlspecialchars($estoqueEdicao['cores']) ?>" required>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Modelo do Produto</label>
                        <input type="text" class="admin-form-input" name="modelo" value="<?= htmlspecialchars($estoqueEdicao['modelo']) ?>">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Quantidade</label>
                        <input type="number" class="admin-form-input" name="quantidade" value="<?= htmlspecialchars($estoqueEdicao['quantidade']) ?>" min="0" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Data de Cadastro</label>
                        <input type="date" class="admin-form-input" name="data" value="<?= htmlspecialchars($estoqueEdicao['data_cadastro']) ?>" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                    <i class="ri-save-line"></i>
                    Atualizar
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
        url.searchParams.delete('novo');
        url.searchParams.delete('editar');
        url.searchParams.delete('adicionar');
        window.history.replaceState({}, '', url.toString());
    }

    // Garante que a função seja acessível globalmente
    window.fecharModal = fecharModal;

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
</script>
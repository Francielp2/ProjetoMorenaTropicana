<?php
// Esta view recebe apenas variáveis prontas do controller
// $titulo_pagina - título da página
// $produtosFormatados - array com todos os produtos formatados
// $categorias - array com todas as categorias
// $filtros - array com os filtros aplicados (termo, categoria)

include_once "admin_header.php";

// Pega mensagens de sucesso/erro da URL
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
$mensagemErro = isset($_GET['erro']) ? $_GET['erro'] : '';
$filtros = $filtros ?? ['termo' => '', 'categoria' => ''];

// Variáveis $produtoEdicao e $produtoVisualizacao são passadas pelo controller
$produtoEdicao = $produtoEdicao ?? null;
$produtoVisualizacao = $produtoVisualizacao ?? null;
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Lista de Produtos</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalCadastro()">
            <i class="ri-add-line"></i>
            Novo Produto
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
    <form method="GET" action="<?= BASE_URL ?>/app/control/AdminController.php" class="admin-search-bar">
        <input type="hidden" name="acao" value="produtos">
        <input type="text" name="termo" class="admin-search-input" placeholder="Buscar por nome ou categoria..." value="<?= htmlspecialchars($filtros['termo']) ?>">
        <button type="submit" class="admin-btn admin-btn-primary" style="min-width: 120px;">
            <i class="ri-search-line"></i>
            Pesquisar
        </button>
        <select class="admin-filter-select" name="categoria" onchange="this.form.submit()">
            <option value="">Todas as Categorias</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $filtros['categoria'] === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($filtros['termo']) || !empty($filtros['categoria'])): ?>
            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=produtos" class="admin-btn admin-btn-secondary" style="min-width: 100px;">
                Limpar Filtros
            </a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produtosFormatados)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum produto encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($produtosFormatados as $produto): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['id']) ?></td>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= htmlspecialchars($produto['categoria']) ?></td>
                        <td><?= htmlspecialchars($produto['preco']) ?></td>
                        <td><?= htmlspecialchars($produto['estoque']) ?></td>
                        <td><span class="admin-badge <?= htmlspecialchars($produto['status_classe']) ?>"><?= htmlspecialchars($produto['status_estoque']) ?></span></td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=produtos&visualizar=<?= $produto['id'] ?><?= !empty($filtros['termo']) ? '&termo=' . urlencode($filtros['termo']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?>"
                                    class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=produtos&editar=<?= $produto['id'] ?><?= !empty($filtros['termo']) ? '&termo=' . urlencode($filtros['termo']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?>"
                                    class="admin-btn admin-btn-icon admin-btn-primary" title="Editar">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=excluirProduto" style="display: inline;" onsubmit="return confirmarExclusao()">
                                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                    <button type="submit" class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Cadastro -->
<?php if (isset($_GET['novo'])): ?>
    <div id="modalCadastro" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3>Cadastrar Novo Produto</h3>
                <button class="admin-modal-close" onclick="fecharModal()">&times;</button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=cadastrarProduto" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">

                    <div class="admin-form-group" style="margin: 0;">
                        <label class="admin-form-label">Nome do Produto</label>
                        <input type="text" class="admin-form-input" name="nome" required>
                    </div>

                    <div class="admin-form-group" style="margin: 0;">
                        <label class="admin-form-label">Descrição</label>
                        <textarea class="admin-form-textarea" name="descricao" required></textarea>
                    </div>

                    <div class="admin-form-row" style="margin: 0; gap: 1rem;">
                        <div class="admin-form-group" style="margin: 0; flex: 1;">
                            <label class="admin-form-label">Categoria</label>
                            <input type="text" class="admin-form-input" name="categoria" placeholder="Ex: Vestidos, Blusas..." required>
                        </div>
                        <div class="admin-form-group" style="margin: 0; flex: 1;">
                            <label class="admin-form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="admin-form-input" name="preco" min="0.01" required>
                        </div>
                    </div>


                    <div class="admin-form-group" style="margin: 0;">
                        <label class="admin-form-label">Imagem do Produto</label>
                        <input type="file" class="admin-form-input" name="imagem" accept="image/*">
                        <small style="color: #666; font-size: 0.875rem; margin-top: 0.25rem; display: block;">Formatos aceitos: JPG, PNG, GIF, WEBP (máx. 5MB)</small>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                        <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                            <i class="ri-save-line"></i>
                            Salvar
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
<?php if (isset($_GET['editar']) && !empty($produtoEdicao)): ?>
    <div id="modalEdicao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3>Editar Produto</h3>
                <button class="admin-modal-close" onclick="fecharModal()">&times;</button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=atualizarProduto" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($produtoEdicao['id']) ?>">

                    <div class="admin-form-group" style="margin: 0;">
                        <label class="admin-form-label">Nome do Produto</label>
                        <input type="text" class="admin-form-input" name="nome" value="<?= htmlspecialchars($produtoEdicao['nome']) ?>" required>
                    </div>

                    <div class="admin-form-group" style="margin: 0;">
                        <label class="admin-form-label">Descrição</label>
                        <textarea class="admin-form-textarea" name="descricao" required><?= htmlspecialchars($produtoEdicao['descricao']) ?></textarea>
                    </div>

                    <div class="admin-form-row" style="margin: 0; gap: 1rem;">
                        <div class="admin-form-group" style="margin: 0; flex: 1;">
                            <label class="admin-form-label">Categoria</label>
                            <input type="text" class="admin-form-input" name="categoria" value="<?= htmlspecialchars($produtoEdicao['categoria']) ?>" required>
                        </div>
                        <div class="admin-form-group" style="margin: 0; flex: 1;">
                            <label class="admin-form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="admin-form-input" name="preco" value="<?= htmlspecialchars($produtoEdicao['preco']) ?>" min="0.01" required>
                        </div>
                    </div>


                    <div class="admin-form-group" style="margin: 0;">
                        <label class="admin-form-label">Imagem do Produto</label>
                        <?php if (!empty($produtoEdicao['imagem'])): ?>
                            <div style="margin-bottom: 0.5rem;">
                                <img src="<?= BASE_URL . htmlspecialchars($produtoEdicao['imagem']) ?>" alt="Imagem atual" style="max-width: 200px; max-height: 200px; border-radius: 5px; border: 1px solid #e0e0e0;">
                                <p style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">Imagem atual</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="admin-form-input" name="imagem" accept="image/*">
                        <small style="color: #666; font-size: 0.875rem; margin-top: 0.25rem; display: block;">Deixe em branco para manter a imagem atual. Formatos aceitos: JPG, PNG, GIF, WEBP (máx. 5MB)</small>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
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

<!-- Modal de Visualização -->
<?php if (isset($_GET['visualizar']) && !empty($produtoVisualizacao)): ?>
    <div id="modalVisualizacao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3>Detalhes do Produto</h3>
                <button class="admin-modal-close" onclick="fecharModal()">&times;</button>
            </div>
            <div class="admin-modal-body">
                <div class="admin-form">
                    <div class="admin-form-group">
                        <label class="admin-form-label">ID</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($produtoVisualizacao['id']) ?>" readonly>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Nome</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($produtoVisualizacao['nome']) ?>" readonly>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Descrição</label>
                        <textarea class="admin-form-textarea" readonly><?= htmlspecialchars($produtoVisualizacao['descricao']) ?></textarea>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Categoria</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($produtoVisualizacao['categoria']) ?>" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Preço</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($produtoVisualizacao['preco']) ?>" readonly>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Estoque Total</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($produtoVisualizacao['estoque_total']) ?> unidades" readonly>
                    </div>
                    <?php if (!empty($produtoVisualizacao['imagem'])): ?>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Imagem do Produto</label>
                            <div style="text-align: center;">
                                <img src="<?= BASE_URL . htmlspecialchars($produtoVisualizacao['imagem']) ?>" alt="Imagem do produto" style="max-width: 300px; max-height: 300px; border-radius: 5px; border: 1px solid #e0e0e0;">
                            </div>
                        </div>
                    <?php endif; ?>
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

<?php include_once "admin_footer.php"; ?>

<script>
    // Função para abrir modal de cadastro
    function abrirModalCadastro() {
        window.location.href = '<?= BASE_URL ?>/app/control/AdminController.php?acao=produtos&novo=1<?= !empty($filtros['termo']) ? '&termo=' . urlencode($filtros['termo']) : '' ?><?= !empty($filtros['categoria']) ? '&categoria=' . urlencode($filtros['categoria']) : '' ?>';
    }

    // Função para fechar modal
    function fecharModal() {
        const modais = document.querySelectorAll('.admin-modal');
        modais.forEach(modal => {
            modal.classList.remove('active');
        });
        const url = new URL(window.location.href);
        url.searchParams.delete('novo');
        url.searchParams.delete('editar');
        url.searchParams.delete('visualizar');
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

    // Função para confirmar exclusão
    function confirmarExclusao() {
        if (!confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')) {
            return false;
        }
        if (!confirm('ATENÇÃO: Todos os dados deste produto, incluindo registros de estoque, serão perdidos permanentemente.\n\nConfirma a exclusão?')) {
            return false;
        }
        return true;
    }
</script>
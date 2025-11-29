<?php
// Esta view recebe apenas variáveis prontas do controller
// $titulo_pagina - título da página
// $usuariosFormatados - array com todos os usuários formatados
// $filtros - array com os filtros aplicados (termo, tipo, status)

include_once "admin_header.php";

// Pega mensagens de sucesso/erro da URL
$mensagemSucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
$mensagemErro = isset($_GET['erro']) ? $_GET['erro'] : '';
$filtros = $filtros ?? ['termo' => '', 'tipo' => '', 'status' => ''];

// Variáveis $usuarioEdicao e $usuarioVisualizacao são passadas pelo controller
$usuarioEdicao = $usuarioEdicao ?? null;
$usuarioVisualizacao = $usuarioVisualizacao ?? null;
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Lista de Usuários</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalCadastro()">
            <i class="ri-add-line"></i>
            Novo Usuário
        </button>
    </div>

    <!-- Mensagens de sucesso/erro -->
    <?php if (!empty($mensagemSucesso)): ?>
        <div class="admin-alert admin-alert-success" style="margin-bottom: 1rem;">
            <?= htmlspecialchars($mensagemSucesso) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensagemErro)): ?>
        <div class="admin-alert admin-alert-danger" style="margin-bottom: 1rem;">
            <?= htmlspecialchars($mensagemErro) ?>
        </div>
    <?php endif; ?>

    <!-- Barra de Busca e Filtros -->
    <form method="GET" action="<?= BASE_URL ?>/app/control/AdminController.php" class="admin-search-bar">
        <input type="hidden" name="acao" value="usuarios">
        <input type="text" name="termo" class="admin-search-input" placeholder="Buscar por nome ou email..." value="<?= htmlspecialchars($filtros['termo']) ?>">
        <button type="submit" class="admin-btn admin-btn-primary" style="min-width: 120px;">
            <i class="ri-search-line"></i>
            Pesquisar
        </button>
        <select class="admin-filter-select" name="status" onchange="this.form.submit()">
            <option value="">Todos os Status</option>
            <option value="Ativo" <?= $filtros['status'] === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
            <option value="Suspenso" <?= $filtros['status'] === 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
        </select>
        <select class="admin-filter-select" name="tipo" onchange="this.form.submit()">
            <option value="">Todos os Tipos</option>
            <option value="CLIENTE" <?= $filtros['tipo'] === 'CLIENTE' ? 'selected' : '' ?>>Cliente</option>
            <option value="ADMIN" <?= $filtros['tipo'] === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
        </select>
        <?php if (!empty($filtros['termo']) || !empty($filtros['tipo']) || !empty($filtros['status'])): ?>
            <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios" class="admin-btn admin-btn-secondary" style="min-width: 100px;">
                Limpar Filtros
            </a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuariosFormatados)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                        Nenhum usuário encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuariosFormatados as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['cpf']) ?></td>
                        <td><span class="admin-badge <?= htmlspecialchars($usuario['tipo_classe']) ?>"><?= htmlspecialchars($usuario['tipo']) ?></span></td>
                        <td><span class="admin-badge <?= htmlspecialchars($usuario['status_classe']) ?>"><?= htmlspecialchars($usuario['status']) ?></span></td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios&visualizar=<?= $usuario['id'] ?><?= !empty($filtros['termo']) ? '&termo=' . urlencode($filtros['termo']) : '' ?><?= !empty($filtros['tipo']) ? '&tipo=' . urlencode($filtros['tipo']) : '' ?><?= !empty($filtros['status']) ? '&status=' . urlencode($filtros['status']) : '' ?>"
                                    class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios&editar=<?= $usuario['id'] ?><?= !empty($filtros['termo']) ? '&termo=' . urlencode($filtros['termo']) : '' ?><?= !empty($filtros['tipo']) ? '&tipo=' . urlencode($filtros['tipo']) : '' ?><?= !empty($filtros['status']) ? '&status=' . urlencode($filtros['status']) : '' ?>"
                                    class="admin-btn admin-btn-icon admin-btn-primary" title="Editar">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=excluirUsuario" style="display: inline;" onsubmit="return confirmarExclusao()">
                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
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
                <h3>Cadastrar Novo Usuário</h3>
                <button class="admin-modal-close" onclick="fecharModal()">&times;</button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=cadastrarUsuario" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Dados Pessoais -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <h4 style="margin: 0; color: #333; font-size: 1rem; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid #e0e0e0;">Dados Pessoais</h4>

                        <div class="admin-form-group" style="margin: 0;">
                            <label class="admin-form-label">Nome Completo</label>
                            <input type="text" class="admin-form-input" name="nome" required>
                        </div>

                        <div class="admin-form-row" style="margin: 0; gap: 1rem;">
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">Email</label>
                                <input type="email" class="admin-form-input" name="email" required>
                            </div>
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">CPF</label>
                                <input type="text" class="admin-form-input" name="cpf" placeholder="Apenas números (11 dígitos)" maxlength="11" required>
                            </div>
                        </div>

                        <div class="admin-form-group" id="campoTelefone" style="display: block; margin: 0;">
                            <label class="admin-form-label">Telefone</label>
                            <input type="text" class="admin-form-input" name="telefone" placeholder="Apenas números (opcional)" maxlength="11">
                        </div>
                    </div>

                    <!-- Configurações de Acesso -->
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
                        <h4 style="margin: 0; color: #333; font-size: 1rem; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid #e0e0e0;">Configurações de Acesso</h4>

                        <div class="admin-form-row" style="margin: 0; gap: 1rem;">
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">Senha</label>
                                <input type="password" class="admin-form-input" name="senha" required minlength="6">
                                <small style="color: #666; font-size: 0.85em; margin-top: 0.25rem; display: block;">Mínimo 6 caracteres</small>
                            </div>
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">Tipo de Usuário</label>
                                <select class="admin-form-select" name="tipo" id="tipoUsuario" required onchange="atualizarCamposCadastro()">
                                    <option value="CLIENTE">Cliente</option>
                                    <option value="ADMIN">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div class="admin-form-group" id="campoStatus" style="display: block; margin: 0;">
                            <label class="admin-form-label">Status</label>
                            <select class="admin-form-select" name="status" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Suspenso">Suspenso</option>
                            </select>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
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
<?php if (isset($_GET['editar']) && !empty($usuarioEdicao)): ?>
    <div id="modalEdicao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3>Editar Usuário</h3>
                <button class="admin-modal-close" onclick="fecharModal()">&times;</button>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form method="POST" action="<?= BASE_URL ?>/app/control/AdminController.php?acao=atualizarUsuario" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($usuarioEdicao['id']) ?>">

                    <!-- Dados Pessoais -->
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <h4 style="margin: 0; color: #333; font-size: 1rem; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid #e0e0e0;">Dados Pessoais</h4>

                        <div class="admin-form-group" style="margin: 0;">
                            <label class="admin-form-label">Nome Completo</label>
                            <input type="text" class="admin-form-input" name="nome" value="<?= htmlspecialchars($usuarioEdicao['nome']) ?>" required>
                        </div>

                        <div class="admin-form-row" style="margin: 0; gap: 1rem;">
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">Email</label>
                                <input type="email" class="admin-form-input" name="email" value="<?= htmlspecialchars($usuarioEdicao['email']) ?>" required>
                            </div>
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">CPF</label>
                                <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioEdicao['cpf']) ?>" readonly>
                                <small style="color: #666; font-size: 0.85em; margin-top: 0.25rem; display: block;">O CPF não pode ser alterado</small>
                            </div>
                        </div>

                        <?php if ($usuarioEdicao['tipo'] === 'CLIENTE'): ?>
                            <div class="admin-form-group" style="margin: 0;">
                                <label class="admin-form-label">Telefone</label>
                                <input type="text" class="admin-form-input" name="telefone"
                                    value="<?= htmlspecialchars(preg_replace('/[^0-9]/', '', $usuarioEdicao['telefone'])) ?>"
                                    placeholder="Apenas números (opcional)" maxlength="11">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Configurações de Acesso -->
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
                        <h4 style="margin: 0; color: #333; font-size: 1rem; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid #e0e0e0;">Configurações de Acesso</h4>

                        <div class="admin-form-row" style="margin: 0; gap: 1rem;">
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">Nova Senha</label>
                                <input type="password" class="admin-form-input" name="senha" placeholder="Deixe em branco para manter a senha atual">
                                <small style="color: #666; font-size: 0.85em; margin-top: 0.25rem; display: block;">Deixe em branco para manter a senha atual</small>
                            </div>
                            <div class="admin-form-group" style="margin: 0; flex: 1;">
                                <label class="admin-form-label">Tipo de Usuário</label>
                                <select class="admin-form-select" name="tipo" required>
                                    <option value="CLIENTE" <?= $usuarioEdicao['tipo'] === 'CLIENTE' ? 'selected' : '' ?>>Cliente</option>
                                    <option value="ADMIN" <?= $usuarioEdicao['tipo'] === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                            </div>
                        </div>

                        <?php if ($usuarioEdicao['tipo'] === 'CLIENTE'): ?>
                            <div class="admin-form-group" style="margin: 0;">
                                <label class="admin-form-label">Status</label>
                                <select class="admin-form-select" name="status" required>
                                    <option value="Ativo" <?= $usuarioEdicao['status'] === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                                    <option value="Suspenso" <?= $usuarioEdicao['status'] === 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botões de Ação -->
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
<?php if (isset($_GET['visualizar']) && !empty($usuarioVisualizacao)): ?>
    <div id="modalVisualizacao" class="admin-modal active">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3>Detalhes do Usuário</h3>
                <button class="admin-modal-close" onclick="fecharModal()">&times;</button>
            </div>
            <div class="admin-modal-body">
                <div class="admin-form">
                    <div class="admin-form-group">
                        <label class="admin-form-label">ID</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['id']) ?>" readonly>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Nome Completo</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['nome']) ?>" readonly>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Email</label>
                        <input type="email" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['email']) ?>" readonly>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">CPF</label>
                        <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['cpf']) ?>" readonly>
                    </div>
                    <?php if (!empty($usuarioVisualizacao['telefone'])): ?>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Telefone</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['telefone']) ?>" readonly>
                        </div>
                    <?php endif; ?>
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Tipo</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['tipo']) ?>" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Status</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['status']) ?>" readonly>
                        </div>
                    </div>
                    <?php if (!empty($usuarioVisualizacao['data_contratacao'])): ?>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Data de Contratação</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars($usuarioVisualizacao['data_contratacao']) ?>" readonly>
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
        window.location.href = '<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios&novo=1<?= !empty($filtros['termo']) ? '&termo=' . urlencode($filtros['termo']) : '' ?><?= !empty($filtros['tipo']) ? '&tipo=' . urlencode($filtros['tipo']) : '' ?><?= !empty($filtros['status']) ? '&status=' . urlencode($filtros['status']) : '' ?>';
    }

    // Sobrescreve a função fecharModal do footer para fechar todos os modais
    // Esta função é executada após o footer ser carregado, então sobrescreve a função do footer
    function fecharModal() {
        // Fecha todos os modais (tanto inline quanto o global)
        const modais = document.querySelectorAll('.admin-modal');
        modais.forEach(modal => {
            modal.classList.remove('active');
        });
        // Remove parâmetros da URL sem recarregar a página
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

    // Função para atualizar campos do formulário baseado no tipo
    function atualizarCamposCadastro() {
        const tipo = document.getElementById('tipoUsuario').value;
        const campoTelefone = document.getElementById('campoTelefone');
        const campoStatus = document.getElementById('campoStatus');
        const selectStatus = campoStatus ? campoStatus.querySelector('select') : null;

        if (tipo === 'CLIENTE') {
            if (campoTelefone) campoTelefone.style.display = 'block';
            if (campoStatus) campoStatus.style.display = 'block';
            if (selectStatus) selectStatus.required = true;
        } else {
            if (campoTelefone) campoTelefone.style.display = 'none';
            if (campoStatus) campoStatus.style.display = 'none';
            if (selectStatus) selectStatus.required = false;
        }
    }

    // Função para confirmar exclusão
    function confirmarExclusao() {
        if (!confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
            return false;
        }
        if (!confirm('ATENÇÃO: Todos os dados deste usuário serão perdidos permanentemente.\n\nIsso inclui:\n- Dados pessoais\n- Pedidos relacionados\n- Endereços\n- Outras informações\n\nConfirma a exclusão?')) {
            return false;
        }
        return true;
    }
</script>
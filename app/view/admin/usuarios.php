<?php
$titulo_pagina = "Gerenciamento de Usuários";
include_once "admin_header.php";
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Lista de Usuários</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalCadastro()">
            <i class="ri-add-line"></i>
            Novo Usuário
        </button>
    </div>

    <!-- Barra de Busca e Filtros -->
    <div class="admin-search-bar">
        <input type="text" class="admin-search-input" placeholder="Buscar por nome, email ou CPF...">
        <select class="admin-filter-select">
            <option value="">Todos os Status</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
            <option value="suspenso">Suspenso</option>
            <option value="cancelado">Cancelado</option>
        </select>
        <select class="admin-filter-select">
            <option value="">Todos os Tipos</option>
            <option value="cliente">Cliente</option>
            <option value="admin">Administrador</option>
        </select>
    </div>

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
            <tr>
                <td>1</td>
                <td>Maria Silva</td>
                <td>maria.silva@email.com</td>
                <td>123.456.789-00</td>
                <td><span class="admin-badge admin-badge-info">Cliente</span></td>
                <td><span class="admin-badge admin-badge-success">Ativo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(1)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(1)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(1)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Ana Costa</td>
                <td>ana.costa@email.com</td>
                <td>987.654.321-00</td>
                <td><span class="admin-badge admin-badge-info">Cliente</span></td>
                <td><span class="admin-badge admin-badge-success">Ativo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(2)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(2)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(2)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>João Administrador</td>
                <td>joao.admin@email.com</td>
                <td>111.222.333-44</td>
                <td><span class="admin-badge admin-badge-primary">Admin</span></td>
                <td><span class="admin-badge admin-badge-success">Ativo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(3)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(3)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(3)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Paula Oliveira</td>
                <td>paula.oliveira@email.com</td>
                <td>555.666.777-88</td>
                <td><span class="admin-badge admin-badge-info">Cliente</span></td>
                <td><span class="admin-badge admin-badge-warning">Suspenso</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(4)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(4)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(4)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Carlos Santos</td>
                <td>carlos.santos@email.com</td>
                <td>999.888.777-66</td>
                <td><span class="admin-badge admin-badge-info">Cliente</span></td>
                <td><span class="admin-badge admin-badge-danger">Inativo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(5)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(5)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(5)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    function abrirModalCadastro() {
        const conteudo = `
        <form class="admin-form" onsubmit="salvarUsuario(event)">
            <div class="admin-form-group">
                <label class="admin-form-label">Nome Completo</label>
                <input type="text" class="admin-form-input" name="nome" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Email</label>
                <input type="email" class="admin-form-input" name="email" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">CPF</label>
                <input type="text" class="admin-form-input" name="cpf" required>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Senha</label>
                    <input type="password" class="admin-form-input" name="senha" required>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Tipo de Usuário</label>
                    <select class="admin-form-select" name="tipo" required>
                        <option value="CLIENTE">Cliente</option>
                        <option value="ADMIN">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status</label>
                <select class="admin-form-select" name="status" required>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                    <option value="Suspenso">Suspenso</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="admin-btn admin-btn-primary" style="flex: 1;">
                    <i class="ri-save-line"></i>
                    Salvar
                </button>
                <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="flex: 1;">
                    Cancelar
                </button>
            </div>
        </form>
    `;
        abrirModal('Cadastrar Novo Usuário', conteudo);
    }

    function editarUsuario(id) {
        const conteudo = `
        <form class="admin-form" onsubmit="atualizarUsuario(event, ${id})">
            <div class="admin-form-group">
                <label class="admin-form-label">Nome Completo</label>
                <input type="text" class="admin-form-input" name="nome" value="Nome do Usuário ${id}" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Email</label>
                <input type="email" class="admin-form-input" name="email" value="usuario${id}@email.com" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">CPF</label>
                <input type="text" class="admin-form-input" name="cpf" value="123.456.789-00" required>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Nova Senha (deixe em branco para manter)</label>
                    <input type="password" class="admin-form-input" name="senha">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Tipo de Usuário</label>
                    <select class="admin-form-select" name="tipo" required>
                        <option value="CLIENTE">Cliente</option>
                        <option value="ADMIN">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status</label>
                <select class="admin-form-select" name="status" required>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                    <option value="Suspenso">Suspenso</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
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
    `;
        abrirModal('Editar Usuário', conteudo);
    }

    function visualizarUsuario(id) {
        const conteudo = `
        <div class="admin-form">
            <div class="admin-form-group">
                <label class="admin-form-label">Nome Completo</label>
                <input type="text" class="admin-form-input" value="Nome do Usuário ${id}" readonly>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Email</label>
                <input type="email" class="admin-form-input" value="usuario${id}@email.com" readonly>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">CPF</label>
                <input type="text" class="admin-form-input" value="123.456.789-00" readonly>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Tipo</label>
                    <input type="text" class="admin-form-input" value="Cliente" readonly>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Status</label>
                    <input type="text" class="admin-form-input" value="Ativo" readonly>
                </div>
            </div>
            <div style="margin-top: 1.5rem;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="width: 100%;">
                    Fechar
                </button>
            </div>
        </div>
    `;
        abrirModal('Detalhes do Usuário', conteudo);
    }

    function excluirUsuario(id) {
        if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
            alert('Usuário excluído com sucesso! (Funcionalidade será implementada)');
            // Aqui você implementaria a lógica de exclusão
        }
    }

    function salvarUsuario(event) {
        event.preventDefault();
        alert('Usuário cadastrado com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }

    function atualizarUsuario(event, id) {
        event.preventDefault();
        alert('Usuário atualizado com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }
</script>

<?php include_once "admin_footer.php"; ?>
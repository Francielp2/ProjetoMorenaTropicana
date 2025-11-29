<?php
// Esta view recebe apenas variáveis prontas do controller
// $titulo_pagina - título da página
// $usuariosFormatados - array com todos os usuários formatados

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
        <input type="text" id="inputPesquisa" class="admin-search-input" placeholder="Buscar por nome ou email..." onkeypress="if(event.key === 'Enter') pesquisarUsuarios()">
        <button class="admin-btn admin-btn-primary" onclick="pesquisarUsuarios()" style="min-width: 120px;">
            <i class="ri-search-line"></i>
            Pesquisar
        </button>
        <select class="admin-filter-select" id="filtroStatus" onchange="aplicarFiltros()">
            <option value="">Todos os Status</option>
            <option value="Ativo">Ativo</option>
            <option value="Suspenso">Suspenso</option>
        </select>
        <select class="admin-filter-select" id="filtroTipo" onchange="aplicarFiltros()">
            <option value="">Todos os Tipos</option>
            <option value="CLIENTE">Cliente</option>
            <option value="ADMIN">Administrador</option>
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
        <tbody id="tbodyUsuarios">
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
                                <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(<?= $usuario['id'] ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(<?= $usuario['id'] ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(<?= $usuario['id'] ?>)">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function abrirModalCadastro() {
        const conteudo = `
        <form class="admin-form" id="formCadastroUsuario" onsubmit="salvarUsuario(event)">
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
                <input type="text" class="admin-form-input" name="cpf" placeholder="Apenas números (11 dígitos)" maxlength="11" required>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Senha</label>
                    <input type="password" class="admin-form-input" name="senha" minlength="6" required>
                    <small style="color: #666; font-size: 0.9em;">Mínimo 6 caracteres</small>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Tipo de Usuário</label>
                    <select class="admin-form-select" name="tipo" id="tipoUsuario" required onchange="atualizarCamposCadastro()">
                        <option value="CLIENTE">Cliente</option>
                        <option value="ADMIN">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="admin-form-group" id="campoTelefone" style="display: block;">
                <label class="admin-form-label">Telefone</label>
                <input type="text" class="admin-form-input" name="telefone" placeholder="Apenas números (opcional)" maxlength="11">
            </div>
            <div class="admin-form-group" id="campoStatus" style="display: block;">
                <label class="admin-form-label">Status</label>
                <select class="admin-form-select" name="status" required>
                    <option value="Ativo">Ativo</option>
                    <option value="Suspenso">Suspenso</option>
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

    function atualizarCamposCadastro() {
        const tipo = document.getElementById('tipoUsuario').value;
        const campoTelefone = document.getElementById('campoTelefone');
        const campoStatus = document.getElementById('campoStatus');

        if (tipo === 'CLIENTE') {
            campoTelefone.style.display = 'block';
            campoStatus.style.display = 'block';
        } else {
            campoTelefone.style.display = 'none';
            campoStatus.style.display = 'none';
        }
    }

    function editarUsuario(id) {
        // Mostra loading no modal
        abrirModal('Editar Usuário', '<div style="text-align: center; padding: 20px;">Carregando dados...</div>');

        // Busca dados reais do usuário
        fetch('<?= BASE_URL ?>/app/control/AdminController.php?acao=buscarUsuario&id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    abrirModal('Erro', '<div style="text-align: center; padding: 20px; color: #d32f2f;">' + data.erro + '</div>');
                    return;
                }

                // Função para escapar HTML
                function escapeHtml(text) {
                    if (!text) return '';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Determina tipo selecionado
                const tipoSelecionado = data.tipo === 'Administrador' ? 'ADMIN' : 'CLIENTE';
                const tipoClienteSelected = tipoSelecionado === 'CLIENTE' ? 'selected' : '';
                const tipoAdminSelected = tipoSelecionado === 'ADMIN' ? 'selected' : '';

                // Monta opções de status
                const statusOptions = ['Ativo', 'Suspenso'];
                let statusOptionsHtml = '';
                statusOptions.forEach(status => {
                    const selected = data.status === status ? 'selected' : '';
                    statusOptionsHtml += `<option value="${status}" ${selected}>${status}</option>`;
                });

                // Monta o formulário com os dados reais
                const conteudo = `
                    <form class="admin-form" onsubmit="atualizarUsuario(event, ${id})">
                        <input type="hidden" name="id" value="${id}">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Nome Completo</label>
                            <input type="text" class="admin-form-input" name="nome" value="${escapeHtml(data.nome || '')}" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Email</label>
                            <input type="email" class="admin-form-input" name="email" value="${escapeHtml(data.email || '')}" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">CPF</label>
                            <input type="text" class="admin-form-input" value="${escapeHtml(data.cpf || '')}" readonly>
                            <small style="color: #666; font-size: 0.9em;">O CPF não pode ser alterado</small>
                        </div>
                        ${tipoSelecionado === 'CLIENTE' ? `
                        <div class="admin-form-group">
                            <label class="admin-form-label">Telefone</label>
                            <input type="text" class="admin-form-input" name="telefone" value="${escapeHtml((data.telefone || '').replace(/\D/g, ''))}" placeholder="Apenas números (opcional)" maxlength="11">
                        </div>
                        ` : ''}
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label class="admin-form-label">Nova Senha (deixe em branco para manter)</label>
                                <input type="password" class="admin-form-input" name="senha" placeholder="Deixe em branco para manter a senha atual">
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">Tipo de Usuário</label>
                                <select class="admin-form-select" name="tipo" required>
                                    <option value="CLIENTE" ${tipoClienteSelected}>Cliente</option>
                                    <option value="ADMIN" ${tipoAdminSelected}>Administrador</option>
                                </select>
                            </div>
                        </div>
                        ${tipoSelecionado === 'CLIENTE' ? `
                        <div class="admin-form-group">
                            <label class="admin-form-label">Status</label>
                            <select class="admin-form-select" name="status" required>
                                ${statusOptionsHtml}
                            </select>
                        </div>
                        ` : ''}
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
            })
            .catch(error => {
                abrirModal('Erro', '<div style="text-align: center; padding: 20px; color: #d32f2f;">Erro ao carregar dados do usuário.</div>');
            });
    }

    function visualizarUsuario(id) {
        // Mostra loading no modal
        abrirModal('Detalhes do Usuário', '<div style="text-align: center; padding: 20px;">Carregando dados...</div>');

        // Busca dados reais do usuário
        fetch('<?= BASE_URL ?>/app/control/AdminController.php?acao=buscarUsuario&id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    abrirModal('Erro', '<div style="text-align: center; padding: 20px; color: #d32f2f;">' + data.erro + '</div>');
                    return;
                }

                // Função para escapar HTML (segurança)
                function escapeHtml(text) {
                    if (!text) return '';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Monta o conteúdo do modal com os dados reais
                const telefoneHtml = data.telefone ? `
                    <div class="admin-form-group">
                        <label class="admin-form-label">Telefone</label>
                        <input type="text" class="admin-form-input" value="${escapeHtml(data.telefone)}" readonly>
                    </div>
                ` : '';

                const dataContratacaoHtml = data.data_contratacao ? `
                    <div class="admin-form-group">
                        <label class="admin-form-label">Data de Contratação</label>
                        <input type="text" class="admin-form-input" value="${escapeHtml(data.data_contratacao)}" readonly>
                    </div>
                ` : '';

                const conteudo = `
                    <div class="admin-form">
                        <div class="admin-form-group">
                            <label class="admin-form-label">ID</label>
                            <input type="text" class="admin-form-input" value="${escapeHtml(data.id)}" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Nome Completo</label>
                            <input type="text" class="admin-form-input" value="${escapeHtml(data.nome || '')}" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Email</label>
                            <input type="email" class="admin-form-input" value="${escapeHtml(data.email || '')}" readonly>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">CPF</label>
                            <input type="text" class="admin-form-input" value="${escapeHtml(data.cpf || '')}" readonly>
                        </div>
                        ${telefoneHtml}
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label class="admin-form-label">Tipo</label>
                                <input type="text" class="admin-form-input" value="${escapeHtml(data.tipo || '')}" readonly>
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">Status</label>
                                <input type="text" class="admin-form-input" value="${escapeHtml(data.status || '')}" readonly>
                            </div>
                        </div>
                        ${dataContratacaoHtml}
                        <div style="margin-top: 1.5rem;">
                            <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="width: 100%;">
                                Fechar
                            </button>
                        </div>
                    </div>
                `;
                abrirModal('Detalhes do Usuário', conteudo);
            })
            .catch(error => {
                abrirModal('Erro', '<div style="text-align: center; padding: 20px; color: #d32f2f;">Erro ao carregar dados do usuário.</div>');
            });
    }

    function excluirUsuario(id) {
        // Primeira confirmação
        if (!confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
            return;
        }

        // Segunda confirmação (mais rigorosa)
        if (!confirm('ATENÇÃO: Todos os dados deste usuário serão perdidos permanentemente.\n\nIsso inclui:\n- Dados pessoais\n- Pedidos relacionados\n- Endereços\n- Outras informações\n\nConfirma a exclusão?')) {
            return;
        }

        // Cria FormData para enviar o ID
        const formData = new FormData();
        formData.append('id', id);

        // Envia requisição para excluir
        fetch('<?= BASE_URL ?>/app/control/AdminController.php?acao=excluirUsuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('Erro: ' + data.erro);
                } else {
                    alert('Usuário excluído com sucesso!');
                    // Recarrega a página para atualizar a tabela
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Erro ao excluir usuário. Tente novamente.');
            });
    }

    function salvarUsuario(event) {
        event.preventDefault();

        // Pega dados do formulário
        const formData = new FormData(event.target);

        // Remove caracteres não numéricos do CPF e telefone
        const cpf = formData.get('cpf').replace(/\D/g, '');
        formData.set('cpf', cpf);

        const telefone = formData.get('telefone');
        if (telefone) {
            formData.set('telefone', telefone.replace(/\D/g, ''));
        }

        // Mostra loading
        const submitButton = event.target.querySelector('button[type="submit"]');
        const textoOriginal = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="ri-loader-4-line"></i> Salvando...';

        // Envia dados para o servidor
        fetch('<?= BASE_URL ?>/app/control/AdminController.php?acao=cadastrarUsuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('Erro: ' + data.erro);
                    submitButton.disabled = false;
                    submitButton.innerHTML = textoOriginal;
                } else {
                    alert('Usuário cadastrado com sucesso!');
                    fecharModal();
                    // Recarrega a página para atualizar a tabela
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Erro ao cadastrar usuário. Tente novamente.');
                submitButton.disabled = false;
                submitButton.innerHTML = textoOriginal;
            });
    }

    function atualizarUsuario(event, id) {
        event.preventDefault();

        // Pega dados do formulário
        const formData = new FormData(event.target);

        // Mostra loading
        const submitButton = event.target.querySelector('button[type="submit"]');
        const textoOriginal = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="ri-loader-4-line"></i> Atualizando...';

        // Envia dados para o servidor
        fetch('<?= BASE_URL ?>/app/control/AdminController.php?acao=atualizarUsuario', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('Erro: ' + data.erro);
                    submitButton.disabled = false;
                    submitButton.innerHTML = textoOriginal;
                } else {
                    alert('Usuário atualizado com sucesso!');
                    fecharModal();
                    // Recarrega a página para atualizar a tabela
                    window.location.reload();
                }
            })
            .catch(error => {
                alert('Erro ao atualizar usuário. Tente novamente.');
                submitButton.disabled = false;
                submitButton.innerHTML = textoOriginal;
            });
    }

    function aplicarFiltros() {
        // Aplica filtros quando mudarem os selects
        pesquisarUsuarios();
    }

    function pesquisarUsuarios() {
        // Pega o termo de pesquisa
        const termo = document.getElementById('inputPesquisa').value.trim();
        const tbody = document.getElementById('tbodyUsuarios');

        // Pega os filtros
        const filtroTipo = document.getElementById('filtroTipo').value;
        const filtroStatus = document.getElementById('filtroStatus').value;

        // Se não houver termo nem filtros, recarrega a página para mostrar todos
        if (termo === '' && filtroTipo === '' && filtroStatus === '') {
            window.location.href = '<?= BASE_URL ?>/app/control/AdminController.php?acao=usuarios';
            return;
        }

        // Mostra loading
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;"><i class="ri-loader-4-line"></i> Pesquisando...</td></tr>';

        // Monta a URL com termo e filtros
        let url = '<?= BASE_URL ?>/app/control/AdminController.php?acao=pesquisarUsuarios';
        if (termo !== '') {
            url += '&termo=' + encodeURIComponent(termo);
        }
        if (filtroTipo !== '') {
            url += '&tipo=' + encodeURIComponent(filtroTipo);
        }
        if (filtroStatus !== '') {
            url += '&status=' + encodeURIComponent(filtroStatus);
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição');
                }
                return response.json();
            })
            .then(data => {
                if (data.erro) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #d32f2f;">Erro: ' + escapeHtml(data.erro) + '</td></tr>';
                    return;
                }

                // Se não houver resultados
                if (!data.usuarios || data.usuarios.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #666;">Nenhum usuário encontrado para "' + escapeHtml(termo) + '"</td></tr>';
                    return;
                }

                // Monta as linhas da tabela
                let html = '';
                data.usuarios.forEach(usuario => {
                    html += `
                        <tr>
                            <td>${escapeHtml(usuario.id)}</td>
                            <td>${escapeHtml(usuario.nome)}</td>
                            <td>${escapeHtml(usuario.email)}</td>
                            <td>${escapeHtml(usuario.cpf)}</td>
                            <td><span class="admin-badge ${escapeHtml(usuario.tipo_classe)}">${escapeHtml(usuario.tipo)}</span></td>
                            <td><span class="admin-badge ${escapeHtml(usuario.status_classe)}">${escapeHtml(usuario.status)}</span></td>
                            <td>
                                <div class="admin-table-actions">
                                    <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarUsuario(${usuario.id})">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarUsuario(${usuario.id})">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirUsuario(${usuario.id})">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #d32f2f;">Erro ao pesquisar. Tente novamente.</td></tr>';
            });
    }

    // Função auxiliar para escapar HTML (já existe no código, mas garantindo que está disponível)
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?php include_once "admin_footer.php"; ?>
<?php
$titulo_pagina = "Gerenciamento de Estoque";
include_once "admin_header.php";
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Controle de Estoque</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalEntrada()">
            <i class="ri-add-line"></i>
            Nova Entrada
        </button>
    </div>

    <!-- Barra de Busca e Filtros -->
    <div class="admin-search-bar">
        <input type="text" class="admin-search-input" placeholder="Buscar por produto...">
        <select class="admin-filter-select">
            <option value="">Todos os Status</option>
            <option value="disponivel">Disponível</option>
            <option value="baixo">Estoque Baixo</option>
            <option value="critico">Estoque Crítico</option>
            <option value="zerado">Sem Estoque</option>
        </select>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Modelo</th>
                <th>Quantidade</th>
                <th>Data Cadastro</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Vestido Floral Verão</td>
                <td>P - Branco</td>
                <td>15</td>
                <td>10/01/2025</td>
                <td><span class="admin-badge admin-badge-success">Disponível</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque" onclick="adicionarEstoque(1)">
                            <i class="ri-add-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar" onclick="editarEstoque(1)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Blusa Manga Longa</td>
                <td>M - Preto</td>
                <td>5</td>
                <td>08/01/2025</td>
                <td><span class="admin-badge admin-badge-warning">Estoque Baixo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque" onclick="adicionarEstoque(2)">
                            <i class="ri-add-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar" onclick="editarEstoque(2)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Calça Jeans Skinny</td>
                <td>G - Azul</td>
                <td>22</td>
                <td>12/01/2025</td>
                <td><span class="admin-badge admin-badge-success">Disponível</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque" onclick="adicionarEstoque(3)">
                            <i class="ri-add-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar" onclick="editarEstoque(3)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Saia Midi Plissada</td>
                <td>P - Rosa</td>
                <td>2</td>
                <td>05/01/2025</td>
                <td><span class="admin-badge admin-badge-danger">Estoque Crítico</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque" onclick="adicionarEstoque(4)">
                            <i class="ri-add-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar" onclick="editarEstoque(4)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Bolsa de Couro</td>
                <td>Único - Marrom</td>
                <td>0</td>
                <td>03/01/2025</td>
                <td><span class="admin-badge admin-badge-danger">Sem Estoque</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque" onclick="adicionarEstoque(5)">
                            <i class="ri-add-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar" onclick="editarEstoque(5)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>6</td>
                <td>Vestido Floral Verão</td>
                <td>M - Branco</td>
                <td>8</td>
                <td>10/01/2025</td>
                <td><span class="admin-badge admin-badge-warning">Estoque Baixo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Adicionar Estoque" onclick="adicionarEstoque(6)">
                            <i class="ri-add-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Editar" onclick="editarEstoque(6)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
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
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: var(--cor_primaria); margin-bottom: 0.5rem;">52</div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Total de Itens</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: hsl(151, 87%, 36%); margin-bottom: 0.5rem;">28</div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Disponíveis</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: hsl(51, 100%, 67%); margin-bottom: 0.5rem;">15</div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Estoque Baixo</div>
        </div>
        <div style="text-align: center; padding: 1.5rem; background-color: var(--cor_container); border-radius: 0.5rem;">
            <div style="font-size: var(--fonte_h3); font-weight: var(--espessura_700); color: #d32f2f; margin-bottom: 0.5rem;">9</div>
            <div style="font-size: var(--fonte_pequena); color: var(--cor_texto_claro);">Críticos/Sem Estoque</div>
        </div>
    </div>
</div>

<script>
    function abrirModalEntrada() {
        const conteudo = `
        <form class="admin-form" onsubmit="registrarEntrada(event)">
            <div class="admin-form-group">
                <label class="admin-form-label">Produto</label>
                <select class="admin-form-select" name="produto" required>
                    <option value="">Selecione um produto...</option>
                    <option value="1">Vestido Floral Verão</option>
                    <option value="2">Blusa Manga Longa</option>
                    <option value="3">Calça Jeans Skinny</option>
                    <option value="4">Saia Midi Plissada</option>
                    <option value="5">Bolsa de Couro</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Modelo do Produto</label>
                <input type="text" class="admin-form-input" name="modelo" placeholder="Ex: P - Branco, M - Preto" required>
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
            <div class="admin-form-group">
                <label class="admin-form-label">Observações</label>
                <textarea class="admin-form-textarea" name="observacoes" placeholder="Observações sobre a entrada de estoque..."></textarea>
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
    `;
        abrirModal('Nova Entrada de Estoque', conteudo);
    }

    function adicionarEstoque(id) {
        const conteudo = `
        <form class="admin-form" onsubmit="adicionarQuantidade(event, ${id})">
            <div class="admin-form-group">
                <label class="admin-form-label">Quantidade Atual</label>
                <input type="text" class="admin-form-input" value="15" readonly>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Quantidade a Adicionar</label>
                <input type="number" class="admin-form-input" name="quantidade" min="1" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Data da Entrada</label>
                <input type="date" class="admin-form-input" name="data" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Observações</label>
                <textarea class="admin-form-textarea" name="observacoes" placeholder="Observações sobre a entrada..."></textarea>
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
    `;
        abrirModal('Adicionar Estoque', conteudo);
    }

    function editarEstoque(id) {
        const conteudo = `
        <form class="admin-form" onsubmit="atualizarEstoque(event, ${id})">
            <div class="admin-form-group">
                <label class="admin-form-label">Produto</label>
                <input type="text" class="admin-form-input" value="Produto ${id}" readonly>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Modelo</label>
                <input type="text" class="admin-form-input" name="modelo" value="P - Branco" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Quantidade</label>
                <input type="number" class="admin-form-input" name="quantidade" value="15" min="0" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Data de Cadastro</label>
                <input type="date" class="admin-form-input" name="data" value="2025-01-10" required>
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
        abrirModal('Editar Estoque', conteudo);
    }

    function registrarEntrada(event) {
        event.preventDefault();
        alert('Entrada de estoque registrada com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }

    function adicionarQuantidade(event, id) {
        event.preventDefault();
        alert('Quantidade adicionada ao estoque! (Funcionalidade será implementada)');
        fecharModal();
    }

    function atualizarEstoque(event, id) {
        event.preventDefault();
        alert('Estoque atualizado com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }
</script>

<?php include_once "admin_footer.php"; ?>
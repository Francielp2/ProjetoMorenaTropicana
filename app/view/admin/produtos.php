<?php
$titulo_pagina = "Gerenciamento de Produtos";
include_once "admin_header.php";
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Lista de Produtos</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalCadastro()">
            <i class="ri-add-line"></i>
            Novo Produto
        </button>
    </div>

    <!-- Barra de Busca e Filtros -->
    <div class="admin-search-bar">
        <input type="text" class="admin-search-input" placeholder="Buscar por nome, categoria...">
        <select class="admin-filter-select">
            <option value="">Todas as Categorias</option>
            <option value="vestidos">Vestidos</option>
            <option value="blusas">Blusas</option>
            <option value="calcas">Calças</option>
            <option value="saias">Saias</option>
            <option value="acessorios">Acessórios</option>
        </select>
    </div>

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
            <tr>
                <td>1</td>
                <td>Vestido Floral Verão</td>
                <td>Vestidos</td>
                <td>R$ 199,90</td>
                <td>15</td>
                <td><span class="admin-badge admin-badge-success">Disponível</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarProduto(1)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarProduto(1)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirProduto(1)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Blusa Manga Longa</td>
                <td>Blusas</td>
                <td>R$ 89,90</td>
                <td>8</td>
                <td><span class="admin-badge admin-badge-warning">Estoque Baixo</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarProduto(2)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarProduto(2)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirProduto(2)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Calça Jeans Skinny</td>
                <td>Calças</td>
                <td>R$ 149,90</td>
                <td>22</td>
                <td><span class="admin-badge admin-badge-success">Disponível</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarProduto(3)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarProduto(3)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirProduto(3)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Saia Midi Plissada</td>
                <td>Saias</td>
                <td>R$ 119,90</td>
                <td>12</td>
                <td><span class="admin-badge admin-badge-success">Disponível</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarProduto(4)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarProduto(4)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirProduto(4)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Bolsa de Couro</td>
                <td>Acessórios</td>
                <td>R$ 179,90</td>
                <td>5</td>
                <td><span class="admin-badge admin-badge-danger">Estoque Crítico</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarProduto(5)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-primary" title="Editar" onclick="editarProduto(5)">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-danger" title="Excluir" onclick="excluirProduto(5)">
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
        <form class="admin-form" onsubmit="salvarProduto(event)">
            <div class="admin-form-group">
                <label class="admin-form-label">Nome do Produto</label>
                <input type="text" class="admin-form-input" name="nome" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Descrição</label>
                <textarea class="admin-form-textarea" name="descricao" required></textarea>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Categoria</label>
                    <select class="admin-form-select" name="categoria" required>
                        <option value="">Selecione...</option>
                        <option value="Vestidos">Vestidos</option>
                        <option value="Blusas">Blusas</option>
                        <option value="Calças">Calças</option>
                        <option value="Saias">Saias</option>
                        <option value="Acessórios">Acessórios</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Preço (R$)</label>
                    <input type="number" step="0.01" class="admin-form-input" name="preco" required>
                </div>
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
                <label class="admin-form-label">URL da Imagem</label>
                <input type="text" class="admin-form-input" name="imagem" placeholder="https://...">
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
        abrirModal('Cadastrar Novo Produto', conteudo);
    }

    function editarProduto(id) {
        const conteudo = `
        <form class="admin-form" onsubmit="atualizarProduto(event, ${id})">
            <div class="admin-form-group">
                <label class="admin-form-label">Nome do Produto</label>
                <input type="text" class="admin-form-input" name="nome" value="Produto ${id}" required>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Descrição</label>
                <textarea class="admin-form-textarea" name="descricao" required>Descrição do produto ${id}</textarea>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Categoria</label>
                    <select class="admin-form-select" name="categoria" required>
                        <option value="Vestidos">Vestidos</option>
                        <option value="Blusas">Blusas</option>
                        <option value="Calças">Calças</option>
                        <option value="Saias">Saias</option>
                        <option value="Acessórios">Acessórios</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Preço (R$)</label>
                    <input type="number" step="0.01" class="admin-form-input" name="preco" value="199.90" required>
                </div>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Tamanhos Disponíveis</label>
                    <input type="text" class="admin-form-input" name="tamanhos" value="P, M, G, GG" required>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Cores Disponíveis</label>
                    <input type="text" class="admin-form-input" name="cores" value="Branco, Preto" required>
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">URL da Imagem</label>
                <input type="text" class="admin-form-input" name="imagem" value="https://exemplo.com/imagem.jpg">
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
        abrirModal('Editar Produto', conteudo);
    }

    function visualizarProduto(id) {
        const conteudo = `
        <div class="admin-form">
            <div class="admin-form-group">
                <label class="admin-form-label">Nome do Produto</label>
                <input type="text" class="admin-form-input" value="Produto ${id}" readonly>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Descrição</label>
                <textarea class="admin-form-textarea" readonly>Descrição completa do produto ${id}</textarea>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Categoria</label>
                    <input type="text" class="admin-form-input" value="Vestidos" readonly>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Preço</label>
                    <input type="text" class="admin-form-input" value="R$ 199,90" readonly>
                </div>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Tamanhos</label>
                    <input type="text" class="admin-form-input" value="P, M, G, GG" readonly>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Cores</label>
                    <input type="text" class="admin-form-input" value="Branco, Preto" readonly>
                </div>
            </div>
            <div style="margin-top: 1.5rem;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="width: 100%;">
                    Fechar
                </button>
            </div>
        </div>
    `;
        abrirModal('Detalhes do Produto', conteudo);
    }

    function excluirProduto(id) {
        if (confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')) {
            alert('Produto excluído com sucesso! (Funcionalidade será implementada)');
            // Aqui você implementaria a lógica de exclusão
        }
    }

    function salvarProduto(event) {
        event.preventDefault();
        alert('Produto cadastrado com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }

    function atualizarProduto(event, id) {
        event.preventDefault();
        alert('Produto atualizado com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }
</script>

<?php include_once "admin_footer.php"; ?>
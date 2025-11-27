<?php
$titulo_pagina = "Gerenciamento de Pedidos";
include_once "admin_header.php";
?>

<div class="admin-content-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Lista de Pedidos</h2>
        <button class="admin-btn admin-btn-primary" onclick="abrirModalFiltros()">
            <i class="ri-filter-line"></i>
            Filtros Avançados
        </button>
    </div>

    <!-- Barra de Busca e Filtros -->
    <div class="admin-search-bar">
        <input type="text" class="admin-search-input" placeholder="Buscar por ID do pedido, cliente...">
        <select class="admin-filter-select">
            <option value="">Todos os Status</option>
            <option value="pendente">Pendente</option>
            <option value="finalizado">Finalizado</option>
            <option value="entregue">Entregue</option>
            <option value="cancelado">Cancelado</option>
        </select>
    </div>

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
            <tr>
                <td>#001234</td>
                <td>Maria Silva</td>
                <td>15/01/2025 14:30</td>
                <td>R$ 299,90</td>
                <td><span class="admin-badge admin-badge-warning">Pendente</span></td>
                <td><span class="admin-badge admin-badge-warning">Pendente</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarPedido(1234)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-success" title="Atualizar Status" onclick="atualizarStatus(1234)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001233</td>
                <td>Ana Costa</td>
                <td>14/01/2025 10:15</td>
                <td>R$ 450,00</td>
                <td><span class="admin-badge admin-badge-success">Entregue</span></td>
                <td><span class="admin-badge admin-badge-success">Confirmado</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarPedido(1233)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-success" title="Atualizar Status" onclick="atualizarStatus(1233)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001232</td>
                <td>Joana Santos</td>
                <td>14/01/2025 09:00</td>
                <td>R$ 189,90</td>
                <td><span class="admin-badge admin-badge-info">Em Trânsito</span></td>
                <td><span class="admin-badge admin-badge-success">Confirmado</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarPedido(1232)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-success" title="Atualizar Status" onclick="atualizarStatus(1232)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001231</td>
                <td>Paula Oliveira</td>
                <td>13/01/2025 16:45</td>
                <td>R$ 320,00</td>
                <td><span class="admin-badge admin-badge-success">Entregue</span></td>
                <td><span class="admin-badge admin-badge-success">Confirmado</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarPedido(1231)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-success" title="Atualizar Status" onclick="atualizarStatus(1231)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#001230</td>
                <td>Carlos Santos</td>
                <td>13/01/2025 11:20</td>
                <td>R$ 150,00</td>
                <td><span class="admin-badge admin-badge-danger">Cancelado</span></td>
                <td><span class="admin-badge admin-badge-danger">Cancelado</span></td>
                <td>
                    <div class="admin-table-actions">
                        <button class="admin-btn admin-btn-icon admin-btn-secondary" title="Visualizar" onclick="visualizarPedido(1230)">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="admin-btn admin-btn-icon admin-btn-success" title="Atualizar Status" onclick="atualizarStatus(1230)">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    function visualizarPedido(id) {
        const conteudo = `
        <div class="admin-form">
            <h3 style="margin-bottom: 1rem; color: var(--cor_titulo);">Pedido #${String(id).padStart(6, '0')}</h3>
            
            <div class="admin-form-group">
                <label class="admin-form-label">Cliente</label>
                <input type="text" class="admin-form-input" value="Nome do Cliente" readonly>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Data do Pedido</label>
                    <input type="text" class="admin-form-input" value="15/01/2025 14:30" readonly>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Status</label>
                    <input type="text" class="admin-form-input" value="Pendente" readonly>
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
                    <tr>
                        <td>Vestido Floral Verão</td>
                        <td>1</td>
                        <td>R$ 199,90</td>
                        <td>R$ 199,90</td>
                    </tr>
                    <tr>
                        <td>Blusa Manga Longa</td>
                        <td>1</td>
                        <td>R$ 89,90</td>
                        <td>R$ 89,90</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Valor Total</label>
                    <input type="text" class="admin-form-input" value="R$ 289,80" readonly style="font-weight: var(--espessura_700); font-size: var(--fonte_grande);">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Forma de Pagamento</label>
                    <input type="text" class="admin-form-input" value="Cartão de Crédito" readonly>
                </div>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="fecharModal()" style="width: 100%;">
                    Fechar
                </button>
            </div>
        </div>
    `;
        abrirModal('Detalhes do Pedido', conteudo);
    }

    function atualizarStatus(id) {
        const conteudo = `
        <form class="admin-form" onsubmit="salvarStatus(event, ${id})">
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pedido</label>
                <select class="admin-form-select" name="status_pedido" required>
                    <option value="PENDENTE">Pendente</option>
                    <option value="FINALIZADO">Finalizado</option>
                    <option value="ENTREGUE">Entregue</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pagamento</label>
                <select class="admin-form-select" name="status_pagamento" required>
                    <option value="PENDENTE">Pendente</option>
                    <option value="CONFIRMADO">Confirmado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Observações</label>
                <textarea class="admin-form-textarea" name="observacoes" placeholder="Adicione observações sobre a atualização..."></textarea>
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
    `;
        abrirModal('Atualizar Status do Pedido', conteudo);
    }

    function abrirModalFiltros() {
        const conteudo = `
        <form class="admin-form" onsubmit="aplicarFiltros(event)">
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label class="admin-form-label">Data Inicial</label>
                    <input type="date" class="admin-form-input" name="data_inicial">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Data Final</label>
                    <input type="date" class="admin-form-input" name="data_final">
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Status do Pedido</label>
                <select class="admin-form-select" name="status">
                    <option value="">Todos</option>
                    <option value="PENDENTE">Pendente</option>
                    <option value="FINALIZADO">Finalizado</option>
                    <option value="ENTREGUE">Entregue</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Valor Mínimo</label>
                <input type="number" step="0.01" class="admin-form-input" name="valor_min" placeholder="R$ 0,00">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">Valor Máximo</label>
                <input type="number" step="0.01" class="admin-form-input" name="valor_max" placeholder="R$ 0,00">
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

    function salvarStatus(event, id) {
        event.preventDefault();
        alert('Status atualizado com sucesso! (Funcionalidade será implementada)');
        fecharModal();
    }

    function aplicarFiltros(event) {
        event.preventDefault();
        alert('Filtros aplicados! (Funcionalidade será implementada)');
        fecharModal();
    }
</script>

<?php include_once "admin_footer.php"; ?>
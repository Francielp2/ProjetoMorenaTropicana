<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Meus Pedidos</h1>
            <p class="descricao_banner">Acompanhe e gerencie seus pedidos</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>
                <li class="item_navegacao">Meus Pedidos</li>
            </ul>
        </div>
    </section>

    <section class="carrinho container section">
        <!-- Mensagens de sucesso/erro -->
        <?php if (!empty($mensagem)): ?>
            <div style="background-color: <?= $tipoMensagem === 'sucesso' ? '#d4edda' : '#f8d7da' ?>; 
                        color: <?= $tipoMensagem === 'sucesso' ? '#155724' : '#721c24' ?>; 
                        padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center;">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <table class="carrinho_tabela tabela">
            <thead class="thead">
                <th class="thead_titulo">Pedido</th>
                <th>Data</th>
                <th>Status</th>
                <th>Valor Total</th>
                <th>Pagamento</th>
                <th>Ações</th>
            </thead>

            <tbody class="tbody">
                <?php if (empty($pedidosFormatados)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                            <i class="ri-shopping-bag-line" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: var(--cor_primaria);"></i>
                            Você ainda não realizou nenhum pedido
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pedidosFormatados as $pedido): ?>
                        <tr>
                            <td class="carriho_dados">
                                <div style="padding: 1rem;">
                                    <h3 class="carrinho_titulo">
                                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=pedidos&visualizar=<?= $pedido['id'] ?>">
                                            Pedido #<?= htmlspecialchars($pedido['id_formatado']) ?>
                                        </a>
                                    </h3>
                                    <span style="font-size: 0.9rem; color: #666;">
                                        <?= htmlspecialchars($pedido['total_itens']) ?> <?= $pedido['total_itens'] == 1 ? 'item' : 'itens' ?>
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span style="display: block; font-weight: 500;">
                                    <?= htmlspecialchars($pedido['data']) ?>
                                </span>
                                <span style="font-size: 0.85rem; color: #666;">
                                    <?= htmlspecialchars($pedido['hora']) ?>
                                </span>
                            </td>

                            <td>
                                <span class="<?= htmlspecialchars($pedido['status_classe']) ?>" 
                                      style="display: inline-block; padding: 0.5rem 1rem; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                                    <?= htmlspecialchars($pedido['status']) ?>
                                </span>
                            </td>

                            <td class="subtotal_coluna">
                                <span class="carrinho_subtotal">
                                    <?= htmlspecialchars($pedido['valor_total']) ?>
                                </span>
                            </td>

                            <td>
                                <span class="<?= htmlspecialchars($pedido['pagamento_classe']) ?>" 
                                      style="display: inline-block; padding: 0.5rem 1rem; border-radius: 1rem; font-size: 0.85rem; font-weight: 600;">
                                    <?= htmlspecialchars($pedido['pagamento_status']) ?>
                                </span>
                            </td>

                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center;">
                                    <?php if ($pedido['pode_pagar']): ?>
                                        <button onclick="abrirModalPagamento(<?= $pedido['id'] ?>, '<?= htmlspecialchars($pedido['valor_total']) ?>')" 
                                                class="btn" 
                                                style="padding: 0.75rem 1.5rem; font-size: 0.9rem; white-space: nowrap;">
                                            <i class="ri-bank-card-line"></i> Pagar
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($pedido['pode_cancelar']): ?>
                                        <button onclick="confirmarCancelamento(<?= $pedido['id'] ?>)" 
                                                class="btn btn-dark" 
                                                style="padding: 0.75rem 1.5rem; font-size: 0.9rem; background-color: #d32f2f; white-space: nowrap;">
                                            <i class="ri-close-circle-line"></i> Cancelar
                                        </button>
                                    <?php endif; ?>

                                    <?php if (!$pedido['pode_pagar'] && !$pedido['pode_cancelar']): ?>
                                        <button class="btn" 
                                                style="padding: 0.75rem 1.5rem; font-size: 0.9rem; background-color: var(--cor_cinza); cursor: not-allowed;" 
                                                disabled>
                                            Sem ações
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<!-- Modal de Pagamento PIX -->
<div id="modalPagamento" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background-color: white; padding: 2.5rem; border-radius: 1rem; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <i class="ri-qr-code-line" style="font-size: 3rem; color: var(--cor_primaria); display: block; margin-bottom: 1rem;"></i>
            <h2 style="font-size: 1.5rem; color: var(--cor_titulo); margin-bottom: 0.5rem;">Pagamento via PIX</h2>
            <p style="color: #666; font-size: 0.95rem;">Pedido #<span id="pedidoNumero"></span></p>
        </div>

        <div style="background-color: var(--cor_container); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.75rem;">Valor a pagar:</p>
            <p style="font-size: 2rem; font-weight: 700; color: var(--cor_primaria);" id="valorPagamento"></p>
        </div>

        <div style="background-color: #fff3e0; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid var(--cor_primaria); margin-bottom: 2rem;">
            <p style="font-size: 0.9rem; color: #666; margin: 0;">
                <i class="ri-information-line" style="color: var(--cor_primaria);"></i>
                O sistema de pagamento PIX será implementado em breve. Por enquanto, use esta tela para confirmar o pedido.
            </p>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button onclick="confirmarPagamento()" class="btn" style="flex: 1; padding: 1rem; justify-content: center; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-check-line"></i> Confirmar Pagamento
            </button>
            <button onclick="fecharModalPagamento()" class="btn btn-dark" style="flex: 1; padding: 1rem; background-color: var(--cor_cinza); justify-content: center; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-close-line"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script>
    let pedidoIdAtual = null;

    // Abre modal de pagamento
    function abrirModalPagamento(pedidoId, valorTotal) {
        pedidoIdAtual = pedidoId;
        document.getElementById('pedidoNumero').textContent = String(pedidoId).padStart(6, '0');
        document.getElementById('valorPagamento').textContent = valorTotal;
        document.getElementById('modalPagamento').style.display = 'flex';
    }

    // Fecha modal de pagamento
    function fecharModalPagamento() {
        pedidoIdAtual = null;
        document.getElementById('modalPagamento').style.display = 'none';
    }

    // Confirma pagamento (será implementado depois)
    function confirmarPagamento() {
        if (pedidoIdAtual) {
            // TODO: Implementar envio para o backend
            alert('Funcionalidade de pagamento será implementada em breve.\n\nPedido #' + pedidoIdAtual);
            fecharModalPagamento();
            
            // Por enquanto, recarrega a página
            // window.location.href = '<?= BASE_URL ?>/app/control/ClienteController.php?acao=pedidos&sucesso=' + encodeURIComponent('Pagamento confirmado!');
        }
    }

    // Confirma cancelamento
    function confirmarCancelamento(pedidoId) {
        if (confirm('Tem certeza que deseja cancelar este pedido?\n\nEsta ação não pode ser desfeita.')) {
            // TODO: Implementar envio para o backend
            alert('Funcionalidade de cancelamento será implementada em breve.\n\nPedido #' + pedidoId);
            
            // Por enquanto, apenas alerta
            // window.location.href = '<?= BASE_URL ?>/app/control/ClienteController.php?acao=pedidos&cancelar=' + pedidoId;
        }
    }

    // Fecha modal ao clicar fora
    document.getElementById('modalPagamento').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalPagamento();
        }
    });

    // Fecha modal ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModalPagamento();
        }
    });
</script>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
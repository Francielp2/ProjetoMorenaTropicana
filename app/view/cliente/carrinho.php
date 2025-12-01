<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Carrinho</h1>
            <p class="descricao_banner">Finalize sua compra com segurança e estilo – sua nova peça favorita está a um clique!</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>

                <li class="item_navegacao">Carrinho</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="carrinho container section">
        <?php if (!empty($mensagem)): ?>
            <div style="background-color: <?= $tipoMensagem === 'sucesso' ? '#d4edda' : '#f8d7da' ?>; 
                        color: <?= $tipoMensagem === 'sucesso' ? '#155724' : '#721c24' ?>; 
                        padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center;">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($itensFormatados)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="ri-shopping-cart-line" style="font-size: 4rem; color: var(--cor_primaria); display: block; margin-bottom: 1.5rem;"></i>
                <h2 style="color: #666; margin-bottom: 1rem;">Seu carrinho está vazio</h2>
                <p style="color: #999; margin-bottom: 2rem;">Adicione produtos ao carrinho para continuar comprando.</p>
                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" class="btn">Ver Produtos</a>
            </div>
        <?php else: ?>
            <table class="carrinho_tabela tabela">
                <thead class="thead">
                    <th class="thead_titulo">Produtos</th>
                    <th>Quantidade</th>
                    <th>Preço total</th>
                </thead>

                <tbody class="tbody">
                    <?php foreach ($itensFormatados as $item): ?>
                        <tr>
                            <td class="carriho_dados">
                                <?php if (!empty($item['imagem'])): ?>
                                    <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= $item['nome_produto'] ?>" class="carrinho_imagem">
                                <?php else: ?>
                                    <div class="carrinho_imagem" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-width:100px;min-height:100px;">
                                        <span style="font-size:0.8rem;color:#999;">Sem imagem</span>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <h3 class="carrinho_titulo">
                                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= $item['id_produto'] ?>">
                                            <?= $item['nome_produto'] ?>
                                        </a>
                                    </h3>

                                    <span class="preco_carrinho">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
                                    
                                    <?php if (!empty($item['tamanho'])): ?>
                                        <div class="carrinho_tamanho">Tamanho: <?= htmlspecialchars($item['tamanho']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($item['cor'])): ?>
                                        <div class="carrinho_tamanho" style="margin-top:0.25rem;">Cor: <?= htmlspecialchars($item['cor']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="quantidade_coluna">
                                <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=carrinho" style="display:inline;">
                                    <input type="hidden" name="id_carrinho" value="<?= $item['id_carrinho'] ?>">
                                    <div class="adicionar_carrinho">
                                        <button type="button" class="rem" onclick="diminuirQuantidade(<?= $item['id_carrinho'] ?>)">-</button>
                                        <input type="number" name="quantidade" id="qtd_<?= $item['id_carrinho'] ?>" value="<?= $item['quantidade'] ?>" min="1" class="contador" readonly>
                                        <button type="button" class="add" onclick="aumentarQuantidade(<?= $item['id_carrinho'] ?>)">+</button>
                                    </div>
                                    <input type="hidden" name="atualizar_quantidade" value="1">
                                </form>
                                
                                <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=carrinho" style="display:inline;margin-top:0.5rem;">
                                    <input type="hidden" name="id_carrinho" value="<?= $item['id_carrinho'] ?>">
                                    <input type="hidden" name="remover_item" value="1">
                                    <button type="submit" style="background:none;border:none;color:#d32f2f;cursor:pointer;font-size:0.85rem;text-decoration:underline;" onclick="return confirm('Deseja remover este item do carrinho?')">
                                        <i class="ri-delete-bin-line"></i> Remover
                                    </button>
                                </form>
                            </td>

                            <td class="subtotal_coluna">
                                <span class="carrinho_subtotal">R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="carrinho_rodape grid">
                <div class="area_cupom">
                    <h3 class="titulo_cupom">Aplicar Cupom</h3>

                    <div class="cupom_div grid">
                        <input type="text" placeholder="Código do Cupom" class="cupom_input">
                        <button type="submit" class="btn btn-dark">Aplicar Cupom</button>
                    </div>
                </div>

                <div class="area_total">
                    <h3 class="Titulo_total">Carrinho Total</h3>

                    <ul class="lista_total grid">
                        <li class="total_item">
                            <h3 class="subtitulo_total">Subtotal:</h3>
                            <span class="valor_total"><?= $subtotalFormatado ?></span>
                        </li>

                        <li class="total_item">
                            <h3 class="subtitulo_total">Frete:</h3>
                            <span class="valor_total"><?= $freteFormatado ?></span>
                        </li>

                        <li>
                            <hr class="total_rule">
                        </li>

                        <li class="total_item">
                            <h3 class="subtitulo_total">Total:</h3>
                            <span class="valor_total"><?= $totalFormatado ?></span>
                        </li>
                    </ul>

                    <div class="ir_para_checkout grid">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=checkout" class="btn btn_checkout">Checkout</a>
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" class="btn btn_continue">Continue Comprando</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
    function aumentarQuantidade(idCarrinho) {
        const input = document.getElementById('qtd_' + idCarrinho);
        if (input) {
            const novoValor = parseInt(input.value) + 1;
            input.value = novoValor;
            
            // Envia o formulário automaticamente
            const form = input.closest('form');
            if (form) {
                form.submit();
            }
        }
    }

    function diminuirQuantidade(idCarrinho) {
        const input = document.getElementById('qtd_' + idCarrinho);
        if (input) {
            const valor = parseInt(input.value);
            if (valor > 1) {
                input.value = valor - 1;
                
                // Envia o formulário automaticamente
                const form = input.closest('form');
                if (form) {
                    form.submit();
                }
            }
        }
    }
</script>

<?php include_once __DIR__ . "/../Rodape.php"; ?>

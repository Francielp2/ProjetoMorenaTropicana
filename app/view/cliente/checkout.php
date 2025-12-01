<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Checkout</h1>
            <p class="descricao_banner">Finalize seus pedidos e garanta peças com o seu estilo</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>

                <li class="item_navegacao">Checkout</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="checkout section">
        <?php if (!empty($mensagem)): ?>
            <div style="background-color: <?= $tipoMensagem === 'sucesso' ? '#d4edda' : '#f8d7da' ?>; 
                        color: <?= $tipoMensagem === 'sucesso' ? '#155724' : '#721c24' ?>; 
                        padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 5px; text-align: center;">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <div class="checkout_container container grid">
            <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=checkout" class="checkout_formulario formulario">
                <h2 class="checkout_titulo">Informações de Entrega</h2>
                
                <div>
                    <input type="text" value="Brasil" class="input" readonly>
                </div>

                <div class="grupo_input">
                    <input type="text" value="<?= htmlspecialchars($rua ?? '') ?>" placeholder="Rua" class="input" readonly>
                    <input type="text" value="<?= htmlspecialchars($numero ?? '') ?>" placeholder="N°" class="input" readonly>
                </div>

                <input type="text" value="<?= htmlspecialchars($bairro ?? '') ?>" placeholder="Bairro" class="input" readonly>

                <div class="checkout_grupo">
                    <div>
                        <input type="text" value="<?= htmlspecialchars($estado ?? '') ?>" placeholder="Estado (UF)" class="input" readonly>
                    </div>

                    <input type="text" value="<?= htmlspecialchars($cepFormatado ?? '') ?>" placeholder="CEP" class="input" readonly>
                </div>

                <?php if (!empty($complemento)): ?>
                    <input type="text" value="<?= htmlspecialchars($complemento) ?>" placeholder="Complemento" class="input" readonly>
                <?php endif; ?>

                <h2 class="checkout_titulo">Forma de Pagamento</h2>

                <ul class="metodos_pagamento grid">
                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_pix" name="forma_pagamento" value="Pix" class="checkout_radio" required>
                        <label for="metodo_pagamento_pix">
                            Pix <img src="<?= BASE_URL ?>/public/assets/image/pix.png" alt="Pix">
                        </label>
                    </li>

                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_cc" name="forma_pagamento" value="Cartão" class="checkout_radio" required>
                        <label for="metodo_pagamento_cc">
                            Cartão de Crédito <img src="<?= BASE_URL ?>/public/assets/image/cartao.png" alt="Cartão">
                        </label>
                    </li>

                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_boleto" name="forma_pagamento" value="Boleto" class="checkout_radio" required>
                        <label for="metodo_pagamento_boleto">
                            Boleto <img src="<?= BASE_URL ?>/public/assets/image/cartao.png" alt="Boleto">
                        </label>
                    </li>
                </ul>

                <button type="submit" name="finalizar_pedido" class="btn btn_finalizar">Finalizar Pedido</button>
            </form>

            <div class="checkout_conteudo">
                <ul class="carrinho_itens grid">
                    <?php foreach ($itensFormatados as $item): ?>
                        <li class="carrinho_item">
                            <div class="carrinho_item_container">
                                <?php if (!empty($item['imagem'])): ?>
                                    <img src="<?= htmlspecialchars($item['imagem']) ?>" class="carrinho_item_imagem" alt="<?= $item['nome_produto'] ?>">
                                <?php else: ?>
                                    <div class="carrinho_item_imagem" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-width:80px;min-height:80px;">
                                        <span style="font-size:0.7rem;color:#999;">Sem imagem</span>
                                    </div>
                                <?php endif; ?>
                                <span class="carrinho_item_quantidade"><?= $item['quantidade'] ?></span>
                            </div>

                            <div>
                                <h3 class="carrinho_item_titulo">
                                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= $item['id_produto'] ?>">
                                        <?= $item['nome_produto'] ?>
                                    </a>
                                </h3>
                                <span class="carrinho_item_preco">R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="carrinho_total">
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
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include_once __DIR__ . "/../Rodape.php"; ?>

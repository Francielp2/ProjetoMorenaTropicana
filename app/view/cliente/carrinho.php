<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Carrinho</h1>
            <p class="descricao_banner">Finalize sua compra com segurança e estilo – sua nova peça favorita está a um clique!</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>

                <li class="item_navegacao">Carrinho</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="carrinho container section">
        <table class="carrinho_tabela tabela">
            <thead class="thead">
                <th class="thead_titulo">Produtos</th>
                <th>Quantidade</th>
                <th>Preço total</th>
            </thead>

            <tbody class="tbody">
                <tr>
                    <td class="carriho_dados">
                        <img src="<?= BASE_URL ?>/public/assets/image/product-5.jpg" alt="" class="carrinho_imagem">

                        <div>
                            <h3 class="carrinho_titulo">
                                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos">Nome do Produto</a>
                            </h3>

                            <span class="preco_carrinho">R$ 200,00</span>
                            <div class="carrinho_tamanho">Tamanho: M</div>
                        </div>
                    </td>

                    <td class="quantidade_coluna">
                        <div class="adicionar_carrinho">
                            <button type="button" class="rem">-</button>
                            <input type="text" value="1" class="contador">
                            <button type="button" class="add">+</button>
                        </div>
                    </td>

                    <td class="subtotal_coluna">
                        <span class="carrinho_subtotal">R$ 200,00</span>
                    </td>
                </tr>

                <tr>
                    <td class="carriho_dados">
                        <img src="<?= BASE_URL ?>/public/assets/image/product-2.jpg" alt="" class="carrinho_imagem">

                        <div>
                            <h3 class="carrinho_titulo">
                                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos">Nome do Produto</a>
                            </h3>

                            <span class="preco_carrinho">R$ 200,00</span>
                            <div class="carrinho_tamanho">Tamanho: M</div>
                        </div>
                    </td>

                    <td class="quantidade_coluna">
                        <div class="adicionar_carrinho">
                            <button type="button" class="rem">-</button>
                            <input type="text" value="1" class="contador">
                            <button type="button" class="add">+</button>
                        </div>
                    </td>

                    <td class="subtotal_coluna">
                        <span class="carrinho_subtotal">R$ 200,00</span>
                    </td>
                </tr>

                <tr>
                    <td class="carriho_dados">
                        <img src="<?= BASE_URL ?>/public/assets/image/product-4.jpg" alt="" class="carrinho_imagem">

                        <div>
                            <h3 class="carrinho_titulo">
                                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos">Nome do Produto</a>
                            </h3>

                            <span class="preco_carrinho">R$ 200,00</span>
                            <div class="carrinho_tamanho">Tamanho: M</div>
                        </div>
                    </td>

                    <td class="quantidade_coluna">
                        <div class="adicionar_carrinho">
                            <button type="button" class="rem">-</button>
                            <input type="text" value="1" class="contador">
                            <button type="button" class="add">+</button>
                        </div>
                    </td>

                    <td class="subtotal_coluna">
                        <span class="carrinho_subtotal">R$ 200,00</span>
                    </td>
                </tr>
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
                        <span class="valor_total">R$ 600,00</span>
                    </li>

                    <li class="total_item">
                        <h3 class="subtitulo_total">Frete:</h3>
                        <span class="valor_total">R$ 00,00</span>
                    </li>

                    <li>
                        <hr class="total_rule">
                    </li>

                    <li class="total_item">
                        <h3 class="subtitulo_total">Total:</h3>
                        <span class="valor_total">R$ 600,00</span>
                    </li>
                </ul>

                <div class="ir_para_checkout grid">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=checkout" class="btn btn_checkout">Checkout</a>
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" class="btn btn_continue">Continue Comprando</a>
                </div>
            </div>
        </div>
    </section>
</main>


<?php include_once __DIR__ . "/../Rodape.php"; ?>
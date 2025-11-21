<?php include_once "../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Checkout</h1>
            <p class="descricao_banner">Finalize seus pedidos e garanta peças com o seu estilo</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/view/cliente/tela_inicial.php">Início</a>

                <li class="item_navegacao">Checkout</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="checkout section">
        <div class="checkout_container container grid">
            <form action="" class="checkout_formulario formulario">
                <h2 class="checkout_titulo">Informações</h2>
                <div>
                    <select name="" id="pais" class="select_checkout input" style="display: none;">
                        <option value="all" selected>Selecione seu País</option>
                        <option value="br">Brasil </option>
                    </select>
                </div>

                <div class="grupo_input">
                    <input type="text" placeholder="Primeiro Nome" class="input">
                    <input type="text" placeholder="Segundo Nome" class="input">
                </div>

                <input type="text" placeholder="Bairro" class="input">

                <input type="text" placeholder="Rua" class="input">

                <input type="text" placeholder="N°" class="input">

                <div class="checkout_grupo">
                    <div>
                        <select name="" id="estado" class="select_checkout input" style="display: none;">
                            <option value="all" selected>Selecione seu estado</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>

                    </div>

                    <input type="text" placeholder="CEP" class="input">
                </div>

                <h2 class="checkout_titulo">Forma de Pagamento</h2>

                <ul class="metodos_pagamento grid">
                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_tb" name="metodo_pagamento" value="transbank" class="checkout_radio">
                        <label for="metodo_pagamento_tb">
                            Trasferência Bancária <img src="<?= BASE_URL ?>/public/assets/image/trasferencia_bancaria.png" alt="">
                        </label>
                    </li>

                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_paypal" name="metodo_pagamento" value="paypal" class="checkout_radio">
                        <label for="metodo_pagamento_paypal">
                            PayPal <img src="<?= BASE_URL ?>/public/assets/image/paypal.png" alt="">
                        </label>
                    </li>

                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_cc" name="metodo_pagamento" value="cc" class="checkout_radio">
                        <label for="metodo_pagamento_cc">
                            Cartão de Crédito <img src="<?= BASE_URL ?>/public/assets/image/cartao.png" alt="">
                        </label>
                    </li>

                    <li class="metodo_pagamento">
                        <input type="radio" id="metodo_pagamento_pix" name="metodo_pagamento" value="pix" class="checkout_radio">
                        <label for="metodo_pagamento_pix">
                            Pix <img src="<?= BASE_URL ?>/public/assets/image/pix.png" alt="">
                        </label>
                    </li>
                </ul>

                <button type="submit" class="btn btn_finalizar">Finalizar Pedido</button>
            </form>

            <div class="checkout_conteudo">
                <ul class="carrinho_itens grid">
                    <li class="carrinho_item">
                        <div class="carrinho_item_container">
                            <img src="<?= BASE_URL ?>/public/assets/image/category-img-1.jpg" class="carrinho_item_imagem" alt="">
                            <span class="carrinho_item_quantidade">1</span>
                        </div>

                        <div>
                            <h3 class="carrinho_item_titulo">
                                <a href="#">Nome do Produto</a>
                            </h3>
                            <span class="carrinho_item_preco">R$ 200,00</span>
                        </div>
                    </li>

                    <li class="carrinho_item">
                        <div class="carrinho_item_container">
                            <img src="<?= BASE_URL ?>/public/assets/image/category-img-2.jpg" class="carrinho_item_imagem" alt="">
                            <span class="carrinho_item_quantidade">1</span>
                        </div>

                        <div>
                            <h3 class="carrinho_item_titulo">
                                <a href="#">Nome do Produto</a>
                            </h3>
                            <span class="carrinho_item_preco">R$ 200,00</span>
                        </div>
                    </li>

                    <li class="carrinho_item">
                        <div class="carrinho_item_container">
                            <img src="<?= BASE_URL ?>/public/assets/image/category-img-3.jpg" class="carrinho_item_imagem" alt="">
                            <span class="carrinho_item_quantidade">1</span>
                        </div>

                        <div>
                            <h3 class="carrinho_item_titulo">
                                <a href="#">Nome do Produto</a>
                            </h3>
                            <span class="carrinho_item_preco">R$ 200,00</span>
                        </div>
                    </li>
                </ul>

                <div class="carrinho_total">
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
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include_once "../rodape.php"; ?>

<script>
    ['pais'].forEach((id) => {
        NiceSelect.bind(document.getElementById(id), {
            searchable: false,
        });
    });


    ['estado'].forEach((id) => {
        NiceSelect.bind(document.getElementById(id), {
            searchable: true,
        });
    })
</script>
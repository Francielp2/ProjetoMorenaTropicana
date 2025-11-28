<section class="produtos_inicio container section">
    <h2 class="section_titulo titulo_central" data-title="Verifique os produtos do dia">
        Produtos em Destaque
    </h2>
    <!-- Carrocel de Produtos -->
    <div class="swiper swiper_produto">
        <div class="swiper-wrapper">

            <!-- Primeiro Cartão De Produtos -->

            <article class="swiper-slide cartao_produto">

                <div class="cabecalho_produto">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-1.jpg" alt="" class="imagem_produto">

                    <div class="conteudo_produto">

                        <div class="topo_produto">
                            <ul class="estrelas_produto">
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>

                                <li class="avaliaçao_produto">4.9</li>
                            </ul>

                            <div class="produto_cores">
                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0,60%,64%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 0%, 100%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(159, 46%, 56%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(223, 60%, 66%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 100%, 68%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(112, 81%, 67%)"></span>
                                </div>
                            </div>
                        </div><!--fechamento do topo produtos -->

                        <div class="tamanho_produto">
                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-small" checked>
                                <label for="x-small" class="tamanho_produto_label">PP</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="small">
                                <label for="small" class="tamanho_produto_label">P</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="medium">
                                <label for="medium" class="tamanho_produto_label">M</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="large">
                                <label for="large" class="tamanho_produto_label">G</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-large">
                                <label for="x-large" class="tamanho_produto_label">GG</label>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn_produto">Adicionar ao Carrinho</a>
                    </div> <!--fechamento do conteudo produto -->
                </div>

                <div class="produto_rodape">
                    <div>
                        <h3 class="titilo_produto">
                            <a href="#">Nome do Produto</a>
                        </h3>
                        <span class="preco_produto">R$ 200,00</span>
                    </div>

                    <a href="#" class="produto_favorito"> <i class="ri-heart-line"></i></a>
                </div>

                <div class="produto_etiqueta new">Novo</div>

            </article>

            <!-- Segundo Cartão De Produtos -->

            <article class="swiper-slide cartao_produto">

                <div class="cabecalho_produto">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-2.jpg" alt="" class="imagem_produto">

                    <div class="conteudo_produto">

                        <div class="topo_produto">
                            <ul class="estrelas_produto">
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>

                                <li class="avaliaçao_produto">4.9</li>
                            </ul>

                            <div class="produto_cores">
                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0,60%,64%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 0%, 100%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(159, 46%, 56%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(223, 60%, 66%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 100%, 68%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(112, 81%, 67%)"></span>
                                </div>
                            </div>
                        </div><!--fechamento do topo produtos -->

                        <div class="tamanho_produto">
                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-small" checked>
                                <label for="x-small" class="tamanho_produto_label">PP</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="small">
                                <label for="small" class="tamanho_produto_label">P</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="medium">
                                <label for="medium" class="tamanho_produto_label">M</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="large">
                                <label for="large" class="tamanho_produto_label">G</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-large">
                                <label for="x-large" class="tamanho_produto_label">GG</label>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn_produto">Adicionar ao Carrinho</a>
                    </div> <!--fechamento do conteudo produto -->
                </div>

                <div class="produto_rodape">
                    <div>
                        <h3 class="titilo_produto">
                            <a href="#">Nome do Produto</a>
                        </h3>
                        <span class="preco_produto">R$ 200,00</span>
                    </div>

                    <a href="#" class="produto_favorito"> <i class="ri-heart-line"></i></a>
                </div>

                <div class="produto_etiqueta new">Novo</div>

            </article>

            <!-- Terceiro Cartão De Produtos -->

            <article class="swiper-slide cartao_produto">

                <div class="cabecalho_produto">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-3.png" alt="" class="imagem_produto">

                    <div class="conteudo_produto">

                        <div class="topo_produto">
                            <ul class="estrelas_produto">
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>

                                <li class="avaliaçao_produto">4.9</li>
                            </ul>

                            <div class="produto_cores">
                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0,60%,64%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 0%, 100%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(159, 46%, 56%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(223, 60%, 66%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 100%, 68%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(112, 81%, 67%)"></span>
                                </div>
                            </div>
                        </div><!--fechamento do topo produtos -->

                        <div class="tamanho_produto">
                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-small" checked>
                                <label for="x-small" class="tamanho_produto_label">PP</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="small">
                                <label for="small" class="tamanho_produto_label">P</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="medium">
                                <label for="medium" class="tamanho_produto_label">M</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="large">
                                <label for="large" class="tamanho_produto_label">G</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-large">
                                <label for="x-large" class="tamanho_produto_label">GG</label>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn_produto">Adicionar ao Carrinho</a>
                    </div> <!--fechamento do conteudo produto -->
                </div>

                <div class="produto_rodape">
                    <div>
                        <h3 class="titilo_produto">
                            <a href="#">Nome do Produto</a>
                        </h3>
                        <span class="preco_produto">R$ 200,00</span>
                    </div>

                    <a href="#" class="produto_favorito"> <i class="ri-heart-line"></i></a>
                </div>

                <div class="produto_etiqueta new">Novo</div>

            </article>

            <!-- Quarto Cartão De Produtos -->

            <article class="swiper-slide cartao_produto">

                <div class="cabecalho_produto">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-4.jpg" alt="" class="imagem_produto">

                    <div class="conteudo_produto">

                        <div class="topo_produto">
                            <ul class="estrelas_produto">
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>
                                <li><i class="ri-star-fill"></i></li>

                                <li class="avaliaçao_produto">4.9</li>
                            </ul>

                            <div class="produto_cores">
                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0,60%,64%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 0%, 100%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(159, 46%, 56%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(223, 60%, 66%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 100%, 68%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(112, 81%, 67%)"></span>
                                </div>
                            </div>
                        </div><!--fechamento do topo produtos -->

                        <div class="tamanho_produto">
                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-small" checked>
                                <label for="x-small" class="tamanho_produto_label">PP</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="small">
                                <label for="small" class="tamanho_produto_label">P</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="medium">
                                <label for="medium" class="tamanho_produto_label">M</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="large">
                                <label for="large" class="tamanho_produto_label">G</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho" id="x-large">
                                <label for="x-large" class="tamanho_produto_label">GG</label>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn_produto">Adicionar ao Carrinho</a>
                    </div> <!--fechamento do conteudo produto -->
                </div>

                <div class="produto_rodape">
                    <div>
                        <h3 class="titilo_produto">
                            <a href="#">Nome do Produto</a>
                        </h3>
                        <span class="preco_produto">R$ 200,00</span>
                    </div>

                    <a href="#" class="produto_favorito"> <i class="ri-heart-line"></i></a>
                </div>

                <div class="produto_etiqueta new">Novo</div>

            </article>

        </div>
    </div>
</section>
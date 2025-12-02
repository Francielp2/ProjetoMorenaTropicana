<section class="produtos_inicio container section">
    <h2 class="section_titulo titulo_central" data-title="Verifique os produtos do dia">
        Produtos em Destaque
    </h2>
    <!-- Carrocel de Produtos -->
    <div class="swiper swiper_produto">
        <div class="swiper-wrapper">
            <?php 
            $idsFavoritos = $idsFavoritos ?? [];
            $produtosDestaque = $produtosDestaque ?? [];
            
            if (!empty($produtosDestaque)): 
                foreach ($produtosDestaque as $produto):
                    $idProduto = (int)($produto['id_produto'] ?? 0);
                    $nomeProduto = htmlspecialchars($produto['nome'] ?? 'Produto sem nome');
                    $precoProduto = isset($produto['preco']) ? number_format((float)$produto['preco'], 2, ',', '.') : '0,00';
                    $imagemProduto = !empty($produto['imagens']) ? BASE_URL . $produto['imagens'] : '';
                    $ehFavorito = in_array($idProduto, $idsFavoritos);
            ?>
            <article class="swiper-slide cartao_produto">
                <div class="cabecalho_produto">
                    <?php if (!empty($imagemProduto)): ?>
                        <img src="<?= htmlspecialchars($imagemProduto) ?>" alt="<?= $nomeProduto ?>" class="imagem_produto">
                    <?php else: ?>
                        <div class="imagem_produto imagem_sem_foto">
                            <span>Sem imagem disponível</span>
                        </div>
                    <?php endif; ?>

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
                                    <input type="radio" name="cor_<?= $idProduto ?>" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0,60%,64%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor_<?= $idProduto ?>" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 0%, 100%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor_<?= $idProduto ?>" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(159, 46%, 56%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor_<?= $idProduto ?>" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(223, 60%, 66%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor_<?= $idProduto ?>" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(0, 100%, 68%)"></span>
                                </div>

                                <div>
                                    <input type="radio" name="cor_<?= $idProduto ?>" class="produto_cor_input">
                                    <span class="produto_cor" style="--background-color: hsl(112, 81%, 67%)"></span>
                                </div>
                            </div>
                        </div><!--fechamento do topo produtos -->

                        <div class="tamanho_produto">
                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho_<?= $idProduto ?>" id="x-small_<?= $idProduto ?>" checked>
                                <label for="x-small_<?= $idProduto ?>" class="tamanho_produto_label">PP</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho_<?= $idProduto ?>" id="small_<?= $idProduto ?>">
                                <label for="small_<?= $idProduto ?>" class="tamanho_produto_label">P</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho_<?= $idProduto ?>" id="medium_<?= $idProduto ?>">
                                <label for="medium_<?= $idProduto ?>" class="tamanho_produto_label">M</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho_<?= $idProduto ?>" id="large_<?= $idProduto ?>">
                                <label for="large_<?= $idProduto ?>" class="tamanho_produto_label">G</label>
                            </div>

                            <div>
                                <input type="radio" class="produto_tamanho_input" name="tamanho_<?= $idProduto ?>" id="x-large_<?= $idProduto ?>">
                                <label for="x-large_<?= $idProduto ?>" class="tamanho_produto_label">GG</label>
                            </div>
                        </div>

                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= $idProduto ?>" class="btn btn_produto">Ver Detalhes</a>
                    </div> <!--fechamento do conteudo produto -->
                </div>

                <div class="produto_rodape">
                    <div>
                        <h3 class="titilo_produto">
                            <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= $idProduto ?>"><?= $nomeProduto ?></a>
                        </h3>
                        <span class="preco_produto">R$ <?= $precoProduto ?></span>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial" style="display: inline;">
                        <input type="hidden" name="id_produto" value="<?= $idProduto ?>">
                        <input type="hidden" name="acao_favorito" value="<?= $ehFavorito ? 'remover' : 'adicionar' ?>">
                        <button type="submit" class="produto_favorito" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="ri-heart<?= $ehFavorito ? '-fill' : '-line' ?>" style="color: <?= $ehFavorito ? '#d32f2f' : 'inherit' ?>;"></i>
                        </button>
                    </form>
                </div>

                <div class="produto_etiqueta new">Novo</div>
            </article>
            <?php 
                endforeach;
            else: 
            ?>
            <div class="swiper-slide" style="text-align: center; padding: 2rem;">
                <p style="font-size: 1.1rem; color: #666;">Nenhum produto em destaque no momento.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

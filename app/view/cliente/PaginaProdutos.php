<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Nossos Produtos</h1>
            <p class="descricao_banner">Explore nossos produtos e descubra peças feitas para destacar seu estilo.</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>

                <li class="item_navegacao">Produtos</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="loja container section">
        <?php 
        $idsFavoritos = $idsFavoritos ?? [];
        $isQuiz = isset($_GET['quiz']) && $_GET['quiz'] == '1';
        $termoBusca = isset($_GET['busca']) ? trim($_GET['busca']) : (isset($_GET['termo']) ? trim($_GET['termo']) : '');
        $temBusca = !empty($termoBusca);
        
        if ($isQuiz): 
        ?>
            <div style="margin-bottom: 2rem; padding: 1.5rem; background-color: <?= !empty($produtos) ? '#d4edda' : '#f8d7da' ?>; color: <?= !empty($produtos) ? '#155724' : '#721c24' ?>; border-radius: 8px; text-align: center;">
                <h2 style="margin-bottom: 0.5rem; font-size: 1.5rem;">
                    <?= !empty($produtos) ? 'Produtos Recomendados' : 'Sem Produtos Recomendados' ?>
                </h2>
                <p style="margin: 0; font-size: 1rem;">
                    <?= !empty($produtos) 
                        ? 'Encontramos ' . count($produtos) . ' produto(s) que correspondem às suas preferências!' 
                        : 'Não encontramos produtos que correspondam aos filtros selecionados. Tente outras opções no quiz.' ?>
                </p>
            </div>
        <?php elseif ($temBusca): ?>
            <div style="margin-bottom: 2rem; padding: 1.5rem; background-color: <?= !empty($produtos) ? '#d4edda' : '#f8d7da' ?>; color: <?= !empty($produtos) ? '#155724' : '#721c24' ?>; border-radius: 8px; text-align: center;">
                <h2 style="margin-bottom: 0.5rem; font-size: 1.5rem;">
                    <?= !empty($produtos) ? 'Resultados da Busca' : 'Nenhum resultado encontrado' ?>
                </h2>
                <p style="margin: 0; font-size: 1rem;">
                    <?= !empty($produtos) 
                        ? 'Encontramos ' . count($produtos) . ' produto(s) para "' . htmlspecialchars($termoBusca) . '"' 
                        : 'Não encontramos produtos para "' . htmlspecialchars($termoBusca) . '". Tente buscar por outro termo.' ?>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="grid produtos_loja">
            <?php if (!empty($produtos)): ?>
                <?php
                foreach ($produtos as $produto):
                    $idProduto = (int)($produto['id_produto'] ?? 0);
                    $nomeProduto = htmlspecialchars($produto['nome'] ?? 'Produto sem nome');
                    $precoProduto = isset($produto['preco']) ? number_format((float)$produto['preco'], 2, ',', '.') : '0,00';
                    $imagemProduto = !empty($produto['imagens']) ? BASE_URL . $produto['imagens'] : '';
                ?>
            <article class="cartao_produto">

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

                    <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" style="display: inline;">
                        <input type="hidden" name="id_produto" value="<?= $idProduto ?>">
                        <input type="hidden" name="acao_favorito" value="<?= in_array($idProduto, $idsFavoritos) ? 'remover' : 'adicionar' ?>">
                        <button type="submit" class="produto_favorito" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="ri-heart<?= in_array($idProduto, $idsFavoritos) ? '-fill' : '-line' ?>" style="color: <?= in_array($idProduto, $idsFavoritos) ? '#d32f2f' : 'inherit' ?>;"></i>
                        </button>
                    </form>
                </div>

            </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; font-size: 1.1rem;">Nenhum produto disponível no momento.</p>
            <?php endif; ?>
        </div>

        <ul class="paginacao">
            <li>
                <a href="#" class="btn_pagina">
                    <i class="ri-arrow-left-double-fill"></i>
                </a>
            </li>

            <li><a href="#" class="link_pagina">01</a></li>

            <li><a href="#" class="link_pagina active">02</a></li>

            <li><a href="#" class="link_pagina">03</a></li>

            <li><span class="pontos">......</span></li>

            <li><a href="#" class="link_pagina">08</a></li>

            <li>
                <a href="#" class="btn_pagina">
                    <i class="ri-arrow-right-double-fill"></i>
                </a>
            </li>

        </ul>

    </section>
</main>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
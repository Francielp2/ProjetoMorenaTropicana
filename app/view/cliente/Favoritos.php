<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Favoritos</h1>
            <p class="descricao_banner">Tudo o que você amou está aqui. Continue explorando e finalize sua escolha.</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>

                <li class="item_navegacao">Favoritos</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="favoritos container section">
        <table class="tabela">
            <thead class="thead">
                <th class="thead_titulo">Produtos</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ação</th>
            </thead>

            <tbody class="tbody">
                <tr>
                    <td class="carriho_dados">
                        <input type="checkbox" class="checkbox_favoritos">
                        <img src="<?= BASE_URL ?>/public/assets/image/product-5.jpg" alt="" class="carrinho_imagem favoritos_imagem">

                        <div>
                            <h3 class="carrinho_titulo">
                                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos">Nome do Produto</a>
                            </h3>

                            <span class="preco_carrinho">R$ 200,00</span>
                            <div class="carrinho_tamanho">Tamanho: M</div>
                        </div>
                    </td>

                    <td class="subtotal_coluna">
                        <span class="carrinho_subtotal">R$ 200,00</span>
                    </td>

                    <td class="avaliacao_coluna">
                        <span class="avaliacao">Em estoque</span>
                    </td>

                    <td class="acoes_coluna">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn-dark favoritos_btn">Adicionar ao carrinho</a>
                        <button class="remover_btn">&times;</button>
                    </td>
                </tr>

                <tr>
                    <td class="carriho_dados">
                        <input type="checkbox" class="checkbox_favoritos">
                        <img src="<?= BASE_URL ?>/public/assets/image/product-4.jpg" alt="" class="carrinho_imagem favoritos_imagem">

                        <div>
                            <h3 class="carrinho_titulo">
                                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos">Nome do Produto</a>
                            </h3>

                            <span class="preco_carrinho">R$ 200,00</span>
                            <div class="carrinho_tamanho">Tamanho: M</div>
                        </div>
                    </td>

                    <td class="subtotal_coluna">
                        <span class="carrinho_subtotal">R$ 200,00</span>
                    </td>

                    <td class="avaliacao_coluna">
                        <span class="avaliacao">Em estoque</span>
                    </td>

                    <td class="acoes_coluna">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn-dark favoritos_btn">Adicionar ao carrinho</a>
                        <button class="remover_btn">&times;</button>
                    </td>
                </tr>

                <tr>
                    <td class="carriho_dados">
                        <input type="checkbox" class="checkbox_favoritos">
                        <img src="<?= BASE_URL ?>/public/assets/image/product-2.jpg" alt="" class="carrinho_imagem favoritos_imagem">

                        <div>
                            <h3 class="carrinho_titulo">
                                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos">Nome do Produto</a>
                            </h3>

                            <span class="preco_carrinho">R$ 200,00</span>
                            <div class="carrinho_tamanho">Tamanho: M</div>
                        </div>
                    </td>

                    <td class="subtotal_coluna">
                        <span class="carrinho_subtotal">R$ 200,00</span>
                    </td>

                    <td class="avaliacao_coluna">
                        <span class="avaliacao">Em estoque</span>
                    </td>

                    <td class="acoes_coluna">
                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos" class="btn btn-dark favoritos_btn">Adicionar ao carrinho</a>
                        <button class="remover_btn">&times;</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
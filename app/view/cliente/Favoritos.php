<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Favoritos</h1>
            <p class="descricao_banner">Tudo o que você amou está aqui. Continue explorando e finalize sua escolha.</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>
                <li class="item_navegacao">Favoritos</li>
            </ul>
        </div>
    </section>

    <section class="favoritos container section">
        <?php if (!empty($mensagem)): ?>
            <div style="background-color: <?= $tipoMensagem === 'sucesso' ? '#d4edda' : '#f8d7da' ?>; color: <?= $tipoMensagem === 'sucesso' ? '#155724' : '#721c24' ?>; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($favoritosFormatados)): ?>
            <div style="text-align: center; padding: 3rem;">
                <i class="ri-heart-line" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                <h2 style="color: #666; margin-bottom: 1rem;">Nenhum produto favoritado ainda</h2>
                <p style="color: #999; margin-bottom: 2rem;">Adicione produtos aos seus favoritos para encontrá-los facilmente depois!</p>
                <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" class="btn btn_produto">
                    Explorar Produtos
                </a>
            </div>
        <?php else: ?>
            <table class="tabela">
                <thead class="thead">
                    <th class="thead_titulo">Produtos</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Ação</th>
                </thead>

                <tbody class="tbody">
                    <?php foreach ($favoritosFormatados as $favorito): ?>
                        <tr>
                            <td class="carriho_dados">
                                <div style="display: flex; align-items: center; justify-content: center; width: 120px; height: 120px; background-color: #f5f5f5; border-radius: 8px; margin-right: 1rem; flex-shrink: 0;">
                                    <?php if (!empty($favorito['imagem'])): ?>
                                        <img src="<?= htmlspecialchars($favorito['imagem']) ?>"
                                            alt="<?= htmlspecialchars($favorito['nome_produto']) ?>"
                                            class="carrinho_imagem favoritos_imagem"
                                            style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; color: #999;">
                                            <i class="ri-image-line" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <h3 class="carrinho_titulo">
                                        <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= $favorito['id_produto'] ?>">
                                            <?= $favorito['nome_produto'] ?>
                                        </a>
                                    </h3>

                                    <span class="preco_carrinho">R$ <?= $favorito['preco'] ?></span>
                                    <?php if (!empty($favorito['categoria'])): ?>
                                        <div class="carrinho_tamanho">Categoria: <?= htmlspecialchars($favorito['categoria']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="subtotal_coluna">
                                <span class="carrinho_subtotal">R$ <?= $favorito['preco'] ?></span>
                            </td>

                            <td class="avaliacao_coluna">
                                <span class="admin-badge <?= htmlspecialchars($favorito['status_classe'] ?? '') ?>"><?= htmlspecialchars($favorito['status_estoque'] ?? '') ?></span>
                            </td>

                            <td class="acoes_coluna" style="text-align: center; vertical-align: middle;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
                                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= $favorito['id_produto'] ?>"
                                        class="btn btn-dark favoritos_btn" style="white-space: nowrap;">
                                        Adicionar ao Carrinho
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=favoritos" style="display: inline;">
                                        <input type="hidden" name="remover_favorito" value="1">
                                        <input type="hidden" name="id_favorito" value="<?= $favorito['id_favorito'] ?>">
                                        <button type="submit" class="remover_btn" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: #d32f2f; padding: 0.5rem;"
                                            onclick="return confirm('Deseja remover este produto dos favoritos?')">
                                            &times;
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Detalhes do produto</h1>
            <p class="descricao_banner">Tudo o que você precisa saber.</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>

                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos">Produtos</a>
                </li>

                <li class="item_navegacao">
                    Detalhes do Produto
                </li>

            </ul>
        </div>
    </section>

    <?php if (!empty($mensagem)): ?>
        <div style="background-color: <?= $tipoMensagem === 'sucesso' ? '#d4edda' : '#f8d7da' ?>; 
                    color: <?= $tipoMensagem === 'sucesso' ? '#155724' : '#721c24' ?>; 
                    padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 5px; text-align: center;">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php
    $ehFavorito = $ehFavorito ?? false;
    if ($produto):
    ?>
        <section class="detalhes">
            <div class="detalhes_conteudo container grid">
                <div class="detalhes_esquerda">
                    <?php
                    $imagemPrincipal = '';
                    if (!empty($produto['imagens'])) {
                        $imagemPrincipal = BASE_URL . $produto['imagens'];
                    }
                    ?>
                    <div class="detalhes_imagens grid">
                        <?php if (!empty($imagemPrincipal)): ?>
                            <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="detalhes_imagem imagem_ativa">
                            <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="detalhes_imagem">
                            <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="detalhes_imagem">
                        <?php else: ?>
                            <div class="detalhes_imagem imagem_ativa" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-height:100px;">
                                <span>Sem imagem</span>
                            </div>
                            <div class="detalhes_imagem" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-height:100px;">
                                <span>Sem imagem</span>
                            </div>
                            <div class="detalhes_imagem" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-height:100px;">
                                <span>Sem imagem</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($imagemPrincipal)): ?>
                        <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="detalhes_imagem_principal">
                    <?php else: ?>
                        <div class="detalhes_imagem_principal" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-height:400px;">
                            <span>Sem imagem disponível</span>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="detalhes_direita">
                    <ul class="estrelas_produto">
                        <li><i class="ri-star-fill"></i></li>
                        <li><i class="ri-star-fill"></i></li>
                        <li><i class="ri-star-fill"></i></li>
                        <li><i class="ri-star-fill"></i></li>
                        <li><i class="ri-star-fill"></i></li>

                        <li class="avaliaçao_produto">(4.9)</li>
                    </ul>

                    <h3 class="titulo_detalhes"><?= htmlspecialchars($produto['nome'] ?? 'Produto sem nome') ?></h3>
                    <span class="preco_produto">R$ <?= isset($produto['preco']) ? number_format((float)$produto['preco'], 2, ',', '.') : '0,00' ?></span>

                    <ul class="detalhes_ponto_chave">
                        <li><i class="ri-check-line"></i>Look Moderno</li>
                        <li><i class="ri-check-line"></i>Ótima qualidade de fabricação</li>
                    </ul>

                    <?php
                    $estoqueTotal = isset($produto['estoque_total']) ? (int)$produto['estoque_total'] : 0;
                    ?>
                    <h3 class="detalhes_texto">Aproveite, <?= $estoqueTotal ?> <?= $estoqueTotal == 1 ? 'item' : 'itens' ?> restantes no estoque</h3>
                    <div class="detalhes_progresso">
                        <div class="barra_progresso"></div>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&amp;id=<?= (int)($produto['id_produto'] ?? 0) ?>">
                        <div class="detalhes_tamanho">
                            <span class="detalhes_tamanho_titulo">Escolha o Tamanho:</span>

                            <div class="tamanho_produto">
                                <?php if (!empty($tamanhosDisponiveis)): ?>
                                    <?php $primeiro = true; ?>
                                    <?php foreach ($tamanhosDisponiveis as $tamanho): ?>
                                        <?php $idTamanho = 'tam_' . preg_replace('/[^a-zA-Z0-9]/', '_', $tamanho); ?>
                                        <div class="tamanho_container" data-tamanho-id="<?= $idTamanho ?>">
                                            <input type="radio" class="produto_tamanho_input" name="tamanho" id="<?= $idTamanho ?>" value="<?= htmlspecialchars($tamanho) ?>" <?= $primeiro ? 'checked' : '' ?> required>
                                            <label for="<?= $idTamanho ?>" class="tamanho_produto_label <?= $primeiro ? 'selecionado' : '' ?>"><?= htmlspecialchars($tamanho) ?></label>
                                        </div>
                                        <?php $primeiro = false; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="font-size:0.9rem;color:#666;">Nenhum tamanho disponível cadastrado.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="detalhes_cores">
                            <span class="detalhes_cores_titulo">Cor:</span>

                            <div class="produto_cores">
                                <?php if (!empty($coresDisponiveis)): ?>
                                    <?php $primeiraCor = true; ?>
                                    <?php foreach ($coresDisponiveis as $cor): ?>
                                        <?php $idCor = 'cor_' . preg_replace('/[^a-zA-Z0-9]/', '_', $cor); ?>
                                        <div class="cor_container <?= $primeiraCor ? 'selecionado' : '' ?>" data-cor-id="<?= $idCor ?>">
                                            <input type="radio" name="cor" class="produto_cor_input" id="<?= $idCor ?>" value="<?= htmlspecialchars($cor) ?>" <?= $primeiraCor ? 'checked' : '' ?> required>
                                            <span class="produto_cor" style="--background-color: hsl(0,0%,80%);"></span>
                                            <span style="font-size:0.8rem;margin-left:5px;"><?= htmlspecialchars($cor) ?></span>
                                        </div>
                                        <?php $primeiraCor = false; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="font-size:0.9rem;color:#666;">Nenhuma cor disponível cadastrada.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="detalhes_carrinho">
                            <div class="adicionar_carrinho">
                                <button type="button" class="rem" onclick="diminuirQuantidade()">-</button>
                                <input type="number" name="quantidade" id="quantidadeInput" value="1" min="1" class="contador" required>
                                <button type="button" class="add" onclick="aumentarQuantidade()">+</button>
                            </div>

                            <button type="submit" name="adicionar_carrinho" class="btn">Adicionar ao Carrinho</button>
                        </div>
                    </form>

                    <div class="detalhes_acoes">
                        <a href="#"><i class="ri-arrow-left-right-line"></i>Adicionar para comparar</a>
                        <form method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=detalhes_produtos&id=<?= (int)($produto['id_produto'] ?? 0) ?>" target="favorito_target" class="form-favorito" data-favorito-uid="prod_<?= (int)($produto['id_produto'] ?? 0) ?>" style="display: inline;">
                            <input type="hidden" name="id_produto" value="<?= (int)($produto['id_produto'] ?? 0) ?>">
                            <input type="hidden" name="acao_favorito" value="<?= $ehFavorito ? 'remover' : 'adicionar' ?>">
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: inherit; font-size: inherit; padding: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="ri-heart<?= $ehFavorito ? '-fill' : '-line' ?>" style="color: <?= $ehFavorito ? '#d32f2f' : 'inherit' ?>;"></i>
                                <?= $ehFavorito ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos' ?>
                            </button>
                        </form>
                    </div>

                    <div class="detalhes_retorno grid">
                        <li><i class="ri-truck-fill"></i>Frete grátis</li>
                        <li><i class="ri-arrow-go-back-line"></i>Devolva de graça em até 30 dias</li>
                    </div>

                </div>
            </div>
        </section>

        <section class="detalhes_content section container">
            <div class="aba_btns">
                <button class="aba_btn aba_ativa" data-target="#description">Descrição</button>
                <button class="aba_btn" data-target="#specifications">Especificações</button>
            </div>

            <div class="aba_content">
                <div class="item_aba aba_ativa" id="description">
                    <div class="descricao grid">
                        <div>
                            <p class="detalhes_descricao">
                                <?= nl2br(htmlspecialchars($produto['descricao'] ?? 'Sem descrição para este produto.')) ?>
                            </p>

                            <h3 class="titulo_descricao">Características do produto</h3>

                            <ul class="lista_descricao grid">
                                <li>Categoria: <?= htmlspecialchars($produto['categoria'] ?? 'Não informada') ?></li>
                                <li>Estoque disponível: <?= $estoqueTotal ?> unidades</li>
                            </ul>
                        </div>

                        <div class="descricao_imagens grid">
                            <?php if (!empty($imagemPrincipal)): ?>
                                <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="descricao_imagem">
                                <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="descricao_imagem">
                                <img src="<?= htmlspecialchars($imagemPrincipal) ?>" alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" class="descricao_imagem">
                            <?php else: ?>
                                <div class="descricao_imagem" style="display:flex;align-items:center;justify-content:center;background:#f4f4f4;min-height:150px;">
                                    <span>Sem imagens adicionais</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="item_aba" id="specifications">
                    <ul class="especificacoes">
                        <li>
                            <h4>Categoria:</h4>
                            <span><?= htmlspecialchars($produto['categoria'] ?? 'Não informada') ?></span>
                        </li>
                        <li>
                            <h4>Preço:</h4>
                            <span>R$ <?= isset($produto['preco']) ? number_format((float)$produto['preco'], 2, ',', '.') : '0,00' ?></span>
                        </li>
                        <li>
                            <h4>Estoque:</h4>
                            <span><?= $estoqueTotal ?> unidades</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="container section" style="text-align: center; padding: 40px;">
            <h2 style="color: #666; margin-bottom: 20px;">Produto não encontrado</h2>
            <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=produtos" class="btn">Voltar para Produtos</a>
        </section>
    <?php endif; ?>
</main>

<script>
    const mainImg = document.querySelector('.detalhes_imagem_principal');
    const smallImg = document.querySelectorAll('.detalhes_imagem');

    if (smallImg.length > 0 && mainImg) {
        smallImg.forEach((img) => {
            img.addEventListener('click', function() {
                if (mainImg.tagName === 'IMG') {
                    mainImg.src = this.src;
                }

                smallImg.forEach((i) => i.classList.remove('imagem_ativa'));
                this.classList.add('imagem_ativa');
            });
        });
    }

    const tabs = document.querySelectorAll('[data-target]'),
        tabContents = document.querySelectorAll('.item_aba');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.target);

            tabContents.forEach((tabContent) => {
                tabContent.classList.remove('aba_ativa');
            });

            if (target) {
                target.classList.add('aba_ativa');
            }

            tabs.forEach((tab) => {
                tab.classList.remove('aba_ativa');
            });
            tab.classList.add('aba_ativa');
        });
    });

    function aumentarQuantidade() {
        const input = document.getElementById('quantidadeInput');
        if (input) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function diminuirQuantidade() {
        const input = document.getElementById('quantidadeInput');
        if (input) {
            const valor = parseInt(input.value);
            if (valor > 1) {
                input.value = valor - 1;
            }
        }
    }

    // Interação visual para tamanhos
    document.addEventListener('DOMContentLoaded', function() {
        // Tamanhos
        const tamanhoInputs = document.querySelectorAll('.produto_tamanho_input');
        tamanhoInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Remove classe selecionado de todos os labels
                document.querySelectorAll('.tamanho_produto_label').forEach(label => {
                    label.classList.remove('selecionado');
                });
                // Adiciona classe selecionado ao label do input marcado
                const label = document.querySelector('label[for="' + this.id + '"]');
                if (label) {
                    label.classList.add('selecionado');
                }
            });

            // Inicializa o estado visual do tamanho marcado por padrão
            if (input.checked) {
                const label = document.querySelector('label[for="' + input.id + '"]');
                if (label) {
                    label.classList.add('selecionado');
                }
            }
        });

        // Cores
        const corInputs = document.querySelectorAll('.produto_cor_input');
        corInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Remove classe selecionado de todos os containers de cor
                document.querySelectorAll('.cor_container').forEach(container => {
                    container.classList.remove('selecionado');
                });
                // Adiciona classe selecionado ao container do input marcado
                const container = this.closest('.cor_container');
                if (container) {
                    container.classList.add('selecionado');
                }
            });

            // Inicializa o estado visual da cor marcada por padrão
            if (input.checked) {
                const container = input.closest('.cor_container');
                if (container) {
                    container.classList.add('selecionado');
                }
            }
        });

        // Permite clicar no container inteiro para selecionar
        document.querySelectorAll('.cor_container').forEach(container => {
            container.addEventListener('click', function(e) {
                // Não dispara se clicar diretamente no input
                if (e.target !== this.querySelector('.produto_cor_input')) {
                    const input = this.querySelector('.produto_cor_input');
                    if (input) {
                        input.checked = true;
                        input.dispatchEvent(new Event('change'));
                    }
                }
            });
        });
    });
</script>

<style>
    /* Estilo para tamanho selecionado */
</style>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
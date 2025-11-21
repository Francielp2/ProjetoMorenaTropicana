<?php include_once "../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Detalhes do produto</h1>
            <p class="descricao_banner">Tudo o que você precisa saber.</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/view/cliente/tela_inicial.php">Início</a>
                </li>

                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/view/cliente/PaginaProdutos.php">Produtos</a>
                </li>

                <li class="item_navegacao">
                    Detalhes do Produto
                </li>

            </ul>
        </div>
    </section>

    <section class="detalhes">
        <div class="detalhes_conteudo container grid">
            <div class="detalhes_esquerda">
                <div class="detalhes_imagens grid">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-1-1.jpg" alt="" class="detalhes_imagem imagem_ativa">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-1-2.jpg" alt="" class="detalhes_imagem">
                    <img src="<?= BASE_URL ?>/public/assets/image/product-1-3.jpg" alt="" class="detalhes_imagem">
                </div>

                <img src="<?= BASE_URL ?>/public/assets/image/product-1-1.jpg" alt="" class="detalhes_imagem_principal">

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

                <h3 class="titulo_detalhes">Nome do Produto</h3>
                <span class="preco_produto">R$ 200,00</span>

                <ul class="detalhes_ponto_chave">
                    <li><i class="ri-check-line"></i>Look Moderno</li>
                    <li><i class="ri-check-line"></i>Ótima qualidade de fabricação</li>
                </ul>

                <h3 class="detalhes_texto">Aproveite, 5 itens restantes no estoque</h3>
                <div class="detalhes_progresso">
                    <div class="barra_progresso"></div>
                </div>

                <div class="detalhes_tamanho">
                    <span class="detalhes_tamanho_titulo">Escolha o Tamanho:</span>

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
                </div>

                <div class="detalhes_cores">
                    <span class="detalhes_cores_titulo">Cor:</span>

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
                </div>

                <div class="detalhes_carrinho">
                    <div class="adicionar_carrinho">
                        <button type="button" class="rem">-</button>
                        <input type="text" value="1" class="contador">
                        <button type="button" class="add">+</button>
                    </div>

                    <a href="#" class="btn">Adicionar ao Carrinho</a>
                </div>

                <div class="detalhes_acoes">
                    <a href="#"><i class="ri-arrow-left-right-line"></i>Adicionar para comparar</a>
                    <a href="#"><i class="ri-heart-line"></i>Adicionar aos Favoritos</a>
                </div>

                <div class="detalhes_retorno grid">
                    <li><i class="ri-truck-fill"></i>Frete grátis apartir de 200 reais</li>
                    <li><i class="ri-arrow-go-back-line"></i>Devolva de graça em até 30 dias</li>
                </div>

            </div>
        </div>
    </section>

    <section class="detalhes_content section container">
        <div class="aba_btns">
            <button class="aba_btn aba_ativa" data-target="#description">Descrição</button>
            <button class="aba_btn" data-target="#specifications">Especificações</button>
            <!-- <button class="aba_btn" data-target="#"></button> -->
        </div>

        <div class="aba_content">
            <div class="item_aba aba_ativa" id="description">
                <div class="descricao grid">
                    <div>
                        <p class="detalhes_descricao">
                            Essa jaqueta jeans feminina na cor cinza é a combinação perfeita entre estilo e versatilidade. Com um acabamento acetinado e corte moderno, ela traz um visual despojado e elegante que se adapta a diferentes ocasiões, do casual ao urbano. Sua tonalidade neutra permite combinações com diversas cores e estampas.
                            <br><br>
                            Feita em jeans de alta qualidade, a peça oferece durabilidade e conforto, com caimento leve e macio ao toque. Os detalhes em costura reforçada e botões metálicos adicionam um charme extra, ressaltando o design clássico com um toque contemporâneo. É uma escolha ideal para quem busca estar sempre bem vestida sem abrir mão da praticidade.
                            <br><br>
                            Use com uma camiseta básica e calça skinny para um look neutro bem urbano, ou aposte em um vestido leve e tênis para uma produção mais descontraída e cheia de personalidade. Seja qual for o seu estilo, essa jaqueta cinza jeans vai se tornar sua peça-curinga favorita no guarda-roupa.
                        </p>

                        <h3 class="titulo_descricao">Características do produto</h3>

                        <ul class="lista_descricao grid">
                            <li>Estilo casual e atemporal.</li>
                            <li>Combina com qualquer look.</li>
                            <li>Elegância com toque urbano.</li>
                        </ul>
                    </div>

                    <div class="descricao_imagens grid">
                        <img src="<?= BASE_URL ?>/public/assets/image/description-1.jpg" alt="" class="descricao_imagem">
                        <img src="<?= BASE_URL ?>/public/assets/image/description-2.jpg" alt="" class="descricao_imagem">
                        <img src="<?= BASE_URL ?>/public/assets/image/description-3.jpg" alt="" class="descricao_imagem">
                    </div>
                </div>
            </div>

            <div class="item_aba" id="specifications">
                <ul class="especificacoes">
                    <li>
                        <h4>Marca:</h4>
                        <span>Artisanhide</span>
                    </li>
                    <li>
                        <h4>Cor:</h4>
                        <span>Cinza</span>
                    </li>
                    <li>
                        <h4>Material</h4>
                        <span>jeans</span>
                    </li>
                    <li>
                        <h4>Peso</h4>
                        <span>250g</span>
                    </li>
                    <li>
                        <h4>Qualidade</h4>
                        <span>premium</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</main>

<script>
    const mainImg = document.querySelector('.detalhes_imagem_principal');
    const smallImg = document.querySelectorAll('.detalhes_imagem');

    smallImg.forEach((img) => {
        img.addEventListener('click', function() {
            mainImg.src = this.src;

            smallImg.forEach((i) => i.classList.remove('imagem_ativa'));
            this.classList.add('imagem_ativa');
        });
    });

    const tabs = document.querySelectorAll('[data-target]'),
        tabContents = document.querySelectorAll('.item_aba');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.target);

            tabContents.forEach((tabContent) => {
                tabContent.classList.remove('aba_ativa');
            });

            target.classList.add('aba_ativa');

            tabs.forEach((tab) => {
                tab.classList.remove('aba_ativa');
            });
            tab.classList.add('aba_ativa');
        });
    });
</script>

<?php include_once "../rodape.php"; ?>
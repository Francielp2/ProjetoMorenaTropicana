<?php include_once "../Cabecalho.php"; ?>

<main class="principal">
    <section class="inicio">
        <div class="swiper home-swiper">
            <div class="swiper-wrapper">

                <!-- slide 1 -->

                <div class="swiper-slide" style="background: url(<?= BASE_URL ?>/public/assets/image/img-inicio1.jpg); repeat: no-repeat; background-position:center center; background-size: cover;">
                    <div class="container">

                        <div class="inicio_tag">
                            <img src="<?= BASE_URL ?>/public/assets/image/raio.svg" alt="" class="img_inicio"> Em Alta
                        </div>

                        <h1 class="titulo_inicio">Moda feminina clássica e elegante</h1>

                        <p class="descricao_inicial">
                            Roupas que unem elegância, conforto e autenticidade para realçar sua verdadeira essência.
                        </p>

                        <a href="<?= BASE_URL ?>/app/view/cliente/PaginaProdutos.php" class="btn">Compre Agora</a>

                    </div>
                </div>

                <!-- Slide 2 -->

                <div class="swiper-slide" style="
                    background: url(<?= BASE_URL ?>/public/assets/image/img-inicio2.png); background-repeat: no-repeat; background-position:center center; background-size: cover;">
                    <div class="container">

                        <div class="inicio_tag">
                            <img src="<?= BASE_URL ?>/public/assets/image/raio.svg" alt="" class="img_inicio"> Em Alta
                        </div>

                        <h1 class="titulo_inicio" style="color: var(--cor_branca)">Moda feminina clássica e elegante</h1>

                        <p class="descricao_inicial" style="color: var(--cor_branca)">
                            Roupas que unem elegância, conforto e autenticidade para realçar sua verdadeira essência.
                        </p>

                        <a href="<?= BASE_URL ?>/app/view/cliente/PaginaProdutos.php" class="btn">Compre Agora</a>

                    </div>
                </div>

                <div class="swiper-pagination"></div>
            </div>
    </section>


</main>

<?php include_once "produtos.php" ?>

<?php include_once "funcionalidades.php" ?>

<?php include_once "boletin-informativo.php"?>

<?php include_once "../rodape.php"; ?>



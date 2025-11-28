<footer class="footer" id="rodape">
    <div class="footer_container grid section">

        <!-- Parte de links sociais e logo -->
        <div class="logo_icones">
            <div class="logo_footer_div">
                <img src="<?= BASE_URL ?>/public/assets/image/logo.png" alt="Logo Rodapé" class="logo_footer">
                <span class="nome_logo_footer">Morena Tropicana</span>
            </div>

            <div class="social_footer">
                <a href="https://www.facebook.com/" class="social_footer_link"><i class="ri-facebook-circle-fill"></i>
                </a>

                <a href="https://www.twitter.com/" class="social_footer_link"><i class="ri-twitter-x-line"></i>
                </a>

                <a href="https://www.instagram.com/" class="social_footer_link"><i class="ri-instagram-line"></i>
                </a>

                <a href="https://www.youtube.com/" class="social_footer_link"><i class="ri-youtube-fill"></i>
                </a>
            </div>
        </div>

        <!-- Contatos -->
        <div>
            <h3 class="titulo_footer">Contatos</h3>

            <ul class="contato_footer grid">
                <li class="item_footer">
                    <img src="<?= BASE_URL ?>/public/assets/image/telefone.svg" alt="" class="icone_footer">
                    <a href="#" class="link_footer">+55 (77) 98128-4165</a>
                </li>

                <li class="item_footer">
                    <img src="<?= BASE_URL ?>/public/assets/image/envelope.svg" alt="" class="icone_footer">
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=morenatropicana.official@gmail.com&su=Contato%20via%20site&body=Olá,%20tenho%20interesse%20em%20saber%20mais." target="_blank" class="link_footer">morenatropicana.official@gmail.com</a>
                </li>

                <li class="item_footer">
                    <img src="<?= BASE_URL ?>/public/assets/image/map.svg" alt="" class="icone_footer">
                    <a href="#" class="endereco_footer">Loja online - <br> atendemos em todo o Brasil</a>
                </li>
            </ul>

        </div>

        <!-- links -->
        <!-- <div>

            <h3 class="titulo_footer">Links</h3>

            <ul class="links_footer">
                <li>
                    <a href="#" class="link_footer">Detalhes</a>
                </li>
                <li>
                    <a href="#" class="link_footer">Avaliações</a>
                </li>
                <li>
                    <a href="#" class="link_footer">Ajuda</a>
                </li>
                <li>
                    <a href="#" class="link_footer">Avisos</a>
                </li>
            </ul>

        </div> -->
        <!-- sobre -->
        <!-- <div>

            <h3 class="titulo_footer">Sobre</h3>

            <ul class="links_footer">
                <li>
                    <a href="#" class="link_footer">Sobre Nós</a>
                </li>
                <li>
                    <a href="#" class="link_footer">Vantagens</a>
                </li>
                <li>
                    <a href="#" class="link_footer">Blog</a>
                </li>
                <li>
                    <a href="#" class="link_footer">Carreiras</a>
                </li>
            </ul>

        </div> -->
        <!-- Termos -->
        <div>

            <h3 class="titulo_footer">Termos</h3>

            <ul class="links_footer">
                <li>
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=politicas" class="link_footer">Política de Privacidade</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=politicas" class="link_footer">Termos & Condições</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=politicas" class="link_footer">Termos de Uso</a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=politicas" class="link_footer">Outros Termos</a>
                </li>
            </ul>

        </div>
    </div>

    <div class="grupo_footer">
        <span class="copy_footer">COPYRIGHT © 2025 Morena Tropicana</span>
    </div>
</footer>

<a href="#cabecalho" class="rolagem_inicio" id="scrollup">
    <i class="ri-arrow-up-line rolagem_inicio_icone"></i>
</a>


<script src="<?= BASE_URL ?>/public/assets/js/nice-select2.js"></script><!--script do nice select-->
<script src="<?= BASE_URL ?>/public/assets/js/principal.js"></script><!--script principalt-->
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script><!--script do swiper-->
<script src="<?= BASE_URL ?>/public/assets/js/inicio.js"></script><!--script do inicio-->

</body>

</html>
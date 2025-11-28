<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="main">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Políticas da Loja</h1>
            <p class="descricao_banner">Encontre as informações importantes de privacidade e outros termos de uso.</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>

                <li class="item_navegacao">Políticas</li>
                </li>
            </ul>
        </div>
    </section>

    <ul class="navegacao nav_politicas">
        <li class="item_navegacao">
            <a href="#pdp">Política De Privacidade</a>
        <li class="item_navegacao"><a href="#cdc">Condições de Compra</a></li>
        <li class="item_navegacao"><a href="#tdu">Termos de Uso</a></li>
        </li>
    </ul>
    <section class="politica_container">
        <!-- POLÍTICA DE PRIVACIDADE -->
        <div class="politica" id="pdp">
            <h2 class="titulo_politica">Política de Privacidade</h2>

            <h3 class="subtitulo_politica">Informações que Coletamos</h3>
            <p>
                Coletamos apenas os dados necessários para garantir uma boa experiência de navegação e para
                possibilitar a compra, entrega e suporte dos nossos produtos e serviços.<br><br>Dados
                fornecidos pelo usuário:
            </p>
            <br>
            <ul>
                <li>Nome completo</li>
                <li>E-mail</li>
                <li>Telefone</li>
                <li>CPF</li>
                <li>Endereço</li>
                <li>Informações de entrega</li>
                <li>Dados de pagamento</li>
            </ul>
            <br><br>
            <p>Dados de navegação:</p>
            <br>
            <ul>
                <li>Endereço IP</li>
                <li>Tipo de dispositivo</li>
                <li>Navegador utilizado</li>
                <li>Páginas visitadas</li>
                <li>Tempo de permanência no site</li>
            </ul>
            <br><br>

            <p>
                Cookies:
                <br><br>Utilizamos cookies para melhorar sua experiência, lembrar preferências, medir
                desempenho e personalizar conteúdos exibidos em nossa plataforma.
            </p>

            <h3 class="subtitulo_politica">Como Utilizamos Seus Dados</h3>
            <ul>
                <li>Processar pedidos e pagamentos</li>
                <li>Realizar entregas e atualizações de status</li>
                <li>Prestar suporte ao cliente</li>
                <li>Personalizar sua experiência no site</li>
                <li>Cumprir obrigações legais e fiscais</li>
                <li>Melhorar o desempenho da plataforma</li>
            </ul>

            <h3 class="subtitulo_politica">Compartilhamento de Dados</h3>
            <p>
                Seus dados podem ser compartilhados apenas com serviços essenciais ao funcionamento
                da loja, como gateways de pagamento, serviços de entrega e ferramentas de análise.
                Não vendemos ou repassamos informações para terceiros com fins comerciais.
            </p>

            <h3 class="subtitulo_politica">Armazenamento e Segurança</h3>
            <p>
                Adotamos práticas de segurança para proteger suas informações contra acessos
                não autorizados, perda ou vazamento. Os dados são armazenados de forma segura
                e utilizados somente para as finalidades desta política.
            </p>

            <h3 class="subtitulo_politica">Direitos do Usuário</h3>
            <ul>
                <li>Acessar os dados cadastrados</li>
                <li>Solicitar correção ou atualização</li>
                <li>Pedir exclusão de informações</li>
                <li>Revogar consentimentos de uso</li>
            </ul>

            <h3 class="subtitulo_politica">Contato</h3>
            <p>
                Em caso de dúvidas sobre esta Política de Privacidade, entre em contato pelos canais
                oficiais disponibilizados em nosso site.
            </p>
        </div>

        <!-- CONDIÇÕES DE COMPRA -->
        <div class="politica" id="cdc">
            <h2 class="titulo_politica">Condições de Compra</h2>

            <h3 class="subtitulo_politica">Informações Gerais</h3>
            <p>
                Ao realizar uma compra em nossa loja, você concorda com a política de privacidade
                e com os meios de pagamento da nossa loja. Todas as informações fornecidas devem ser verdadeiras
                e atualizadas para garantir o sucesso do pedido.
            </p>

            <h3 class="subtitulo_politica">Processo de Pedido</h3>
            <ul>
                <li>O pedido só é confirmado após aprovação do pagamento.</li>
                <li>Em caso de divergências nos dados, o pedido pode ser cancelado.</li>
                <li>Produtos são enviados conforme disponibilidade em estoque.</li>
            </ul>

            <h3 class="subtitulo_politica">Preços e Pagamentos</h3>
            <ul>
                <li>Os preços podem ser alterados sem aviso prévio.</li>
                <li>A cobrança é feita seguindo a forma de pagamento escolhida no checkout.</li>
                <li>Promoções são válidas apenas enquanto durarem os estoques.</li>
            </ul>

            <h3 class="subtitulo_politica">Envio e Entrega</h3>
            <ul>
                <li>O prazo estimado de entrega é informado no checkout.</li>
                <li>Fatores externos (clima, greves, logística) podem alterar os prazos.</li>
                <li>Informações incorretas de endereço são responsabilidade do comprador.</li>
            </ul>
        </div>

        <!-- TERMOS DE USO -->
        <div class="politica" id="tdu">
            <h2 class="titulo_politica">Termos de Uso</h2>

            <h3 class="subtitulo_politica">Aceitação dos Termos</h3>
            <p>
                Ao acessar nosso site, você concorda com todas as regras descritas aqui.
                Caso não concorde, recomendamos interromper o uso da plataforma.
            </p>

            <h3 class="subtitulo_politica">Uso da Plataforma</h3>
            <ul>
                <li>É proibido utilizar o site para fins ilegais ou não autorizados.</li>
                <li>O conteúdo do site não pode ser copiado sem autorização.</li>
                <li>Nos reservamos o direito de atualizar ou modificar estes termos a qualquer momento.</li>
            </ul>

            <h3 class="subtitulo_politica">Responsabilidades do Usuário</h3>
            <ul>
                <li>Fornecer informações verdadeiras e completas ao criar pedidos.</li>
                <li>Não tentar acessar áreas protegidas ou restritas do sistema.</li>
                <li>Não realizar práticas que afetem o funcionamento do site.</li>
            </ul>

            <h3 class="subtitulo_politica">Limitação de Responsabilidade</h3>
            <p>
                A loja não se responsabiliza por danos causados por mau uso do site,
                falhas de conexão ou ações externas que comprometam o acesso.
            </p>
        </div>
    </section>
</main>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
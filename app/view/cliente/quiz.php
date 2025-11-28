<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Quiz de Estilo</h1>
            <p class="descricao_banner">Descubra as peças perfeitas para o seu estilo</p>

            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>
                <li class="item_navegacao">Quiz</li>
            </ul>
        </div>
    </section>

    <section class="secao_quiz">
        <div class="container">
            <div class="card_quiz">
                <!-- Informações do Quiz -->
                <div class="info_quiz">
                    <div class="progresso_quiz">
                        <span class="numero_questao">Questão 1 de 5</span>
                        <div class="barra_progresso">
                            <div class="progresso_preenchido" style="width: 20%"></div>
                        </div>
                    </div>

                    <div class="descricao_quiz">
                        <i class="ri-information-line"></i>
                        <p>Selecione todas as opções que combinam com você. Quanto mais detalhes, melhores serão suas recomendações!</p>
                    </div>
                </div>

                <!-- Formulário da Questão -->
                <form class="form_quiz" id="form_questao" method="POST">
                    <div class="conteudo_questao">
                        <h2 class="pergunta_quiz">Quais estilos de roupas mais combinam com sua personalidade?</h2>

                        <div class="opcoes_quiz">
                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="casual">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Casual</strong>
                                    <small>Confortável e descontraído para o dia a dia</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="elegante">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Elegante</strong>
                                    <small>Sofisticado e refinado para ocasiões especiais</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="esportivo">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Esportivo</strong>
                                    <small>Prático e funcional para atividades físicas</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="boho">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Boho Chic</strong>
                                    <small>Livre e artístico com toque vintage</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="moderno">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Moderno</strong>
                                    <small>Minimalista e contemporâneo</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="romantico">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Romântico</strong>
                                    <small>Delicado e feminino com detalhes suaves</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="rock">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Rock</strong>
                                    <small>Ousado e rebelde com atitude</small>
                                </span>
                            </label>

                            <label class="opcao_checkbox">
                                <input type="checkbox" name="estilos[]" value="tropical">
                                <span class="checkbox_custom"></span>
                                <span class="texto_opcao">
                                    <strong>Tropical</strong>
                                    <small>Vibrante e colorido com estampas alegres</small>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Botões de Navegação -->
                    <div class="navegacao_quiz">
                        <button type="button" class="botao_quiz botao_voltar" disabled>
                            <i class="ri-arrow-left-line"></i>
                            Voltar
                        </button>

                        <button type="submit" class="botao_quiz botao_proximo">
                            Próxima
                            <i class="ri-arrow-right-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
    // Prevenir envio do formulário (implementar lógica depois)
    document.getElementById('form_questao').addEventListener('submit', (e) => {
        e.preventDefault();

        // Verificar se pelo menos uma opção foi selecionada
        const checkboxes = document.querySelectorAll('input[name="estilos[]"]:checked');

        if (checkboxes.length === 0) {
            alert('Por favor, selecione pelo menos uma opção antes de continuar.');
            return;
        }

        // Aqui você implementará a lógica para ir para a próxima questão
        alert('Próxima questão será carregada! (Implementar com PHP)');
    });

    // Botão voltar (implementar navegação depois)
    document.querySelector('.botao_voltar').addEventListener('click', () => {
        // Lógica para voltar à questão anterior
        alert('Voltar à questão anterior (Implementar com PHP)');
    });
</script>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
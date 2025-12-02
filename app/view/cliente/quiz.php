<?php include_once __DIR__ . "/../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Quiz de Busca</h1>
            <p class="descricao_banner">Responda as perguntas e encontre os produtos ideais para você</p>

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
                        <span class="numero_questao">Questão <?= $etapa ?> de 3</span>
                        <div class="barra_progresso">
                            <div class="progresso_preenchido" style="width: <?= ($etapa / 3) * 100 ?>%"></div>
                        </div>
                    </div>

                    <div class="descricao_quiz">
                        <i class="ri-information-line"></i>
                        <p>Todas as opções mostradas estão disponíveis no nosso estoque!</p>
                    </div>
                </div>

                <!-- Mensagem de Erro -->
                <?php if (!empty($mensagemErro)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #d32f2f;">
                        <strong><i class="ri-error-warning-line"></i> Atenção:</strong> <?= htmlspecialchars($mensagemErro) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulário do Quiz -->
                <form class="form_quiz" method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=quiz&etapa=<?= $etapa ?>">
                    <div class="conteudo_questao">
                        <?php if ($etapa == 1): ?>
                            <!-- Pergunta 1: Categoria -->
                            <h2 class="pergunta_quiz">1. Qual categoria de produto você procura?</h2>
                            <?php if (empty($categorias)): ?>
                                <div style="background-color: #fff3cd; color: #856404; padding: 1.5rem; border-radius: 8px; text-align: center; margin-top: 1rem;">
                                    <p><strong>Não há categorias disponíveis no momento.</strong></p>
                                    <p style="margin-top: 0.5rem; font-size: 0.9rem;">Por favor, tente novamente mais tarde.</p>
                                </div>
                            <?php else: ?>
                                <p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;">
                                    <i class="ri-checkbox-circle-line" style="color: #28a745;"></i> 
                                    <strong><?= count($categorias) ?></strong> categoria(s) disponível(is) com produtos em estoque
                                </p>
                                <div class="opcoes_quiz">
                                    <?php 
                                    $categoriasSelecionadas = !empty($categoriaSelecionada) ? explode(',', $categoriaSelecionada) : [];
                                    foreach ($categorias as $cat): 
                                        $estaSelecionada = in_array($cat, $categoriasSelecionadas);
                                    ?>
                                        <label class="opcao_checkbox">
                                            <input type="checkbox" name="categoria[]" value="<?= htmlspecialchars($cat) ?>" <?= $estaSelecionada ? 'checked' : '' ?>>
                                            <span class="checkbox_custom"></span>
                                            <span class="texto_opcao">
                                                <strong><?= htmlspecialchars($cat) ?></strong>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($etapa == 2): ?>
                            <!-- Pergunta 2: Tamanho -->
                            <h2 class="pergunta_quiz">2. Qual tamanho de produto você procura?</h2>
                            <div style="background-color: #e7f3ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                <p style="margin: 0; color: #0066cc; font-size: 0.9rem;">
                                    <i class="ri-information-line"></i> 
                                    Categoria(s) selecionada(s): <strong><?= htmlspecialchars(str_replace(',', ', ', $categoriaSelecionada)) ?></strong>
                                </p>
                            </div>
                            <?php if (empty($tamanhos)): ?>
                                <div style="background-color: #fff3cd; color: #856404; padding: 1.5rem; border-radius: 8px; text-align: center; margin-top: 1rem;">
                                    <p><strong>Não há tamanhos disponíveis para esta(s) categoria(s).</strong></p>
                                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=quiz&etapa=1" 
                                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background-color: #d4a574; color: white; text-decoration: none; border-radius: 5px;">
                                        <i class="ri-arrow-left-line"></i> Voltar e escolher outra(s) categoria(s)
                                    </a>
                                </div>
                            <?php else: ?>
                                <p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;">
                                    <i class="ri-checkbox-circle-line" style="color: #28a745;"></i> 
                                    <strong><?= count($tamanhos) ?></strong> tamanho(s) disponível(is) para esta(s) categoria(s)
                                </p>
                                <div class="opcoes_quiz">
                                    <?php 
                                    $tamanhosSelecionados = !empty($tamanhoSelecionado) ? explode(',', $tamanhoSelecionado) : [];
                                    foreach ($tamanhos as $tam): 
                                        $estaSelecionado = in_array($tam, $tamanhosSelecionados);
                                    ?>
                                        <label class="opcao_checkbox">
                                            <input type="checkbox" name="tamanho[]" value="<?= htmlspecialchars($tam) ?>" <?= $estaSelecionado ? 'checked' : '' ?>>
                                            <span class="checkbox_custom"></span>
                                            <span class="texto_opcao">
                                                <strong><?= htmlspecialchars($tam) ?></strong>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($etapa == 3): ?>
                            <!-- Pergunta 3: Cor -->
                            <h2 class="pergunta_quiz">3. Qual cor de produto você procura?</h2>
                            <div style="background-color: #e7f3ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                <p style="margin: 0; color: #0066cc; font-size: 0.9rem;">
                                    <i class="ri-information-line"></i> 
                                    Categoria(s): <strong><?= htmlspecialchars(str_replace(',', ', ', $categoriaSelecionada)) ?></strong> | 
                                    Tamanho(s): <strong><?= htmlspecialchars(str_replace(',', ', ', $tamanhoSelecionado)) ?></strong>
                                </p>
                            </div>
                            <?php if (empty($cores)): ?>
                                <div style="background-color: #fff3cd; color: #856404; padding: 1.5rem; border-radius: 8px; text-align: center; margin-top: 1rem;">
                                    <p><strong>Não há cores disponíveis para esta combinação.</strong></p>
                                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=quiz&etapa=2" 
                                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background-color: #d4a574; color: white; text-decoration: none; border-radius: 5px;">
                                        <i class="ri-arrow-left-line"></i> Voltar e escolher outro(s) tamanho(s)
                                    </a>
                                </div>
                            <?php else: ?>
                                <p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;">
                                    <i class="ri-checkbox-circle-line" style="color: #28a745;"></i> 
                                    <strong><?= count($cores) ?></strong> cor(es) disponível(is) para esta combinação
                                </p>
                                <div class="opcoes_quiz">
                                    <?php 
                                    $corSelecionada = $_SESSION['quiz_cor'] ?? '';
                                    $coresSelecionadas = !empty($corSelecionada) ? explode(',', $corSelecionada) : [];
                                    foreach ($cores as $cor): 
                                        $estaSelecionada = in_array($cor, $coresSelecionadas);
                                    ?>
                                        <label class="opcao_checkbox">
                                            <input type="checkbox" name="cor[]" value="<?= htmlspecialchars($cor) ?>" <?= $estaSelecionada ? 'checked' : '' ?>>
                                            <span class="checkbox_custom"></span>
                                            <span class="texto_opcao">
                                                <strong><?= htmlspecialchars($cor) ?></strong>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Botões de Navegação -->
                    <div class="navegacao_quiz">
                        <?php if ($etapa > 1): ?>
                            <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=quiz&etapa=<?= $etapa - 1 ?>" class="botao_quiz botao_voltar">
                                <i class="ri-arrow-left-line"></i>
                                Voltar
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if ($etapa == 1 && !empty($categorias)): ?>
                            <button type="submit" class="botao_quiz botao_proximo" onclick="return validarSelecao('categoria[]')">
                                Próxima
                                <i class="ri-arrow-right-line"></i>
                            </button>
                        <?php elseif ($etapa == 2 && !empty($tamanhos)): ?>
                            <button type="submit" class="botao_quiz botao_proximo" onclick="return validarSelecao('tamanho[]')">
                                Próxima
                                <i class="ri-arrow-right-line"></i>
                            </button>
                        <?php elseif ($etapa == 3 && !empty($cores)): ?>
                            <button type="submit" class="botao_quiz botao_proximo" onclick="return validarSelecao('cor[]')">
                                Ver Produtos Recomendados
                                <i class="ri-arrow-right-line"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<style>
    .navegacao_quiz {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }

    .botao_voltar {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<script>
    function validarSelecao(nomeCampo) {
        const checkboxes = document.querySelectorAll('input[name="' + nomeCampo + '"]:checked');
        if (checkboxes.length === 0) {
            alert('Por favor, selecione pelo menos uma opção antes de continuar.');
            return false;
        }
        return true;
    }
</script>

<?php include_once __DIR__ . "/../Rodape.php"; ?>

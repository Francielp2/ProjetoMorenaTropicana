<?php
// Esta view recebe apenas variáveis prontas do controller
// $mensagem - mensagem de sucesso/erro
// $tipoMensagem - tipo da mensagem ('sucesso' ou 'erro')
// $nome - nome do cliente
// $email - email do cliente
// $cpfFormatado - CPF formatado (XXX.XXX.XXX-XX)
// $telefone - telefone sem formatação (para o input)
// $telefoneFormatado - telefone formatado (para exibição)
// $rua - rua do endereço
// $numero - número do endereço
// $bairro - bairro do endereço
// $cepFormatado - CEP formatado (XXXXX-XXX)
// $estado - estado (UF)
// $complemento - complemento do endereço

include_once __DIR__ . "/../Cabecalho.php";
?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Minha Conta</h1>
            <p class="descricao_banner">Veja suas informações pessoais e gerencie sua conta</p>
            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/control/ClienteController.php?acao=tela_inicial">Início</a>
                </li>
                <li class="item_navegacao">Minha Conta</li>
            </ul>
        </div>
    </section>

    <section class="secao_conta">
        <div class="container">
            <div class="header_conta">
                <div class="usuario_info">
                    <div class="icone_usuario">
                        <i class="ri-user-line"></i>
                    </div>
                    <span class="email_usuario"><?= htmlspecialchars($email) ?></span>
                </div>
            </div>

            <!-- Mensagens de sucesso/erro -->
            <?php if ($mensagem): ?>
                <div style="background-color: <?= $tipoMensagem === 'sucesso' ? '#d4edda' : '#f8d7da' ?>; 
                            color: <?= $tipoMensagem === 'sucesso' ? '#155724' : '#721c24' ?>; 
                            padding: 15px; margin: 20px 0; border-radius: 5px; text-align: center;">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <section class="section container">
                <div class="aba_btns">
                    <button class="aba_btn aba_ativa" data-target="#infopessoais">Informações Pessoais</button>
                    <button class="aba_btn" data-target="#endereco">Endereço</button>
                </div>

                <div class="aba_content">
                    <!-- Aba Informações Pessoais -->
                    <div class="item_aba aba_ativa" id="infopessoais">
                        <form class="card_informacoes" id="form_info_pessoais" method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=conta">
                            <input type="hidden" name="atualizar_dados_pessoais" value="1">
                            <h2 class="titulo_secao">Informações Pessoais</h2>

                            <div class="grupo_informacoes">
                                <div class="campo_info">
                                    <label class="label_info" for="nome_completo">Nome Completo</label>
                                    <input type="text" id="nome_completo" name="nome_completo" class="valor_info" value="<?= htmlspecialchars($nome) ?>" required>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="email">E-mail</label>
                                    <input type="email" id="email" name="email" class="valor_info" value="<?= htmlspecialchars($email) ?>" readonly>
                                    <small style="color: #666; font-size: 0.9em;">O email não pode ser alterado</small>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="cpf">CPF</label>
                                    <input type="text" id="cpf" name="cpf" class="valor_info" value="<?= htmlspecialchars($cpfFormatado) ?>" readonly>
                                    <small style="color: #666; font-size: 0.9em;">O CPF não pode ser alterado</small>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="telefone">Telefone</label>
                                    <input type="tel" id="telefone" name="telefone" class="valor_info" value="<?= htmlspecialchars($telefone ?: '') ?>" placeholder="Ex: 99999999999" maxlength="11">
                                </div>
                            </div>

                            <div class="card_acoes">
                                <button type="submit" class="botao_acao btn">
                                    <i class="ri-save-line"></i>
                                    Editar dados
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Aba Endereço -->
                    <div class="item_aba" id="endereco">
                        <form class="card_informacoes" id="form_endereco" method="POST" action="<?= BASE_URL ?>/app/control/ClienteController.php?acao=conta">
                            <input type="hidden" name="atualizar_endereco" value="1">
                            <h2 class="titulo_secao">Endereço de Entrega</h2>

                            <div class="grupo_informacoes">
                                <div class="campo_info_grupo">
                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="rua">Rua</label>
                                        <input type="text" id="rua" name="rua" class="valor_info" value="<?= htmlspecialchars($rua) ?>" required>
                                    </div>

                                    <div class="campo_info campo_pequeno">
                                        <label class="label_info" for="numero">N°</label>
                                        <input type="text" id="numero" name="numero" class="valor_info" value="<?= htmlspecialchars($numero) ?>">
                                    </div>
                                </div>

                                <div class="campo_info_grupo">
                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="bairro">Bairro</label>
                                        <input type="text" id="bairro" name="bairro" class="valor_info" value="<?= htmlspecialchars($bairro) ?>">
                                    </div>

                                    <div class="campo_info campo_pequeno">
                                        <label class="label_info" for="cep">CEP</label>
                                        <input type="text" id="cep" name="cep" class="valor_info" value="<?= htmlspecialchars($cepFormatado) ?>" placeholder="00000-000" maxlength="9" required>
                                    </div>
                                </div>

                                <div class="campo_info_grupo">
                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="estado">Estado (UF)</label>
                                        <input type="text" id="estado" name="estado" class="valor_info" value="<?= htmlspecialchars($estado) ?>" placeholder="BA" maxlength="2" style="text-transform: uppercase;">
                                    </div>

                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="complemento">Complemento</label>
                                        <input type="text" id="complemento" name="complemento" class="valor_info" value="<?= htmlspecialchars($complemento) ?>" placeholder="Apartamento, Bloco, etc.">
                                    </div>
                                </div>
                            </div>

                            <div class="card_acoes">
                                <button type="submit" class="botao_acao btn">
                                    <i class="ri-save-line"></i>
                                    Salvar Endereço
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Botão de Excluir Conta -->
            <div class="card_informacoes">
                <div class="card_acoes">
                    <a href="<?= BASE_URL ?>/app/control/LogoutController.php" class="botao_acao btn" style="text-decoration: none; display: inline-block;">
                        <i class="ri-logout-box-line"></i>
                        Sair da sua conta
                    </a>

                    <button type="button" class="botao_acao botao_excluir" onclick="confirmarExclusao()">
                        <i class="ri-delete-bin-line"></i>
                        Excluir Cadastro
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    // Sistema de Abas
    const tabs = document.querySelectorAll('[data-target]');
    const tabContents = document.querySelectorAll('.item_aba');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.target);

            // Remove classe ativa de todas as abas
            tabContents.forEach((tabContent) => {
                tabContent.classList.remove('aba_ativa');
            });

            // Adiciona classe ativa na aba selecionada
            target.classList.add('aba_ativa');

            // Remove classe ativa de todos os botões
            tabs.forEach((t) => {
                t.classList.remove('aba_ativa');
            });

            // Adiciona classe ativa no botão clicado
            tab.classList.add('aba_ativa');
        });
    });

    // Confirmação de exclusão de conta
    function confirmarExclusao() {
        if (confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) {
            if (confirm('ATENÇÃO: Todos os seus dados serão perdidos permanentemente. Confirma a exclusão?')) {
                // Redireciona para a própria página com parâmetro de exclusão
                window.location.href = '<?= BASE_URL ?>/app/control/ClienteController.php?acao=conta&excluir=confirmar';
            }
        }
    }

    // Máscara para CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    }

    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    // Converter estado para maiúsculas
    const estadoInput = document.getElementById('estado');
    if (estadoInput) {
        estadoInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    }
</script>

<?php include_once __DIR__ . "/../Rodape.php"; ?>
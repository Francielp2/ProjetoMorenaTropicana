<?php include_once "../Cabecalho.php"; ?>

<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Minha Conta</h1>
            <p class="descricao_banner">Veja suas informações pessoais e gerencie sua conta</p>
            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/view/cliente/tela_inicial.php">Início</a>
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
                    <span class="email_usuario">emailusuario@email.com</span>
                </div>
            </div>

            <section class="section container">
                <div class="aba_btns">
                    <button class="aba_btn aba_ativa" data-target="#infopessoais">Informações Pessoais</button>
                    <button class="aba_btn" data-target="#endereco">Endereço</button>
                </div>

                <div class="aba_content">
                    <!-- Aba Informações Pessoais -->
                    <div class="item_aba aba_ativa" id="infopessoais">
                        <form class="card_informacoes" id="form_info_pessoais" method="POST">
                            <h2 class="titulo_secao">Informações Pessoais</h2>

                            <div class="grupo_informacoes">
                                <div class="campo_info">
                                    <label class="label_info" for="nome_completo">Nome Completo</label>
                                    <input type="text" id="nome_completo" name="nome_completo" class="valor_info" value="Nome Do Usuario" readonly>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="email">E-mail</label>
                                    <input type="email" id="email" name="email" class="valor_info" value="emailusuario@email.com" readonly>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="cpf">CPF</label>
                                    <input type="text" id="cpf" name="cpf" class="valor_info" value="123.456.789-00" readonly>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="telefone">Telefone</label>
                                    <input type="tel" id="telefone" name="telefone" class="valor_info" value="+55 (77) 98128-4165" readonly>
                                </div>
                            </div>

                            <div class="card_acoes">
                                <button type="submit" class="botao_acao btn">
                                    <i class="ri-edit-line"></i>
                                    Solicitar Modificação das Informações
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Aba Endereço -->
                    <div class="item_aba" id="endereco">
                        <form class="card_informacoes" id="form_endereco" method="POST">
                            <h2 class="titulo_secao">Endereço de Entrega</h2>

                            <div class="grupo_informacoes">
                                <div class="campo_info_grupo">
                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="bairro">Rua</label>
                                        <input type="text" id="bairro" name="bairro" class="valor_info" value="rua do cliente" readonly>
                                    </div>

                                    <div class="campo_info campo_pequeno">
                                        <label class="label_info" for="cep">N°</label>
                                        <input type="text" id="cep" name="cep" class="valor_info" value="15" readonly>
                                    </div>
                                </div>

                                <div class="campo_info_grupo">
                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="bairro">Bairro</label>
                                        <input type="text" id="bairro" name="bairro" class="valor_info" value="Centro" readonly>
                                    </div>

                                    <div class="campo_info campo_pequeno">
                                        <label class="label_info" for="cep">CEP</label>
                                        <input type="text" id="cep" name="cep" class="valor_info" value="46430-000" readonly>
                                    </div>
                                </div>

                                <div class="campo_info_grupo">
                                    <div class="campo_info campo_medio">
                                        <label class="label_info" for="cidade">Cidade</label>
                                        <input type="text" id="cidade" name="cidade" class="valor_info" value="Guanambi" readonly>
                                    </div>

                                    <div class="campo_info campo_pequeno">
                                        <label class="label_info" for="estado">Estado</label>
                                        <input type="text" id="estado" name="estado" class="valor_info" value="Bahia" readonly>
                                    </div>
                                </div>

                                <div class="campo_info">
                                    <label class="label_info" for="complemento">Complemento</label>
                                    <input type="text" id="complemento" name="complemento" class="valor_info" value="Apartamento 302, Bloco B" readonly>
                                </div>
                            </div>

                            <div class="card_acoes">
                                <button type="submit" class="botao_acao btn">
                                    <i class="ri-edit-line"></i>
                                    Alterar dados de endereço
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Botão de Excluir Conta -->
            <div class="card_informacoes">
                <div class="card_acoes">
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
                // Aqui você pode adicionar o código para processar a exclusão
                alert('Funcionalidade de exclusão será implementada.');
                // window.location.href = 'processar_exclusao.php';
            }
        }
    }

    // Prevenir envio dos formulários (adicione a lógica de envio depois)
    document.getElementById('form_info_pessoais').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Solicitação de modificação enviada! (Implementar lógica de envio)');
    });

    document.getElementById('form_endereco').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Solicitação de modificação enviada! (Implementar lógica de envio)');
    });
</script>

<?php include_once "../rodape.php"; ?>
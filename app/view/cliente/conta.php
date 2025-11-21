<?php include_once "../Cabecalho.php"; ?>
<main class="principal">
    <section class="banner">
        <div class="container">
            <h1 class="titulo_banner">Minha Conta</h1>
            <p class="descricao_banner">Veja suas informações pessoais e gerencie sua conta</p>
            <ul class="navegacao">
                <li class="item_navegacao">
                    <a href="<?= BASE_URL ?>/app/view/cliente/tela_inicial.php">Início</a>
                <li class="item_navegacao">Minha Conta</li>
                </li>
            </ul>
        </div>
    </section>

    <section class="secao_conta">
        <div class="container">
            <div class="header_conta">
                <div class="usuario_info">
                    <div class="icone_usuario">
                        <span><i class="ri-user-line iconeuser"></i></span>
                    </div>
                    <span class="email_usuario">morenatropicana792@gmail.com</span>
                </div>
            </div>

            <div class="conteudo_conta">
                <form class="card_informacoes" id="form_info_pessoais" method="POST">
                    <h2 class="titulo_secao">Informações Pessoais</h2>

                    <div class="grupo_informacoes">
                        <div class="campo_info">
                            <label class="label_info" for="nome_completo">Nome Completo</label>
                            <input type="text" id="nome_completo" name="nome_completo" class="valor_info" value="Maria Silva Santos" readonly>
                        </div>

                        <div class="campo_info">
                            <label class="label_info" for="email">E-mail</label>
                            <input type="email" id="email" name="email" class="valor_info" value="morenatropicana792@gmail.com" readonly>
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
                    <button type="submit" class="botao_editar btn btninfopessoais">
                        Solicitar Modificação das Informações
                    </button>
                </form>

                <form class="card_informacoes" id="form_endereco" method="POST">
                    <h2 class="titulo_secao">Endereço de Entrega</h2>

                    <div class="grupo_informacoes">
                        <div class="campo_info">
                            <label class="label_info" for="endereco">Endereço</label>
                            <input type="text" id="endereco" name="endereco" class="valor_info" value="Rua das Flores, 123" readonly>
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
                </form>

                <div class="card_acoes">
                    <button type="button" class="botao_editar btn" onclick="document.getElementById('form_endereco').submit()">
                        Solicitar Modificação das Informações
                    </button>

                    <button type="button" class="botao_excluir btn" onclick="confirmarExclusao()">
                        Excluir Cadastro
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    function confirmarExclusao() {
        if (confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.')) {
            // Aqui você pode adicionar a lógica de exclusão
            // window.location.href = 'excluir_conta.php';
            console.log('Conta excluída');
        }
    }
</script>

<?php include_once "../rodape.php"; ?>
<?php
/* =============== FUNÇÃO QUE LÊ PASTAS (NÍVEL 1) =============== */
function listarSubpastas($pasta)
{
    $ignorar = ['.', '..', '.git', '.vscode', '.idea', '.env', '.DS_Store'];
    $itens = array_diff(scandir($pasta), $ignorar);

    $subpastas = [];
    $arquivos = [];

    foreach ($itens as $item) {
        $caminho = $pasta . DIRECTORY_SEPARATOR . $item;

        if (is_dir($caminho)) $subpastas[] = $item;
        else $arquivos[] = $item;
    }

    return [$subpastas, $arquivos];
}

/* =============== LISTAR ARQUIVOS =============== */
function listarArquivosDentro($pasta)
{
    if (!is_dir($pasta)) return [];

    $ignorar = ['.', '..'];
    $itens = array_diff(scandir($pasta), $ignorar);

    $arquivos = [];

    foreach ($itens as $item) {
        $caminho = $pasta . DIRECTORY_SEPARATOR . $item;

        if (is_file($caminho)) $arquivos[] = $item;
    }

    return $arquivos;
}

/* =============== LISTAR SUBPASTAS NÍVEL 2 =============== */
function listarSubpastasNivel2($pasta)
{
    if (!is_dir($pasta)) return [];

    $ignorar = ['.', '..'];
    $itens = array_diff(scandir($pasta), $ignorar);

    $pastas = [];

    foreach ($itens as $item) {
        $caminho = $pasta . DIRECTORY_SEPARATOR . $item;

        if (is_dir($caminho)) $pastas[] = $item;
    }

    return $pastas;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Mapa do Projeto (MVC)</title>

    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #0e0e0e;
            color: #eee;
            padding: 30px;
        }

        .mapa-container {
            max-width: 95%;
            margin: auto;
            background: #1a1a1a;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #333;
            box-shadow: 0 0 25px #0008;
        }

        h1 {
            text-align: center;
            color: #00d4ff;
            margin-bottom: 25px;
            font-size: 32px;
        }

        /* CARD ROOT */
        .root-card {
            background: #111;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #333;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #00e5ff;
        }

        /* SUBPASTAS EM LINHA */
        .subpastas-row {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: nowrap;
            margin-bottom: 35px;
        }

        .pasta-card {
            padding: 15px 20px;
            background: #222;
            border-radius: 10px;
            border: 1px solid #333;
            text-align: center;
            font-weight: bold;
            min-width: 160px;
            box-shadow: 0 0 12px #0005;
            color: #fff;
        }

        /* CORES MVC (PASTAS) */
        .mvc-model {
            background: #002f6c;
            border-color: #007bff;
        }

        .mvc-view {
            background: #003d24;
            border-color: #28a745;
        }

        .mvc-controller {
            background: #5a0000;
            border-color: #ff4136;
        }

        .mvc-config {
            background: #333;
            border-color: #888;
        }

        /* ÁREA DE LISTAGEM */
        h2 {
            margin-left: 10px;
            margin-top: 40px;
            font-size: 24px;
            color: #00d4ff;
        }

        /* LISTA DE ARQUIVOS */
        .arquivos-lista {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-left: 50px;
            border-left: 2px dashed #444;
            margin-bottom: 25px;
        }

        .arquivo-item {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 15px;
            width: fit-content;
            color: #fff;
            box-shadow: 0 0 10px #0004;
            border: 1px solid #333;
        }

        /* CORES PARA ARQUIVOS */
        .arquivo-controller {
            background: #5a0000;
            border-color: #ff4136;
        }

        .arquivo-model {
            background: #002f6c;
            border-color: #007bff;
        }

        .arquivo-view {
            background: #003d24;
            border-color: #28a745;
        }

        .arquivo-config {
            background: #2e2e2e;
            border-color: #777;
        }

        /* SUBPASTAS NÍVEL 2 */
        .sub-sub {
            margin-left: 40px;
            padding-left: 20px;
            border-left: 2px dashed #555;
            margin-bottom: 20px;
        }

        .sub-sub-title {
            font-size: 19px;
            margin-bottom: 10px;
            color: #bbb;
        }
    </style>
</head>

<body>

    <div class="mapa-container">

        <h1>Mapa do Projeto (MVC)</h1>

        <!-- CARD PRINCIPAL -->
        <div class="root-card">📁 app</div>

        <?php
        $pastaPrincipal = __DIR__;
        list($subpastas, $arquivosRaiz) = listarSubpastas($pastaPrincipal);
        ?>

        <!-- PASTAS EM LINHA -->
        <div class="subpastas-row">
            <?php foreach ($subpastas as $p): ?>

                <?php
                $classe = "mvc-config";
                if (stripos($p, "model") !== false) $classe = "mvc-model";
                if (stripos($p, "view") !== false) $classe = "mvc-view";
                if (stripos($p, "control") !== false) $classe = "mvc-controller";
                ?>

                <div class="pasta-card <?= $classe ?>">📁 <?= $p ?></div>

            <?php endforeach; ?>
        </div>

        <!-- ARQUIVOS E SUBPASTAS -->
        <?php foreach ($subpastas as $pasta): ?>
            <?php
            $caminho = $pastaPrincipal . "/" . $pasta;
            $arquivos = listarArquivosDentro($caminho);
            $subpastas2 = listarSubpastasNivel2($caminho);
            ?>

            <h2><?= ucfirst($pasta) ?></h2>

            <div class="arquivos-lista">
                <!-- Arquivos -->
                <?php foreach ($arquivos as $arq): ?>

                    <?php
                    $classeArquivo = "arquivo-config";
                    if (stripos($pasta, "model") !== false) $classeArquivo = "arquivo-model";
                    if (stripos($pasta, "view") !== false) $classeArquivo = "arquivo-view";
                    if (stripos($pasta, "control") !== false) $classeArquivo = "arquivo-controller";
                    ?>

                    <div class="arquivo-item <?= $classeArquivo ?>">📄 <?= $arq ?></div>

                <?php endforeach; ?>

                <!-- Subpastas nível 2 -->
                <?php foreach ($subpastas2 as $sub2): ?>
                    <div class="sub-sub">
                        <div class="sub-sub-title">📁 <?= $sub2 ?></div>

                        <?php
                        $arquivosSub2 = listarArquivosDentro($caminho . "/" . $sub2);
                        foreach ($arquivosSub2 as $arq2):
                        ?>
                            <div class="arquivo-item arquivo-view">📄 <?= $arq2 ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>
</body>

</html>
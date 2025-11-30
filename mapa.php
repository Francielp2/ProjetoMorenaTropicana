<?php
/* =============== FUNÇÃO QUE LÊ AS PASTAS =============== */
function listarEstrutura($pasta)
{
    $ignorar = ['.', '..', '.git', '.vscode', '.idea', '.env', '.DS_Store'];
    $itens = array_diff(scandir($pasta), $ignorar);

    echo "<ul class='tree'>";

    foreach ($itens as $item) {
        $caminho = $pasta . DIRECTORY_SEPARATOR . $item;

        if (is_dir($caminho)) {
            echo "<li class='pasta'>📁 {$item}";
            listarEstrutura($caminho);
            echo "</li>";
        } else {
            echo "<li class='arquivo'>📄 {$item}</li>";
        }
    }

    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mapa do Projeto</title>

    <style>
        /* ===== MAPA DE PASTAS ESTILO "EXPLORER" ===== */

        body {
            font-family: "Segoe UI", sans-serif;
            background: #111;
            color: #eee;
            margin: 0;
            padding: 30px;
        }

        .mapa-container {
            max-width: 900px;
            margin: auto;
            background: #1a1a1a;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px #0008;
            border: 1px solid #333;
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
            color: #00d4ff;
            text-shadow: 0 0 10px #00d4ff55;
        }

        ul.tree {
            list-style: none;
            padding-left: 20px;
        }

        ul.tree li {
            padding: 6px 0;
            font-size: 16px;
            line-height: 22px;
            position: relative;
        }

        ul.tree li::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 12px;
            width: 10px;
            height: 1px;
            background: #444;
        }

        ul.tree li::after {
            content: "";
            position: absolute;
            left: -12px;
            top: -8px;
            width: 1px;
            height: 22px;
            background: #444;
        }

        li.pasta::before {
            content: "📁 ";
            position: static;
        }

        li.arquivo::before {
            content: "📄 ";
            position: static;
        }

        ul.tree ul {
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px dashed #333;
        }

        ul.tree li:hover {
            color: #00d4ff;
            cursor: default;
            text-shadow: 0 0 8px #00d4ff55;
        }
    </style>

</head>
<body>

<div class="mapa-container">
    <h1>Mapa do Projeto</h1>

    <?php listarEstrutura(__DIR__); ?>
</div>

</body>
</html>

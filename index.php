<?php
/* ---REQUIRE AS CONFIGURAÇÕES DE CAMINHOS--- */
require_once __DIR__ . "/app/config/config.php";

/* ---DIRECIONA O USUÁRIO PARA TELA DE LOGIN--- */
header("Location: " . BASE_URL . "/app/control/LoginController.php");
exit;

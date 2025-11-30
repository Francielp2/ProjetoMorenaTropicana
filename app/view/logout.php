<?php
/*   Este arquivo redireciona para o LogoutController*/
require_once __DIR__ . "/../config/config.php";
header("Location: " . BASE_URL . "/app/control/LogoutController.php");
exit;

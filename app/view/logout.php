<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../control/AuthController.php";

$authController = new AuthController();
$authController->logout();


<?php
$server = $_SERVER['SERVER_SOFTWARE'] ?? "";

if (stripos($server, 'xampp') !== false || stripos($server, 'apache') !== false) {
    // Está rodando no XAMPP
    define("BASE_URL", "/ProjetoFinalPSW");
} else {
    // Está rodando no servidor embutido do PHP
    define("BASE_URL", "");
}

<?php

/* ---CRIAÇÃO DA CLASSE DE CONEXÃO--- */

class Database
{
    // private $host = "localhost";
    // private $dbname = "morena_tropicana";
    // private $username = "root";
    // private $password = "databasekey@31";
    private $conn;

    /* ---FUNÇÃO DE CONEXÃO COM PDO--- */
    public function conexao($host = "localhost", $dbname = "morena_tropicana", $username = "root", $password = "databasekey@31")
    {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $host . ";dbname=" . $dbname,
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Conexão bem-sucedida!";
            return ($this->conn);
        } catch (PDOException $e) {
            die("Erro ao conectar: " . $e->getMessage());
        }
    }
}



<?php

class Database {

    private $host;
    private $username;
    private $password;
    private $database;

    public function __construct() {

        /* Try loading .env file */

        $envPath = __DIR__ . "/../../.env";

        if(file_exists($envPath)){

            $env = parse_ini_file($envPath);

            $this->host = $env['DB_HOST'] ?? "localhost";
            $this->username = $env['DB_USER'] ?? "root";
            $this->password = $env['DB_PASS'] ?? "";
            $this->database = $env['DB_NAME'] ?? "worknest_erp";

        }

        else{

            $this->host = "localhost";
            $this->username = "root";
            $this->password = "";
            $this->database = "worknest_erp";

        }

    }



    public function connect() {

        $conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($conn->connect_error) {

            die("Database Connection Failed");

        }

        $conn->set_charset("utf8mb4");

        return $conn;
    }

}

<?php

class Database {

    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "worknest_db";

    public function connect() {

        $conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($conn->connect_error) {
            die("Database Connection Failed: " . $conn->connect_error);
        }

        return $conn;
    }
}
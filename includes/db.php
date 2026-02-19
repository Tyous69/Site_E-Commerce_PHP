<?php

$host = "localhost";
$user = "root";

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
?>
<?php
// includes/db.php

$host = "localhost";
$user = "root";
$password = ""; // Sur Windows/XAMPP, c'est vide par défaut
$database = "php_exam_db"; // Assure-toi que c'est le nom exact de ta DB dans PhpMyAdmin

// Connexion à la base de données
$mysqli = new mysqli($host, $user, $password, $database);

// Vérification de la connexion
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Forcer l'UTF-8 pour éviter les problèmes d'accents
$mysqli->set_charset("utf8mb4");
?>
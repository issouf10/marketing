<?php
// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = ""; // Par défaut vide sur XAMPP/WAMP
$dbname = "healthcare_db";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}
?>
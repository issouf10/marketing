<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $habitation = htmlspecialchars($_POST['habitation']);
    $sexe = htmlspecialchars($_POST['sexe']);
    $contact = htmlspecialchars($_POST['contact']);
    $email = htmlspecialchars($_POST['email']);
    $password_raw = $_POST['password'];
    $confirm_password_raw = $_POST['confirm_password'];

    // Vérification de la correspondance des mots de passe
    if ($password_raw !== $confirm_password_raw) {
        die("Erreur : Les mots de passe ne correspondent pas.");
    }

    // Hachage du mot de passe pour la sécurité
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    // Vérifier si l'email existe déjà
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "Cet email est déjà utilisé.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (nom, prenom, habitation, sexe, contact, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nom, $prenom, $habitation, $sexe, $contact, $email, $password);

        if ($stmt->execute()) {
            echo "Inscription réussie ! <a href='login.html'>Connectez-vous ici</a>";
        } else {
            echo "Erreur lors de l'inscription.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
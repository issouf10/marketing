<?php
// 1. Configuration de la base de données
$servername = "localhost"; // L'adresse de votre serveur de base de données
$username = "root";        // Votre nom d'utilisateur pour la base de données
$password = "";            // Votre mot de passe pour la base de données (laissez vide si pas de mot de passe)
$dbname = "healthcare_db"; // Le nom de votre base de données

// 2. Vérifier si la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Récupérer et nettoyer les données du formulaire
    // htmlspecialchars() convertit les caractères spéciaux en entités HTML pour prévenir les attaques XSS
    // trim() supprime les espaces en début et fin de chaîne
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // 4. Valider les données (simple validation ici)
    if (empty($nom) || empty($email) || empty($message)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Erreur : L'adresse email n'est pas valide.");
    }

    // 5. Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion à la base de données : " . $conn->connect_error);
    }

    // 6. Préparer la requête SQL pour insérer les données
    // Utilisation de requêtes préparées pour prévenir les injections SQL
    $stmt = $conn->prepare("INSERT INTO contacts (nom, email, message) VALUES (?, ?, ?)");
    
    // Vérifier si la préparation de la requête a échoué
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    // Lier les paramètres (s = string)
    $stmt->bind_param("sss", $nom, $email, $message);

    // 7. Exécuter la requête
    if ($stmt->execute()) {
        echo "Votre message a été envoyé avec succès ! Nous vous répondrons bientôt.";
    } else {
        echo "Erreur lors de l'envoi de votre message : " . $stmt->error;
    }

    // 8. Fermer la connexion
    $stmt->close();
    $conn->close();
} else {
    echo "Accès non autorisé. Ce script ne peut être appelé que via une méthode POST.";
}
?>
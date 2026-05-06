<?php
session_start();

// Vérifier si l'utilisateur est connecté
// Si pas de session, retour à la page de connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - HealthCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>HealthCare</h1>
        <nav>
            <a href="index.html">Accueil</a>
            <a href="logout.php" class="btn-login">Déconnexion</a>
        </nav>
    </header>

    <main class="auth-container">
        <div class="auth-card" style="max-width: 600px;">
            <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_nom']); ?> !</h2>
            <p>Heureux de vous revoir sur votre espace santé sécurisé.</p>
            
            <div style="margin-top: 30px; text-align: left; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h3>Actions rapides</h3>
                <p>• Consulter mon dossier médical</p>
                <p>• Prendre rendez-vous avec un spécialiste</p>
                <p>• Modifier mes informations personnelles</p>
            </div>

            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <div style="margin-top: 50px; text-align: left;">
                    <h3>Messages de Contact Reçus</h3>
                    <?php
                    require_once 'config.php'; // Inclure la connexion à la base de données

                    $sql = "SELECT id, nom, email, message, created_at FROM contacts ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table class='contact-messages-table'>";
                        echo "<thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Message</th><th>Date</th></tr></thead>";
                        echo "<tbody>";
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["nom"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars(substr($row["message"], 0, 100)) . "...</td>"; // Affiche les 100 premiers caractères
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo "<p>Aucun message de contact pour le moment.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
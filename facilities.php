<?php
require_once 'config.php';
session_start();

$zone_filter = isset($_GET['zone']) ? htmlspecialchars($_GET['zone']) : '';
$type_filter = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';

// Récupération de la position de l'utilisateur
$lat = isset($_GET['lat']) && is_numeric($_GET['lat']) ? floatval($_GET['lat']) : null;
$lng = isset($_GET['lng']) && is_numeric($_GET['lng']) ? floatval($_GET['lng']) : null;

// Construction de la requête avec filtres
$select_distance = "";
$order_by = " ORDER BY est_de_garde DESC, nom ASC";

if ($lat !== null && $lng !== null) {
    // Formule Haversine pour calculer la distance en KM
    $select_distance = ", (6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude)))) AS distance";
    $order_by = " ORDER BY distance ASC";
}

$query = "SELECT *" . $select_distance . " FROM facilities WHERE 1=1";
if (!empty($zone_filter)) {
    $query .= " AND zone LIKE '%$zone_filter%'";
}
if (!empty($type_filter)) {
    $query .= " AND type = '$type_filter'";
}
$query .= $order_by;

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Établissements de Santé - HealthCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-container { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }
        .facility-card { text-align: left; position: relative; }
        .badge-garde { background: #ff4757; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; position: absolute; top: 15px; right: 15px; font-weight: bold; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }
        .filter-input { padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-width: 200px; }
    </style>
</head>
<body>
    <header>
        <h1>HealthCare</h1>
        <nav>
            <a href="index.html">Accueil</a>
            <a href="facilities.php">Établissements</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Mon Profil</a>
            <?php else: ?>
                <a href="login.html" class="btn-login">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="features">
        <h2>Hôpitaux et Pharmacies</h2>
        <p>Trouvez les soins dont vous avez besoin dans votre zone.</p>

        <form method="GET" class="search-container">
            <input type="text" name="zone" placeholder="Zone (ex: Cocody, Plateau...)" class="filter-input" value="<?php echo $zone_filter; ?>">
            <select name="type" class="filter-input">
                <option value="">Tous les types</option>
                <option value="hopital" <?php echo $type_filter == 'hopital' ? 'selected' : ''; ?>>Hôpitaux</option>
                <option value="pharmacie" <?php echo $type_filter == 'pharmacie' ? 'selected' : ''; ?>>Pharmacies</option>
            </select>
            <button type="submit" class="btn-primary" style="width: auto;">Rechercher</button>
            <button type="button" onclick="getLocation()" class="btn-hero" style="background: #2ed573; font-size: 0.9rem; padding: 10px 20px;">📍 Proche de moi</button>
        </form>

        <div class="cards">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card facility-card">
                        <?php if($row['est_de_garde']): ?>
                            <span class="badge-garde">DE GARDE</span>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($row['nom']); ?></h3>
                        <p><strong>Type :</strong> <?php echo ucfirst($row['type']); ?></p>
                        <p><strong>Zone :</strong> <?php echo htmlspecialchars($row['zone']); ?></p>
                        <p><strong>Adresse :</strong> <?php echo htmlspecialchars($row['adresse']); ?></p>
                        <p><strong>📞</strong> <?php echo htmlspecialchars($row['telephone']); ?></p>
                        <?php if (isset($row['distance'])): ?>
                            <p style="color: #2ed573; font-weight: bold;">📍 À <?php echo round($row['distance'], 1); ?> km de vous</p>
                        <?php endif; ?>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($row['nom'] . ' ' . $row['adresse']); ?>" target="_blank" class="btn-primary" style="display:inline-block; margin-top:10px; text-decoration:none; font-size:0.9rem; width:100%; text-align:center;">Voir sur la carte</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun établissement trouvé pour cette recherche.</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // On ajoute les coordonnées dans l'URL et on recharge
                    const url = new URL(window.location.href);
                    url.searchParams.set('lat', lat);
                    url.searchParams.set('lng', lng);
                    window.location.href = url.href;
                }, error => {
                    alert("Erreur de géolocalisation : " + error.message);
                });
            } else {
                alert("La géolocalisation n'est pas supportée par votre navigateur.");
            }
        }
    </script>
</body>
</html>
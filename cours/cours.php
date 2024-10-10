<?php
include('../includes/database.php');
session_start();

// Récupérer le slug du cours depuis l'URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Requête pour récupérer les informations du cours
$stmt = $conn->prepare("SELECT * FROM cours WHERE slug = :slug");
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$cour = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cour) {
    echo "Cours non trouvé.";
    exit;
}

// Extraire les sections du contenu du cours (supposons que chaque section commence par <h2>)
preg_match_all('/<h2>(.*?)<\/h2>/', $cour['contenu'], $matches);
$sections = $matches[1];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($cour['titre']); ?> - Plateforme de Cours PHP</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar-light">
        <button onclick="window.location.href='../index.php'" class="back-btn">← Retour</button>
            <nav class="sub-navigation">
                <ul>
                    <?php foreach ($sections as $index => $section): ?>
                        <li><a href="#section<?php echo $index + 1; ?>"><?php echo htmlspecialchars($section); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <h1><?php echo htmlspecialchars($cour['titre']); ?></h1>
            <article>
                <?php echo $cour['contenu']; ?>
            </article>
        </main>
    </div>
    <script src="/js/defilement.js"></script>
</body>
</html>

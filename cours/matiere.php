<?php
include('../includes/database.php');
include('../templates/header.php');
include('../templates/footer.php');
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Récupérer le slug de la matière depuis l'URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Requête pour récupérer les informations de la matière
$stmt = $conn->prepare("SELECT * FROM matieres WHERE slug = :slug");
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$matiere = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si la matière existe
if (!$matiere) {
    echo "Matière non trouvée.";
    exit;
}

// Récupérer les cours associés à la matière
$stmt = $conn->prepare("SELECT * FROM cours WHERE matiere_id = :matiere_id ORDER BY titre");
$stmt->bindParam(':matiere_id', $matiere['id']);
$stmt->execute();
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($matiere['nom']); ?> - Plateforme de Cours PHP</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="container">
        <?php afficher_header(); ?>

        <main class="content">
            <h2>Cours pour la matière : <?php echo htmlspecialchars($matiere['nom']); ?></h2>
            <?php if ($isAdmin): ?>
                <a href="../formulaires/add_cours.php?matiere_id=<?php echo htmlspecialchars($matiere['id']); ?>" class="btn">Ajouter un Cours</a>
            <?php endif; ?>
            <section>
                <?php if (count($cours) > 0): ?>
                    <?php foreach ($cours as $cour): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($cour['titre']); ?></h3>
                            <a href="cours.php?slug=<?php echo htmlspecialchars($cour['slug']); ?>" class="btn">Voir le cours</a>
                            <?php if ($isAdmin): ?>
                                <div class="admin-buttons">
                                    <a href="../formulaires/add_cours.php?id=<?php echo htmlspecialchars($cour['id']); ?>" class="btn edit-btn">Modifier</a>
                                    <a href="../includes/delete_cours.php?id=<?php echo htmlspecialchars($cour['id']); ?>" class="btn delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')">Supprimer</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun cours disponible pour cette matière pour le moment.</p>
                <?php endif; ?>
            </section>
        </main>
        
        <?php afficher_footer(); ?>
    </div> <!-- Fin de la div container -->

    <script src="/js/scripte.js"></script>
    <script src="/js/defilement.js"></script>
</body>
</html>

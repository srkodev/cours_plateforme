<?php

include('includes/database.php');
include('templates/header.php');
include('templates/footer.php');
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Cours PHP</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="container">
        <?php
        // Appeler la fonction pour afficher la sidebar
        afficher_header();
        ?>

        <main class="content">
            <h2>Les Matières</h2>
            <?php if ($isAdmin): ?>
                <a href="formulaires/add_matiere.php" class="btn">Ajouter une Matière</a>
            <?php endif; ?>
            <section>
                <?php
                // Récupérer toutes les matières depuis la base de données
                $stmt = $conn->prepare("SELECT * FROM matieres ORDER BY nom");
                $stmt->execute();
                $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($matieres as $matiere) {
                    echo '<div class="card">
                            <h3>' . htmlspecialchars($matiere['nom']) . '</h3>
                            <a href="cours/matiere.php?slug=' . htmlspecialchars($matiere['slug']) . '" class="btn">Voir les cours</a>';

                    // Afficher les boutons "Modifier" et "Supprimer" si l'utilisateur est administrateur
                    if ($isAdmin) {
                        echo '<div class="admin-buttons">
                                <a href="formulaires/add_matiere.php?id=' . htmlspecialchars($matiere['id']) . '" class="btn edit-btn">Modifier</a>
                                <a href="includes/delete_matiere.php?id=' . htmlspecialchars($matiere['id']) . '" class="btn delete-btn" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette matière ?\')">Supprimer</a>
                              </div>';
                    }

                    echo '</div>';
                }
                ?>
            </section>
        </main>
        
    </div> <!-- Fin de la div container -->

    <script src="/js/scripte.js"></script>
    <script src="/js/defilement.js"></script>
</body>
</html>

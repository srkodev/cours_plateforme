<?php
function afficher_header() {
    echo '
        <div class="hamburger-menu">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <aside class="sidebar">
            <div class="logo">
                <img src="/image/vosoft_logo.svg" alt="Logo" width="100">
            </div>
            <h2>Cours</h2>
            <input type="text" id="search-bar" placeholder="Rechercher un cours..." class="search-bar">
            <nav class="course-list">
                <ul>
                    <li><a href="/index.php">Accueil</a></li>';
                    
                    // Récupérer les matières pour les afficher dans la sidebar
                    $stmt = $GLOBALS['conn']->prepare("SELECT nom, slug FROM matieres");
                    $stmt->execute();
                    $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($matieres as $matiere) {
                        echo '<li><a href="/cours/matiere.php?slug=' . htmlspecialchars($matiere['slug']) . '">' . htmlspecialchars($matiere['nom']) . '</a></li>';
                    }
                    
    echo '      </ul>
            </nav>
            <div class="copyright">
                © 2024 - Tous droits réservés - Jules Crevoisier
            </div>
        </aside>

        <nav class="mobile-nav">
            <ul>
                <li><a href="/index.php">Accueil</a></li>';
                
                    // Récupérer les matières pour les afficher dans la navigation mobile
                    foreach ($matieres as $matiere) {
                        echo '<li><a href="/cours/matiere.php?slug=' . htmlspecialchars($matiere['slug']) . '">' . htmlspecialchars($matiere['nom']) . '</a></li>';
                    }
                    
    echo '      </ul>
        </nav>';
}
?>

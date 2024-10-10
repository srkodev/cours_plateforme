<?php
include('../includes/database.php');
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

// Vérifier si l'ID du cours est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID du cours non fourni ou invalide.";
    exit;
}

$cours_id = intval($_GET['id']);

// Récupérer les informations du cours avant de le supprimer pour obtenir le slug de la matière
$stmt = $conn->prepare("SELECT matiere_id FROM cours WHERE id = :id");
$stmt->bindParam(':id', $cours_id, PDO::PARAM_INT);
$stmt->execute();
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    echo "Cours non trouvé.";
    exit;
}

// Supprimer le cours de la base de données
$stmt = $conn->prepare("DELETE FROM cours WHERE id = :id");
$stmt->bindParam(':id', $cours_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    // Récupérer le slug de la matière pour la redirection
    $stmt = $conn->prepare("SELECT slug FROM matieres WHERE id = :id");
    $stmt->bindParam(':id', $cours['matiere_id'], PDO::PARAM_INT);
    $stmt->execute();
    $matiere = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($matiere) {
        // Redirection vers la page de la matière
        header("Location: ../cours/matiere.php?slug=" . urlencode($matiere['slug']));
        exit;
    } else {
        echo "Matière associée non trouvée.";
    }
} else {
    echo "Erreur lors de la suppression du cours.";
}
?>

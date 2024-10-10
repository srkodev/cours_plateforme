<?php
include('../includes/database.php');
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

// Vérifier si l'ID de la matière est passé en paramètre
if (!isset($_GET['id'])) {
    echo "ID de la matière non fourni.";
    exit;
}

$matiere_id = $_GET['id'];

// Supprimer la matière de la base de données
$stmt = $conn->prepare("DELETE FROM matieres WHERE id = :id");
$stmt->bindParam(':id', $matiere_id);

if ($stmt->execute()) {
    echo "Matière supprimée avec succès.";
    header("Location: ../index.php");
    exit;
} else {
    echo "Erreur lors de la suppression de la matière.";
}
?>

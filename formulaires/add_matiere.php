<?php
include('../includes/database.php');
session_start();

// Vérifier si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Rediriger vers la page d'accueil si l'utilisateur n'est pas administrateur
    header('Location: /index.php');
    exit;
}

// Vérifier si l'on est en mode édition ou création
$isEditing = isset($_GET['id']) && is_numeric($_GET['id']);
$matiere = null;

if ($isEditing) {
    // Récupérer les informations de la matière si on est en mode édition
    $matiere_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM matieres WHERE id = :id");
    $stmt->bindParam(':id', $matiere_id, PDO::PARAM_INT);
    $stmt->execute();
    $matiere = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$matiere) {
        // Rediriger vers index.php si la matière n'est pas trouvée
        header('Location: /index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $nom));

    if ($isEditing) {
        // Mettre à jour la matière
        $stmt = $conn->prepare("UPDATE matieres SET nom = :nom, slug = :slug WHERE id = :id");
        $stmt->bindParam(':id', $matiere_id, PDO::PARAM_INT);
    } else {
        // Insérer une nouvelle matière
        $stmt = $conn->prepare("INSERT INTO matieres (nom, slug) VALUES (:nom, :slug)");
    }

    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':slug', $slug);

    if ($stmt->execute()) {
        header('Location: ../index.php');
        exit;
    } else {
        echo "Erreur lors de l'enregistrement de la matière.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEditing ? 'Modifier une Matière' : 'Ajouter une Matière'; ?></title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <h1><?php echo $isEditing ? 'Modifier la Matière' : 'Ajouter une Matière'; ?></h1>
    <form action="" method="POST">
        <label for="nom">Nom de la matière :</label>
        <input type="text" id="nom" name="nom" value="<?php echo $isEditing ? htmlspecialchars($matiere['nom']) : ''; ?>" required>

        <button type="submit"><?php echo $isEditing ? 'Enregistrer les modifications' : 'Enregistrer la matière'; ?></button>
    </form>
</body>
</html>

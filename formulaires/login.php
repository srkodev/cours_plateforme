<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../includes/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Récupérer l'utilisateur depuis la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur");
    $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
        $_SESSION['role'] = $utilisateur['role'];
        header('Location: ../index.php'); // Rediriger vers index.php après connexion
        exit;
    } else {
        $erreur = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Back Office</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <h1>Connexion</h1>
    <form action="" method="POST">
        <label for="nom_utilisateur">Nom d'utilisateur :</label>
        <input type="text" id="nom_utilisateur" name="nom_utilisateur" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>

        <button type="submit">Se connecter</button>
    </form>

    <?php if (isset($erreur)): ?>
        <p class="error-message"><?php echo htmlspecialchars($erreur); ?></p>
    <?php endif; ?>
</body>
</html>

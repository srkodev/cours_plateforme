<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../includes/database.php');
session_start();

// Vérifier si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Rediriger vers la page d'accueil si l'utilisateur n'est pas administrateur
    header('Location: /index.php');
    exit;
}

// Vérifier si l'on est en mode édition ou création
$isEditing = false;
$cours = null;
$cours_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    // Récupérer l'ID via POST en mode édition
    $cours_id = intval($_POST['id']);
    $isEditing = true;
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Récupérer l'ID via GET si fourni
    $cours_id = intval($_GET['id']);
    $isEditing = true;
}

if ($isEditing && $cours_id) {
    // Récupérer les informations du cours si on est en mode édition
    $stmt = $conn->prepare("SELECT * FROM cours WHERE id = :id");
    $stmt->bindParam(':id', $cours_id, PDO::PARAM_INT);
    $stmt->execute();
    $cours = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cours) {
        // Rediriger vers index.php si le cours n'est pas trouvé
        header('Location: /index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $matiere_id = $_POST['matiere_id'];
    $contenu = $_POST['contenu'];
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $titre));

    // Vérification supplémentaire : S'assurer que la matière existe
    $stmt = $conn->prepare("SELECT * FROM matieres WHERE id = :id");
    $stmt->bindParam(':id', $matiere_id, PDO::PARAM_INT);
    $stmt->execute();
    $matiere = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$matiere) {
        // Rediriger ou afficher un message si la matière n'est pas trouvée
        echo "Matière non trouvée.";
        exit;
    }

    if ($isEditing) {
        // Mettre à jour le cours
        $stmt = $conn->prepare("UPDATE cours SET titre = :titre, slug = :slug, contenu = :contenu, matiere_id = :matiere_id WHERE id = :id");
        $stmt->bindParam(':id', $cours_id, PDO::PARAM_INT);
    } else {
        // Insérer un nouveau cours
        $stmt = $conn->prepare("INSERT INTO cours (titre, slug, contenu, matiere_id) VALUES (:titre, :slug, :contenu, :matiere_id)");
    }

    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':slug', $slug);
    $stmt->bindParam(':contenu', $contenu);
    $stmt->bindParam(':matiere_id', $matiere_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: ../cours/matiere.php?slug=' . urlencode($matiere['slug']));
        exit;
    } else {
        echo "Erreur lors de l'enregistrement du cours.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEditing ? 'Modifier un Cours' : 'Ajouter un Cours'; ?></title>
    <link rel="stylesheet" href="/css/styles.css">
    <script src="https://cdn.tiny.cloud/1/1a4y6mqw7pq692nvjga7nm9bu6zrcryjc8sxaj8npy9a4a1a/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '.editor',
            plugins: 'image link lists codesample textcolor colorpicker',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image | blocks fontfamily fontsizeinput | forecolor backcolor | codesample',
            menubar: false,
            height: 400,
            images_upload_url: '/includes/upload_image.php',
            automatic_uploads: true,
            file_picker_types: 'image',
            image_caption: true,
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype === 'image') {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function () {
                        const file = this.files[0];
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            callback(e.target.result, {
                                alt: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                }
            },
            codesample_languages: [
                {text: 'HTML/XML', value: 'markup'},
                {text: 'JavaScript', value: 'javascript'},
                {text: 'CSS', value: 'css'},
                {text: 'PHP', value: 'php'},
                {text: 'Python', value: 'python'},
                {text: 'Ruby', value: 'ruby'},
                {text: 'C', value: 'c'},
                {text: 'C#', value: 'csharp'},
                {text: 'C++', value: 'cpp'}
            ],
            font_size_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt"
        });
    </script>
</head>
<body>
    <h1 class="form-title"><?php echo $isEditing ? 'Modifier le Cours' : 'Ajouter un Cours'; ?></h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <?php if ($isEditing): ?>
            <input type="hidden" name="id" value="<?php echo $cours_id; ?>">
        <?php endif; ?>
        <label for="titre">Titre du cours :</label>
        <input type="text" id="titre" name="titre" value="<?php echo $isEditing ? htmlspecialchars($cours['titre']) : ''; ?>" required>

        <label for="matiere_id">Matière :</label>
        <select id="matiere_id" name="matiere_id">
            <?php
            $stmt = $conn->prepare("SELECT * FROM matieres");
            $stmt->execute();
            $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($matieres as $matiere) {
                $selected = $isEditing && $matiere['id'] == $cours['matiere_id'] ? 'selected' : '';
                echo '<option value="' . $matiere['id'] . '" ' . $selected . '>' . htmlspecialchars($matiere['nom']) . '</option>';
            }
            ?>
        </select>

        <label for="contenu">Contenu du cours :</label>
        <textarea id="contenu" name="contenu" class="editor"><?php echo $isEditing ? htmlspecialchars($cours['contenu']) : ''; ?></textarea>

        <button type="submit"><?php echo $isEditing ? 'Enregistrer les modifications' : 'Enregistrer le cours'; ?></button>
    </form>
</body>
</html>

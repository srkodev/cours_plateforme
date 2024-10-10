<?php
if ($_FILES) {
    $file = $_FILES['file'];
    $fileName = uniqid() . '_' . basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $fileDestination = '../uploads/' . $fileName;

    // Vérifier si le fichier est bien une image
    $fileType = mime_content_type($fileTmpName);
    if (strpos($fileType, 'image') === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Le fichier doit être une image.']);
        exit;
    }

    if (move_uploaded_file($fileTmpName, $fileDestination)) {
        $fileUrl = '/uploads/' . $fileName;
        echo json_encode(['location' => $fileUrl]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors du téléchargement de l\'image.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Aucun fichier reçu.']);
}
?>

<?php
$servername = "localhost";
$username = "cours"; // Remplace par le nom de ton utilisateur
$password = "cours"; // Remplace par le mot de passe de cet utilisateur
$dbname = "cours_plateforme";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

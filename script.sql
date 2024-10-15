-- Créer la base de données
CREATE DATABASE IF NOT EXISTS cours_plateforme;

-- Créer un utilisateur MariaDB et lui donner des droits sur la base de données
-- Remplacez 'nom_utilisateur' et 'mot_de_passe' par vos informations
CREATE USER 'cours'@'localhost' IDENTIFIED BY 'cours';

-- Accorder tous les privilèges à cet utilisateur sur la base de données
GRANT ALL PRIVILEGES ON cours_plateforme.* TO 'cours'@'localhost';

-- Appliquer les changements de privilèges
FLUSH PRIVILEGES;

-- Utiliser la base de données
USE cours_plateforme;

-- Créer la table des utilisateurs (pour les administrateurs)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Créer la table des matières
CREATE TABLE IF NOT EXISTS matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Créer la table des cours
CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    contenu TEXT NOT NULL,
    matiere_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);

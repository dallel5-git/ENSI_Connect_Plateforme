<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    die('ID invalide');
}

try {
    $db = (new Database())->connect();
    
    $stmt = $db->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$id]);
    $resource = $stmt->fetch();
    
    if (!$resource) {
        http_response_code(404);
        die('Ressource introuvable');
    }
    
    $filepath = UPLOADS_DIR . $resource['filename'];
    
    if (!file_exists($filepath)) {
        http_response_code(404);
        die('Fichier introuvable sur le serveur');
    }
    
    // Incrémenter le compteur
    $stmt = $db->prepare("UPDATE resources SET downloads = downloads + 1 WHERE id = ?");
    $stmt->execute([$id]);
    
    // Envoyer le fichier
    header('Content-Description: File Transfer');
    header('Content-Type: ' . ($resource['file_type'] ?: 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . $resource['original_filename'] . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    
    readfile($filepath);
    exit();
    
} catch(PDOException $e) {
    http_response_code(500);
    die('Erreur serveur');
}
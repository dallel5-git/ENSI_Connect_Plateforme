<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';


$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    Helpers::response(false, 'ID invalide', null, 400);
}

try {
    $db = (new Database())->connect();
    
    // Récupérer la ressource
    $stmt = $db->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$id]);
    $resource = $stmt->fetch();
    
    if (!$resource) {
        Helpers::response(false, 'Ressource introuvable', null, 404);
    }
    

    
    // Supprimer fichier physique
    $filepath = UPLOADS_DIR . $resource['filename'];
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    // Supprimer de la BD
    $stmt = $db->prepare("DELETE FROM resources WHERE id = ?");
    $stmt->execute([$id]);
    
    Helpers::response(true, 'Ressource supprimée avec succès');
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', null, 500);
}
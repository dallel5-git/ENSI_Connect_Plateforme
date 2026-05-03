<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';


$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    Helpers::response(false, 'ID invalide', null, 400);
}

try {
    $db = (new Database())->connect();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'professor'");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        Helpers::response(true, 'Professeur supprimé avec succès');
    } else {
        Helpers::response(false, 'Professeur introuvable', null, 404);
    }
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', null, 500);
}
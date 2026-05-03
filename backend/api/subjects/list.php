<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('GET');

try {
    $db = (new Database())->connect();
    $stmt = $db->query("SELECT id, name, code FROM subjects ORDER BY name");
    $subjects = $stmt->fetchAll();
    
    Helpers::response(true, 'Liste des matières', ['subjects' => $subjects]);
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', ['error' => $e->getMessage()], 500);
}

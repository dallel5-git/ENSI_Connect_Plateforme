<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('GET');

try {
    $db = (new Database())->connect();
    
    $stats = [];
    
    // Nombre de profs
    $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'professor'");
    $stats['total_professors'] = (int)$stmt->fetch()['total'];
    
    // Nombre d'étudiants
    $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
    $stats['total_students'] = (int)$stmt->fetch()['total'];
    
    // Nombre de ressources
    $stmt = $db->query("SELECT COUNT(*) as total FROM resources");
    $stats['total_resources'] = (int)$stmt->fetch()['total'];
    
    // Nombre de matières
    $stmt = $db->query("SELECT COUNT(*) as total FROM subjects");
    $stats['total_subjects'] = (int)$stmt->fetch()['total'];
    
    // Ressources par type
    $stmt = $db->query("SELECT type, COUNT(*) as count FROM resources GROUP BY type");
    $stats['resources_by_type'] = $stmt->fetchAll();
    
    // Dernières ressources
    $stmt = $db->query("
        SELECT r.id, r.title, r.type, r.created_at, u.fullname as professor, s.name as subject
        FROM resources r
        JOIN users u ON r.professor_id = u.id
        JOIN subjects s ON r.subject_id = s.id
        ORDER BY r.created_at DESC
        LIMIT 5
    ");
    $stats['latest_resources'] = $stmt->fetchAll();
    
    // Top downloads
    $stmt = $db->query("
        SELECT id, title, downloads FROM resources
        ORDER BY downloads DESC LIMIT 5
    ");
    $stats['top_downloads'] = $stmt->fetchAll();
    
    Helpers::response(true, 'Statistiques récupérées', $stats);
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', null, 500);
}
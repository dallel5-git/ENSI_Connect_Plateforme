<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('GET');

try {
    $db = (new Database())->connect();
    
    $type = $_GET['type'] ?? '';
    $subject_id = $_GET['subject'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $sql = "
        SELECT r.*, s.name as subject_name, s.code as subject_code,
               u.fullname as professor_name
        FROM resources r
        INNER JOIN subjects s ON r.subject_id = s.id
        INNER JOIN users u ON r.professor_id = u.id
        WHERE 1=1
    ";
    
    $params = [];
    
    if (!empty($type)) {
        $sql .= " AND r.type = ?";
        $params[] = $type;
    }
    
    if (!empty($subject_id)) {
        $sql .= " AND r.subject_id = ?";
        $params[] = $subject_id;
    }
    
    if (!empty($search)) {
        $sql .= " AND (r.title LIKE ? OR s.name LIKE ? OR u.fullname LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY r.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $resources = $stmt->fetchAll();
    
    // Formater taille et URL
    foreach ($resources as &$r) {
        $r['file_size_formatted'] = Helpers::formatFileSize($r['file_size']);
        $r['download_url'] = API_URL . '/resources/download.php?id=' . $r['id'];
    }
    
    Helpers::response(true, count($resources) . ' ressource(s) trouvée(s)', [
        'resources' => $resources,
        'total' => count($resources)
    ]);
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', null, 500);
}
<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('GET');

try {
    $db = (new Database())->connect();
    
    // Filtres optionnels
    $search = $_GET['search'] ?? '';
    $department = $_GET['department'] ?? '';
    
    $sql = "
        SELECT u.id, u.fullname, u.email, u.phone, u.avatar,
               p.department, p.subject, p.bio, p.office
        FROM users u
        INNER JOIN professors p ON p.user_id = u.id
        WHERE u.role = 'professor'
    ";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (u.fullname LIKE ? OR p.subject LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($department)) {
        $sql .= " AND p.department = ?";
        $params[] = $department;
    }
    
    $sql .= " ORDER BY u.fullname ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $professors = $stmt->fetchAll();
    
    Helpers::response(true, count($professors) . ' professeur(s) trouvé(s)', [
        'professors' => $professors,
        'total' => count($professors)
    ]);
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', null, 500);
}
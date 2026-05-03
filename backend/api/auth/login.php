<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('POST');

$data = Helpers::getJSONInput();
Helpers::requireFields($data, ['email', 'password', 'role']);

$email = strtolower(Helpers::sanitize($data['email']));
$password = $data['password'];
$role = Helpers::sanitize($data['role']);

try {
    $db = (new Database())->connect();
    
    // Rechercher l'utilisateur (comparaison directe du mot de passe)
    $stmt = $db->prepare("
        SELECT u.*, 
               p.department, p.subject as prof_subject,
               s.student_number, s.level, s.specialty
        FROM users u
        LEFT JOIN professors p ON p.user_id = u.id
        LEFT JOIN students s ON s.user_id = u.id
        WHERE u.email = ? AND u.password = ? AND u.role = ?
    ");
    $stmt->execute([$email, $password, $role]);
    $user = $stmt->fetch();
    
    if (!$user) {
        Helpers::response(false, 'Email, mot de passe ou profil incorrect', null, 401);
    }
    
    // Réponse simple
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'data' => [
            'user' => [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'role' => $user['role'],
                'email' => $user['email']
            ]
        ]
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
}               
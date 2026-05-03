<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('POST');

$data = Helpers::getJSONInput();
Helpers::requireFields($data, ['fullname', 'email', 'password', 'role']);

$fullname = Helpers::sanitize($data['fullname']);
$email = strtolower(Helpers::sanitize($data['email']));
$password = $data['password'];  // ⚠️ Pas de hash !
$role = Helpers::sanitize($data['role']);

// Validations
if (!Helpers::isValidEmail($email)) {
    Helpers::response(false, 'Email invalide', null, 400);
}

if (strlen($password) < 4) {
    Helpers::response(false, 'Mot de passe trop court (min 4 caractères)', null, 400);
}

if (!in_array($role, ['student', 'professor'])) {
    Helpers::response(false, 'Rôle invalide', null, 400);
}

try {
    $db = (new Database())->connect();
    
    // Vérifier si email existe déjà
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        Helpers::response(false, 'Cet email est déjà utilisé', null, 409);
    }
    
    // Insertion SANS hash (mot de passe en clair)
    $stmt = $db->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fullname, $email, $password, $role]);
    $userId = $db->lastInsertId();
    
    // Créer entrée dans la table correspondante
    if ($role === 'student') {
        $studentNumber = 'ENSI' . date('Y') . str_pad($userId, 4, '0', STR_PAD_LEFT);
        $level = $data['level'] ?? '1A';
        $stmt = $db->prepare("INSERT INTO students (user_id, student_number, level) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $studentNumber, $level]);
    } elseif ($role === 'professor') {
        $department = $data['department'] ?? 'Informatique';
        $subject = $data['subject'] ?? 'Non défini';
        $stmt = $db->prepare("INSERT INTO professors (user_id, department, subject) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $department, $subject]);
    }
    
    Helpers::response(true, 'Inscription réussie ! Vous pouvez maintenant vous connecter.', [
        'user_id' => $userId
    ], 201);
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', ['error' => $e->getMessage()], 500);
}
<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';


$data = Helpers::getJSONInput();
Helpers::requireFields($data, ['fullname', 'email', 'password', 'department', 'subject']);

$fullname = Helpers::sanitize($data['fullname']);
$email = strtolower(Helpers::sanitize($data['email']));
$password = $data['password'];  // ⚠️ Pas de hash !
$department = Helpers::sanitize($data['department']);
$subject = Helpers::sanitize($data['subject']);
$phone = Helpers::sanitize($data['phone'] ?? '');
$bio = Helpers::sanitize($data['bio'] ?? '');
$office = Helpers::sanitize($data['office'] ?? '');

try {
    $db = (new Database())->connect();
    $db->beginTransaction();
    
    // Vérifier email
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        Helpers::response(false, 'Cet email existe déjà', null, 409);
    }
    
    // Insérer user (mot de passe en clair)
    $stmt = $db->prepare("INSERT INTO users (fullname, email, password, role, phone) VALUES (?, ?, ?, 'professor', ?)");
    $stmt->execute([$fullname, $email, $password, $phone]);
    $userId = $db->lastInsertId();
    
    // Insérer prof
    $stmt = $db->prepare("INSERT INTO professors (user_id, department, subject, bio, office) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $department, $subject, $bio, $office]);
    
    $db->commit();
    
    Helpers::response(true, 'Professeur ajouté avec succès', ['user_id' => $userId], 201);
    
} catch(PDOException $e) {
    $db->rollBack();
    Helpers::response(false, 'Erreur serveur', ['error' => $e->getMessage()], 500);
}
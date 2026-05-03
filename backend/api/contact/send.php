<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

Helpers::checkMethod('POST');

$data = Helpers::getJSONInput();
Helpers::requireFields($data, ['fullname', 'email', 'subject', 'message']);

$fullname = Helpers::sanitize($data['fullname']);
$email = strtolower(Helpers::sanitize($data['email']));
$subject = Helpers::sanitize($data['subject']);
$message = Helpers::sanitize($data['message']);

if (!Helpers::isValidEmail($email)) {
    Helpers::response(false, 'Email invalide', null, 400);
}

if (strlen($message) < 10) {
    Helpers::response(false, 'Message trop court (min 10 caractères)', null, 400);
}

try {
    $db = (new Database())->connect();
    
    $stmt = $db->prepare("INSERT INTO contact_messages (fullname, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fullname, $email, $subject, $message]);
    
    Helpers::response(true, 'Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.', [
        'message_id' => $db->lastInsertId()
    ], 201);
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', null, 500);
}
<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
// Récupérer professor_id depuis le POST (plus de JWT)
$professor_id = $_POST['professor_id'] ?? null;
if (!$professor_id) {
    Helpers::response(false, 'professor_id manquant', null, 400);
}

// Vérifier fichier
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    Helpers::response(false, 'Aucun fichier ou erreur lors de l\'upload', null, 400);
}

$file = $_FILES['file'];

// Vérifier taille
if ($file['size'] > MAX_FILE_SIZE) {
    Helpers::response(false, 'Fichier trop volumineux (max ' . Helpers::formatFileSize(MAX_FILE_SIZE) . ')', null, 400);
}

// Vérifier extension
if (!Helpers::isAllowedExtension($file['name'])) {
    Helpers::response(false, 'Extension non autorisée. Autorisées: ' . implode(', ', ALLOWED_EXTENSIONS), null, 400);
}

// Champs requis
$title = Helpers::sanitize($_POST['title'] ?? '');
$description = Helpers::sanitize($_POST['description'] ?? '');
$type = Helpers::sanitize($_POST['type'] ?? '');
$subject_id = $_POST['subject_id'] ?? null;

if (empty($title) || empty($type) || empty($subject_id)) {
    Helpers::response(false, 'Champs requis : title, type, subject_id', null, 400);
}

if (!in_array($type, ['cours', 'td', 'tp', 'devoir'])) {
    Helpers::response(false, 'Type invalide', null, 400);
}

try {
    $db = (new Database())->connect();
    
    // Vérifier que la matière existe
    $stmt = $db->prepare("SELECT id FROM subjects WHERE id = ?");
    $stmt->execute([$subject_id]);
    if (!$stmt->fetch()) {
        Helpers::response(false, 'Matière invalide', null, 400);
    }
    
    // Créer dossier upload si n'existe pas
    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }
    
    // Générer nom unique
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('res_') . '_' . time() . '.' . $ext;
    $filepath = UPLOADS_DIR . $filename;
    
    // Déplacer le fichier
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        Helpers::response(false, 'Erreur lors de l\'enregistrement du fichier', null, 500);
    }
    
    // Enregistrer en BD
    $stmt = $db->prepare("
        INSERT INTO resources (title, description, type, subject_id, professor_id, filename, original_filename, file_size, file_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $title,
        $description,
        $type,
        $subject_id,
        $professor_id,
        $filename,
        $file['name'],
        $file['size'],
        $file['type']
    ]);
    
    Helpers::response(true, 'Fichier uploadé avec succès', [
        'resource_id' => $db->lastInsertId(),
        'filename' => $filename
    ], 201);
    
} catch(PDOException $e) {
    Helpers::response(false, 'Erreur serveur', ['error' => $e->getMessage()], 500);
}

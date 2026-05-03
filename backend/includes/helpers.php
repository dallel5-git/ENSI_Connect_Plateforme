<?php
class Helpers {
    
    // Réponse JSON standardisée
    public static function response($success, $message, $data = null, $code = 200) {
        http_response_code($code);
        $response = ['success' => $success, 'message' => $message];
        if ($data !== null) $response['data'] = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // Validation email
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Nettoyer les données
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    // Générer un token aléatoire
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    // Récupérer les données JSON du body
    public static function getJSONInput() {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        return $data ?: [];
    }
    
    // Valider les champs requis
    public static function requireFields($data, $fields) {
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missing[] = $field;
            }
        }
        if (!empty($missing)) {
            self::response(false, 'Champs manquants : ' . implode(', ', $missing), null, 400);
        }
    }
    
    // Vérifier extension de fichier
    public static function isAllowedExtension($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ALLOWED_EXTENSIONS);
    }
    
    // Formatter taille fichier
    public static function formatFileSize($bytes) {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
    
    // Valider méthode HTTP
    public static function checkMethod($method) {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            self::response(false, 'Méthode HTTP non autorisée', null, 405);
        }
    }
}
<?php
// Configuration globale
define('APP_NAME', 'ENSI');
define('APP_URL', 'http://localhost/ensi_website');
define('API_URL', APP_URL . '/backend/api');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('UPLOADS_URL', APP_URL . '/backend/uploads/');       


// Configuration upload
define('MAX_FILE_SIZE', 20 * 1024 * 1024); // 20 MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'png', 'jpg', 'jpeg']);

// Timezone
date_default_timezone_set('Africa/Tunis');

// Error reporting (désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);


<?php

define('DB_HOST', 'db.fr-pari1.bengt.wasmernet.com');
define('DB_USER', '290c6a97764b80005ea0e33253b5');
define('DB_PASS', '0691290c-6a97-78c7-8000-452cc05b6161');
define('DB_NAME', 'webdev');

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'webdev');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500); die(json_encode([
        'success' => false, 'message' => 'DB connection failed']));
}

// else echo 'Connection Successful';

$conn->set_charset('utf8mb4');
?>

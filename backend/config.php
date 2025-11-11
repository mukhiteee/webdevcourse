<?php

// define('DB_HOST', 'fdb1034.awardspace.net');
// define('DB_USER', '4704687_webdevcourse');
// define('DB_PASS', '@Codewithmukhiteee_WEBDEVCOURSE1');
// define('DB_NAME', '4704687_webdevcourse');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'webdev');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500); die(json_encode([
        'success' => false, 'message' => 'DB connection failed']));
}

// else echo 'Connection Successful';

$conn->set_charset('utf8mb4');
?>

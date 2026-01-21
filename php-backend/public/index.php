<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS, DELETE, PUT, PATCH, UPDATE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $pdo = require_once __DIR__ . "/../data/database.php";
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load database']);

    exit;
}

require_once __DIR__ . "/../routes/task.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = '/' . trim($uri, '/');

$method = $_SERVER['REQUEST_METHOD'];

route($method, $path, $pdo);


?>
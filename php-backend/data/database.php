<?php

$host = "localhost";
$user = "root";
$password = "Cabangal10";
$db_name = "todo";
$charset = "utf8mb4";

$connection = "mysql:host=$host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, 
];

try {
    $pdo = new PDO($connection, $user, $password, $options);
    return $pdo;
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'error' => 'Database connection failed',
        'message' => $e->getMessage() // for debugging only, remove for real life production use
    ]);

    exit;
}

?>
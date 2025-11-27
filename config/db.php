<?php
// Database configuration for XAMPP / local development
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'screams_of_oblivion';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['error' => 'Falha na conexão com o banco de dados']);
    exit;
}
$mysqli->set_charset('utf8mb4');

// Helper: simple function to JSON-escape output if needed
function json_resp($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

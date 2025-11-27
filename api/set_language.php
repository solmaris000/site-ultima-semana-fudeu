<?php
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);
$idioma = trim($input['idioma'] ?? 'en');

if ($id <= 0) {
    json_resp(['sucesso' => false, 'mensagem' => 'ID inválido']);
    exit;
}

if (!in_array($idioma, ['en', 'pt'])) {
    json_resp(['sucesso' => false, 'mensagem' => 'Idioma inválido']);
    exit;
}

// Check if config row exists
$stmt = $mysqli->prepare('SELECT id FROM configuracoes_usuario WHERE usuario_id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

if ($exists) {
    // Update
    $stmt = $mysqli->prepare('UPDATE configuracoes_usuario SET idioma = ? WHERE usuario_id = ? LIMIT 1');
    $stmt->bind_param('si', $idioma, $id);
} else {
    // Insert
    $stmt = $mysqli->prepare('INSERT INTO configuracoes_usuario (usuario_id, idioma) VALUES (?, ?)');
    $stmt->bind_param('is', $id, $idioma);
}

$ok = $stmt->execute();
if (!$ok) {
    json_resp(['sucesso' => false, 'mensagem' => 'Erro ao atualizar idioma']);
    exit;
}
$stmt->close();

json_resp(['sucesso' => true, 'idioma' => $idioma]);

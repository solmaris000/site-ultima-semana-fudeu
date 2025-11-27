<?php
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    json_resp(['sucesso' => false, 'mensagem' => 'ID inválido']);
    exit;
}

$stmt = $mysqli->prepare('SELECT idioma FROM configuracoes_usuario WHERE usuario_id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($idioma);
$found = $stmt->fetch();
$stmt->close();

if (!$found) {
    // Return default language if no setting exists
    json_resp(['sucesso' => true, 'idioma' => 'en']);
} else {
    json_resp(['sucesso' => true, 'idioma' => $idioma ?: 'en']);
}

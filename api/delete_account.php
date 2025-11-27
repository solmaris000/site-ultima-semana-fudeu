<?php
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    json_resp(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$id = intval($input['id'] ?? 0);
if ($id <= 0) {
    json_resp(['sucesso' => false, 'mensagem' => 'ID inválido']);
    exit;
}

// Delete user. Cascading constraints should remove related rows (configuracoes_usuario)
$stmt = $mysqli->prepare('DELETE FROM usuarios WHERE id = ? LIMIT 1');
if (!$stmt) {
    json_resp(['sucesso' => false, 'mensagem' => 'Erro no servidor']);
    exit;
}
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
if (!$ok) {
    json_resp(['sucesso' => false, 'mensagem' => 'Erro ao excluir usuário']);
    exit;
}
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected <= 0) {
    json_resp(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
    exit;
}

json_resp(['sucesso' => true]);

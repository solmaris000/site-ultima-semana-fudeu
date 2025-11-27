<?php
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    json_resp(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$id = intval($input['id'] ?? 0);
if ($id <= 0) {
    json_resp(['sucesso' => false, 'mensagem' => 'ID de usuário inválido']);
    exit;
}

$fields = [];
$types = '';
$params = [];

if (isset($input['nome_exibicao'])) {
    $fields[] = 'nome_exibicao = ?';
    $types .= 's';
    $params[] = $input['nome_exibicao'];
}
if (isset($input['avatar'])) {
    $fields[] = 'avatar = ?';
    $types .= 's';
    $params[] = $input['avatar'];
}

if (empty($fields)) {
    json_resp(['sucesso' => false, 'mensagem' => 'Nada para atualizar']);
    exit;
}

$sql = 'UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = ? LIMIT 1';
$types .= 'i';
$params[] = $id;

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$ok = $stmt->execute();
if (!$ok) {
    json_resp(['sucesso' => false, 'mensagem' => 'Erro ao atualizar banco']);
    exit;
}
$stmt->close();

$stmt = $mysqli->prepare('SELECT id, nome_usuario, email, nome_exibicao, avatar FROM usuarios WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($id_r, $nome_usuario, $email, $nome_exibicao, $avatar);
$stmt->fetch();
$stmt->close();

$usuario = ['id' => $id_r, 'nome_usuario' => $nome_usuario, 'email' => $email, 'nome_exibicao' => $nome_exibicao, 'avatar' => $avatar];
json_resp(['sucesso' => true, 'usuario' => $usuario]);

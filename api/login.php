<?php
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    json_resp(['sucesso' => false, 'erro' => 'Dados inválidos']);
    exit;
}

$email = trim($input['email'] ?? '');
$senha = $input['senha'] ?? '';

if (!$email || !$senha) {
    json_resp(['sucesso' => false, 'erro' => 'Credenciais inválidas']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, nome_usuario, email, senha, nome_exibicao, avatar FROM usuarios WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    json_resp(['sucesso' => false, 'erro' => 'Usuário não encontrado']);
    exit;
}
$stmt->bind_result($id, $nome_usuario, $email_db, $hash, $nome_exibicao, $avatar);
$stmt->fetch();

if (!password_verify($senha, $hash)) {
    json_resp(['sucesso' => false, 'erro' => 'Senha incorreta']);
    exit;
}

$user = ['id' => $id, 'nome_usuario' => $nome_usuario, 'email' => $email_db, 'nome_exibicao' => $nome_exibicao, 'avatar' => $avatar];
json_resp(['sucesso' => true, 'usuario' => $user]);

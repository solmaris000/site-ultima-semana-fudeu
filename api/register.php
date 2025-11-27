<?php
require_once __DIR__ . '/../config/db.php';

// Expect JSON body
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    json_resp(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$nome_usuario = trim($input['nome_usuario'] ?? '');
$email = trim($input['email'] ?? '');
$senha = $input['senha'] ?? '';
$nome_exibicao = trim($input['nome_exibicao'] ?? $nome_usuario);

if (!$nome_usuario || !$email || !$senha) {
    json_resp(['sucesso' => false, 'mensagem' => 'Campos obrigatórios ausentes']);
    exit;
}

// Check existing username or email
$stmt = $mysqli->prepare('SELECT id FROM usuarios WHERE nome_usuario = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $nome_usuario, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    json_resp(['sucesso' => false, 'mensagem' => 'Nome de usuário ou email já está em uso']);
    exit;
}
$stmt->close();

$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare('INSERT INTO usuarios (nome_usuario, email, senha, nome_exibicao) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $nome_usuario, $email, $hash, $nome_exibicao);
$ok = $stmt->execute();
if (!$ok) {
    json_resp(['sucesso' => false, 'mensagem' => 'Erro ao criar conta']);
    exit;
}
$insert_id = $stmt->insert_id;
$stmt->close();

// Fetch and return the full user record (including avatar) for consistency with login
$stmt = $mysqli->prepare('SELECT id, nome_usuario, email, nome_exibicao, avatar FROM usuarios WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $insert_id);
$stmt->execute();
$stmt->bind_result($id_r, $nome_usuario_r, $email_r, $nome_exibicao_r, $avatar_r);
$stmt->fetch();
$stmt->close();

$usuario = ['id' => $id_r, 'nome_usuario' => $nome_usuario_r, 'email' => $email_r, 'nome_exibicao' => $nome_exibicao_r, 'avatar' => $avatar_r];
json_resp(['sucesso' => true, 'usuario' => $usuario]);

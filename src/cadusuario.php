<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type
header('Content-Type: application/json');

// Include the shared DB connection
require_once 'conexao.php';
$con->set_charset("utf8");

// Get JSON input
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// Extract and validate data
$nmUsuario    = trim($jsonParam['nmUsuario'] ?? '');
$deEmail      = trim($jsonParam['deEmail'] ?? '');
$deSenha      = trim($jsonParam['deSenha'] ?? '');
$cdSexo       = intval($jsonParam['cdSexo'] ?? 0);
$cdTipo       = intval($jsonParam['cdTipo'] ?? 0);
$dtNascimento = !empty($jsonParam['dtNascimento']) ? date('Y-m-d', strtotime($jsonParam['dtNascimento'])) : null;
$opTermo      = !empty($jsonParam['opTermo']) ? 1 : 0;

// Validate required fields
if (empty($nmUsuario) || empty($deEmail) || empty($deSenha) || !$dtNascimento) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes.']);
    exit;
}

// Prepare and bind
$stmt = $con->prepare("
    INSERT INTO Usuario (nmUsuario, deEmail, deSenha, cdSexo, cdTipo, dtNascimento, opTermo)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $con->error]);
    exit;
}

$stmt->bind_param("sssiisi", $nmUsuario, $deEmail, $deSenha, $cdSexo, $cdTipo, $dtNascimento, $opTermo);

// Execute and return result
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Usuário inserido com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro no registro do usuário: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>

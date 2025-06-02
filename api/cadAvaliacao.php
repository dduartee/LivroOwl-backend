<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Header JSON
header('Content-Type: application/json');

// Conexão com banco
require_once 'conexao.php';
$con->set_charset("utf8");

// Ler o JSON de entrada
$jsonInput = json_decode(file_get_contents('php://input'), true);

// Verificar JSON
if (!$jsonInput) {
    echo json_encode(['success' => false, 'message' => 'JSON inválido ou ausente.']);
    exit;
}

// Extrair campos
$comentario = trim($jsonInput['comentario'] ?? '');
$estrelas   = floatval($jsonInput['estrelas'] ?? 0);
$like       = intval($jsonInput['liked'] ?? 0);
$dataAval   = !empty($jsonInput['timestamp_avaliado']) ? date('Y-m-d', strtotime($jsonInput['timestamp_avaliado'])) : null;
$idLivro    = intval($jsonInput['idLivro'] ?? 0);

// Validar campos obrigatórios
if ($estrelas <= 0 || !$dataAval || $idLivro <= 0) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes ou inválidos.']);
    exit;
}

// Gerar um novo ID para a avaliação (opcional — depende do seu banco)
$idAvaliacao = null;
$result = $con->query("SELECT MAX(idAvaliacao) + 1 AS nextId FROM Avaliacao WHERE idLivro = $idLivro");
if ($result && $row = $result->fetch_assoc()) {
    $idAvaliacao = $row['nextId'] ?? 1;
} else {
    $idAvaliacao = 1;
}

// Preparar e executar o insert
$stmt = $con->prepare("
    INSERT INTO Avaliacao (idAvaliacao, comentario, estrelas, `like`, dataAval, idLivro)
    VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . $con->error]);
    exit;
}

$stmt->bind_param("isdisi", $idAvaliacao, $comentario, $estrelas, $like, $dataAval, $idLivro);

if ($stmt->execute()) {
    // Converter data para timestamp
    $timestampAvaliado = strtotime($dataAval);

    echo json_encode([
        'success' => true,
        'message' => 'Avaliação cadastrada com sucesso!',
        'id' => $idAvaliacao,
        'timestamp_avaliado' => $timestampAvaliado
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao inserir avaliação: ' . $stmt->error
    ]);
}

$stmt->close();
$con->close();

?>

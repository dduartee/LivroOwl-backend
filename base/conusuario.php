<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the existing connection script
require_once 'conexao.php';
$con->set_charset("utf8");

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);
$nmUsuario = isset($input['nmUsuario']) ? trim($input['nmUsuario']) : '';

// SQL with case-insensitive search using LOWER()
$sql = "SELECT idUsuario, nmUsuario, deEmail, deSenha, cdSexo, cdTipo, dtNascimento, opTermo 
        FROM Usuario 
        WHERE LOWER(nmUsuario) LIKE LOWER(?)";

$stmt = $con->prepare($sql);
$likeParam = '%' . $nmUsuario . '%';
$stmt->bind_param('s', $likeParam);

$stmt->execute();
$result = $stmt->get_result();

$response = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = array_map(fn($val) => mb_convert_encoding($val, 'UTF-8', 'ISO-8859-1'), $row);
    }
} else {
    $response[] = [
        "idUsuario" => 0,
        "nmUsuario" => "",
        "deEmail" => "",
        "deSenha" => "",
        "cdSexo" => 0,
        "cdTipo" => 0,
        "dtNascimento" => "",
        "opTermo" => false
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

$stmt->close();
$con->close();

?>

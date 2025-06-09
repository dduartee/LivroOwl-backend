<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com banco
require_once 'conexao.php';
$con->set_charset("utf8");

// Entrada JSON
$input = json_decode(file_get_contents('php://input'), true);

// Parâmetros de filtro (opcionais)
$idLivro     = isset($input['idLivro']) ? intval($input['idLivro']) : 0;
$comentario  = isset($input['comentario']) ? trim($input['comentario']) : '';

// SQL base com JOIN
$sql = "SELECT 
            a.idAvaliacao,
            a.comentario,
            a.estrelas,
            a.`like` AS liked,
            UNIX_TIMESTAMP(a.dataAval) * 1000 AS timestamp_avaliado,
            a.idLivro,
            l.nome AS nomeLivro,
            l.urlCover AS urlCover
        FROM Avaliacao a
        JOIN Livro l ON a.idLivro = l.idLivro
        WHERE 1=1";

// Filtros dinâmicos
$params = [];
$types = '';

if ($idLivro > 0) {
    $sql .= " AND a.idLivro = ?";
    $params[] = $idLivro;
    $types .= 'i';
}

if (!empty($comentario)) {
    $sql .= " AND LOWER(a.comentario) LIKE LOWER(?)";
    $params[] = '%' . $comentario . '%';
    $types .= 's';
}

$stmt = $con->prepare($sql);

// Bind se houver parâmetros
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$response = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        while ($row = $result->fetch_assoc()) {
            $row['liked'] = ($row['liked'] == 1); // converte para booleano verdadeiro/falso
            foreach ($row as $key => $val) {
                $row[$key] = is_string($val) ? mb_convert_encoding($val, 'UTF-8', 'ISO-8859-1') : $val;
            }
            $response[] = $row;
        }
    }
} else {
    $response[] = [
        "idAvaliacao" => 0,
        "comentario" => "",
        "estrelas" => 0,
        "liked" => false,
        "timestamp_avaliado" => 0,
        "idLivro" => 0,
        "nomeLivro" => "",
        "urlCover" => ""
    ];
}
// Saída JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

$stmt->close();
$con->close();

?>

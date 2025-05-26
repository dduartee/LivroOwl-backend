<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'conexao.php';
$con->set_charset("utf8");

// Lê o JSON
$jsonData = json_decode(file_get_contents('php://input'), true);

if (!$jsonData || !is_array($jsonData)) {
    echo json_encode(['success' => false, 'message' => 'JSON inválido ou ausente.']);
    exit;
}

// Assumimos apenas um livro por vez
$firstKey = array_key_first($jsonData);
$livroData = $jsonData[$firstKey];

// Extrai os dados principais
$titulo = trim($jsonData['nome'] ?? '');
$autor = $jsonData['autor'] ?? '';
$genero = $jsonData['genero'] ?? '';
$isbn = '0000000000000'; // coloque um valor padrão ou gere
$anoPub = '2020';
$urlCover = trim($livroData['coverURL'] ?? '');

if (empty($titulo) || empty($isbn) || empty($anoPub)) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios do livro ausentes.']);
    exit;
}

// Insere o livro
$stmtLivro = $con->prepare("INSERT INTO Livro (nome, ISBN, anoPub, urlCover) VALUES (?, ?, ?, ?)");
$stmtLivro->bind_param("ssss", $titulo, $isbn, $anoPub, $urlCover);

if (!$stmtLivro->execute()) {
    echo json_encode(['success' => false, 'message' => 'Erro ao inserir livro: ' . $stmtLivro->error]);
    exit;
}

$idLivro = $stmtLivro->insert_id;
$stmtLivro->close();

// Autores
if (!empty($livroData['authors']) && is_array($livroData['authors'])) {
    foreach ($livroData['authors'] as $autor) {
        $nomeAutor = trim($autor['name']);

        // Verifica se o autor já existe
        $stmtBuscaAutor = $con->prepare("SELECT idAutor FROM Autor WHERE nome = ?");
        $stmtBuscaAutor->bind_param("s", $nomeAutor);
        $stmtBuscaAutor->execute();
        $stmtBuscaAutor->store_result();
        $stmtBuscaAutor->bind_result($idAutor);
        
        if ($stmtBuscaAutor->num_rows > 0) {
            $stmtBuscaAutor->fetch();
        } else {
            // Insere novo autor
            $idAutor = uniqid(); // você pode trocar para UUID ou outro método
            $stmtInsereAutor = $con->prepare("INSERT INTO Autor (idAutor, nome) VALUES (?, ?)");
            $stmtInsereAutor->bind_param("ss", $idAutor, $nomeAutor);
            $stmtInsereAutor->execute();
            $stmtInsereAutor->close();
        }

        // Associa livro ao autor
        $stmtAutoria = $con->prepare("INSERT IGNORE INTO Autoria (Livro_idLivro, Autor_idAutor) VALUES (?, ?)");
        $stmtAutoria->bind_param("is", $idLivro, $idAutor);
        $stmtAutoria->execute();
        $stmtAutoria->close();

        $stmtBuscaAutor->close();
    }
}

// Gêneros (subjects)
if (!empty($livroData['subjects']) && is_array($livroData['subjects'])) {
    foreach ($livroData['subjects'] as $subject) {
        $nomeGenero = trim($subject['name']);

        // Verifica se o gênero já existe
        $stmtBuscaGenero = $con->prepare("SELECT idGenero FROM Genero WHERE nome = ?");
        $stmtBuscaGenero->bind_param("s", $nomeGenero);
        $stmtBuscaGenero->execute();
        $stmtBuscaGenero->store_result();
        $stmtBuscaGenero->bind_result($idGenero);

        if ($stmtBuscaGenero->num_rows > 0) {
            $stmtBuscaGenero->fetch();
        } else {
            // Insere novo gênero
            $stmtMaxGenero = $con->query("SELECT MAX(idGenero) AS maxId FROM Genero");
            $row = $stmtMaxGenero->fetch_assoc();
            $idGenero = $row['maxId'] + 1;

            $stmtInsereGenero = $con->prepare("INSERT INTO Genero (idGenero, nome) VALUES (?, ?)");
            $stmtInsereGenero->bind_param("is", $idGenero, $nomeGenero);
            $stmtInsereGenero->execute();
            $stmtInsereGenero->close();
        }

        // Associa livro ao gênero
        $stmtLivroGenero = $con->prepare("INSERT IGNORE INTO LivroGenero (Livro_idLivro, Genero_idGenero) VALUES (?, ?)");
        $stmtLivroGenero->bind_param("ii", $idLivro, $idGenero);
        $stmtLivroGenero->execute();
        $stmtLivroGenero->close();

        $stmtBuscaGenero->close();
    }
}

$con->close();

echo json_encode(['success' => true, 'message' => 'Livro inserido com sucesso!', 'idLivro' => $idLivro]);

?>

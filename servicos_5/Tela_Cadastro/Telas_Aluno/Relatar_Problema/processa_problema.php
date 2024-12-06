<?php
// processa_problema.php

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "ScanView");

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Captura os dados do formulário
$nome = $_POST['nome'];
$laboratorio = $_POST['laboratorio'];
$computador = $_POST['computador'];
$problemas = isset($_POST['problema']) ? implode(", ", $_POST['problema']) : '';

// ** Sempre cria um novo registro na tabela Computadores **
$sql_novo_computador = "INSERT INTO Computadores (Laboratorio, Computador, ID_Aluno) VALUES ('$laboratorio', '$computador', (SELECT ID_Aluno FROM Alunos WHERE Nome_Completo = '$nome'))";

if ($conn->query($sql_novo_computador) === TRUE) {
    $id_computador = $conn->insert_id;  // Obtém o ID do novo computador inserido
} else {
    echo "Erro ao registrar o computador: " . $conn->error;
    exit;
}

// Agora, inserimos o novo problema na tabela Problemas
$sql_problema = "INSERT INTO Problemas (ID_Computador, Descricao, Nome_Aluno) VALUES ('$id_computador', '$problemas', '$nome')";

if ($conn->query($sql_problema) === TRUE) {
    // Redireciona para uma página HTML de sucesso após inserir o problema
    header("Location: enviado/enviado.php");
    exit(); // Não esqueça do exit() após o header para garantir que o script pare aqui
} else {
    echo "Erro: " . $sql_problema . "<br>" . $conn->error;
}

// Fecha a conexão
$conn->close();
?>

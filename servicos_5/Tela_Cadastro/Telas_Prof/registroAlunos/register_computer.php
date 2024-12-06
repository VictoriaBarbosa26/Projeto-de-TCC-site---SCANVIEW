<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter dados do formulário
    $id_aluno = $_POST['id_aluno'];  // Use o ID do aluno em vez do nome
    $computador = $_POST['computador'];
    $data_hora = date('Y-m-d H:i:s'); // Data e hora atuais

    // Inserir dados na tabela Computadores
    $sql = "INSERT INTO Computadores_Professores (ID_Aluno, Computador, Data_Hora) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $id_aluno, $computador, $data_hora); // Ajustado para refletir o tipo de dados correto

    if ($stmt->execute()) {
        echo "Registro inserido com sucesso!";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

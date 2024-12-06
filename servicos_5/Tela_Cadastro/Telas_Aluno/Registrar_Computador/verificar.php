<?php
// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "ScanView");

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter dados do formulário
$nome_aluno = $_POST['nome'];
$laboratorio = $_POST['laboratorio'];
$computador = $_POST['computador'];
$id_professor = $_POST['professor'];

// Verificar se o aluno existe pelo nome
$sql_aluno = "SELECT ID_Aluno FROM Alunos WHERE Nome_Completo = ?";
$stmt = $conn->prepare($sql_aluno);
$stmt->bind_param("s", $nome_aluno);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("<span style='color: white; font-weight: 400;'>Erro: Nome do aluno não encontrado.</span>");
}

$stmt->bind_result($id_aluno);
$stmt->fetch();

// Verificar se o ID do professor existe
$sql_professor = "SELECT ID_Prof FROM Professor WHERE ID_Prof = ?";
$stmt = $conn->prepare($sql_professor);
$stmt->bind_param("i", $id_professor);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("<span style='color: white; font-weight: 400;'>Erro: ID do professor não encontrado.</span>");
}

// Inserir dados na tabela Computadores_Professores
$sql = "INSERT INTO Computadores_Professores (Nome_Aluno, Laboratorio, Computador, ID_Aluno, ID_Professor) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $nome_aluno, $laboratorio, $computador, $id_aluno, $id_professor);

if ($stmt->execute()) {
    echo "<span style='color: white; font-weight: 400;'>Registro inserido com sucesso</span>";
} else {
    echo "<span style='color: white; font-weight: 400;'>Erro: " . $stmt->error . "</span>";
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>

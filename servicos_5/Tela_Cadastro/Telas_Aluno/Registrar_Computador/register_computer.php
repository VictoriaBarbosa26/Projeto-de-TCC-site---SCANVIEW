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
    $computador = $_POST['computador'];
    $id_aluno = isset($_POST['id_aluno']) ? intval($_POST['id_aluno']) : null;
    $id_professor = isset($_POST['id_professor']) ? intval($_POST['id_professor']) : null;
    $data_hora = date('Y-m-d H:i:s'); // Data e hora atuais

    // Inserir dados na tabela Computadores
    $sql = "INSERT INTO Computadores_Professores (ID_Aluno, ID_Professor, Computador, Data_Hora) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $id_aluno, $id_professor, $computador, $data_hora);

    if ($conn->query($sql_problema) === TRUE) {
        // Redireciona para uma página HTML de sucesso após inserir o problema
        header("Location: enviado/enviado.html");
        exit(); // Não esqueça do exit() após o header para garantir que o script pare aqui
    } else {
        echo "Erro: " . $sql_problema . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

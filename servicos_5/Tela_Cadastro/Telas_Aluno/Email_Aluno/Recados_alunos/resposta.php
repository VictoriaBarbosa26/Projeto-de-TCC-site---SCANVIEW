<?php
session_start();

// Verificar se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    echo "Você não está logado. Redirecionando para a página de login...";
    header("refresh:3;url=../../Login/Login.php");
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o ID da mensagem e a resposta do formulário
$mensagem_id = isset($_POST['mensagem_id']) ? (int)$_POST['mensagem_id'] : 0;
$resposta = isset($_POST['resposta']) ? $_POST['resposta'] : '';

// Verificar se a resposta foi preenchida
if ($mensagem_id > 0 && !empty($resposta)) {
    // Recuperar o ID do aluno logado
    $aluno_id = $_SESSION['aluno_id'];

    // Inserir a resposta no banco de dados
    $sql = "INSERT INTO respostas (mensagem_id, aluno_id, resposta, data_resposta) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }

    $stmt->bind_param("iis", $mensagem_id, $aluno_id, $resposta);
    if ($stmt->execute()) {
        // Sucesso! A resposta foi salva.
        header("Location: enviado/enviado.php"); // Redireciona para a tela de sucesso
        exit();
    } else {
        echo "Erro ao enviar a resposta.";
    }
} else {
    echo "Erro: ID da mensagem ou resposta inválidos.";
}

$stmt->close();
$conn->close();
?>

<?php
session_start();

// Verificar se o administrador está logado
if (!isset($_SESSION['adm_id'])) {
    echo "Você não está logado.";
    exit();
}

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter o ID da mensagem passada pela requisição
$mensagem_id = isset($_POST['mensagem_id']) ? (int)$_POST['mensagem_id'] : 0;

// Verificar se o ID da mensagem é válido
if ($mensagem_id > 0) {
    // Atualizar o status da mensagem para "lida"
    $sql = "UPDATE mensagens SET status = 'lida' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }

    $stmt->bind_param("i", $mensagem_id);
    if ($stmt->execute()) {
        echo "Mensagem marcada como lida.";
    } else {
        echo "Erro ao marcar como lida.";
    }

    $stmt->close();
} else {
    echo "ID inválido.";
}

$conn->close();
?>

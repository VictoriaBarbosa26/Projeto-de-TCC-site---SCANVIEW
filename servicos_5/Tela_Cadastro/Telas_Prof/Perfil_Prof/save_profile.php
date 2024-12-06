<?php
session_start();

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifique se a variável da sessão está definida
if (!isset($_SESSION['prof_id'])) {
    echo "Você não está logado.";
    exit();
}

// Recuperar o ID do professor logado
$prof_id = $_SESSION['prof_id'];

// Receber dados JSON da requisição
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $nome = trim($data['nome']);
    $email = trim($data['email']);
    $senha = trim($data['senha']);

    // Verificar se os campos não estão vazios
    if (empty($nome) || empty($email) || empty($senha)) {
        echo "Todos os campos são obrigatórios.";
        exit();
    }

    // Validar o e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "E-mail inválido.";
        exit();
    }

    // Opcional: Hash da senha para segurança
    // É altamente recomendado não armazenar senhas em texto puro.
    // Descomente a linha abaixo para usar hashing.
    // $senha = password_hash($senha, PASSWORD_DEFAULT);

    // Preparar a atualização
    $sql = "UPDATE Professor SET Nome_Completo = ?, Email = ?, Senha = ? WHERE ID_Prof = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Erro na preparação da consulta: " . $conn->error;
        exit();
    }
    $stmt->bind_param("sssi", $nome, $email, $senha, $prof_id);

    if ($stmt->execute()) {
        echo "Perfil atualizado com sucesso.";
    } else {
        echo "Erro ao atualizar perfil: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Nenhum dado recebido.";
}

// Fechar conexão
$conn->close();
?>

<?php
session_start();

// Verifica se o administrador está logado
if (!isset($_SESSION['adm_id'])) {
    header("Location: ../../../../../login/Login.php");
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";  
$username = "root";        
$password = "";            
$dbname = "ScanView";      

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Pega os dados do formulário
$data = $_POST['data']; 
$hora = $_POST['hora']; 
$laboratorio = $_POST['laboratorio']; 
$computador_numero = $_POST['computador_id']; // Número do computador enviado pelo formulário

// Exibe o valor do número do computador para depuração
echo "Número do Computador: " . $computador_numero;

// Verifica se o número do computador foi passado e é um número válido
if (empty($computador_numero) || !is_numeric($computador_numero)) {
    die("Erro: O campo 'Número do Computador' não pode ser vazio ou não numérico.");
}

// Converte o número do computador para inteiro para garantir que ele seja numérico
$computador_numero = intval($computador_numero);

// Passo 1: Busca o ID do computador usando o número do computador
$query = "SELECT ID_Computador FROM computadores WHERE Computador = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $computador_numero);  // 'i' para inteiro, já que 'Computador' é um número inteiro
$stmt->execute();
$stmt->bind_result($computador_id);
$stmt->fetch();

// Fechar o statement após pegar o resultado
$stmt->close(); // Importante fechar o statement após consumir o resultado

// Verifica se o computador existe e obtem o ID
if (empty($computador_id)) {
    die("Erro: O número do computador não existe na tabela 'computadores'.");
}

// Passo 2: Agora que temos o ID do computador, podemos continuar o processo de agendamento
$adm_id = $_SESSION['adm_id'];  
$status = 'pendente';  // O status inicial será 'pendente'

// Passo 2: Insere o agendamento no banco de dados, incluindo o 'ID_Computador'
$query = "INSERT INTO agendamentos (id_administrador, data, hora, laboratorio, status, ID_Computador) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssi", $adm_id, $data, $hora, $laboratorio, $status, $computador_id);
$stmt->execute();
$stmt->close();  // Fechar o statement após a execução

// Passo 3: Agendar a notificação para o administrador (um dia antes do agendamento)
$notification_date = date('Y-m-d H:i:s', strtotime("$data $hora -1 day"));

$notification_query = "INSERT INTO notificacao (remetente, destinatario, assunto, data_envio) VALUES (?, ?, ?, ?)";
$remetente = "Sistema";
$destinatario = "Administrador";
$assunto = "Agendamento de Manutenção";

$stmt = $conn->prepare($notification_query);
$stmt->bind_param("ssss", $remetente, $destinatario, $assunto, $notification_date);
$stmt->execute();
$stmt->close();  // Fechar o statement da notificação

// Passo 4: Atualiza o status do agendamento para 'enviado'
// A atualização não precisa passar o parâmetro 'status' pois já está hardcoded no SQL
$update_status_query = "UPDATE agendamentos SET status = 'enviado' WHERE id_administrador = ? AND data = ? AND hora = ? AND laboratorio = ? AND ID_Computador = ?";
$stmt = $conn->prepare($update_status_query);
$stmt->bind_param("isssi", $adm_id, $data, $hora, $laboratorio, $computador_id);  // Corrigido para 5 parâmetros
$stmt->execute();
$stmt->close();  // Fechar o statement após atualização

// Fechar a conexão
$conn->close();

// Redireciona para a página de sucesso
header("Location: enviado/enviado.php");
exit();
?>

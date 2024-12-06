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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se há IDs selecionados
    if (!empty($_POST['selected'])) {
        $ids = implode(",", $_POST['selected']);

        // Excluir registros selecionados
        $sql = "DELETE FROM Registro_Aulas WHERE id IN ($ids)";
        
        if ($conn->query($sql) === TRUE) {
            echo "Registros excluídos com sucesso!";
        } else {
            echo "Erro ao excluir registros: " . $conn->error;
        }
    }
}

// Redirecionar de volta à página de registros
header("Location: registroAulas.php");
exit;
?>

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

// Captura da data
$data = $_GET['data'] ?? '';

// Consulta para obter registros de aulas filtrados
$sql = "SELECT * FROM Registro_Aulas WHERE Data = '$data'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Aulas - Filtrar</title>
    <link rel="stylesheet" href="registroAulas/registroAulas.css">
</head>
<body>
    <h2>Registros de Aulas em <?= htmlspecialchars($data) ?></h2>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>Aula de: " . $row['Disciplina'] . " - " . $row['Data'] . "</li>";
            }
        } else {
            echo "<li>Nenhum registro encontrado para a data selecionada.</li>";
        }
        ?>
    </ul>
    <a href="registroAulas.php">Voltar</a>
</body>
</html>

<?php
$conn->close();
?>

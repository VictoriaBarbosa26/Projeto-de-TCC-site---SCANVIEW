<?php
// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "ScanView");

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber os problemas selecionados
    if (isset($_POST['problemas'])) {
        $problemas = $_POST['problemas'];
        foreach ($problemas as $problema) {
            // Inserir problemas na tabela de problemas (supondo que você tenha uma tabela de problemas)
            $sql = "INSERT INTO Problemas (Computador, Descricao) VALUES (?, 'Problema relatado')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $problema);
            $stmt->execute();
        }
        echo "<span style='color: white; font-weight: 400;'>Problemas relatados com sucesso!</span>";
    } else {
        echo "<span style='color: white; font-weight: 400;'>Nenhum problema selecionado.</span>";
    }
}

$conn->close();
?>

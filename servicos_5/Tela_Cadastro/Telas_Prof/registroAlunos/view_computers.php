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

// Recuperar dados da tabela Computadores_Professores e incluir o nome, curso, data e número do computador
$sql = "SELECT c.Computador, c.Data_Hora, a.Nome_Completo, a.Curso
        FROM Computadores_Professores c
        LEFT JOIN Alunos a ON c.ID_Aluno = a.ID_Aluno
        ORDER BY c.Data_Hora DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Exibir dados em uma tabela HTML
    while($row = $result->fetch_assoc()) {
        // Formatar a data para exibir apenas o dia
        $data_formatada = date('d/m/Y', strtotime($row['Data_Hora']));
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Computador']) . "</td>"; // Exibe o número do computador
        echo "<td>" . htmlspecialchars($row['Nome_Completo']) . "</td>"; // Exibe o nome do aluno
        echo "<td>" . htmlspecialchars($data_formatada) . "</td>"; // Exibe a data formatada (apenas o dia)
        echo "<td>" . htmlspecialchars($row['Curso']) . "</td>"; // Exibe o curso
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>Nenhum registro encontrado.</td></tr>";
}

$conn->close();
?>

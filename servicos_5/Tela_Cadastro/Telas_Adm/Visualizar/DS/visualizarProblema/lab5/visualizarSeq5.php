<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="visualizarSeq5.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');


    .voltar-img {
        width: 30px; /* Tamanho padrão */
        height: auto; /* Mantém a proporção da imagem */
        position: absolute; /* Posição fixa em relação à viewport */
        left: 30px; /* Distância fixa da esquerda */
        top: -920px; /* Ajusta a posição para 10px do topo */
        z-index: 1000; /* Garante que o botão fique acima de outros elementos */
    }

    </style>
</head>
<body>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../index.html">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="PERFIL.HTLM">
                        <img src="IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="EMAIL.HTML">
                        <img src="IMG/email.png" alt="Imagem 3">
                        <span class="dropdown-link">E-mail</span>
                    </a>
                </div>
            </div>
            <div class="Perfil-Momentaneo">
                <img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
            </div>
        </div>
        <span class="categorias">Visualizar Problemas</span>
    </nav>

    <div class="diagonal-gradient">
        <ul class="computer-list">
            <?php
                // Conexão com o banco de dados
                $conexao = new mysqli("localhost", "root", "", "ScanView");

                // Verifica se a conexão foi bem-sucedida
                if ($conexao->connect_error) {
                    die("Conexão falhou: " . $conexao->connect_error);
                }

                // Lê o curso da URL (se presente)
$curso = isset($_GET['curso']) ? $_GET['curso'] : null;

           
            if ($curso) {
                
                $sql = "SELECT c.Computador AS computador_numero, c.ID_Computador, a.Nome_Completo AS aluno_nome
                        FROM Problemas p 
                        JOIN Computadores c ON p.ID_Computador = c.ID_Computador
                        LEFT JOIN Alunos a ON c.ID_Aluno = a.ID_Aluno
                        WHERE c.Laboratorio = 5 
                        AND p.ID_Problema NOT IN (SELECT ID_Problema FROM Solucoes)
                        AND a.Curso = ?"; 

                // Prepara e executa a consulta com o curso
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param('s', $curso); 
                $stmt->execute();
                $resultado = $stmt->get_result();
            } else {
                // Caso o curso não esteja na URL, exibe todos os resultados
                $sql = "SELECT c.Computador AS computador_numero, c.ID_Computador, a.Nome_Completo AS aluno_nome
                        FROM Problemas p 
                        JOIN Computadores c ON p.ID_Computador = c.ID_Computador
                        LEFT JOIN Alunos a ON c.ID_Aluno = a.ID_Aluno
                        WHERE c.Laboratorio = 5 
                        AND p.ID_Problema NOT IN (SELECT ID_Problema FROM Solucoes)";
                
                $resultado = $conexao->query($sql);
            }

                // Verifica se há resultados
                if ($resultado->num_rows > 0) {
                    // Saída de cada linha
                    while ($row = $resultado->fetch_assoc()) {
                        echo '<li class="computer-item">';
                        echo '<div class="computer-number">' . htmlspecialchars($row['computador_numero']) . '</div>';
                        echo '<span>' . htmlspecialchars($row['aluno_nome'] ?? 'Desconhecido') . '</span>'; // Nome do aluno ou 'Desconhecido'
                        echo '<a href="Finalizacao5/finalizacao5.php?id_computador=' . $row['ID_Computador'] . '" class="view-icon">&#128065;</a>';
                        echo '</li>';
                    }
                } else {
                    echo '<li class="computer-item">Nenhum problema relatado.</li>';
                }

                // Fecha a conexão
                $conexao->close();
            ?>
        </ul>
    </div>

    <!-- Link "Voltar" -->
    <a href="../visualisarProblema.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>

    <script src="script.js"></script>
</body>
</html>

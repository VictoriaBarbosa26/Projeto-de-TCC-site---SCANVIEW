<?php
session_start();

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter o curso da URL (se existir)
$curso = isset($_GET['curso']) ? $_GET['curso'] : '';

// Definir o laboratório para filtragem (exemplo: 1)
$laboratorio = 1;

// Consulta para obter a lista de manutenções com filtro de curso e laboratório
$computadores = [];
$computadores_query = "
    SELECT s.ID_Solucao, s.DataResolucao, c.Computador AS computador_numero, a.status, al.Curso AS curso_aluno
    FROM Solucoes s
    JOIN Problemas p ON s.ID_Problema = p.ID_Problema
    JOIN Computadores c ON p.ID_Computador = c.ID_Computador
    LEFT JOIN agendamentos a ON a.ID_Computador = c.ID_Computador
    LEFT JOIN Alunos al ON c.ID_Aluno = al.ID_Aluno
    WHERE c.Laboratorio = ?  -- Filtrando por laboratório
"; 

// Adicionar filtro para o curso, caso seja fornecido
if ($curso) {
    $computadores_query .= " AND al.Curso = ?";  // Filtrando por curso (se presente)
}

$stmt = $conn->prepare($computadores_query);

// Se o curso foi especificado, vincular os parâmetros
if ($curso) {
    $stmt->bind_param('is', $laboratorio, $curso);  // 'i' para laboratório (int), 's' para curso (string)
} else {
    $stmt->bind_param('i', $laboratorio);  // Apenas o laboratório
}

// Executar a consulta
$stmt->execute();
$result = $stmt->get_result();

// Processar os resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Verificar se o status não é 'enviado' na tabela agendamentos
        if ($row['status'] !== 'enviado') {
            $computadores[] = [
                "computador" => $row['computador_numero'],
                "data" => $row['DataResolucao'],
                "curso" => $row['curso_aluno']
            ];
        }
    }
}

// Verifique se a variável da sessão está definida
if (!isset($_SESSION['adm_id'])) {
    echo "Você não está logado. Redirecionando para a página de login...";
    header("refresh:3;url=../../Login/Login.php"); // Redireciona após 3 segundos
    exit();
}

// Recuperar os dados do administrador logado
$adm_id = $_SESSION['adm_id'];
$sql = "SELECT Nome_Completo, Email, Senha FROM Administradores WHERE ID_Adm = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adm_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $adm = $result->fetch_assoc();
} else {
    echo "Administrador não encontrado.";
    exit();
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="AgendamentoC3.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins em Negrito*/
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins normal*/
    	/* Ícone de perfil e informações */
		.Perfil-Momentaneo .info .nome {
            position: absolute;
			font-size: 16px;
			font-weight: bold;
            left: -2px;
            top: -20px;
		}

		.Perfil-Momentaneo .info .email {
            position: absolute;
			font-size: 14px;
			color: #ccc;
            left: -2px;
            top: 5px;
		}
        
        .Perfil-Momentaneo img {
            position: absolute;
            width: 80px;
            height: auto;
            border-radius: 50%;
            object-fit: cover;
            left: -100px;
            top: -40px;
        }
        /* Estilos para o texto "Categorias" */
        .categorias {
            color: #2EDBD3; /* Cor do texto */
            font-size: 25px; /* Tamanho da fonte */
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: normal;
            position: absolute;
            top: 50%;
            left: 750px;
            transform: translate(-50%, -50%); /* Centraliza horizontal e verticalmente */
            white-space: nowrap; /* Evita que o texto quebre em linhas */
        }
        .voltar-img {
            width: 30px; /* Tamanho inicial para telas grandes */
            height: auto; /* Mantém a proporção da imagem */
            position: absolute; /* Posicionamento absoluto */
            left: 30px; /* Distância fixa da esquerda */
            top: -920px; /* Posição inicial conforme solicitado */
            transform: translateY(-50%); /* Centraliza verticalmente */
        }
        .computer-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 35px;
            font-size: large;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../../index.php">
                        <img src="../../IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../../../Perfil_Adm/perfil_adm.php">
                        <img src="../../IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../../../Email_Adm/email_adm.php">
                        <img src="../../IMG/email.png" alt="Imagem 3">
                        <span class="dropdown-link">E-mail</span>
                    </a>
                </div>
            </div>
            </div>
			<div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($adm['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($adm['Email']); ?></div>
			</div>

        <span class="categorias">Agendamento de Manutenção</span>
    </nav>
    <div class="diagonal-gradient">
            <ul class="computer-list">
            <?php if (empty($computadores)): ?> <!-- Verifica se o array está vazio -->
                <li class="computer-item">Nenhum agendamento.</li> <!-- Exibe a mensagem -->
            <?php else: ?>
                <?php foreach ($computadores as $computador): ?>
                    <li class="computer-item">
                        <div class="computer-number"><?php echo htmlspecialchars($computador['computador']); ?></div>
                        <span>Última manutenção: <?php echo date('d/m/Y', strtotime($computador['data'])); ?></span>
                        <a href="FinalAgenda3/finalAgenda3.php?computador=<?php echo urlencode($computador['computador']); ?>" class="view-icon">&#128065;</a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <a href="../agendamento.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>
    
    <script src="script.js"></script>
</body>
</html>

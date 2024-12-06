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

// Obter o número do computador da URL
$computador_numero = isset($_GET['computador']) ? $_GET['computador'] : '';

// Consulta para obter o ID do problema com base no número do computador
$problema_query = "
    SELECT p.ID_Problema
    FROM Problemas p
    JOIN Computadores c ON p.ID_Computador = c.ID_Computador
    WHERE c.Computador = ?
";

$stmt = $conn->prepare($problema_query);
$stmt->bind_param("s", $computador_numero);
$stmt->execute();
$result = $stmt->get_result();

$id_problema = '';
if ($row = $result->fetch_assoc()) {
    $id_problema = $row['ID_Problema'];
}

// Consulta para obter a descrição da solução com base no ID do problema
$solucao_query = "
    SELECT s.Descricao, s.DataResolucao
    FROM Solucoes s
    WHERE s.ID_Problema = ?
";

$stmt = $conn->prepare($solucao_query);
$stmt->bind_param("i", $id_problema);
$stmt->execute();
$result = $stmt->get_result();

$descricao = '';
$data_resolucao = '';

if ($row = $result->fetch_assoc()) {
    $descricao = $row['Descricao'];
    $data_resolucao = $row['DataResolucao'];
} else {
    error_log("Nenhuma solução encontrada para o ID do problema: " . $id_problema);
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

$stmt->close();
$conn->close();

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="finalAgenda5.css">
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
    .resolve-button {
    width: 350px; /* Largura fixa */
    background-color: #66CAFF;
    font-family: 'Poppins', sans-serif;
    border: none;
    padding: 15px 30px;
    font-size: larger;
    cursor: pointer;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
    color: #000000;
    position: absolute; /* Para usar left */
    left: 50%; /* Centraliza o botão em relação ao container */
    transform: translateX(-50%); /* Move o botão para a esquerda para centralizar */
    margin-top: 50px; /* Margem superior */
    }

    .resolve-button:hover {
    background-color: #003185;
    }
    
    .maintenance-details {
        margin-top: 20px;
        font-size: 20px;
        color: white;
    }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../../../index.html">
                        <img src="../../IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../../../../Perfil_Adm/perfil_adm.php">
                        <img src="../../IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../../../../Email_Adm/email_adm.php">
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
        <span class="categorias">Agendamento de manutenção</span>
    </nav>
    
    <div class="diagonal-gradient">
        <div class="content">
            <h2>Computador: <?php echo htmlspecialchars($computador_numero); ?></h2>
            <div class="computer-details">
                <div class="maintenance-details">
                    <p><?php echo htmlspecialchars($descricao); ?></p>
                </div>
            </div>
            <div class="button-container">
                <a href="../agendaNova2/AgendaManutencao2.php?computador=<?php echo urlencode($computador_numero); ?>"> 
                    <button class="resolve-button">Agendar nova manutenção</button>
                </a>
            </div>
        </div>
    </div>

    <a href="../AgendamentoC5.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>
    
    <script src="script.js"></script>
</body>
</html>

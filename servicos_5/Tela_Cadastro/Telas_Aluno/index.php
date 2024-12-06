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
if (!isset($_SESSION['aluno_id'])) {
    echo "Você não está logado. Redirecionando para a página de login...";
    header("refresh:3;url=../../Login/Login.php"); // Redireciona após 3 segundos
    exit();
}

// Recuperar os dados do aluno logado
$aluno_id = $_SESSION['aluno_id'];
$sql = "SELECT Nome_Completo, Email, Senha FROM Alunos WHERE ID_Aluno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $aluno = $result->fetch_assoc();
} else {
    echo "Aluno não encontrado.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Define o conjunto de caracteres como UTF-8 -->
    <meta charset="UTF-8">

    <!-- Configura a visualização da página para dispositivos móveis -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Define o título da página -->
    <title>ScanView</title>

    <!-- Vincula o arquivo CSS externo "index.css" ao documento HTML -->
    <link rel="stylesheet" href="index.css">

    <!-- Importação da fonte do Google -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /* Fonte Poppins em Negrito */
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /* Fonte Poppins normal */
    
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
	</style>
</head>

<body>
    <!-- Barra de navegação -->
    <nav class="navbar">

        <!-- Dropdown -->
        <div class="dropdown">

            <!-- Botão do Dropdown -->
            <button class="dropbtn">☰</button>

            <!-- Conteúdo do Dropdown -->
            <div class="dropdown-content">

                <!-- Item do Dropdown - Início -->
                <div class="dropdown-item">
                    <a href="index.php" style="text-decoration: none;">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>

                <!-- Item do Dropdown - Perfil -->
                <div class="dropdown-item">
                    <a href="Perfil_Aluno/perfil_aluno.php" style="text-decoration: none;">
                        <img src="IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>

                <!-- Item do Dropdown - E-mail -->
                <div class="dropdown-item">
                    <a href="Email_Aluno/Email_aluno.php" style="text-decoration: none;">
                        <img src="IMG/email.png" alt="Imagem 3">
                        <span class="dropdown-link">E-mail</span>
                    </a>
                </div>
            </div>
			</div>
			<div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($aluno['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($aluno['Email']); ?></div>
				</div>


        <!-- Texto fixo "Categorias" -->
        <span class="categorias">Categorias</span>
    </nav>

    <!-- Gradiente de Fundo -->
    <div class="diagonal-gradient"></div>
	
	<!-- Container principal -->
	<div class="container">

		<!-- Caixa de conteúdo - Relatar Problemas -->
		<div class="caixinha-container">
			<a href="./Relatar_Problema/relatar.php" style="text-decoration: none;">
				<div class="caixinha">
					<img src="IMG/relatar.png" alt="Descrição da imagem">
				</div>
				<span class="mensagem">Relatar Problemas</span>
			</a>
		</div>
		
		<!-- Caixa de conteúdo - Registrar Computador -->
		<div class="caixinha-container">
			<a href="./Registrar_Computador/registro.php" style="text-decoration: none;">
				<div class="caixinha">
					<img src="IMG/registroPC.jpg" alt="Descrição da imagem">
				</div>
				<span class="mensagem">Registrar Computador</span>
			</a>
		</div>
	</div>
	
	<!-- Vincula o arquivo JavaScript externo "index.js" ao documento HTML -->
    <script src="index.js"></script>
</body>
</html>
<?php
$servername = "localhost";
$username = "root"; // Altere conforme necessário
$password = ""; // Altere conforme necessário
$dbname = "ScanView";


$conn = new mysqli($servername, $username, $password, $dbname);


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
    echo "Você não está logado. Redirecionando para a página de login...";
    header("refresh:3;url=../../Login/Login.php"); // Redireciona após 3 segundos
    exit();
}

// Recuperar os dados do professor logado
$prof_id = $_SESSION['prof_id'];
$sql = "SELECT Nome_Completo, Email, Senha FROM Professor WHERE ID_Prof = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $prof = $result->fetch_assoc();
} else {
    echo "Professor não encontrado.";
    exit();
}


if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Consulta para obter os horários dos administradores junto com os nomes
$sql = "SELECT h.*, a.Nome_Completo FROM horarios_adm h
        JOIN Administradores a ON h.adm_id = a.ID_Adm";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- DEFINE O CONJUNTO DE CARACTERES COMO UTF-8 -->
    <meta charset="UTF-8">
    <!-- CONFIGURA A VISUALIZAÇÃO DA PÁGINA PARA DISPOSITIVOS MÓVEIS -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DEFINE O TÍTULO DA PÁGINA -->
    <title>ScanView</title>
    <!-- VINCULA O ARQUIVO CSS EXTERNO "INDEX.CSS" AO DOCUMENTO HTML -->
    <link rel="stylesheet" href="index.css">
    <!-- IMPORTAÇÃO DA FONTE DO GOOGLE -->
    <style>
        /* Importa a fonte "Poppins" do Google Fonts, com variações de peso e estilo */
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

    </style>
</head>
<body>
    <!-- Cria a barra de navegação -->
    <nav class="navbar">
        <!-- Dropdown -->
        <div class="dropdown">
            <!-- Botão do Dropdown -->
            <button class="dropbtn">☰</button>
            <!-- Conteúdo do Dropdown -->
            <div class="dropdown-content">
                <!-- Item do Dropdown - Início -->
                <div class="dropdown-item">
                    <a href="index.html">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <!-- Item do Dropdown - Perfil -->
                <div class="dropdown-item">
                    <a href="Perfil_Prof/perfil_prof.php">
                        <img src="IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <!-- Item do Dropdown - E-mail -->
                <div class="dropdown-item">
                    <a href="EMAIL.HTML">
                        <img src="IMG/email.png" alt="Imagem 3">
                        <span class="dropdown-link">E-mail</span>
                    </a>
                </div>
            </div>
        </div>
        
            <!-- Imagem do perfil momentâneo -->
            <div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($prof['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($prof['Email']); ?></div>
			</div>

        <!-- Adicionando o elemento span para "Categorias" -->
        <span class="categorias">Categorias</span>
    </nav>
    <!-- Gradiente de Fundo -->
    <div class="diagonal-gradient"></div>
	
	<!-- Container principal -->
	<div class="container">
		<!-- Primeiro bloco - Relatar Problemas -->
		<div class="caixinha-container">
			<a href="./Relatar_Problema/relatar.php" style="text-decoration: none;">
				<div class="caixinha">
					<img src="IMG/relatar.png" alt="Descrição da imagem">
				</div>
				<span class="mensagem">Relatar Problemas</span>
			</a>
		</div>
		
		<!-- Segundo bloco - Registro de Aulas -->
		<div class="caixinha-container">
			<a href="registroAulas/registrarAulas.php" style="text-decoration: none;">
				<div class="caixinha">
					<img src="IMG/registroPC.jpg" alt="Descrição da imagem">
				</div>
				<span class="mensagem">Registro de Aulas</span>
			</a>
		</div>

        <!-- Terceiro bloco - Registros dos Alunos -->
        <div class="caixinha-container">
			<a href="registroAlunos/registroAlunos.php" style="text-decoration: none;">
				<div class="caixinha">
					<img src="IMG/registroAluno.png" alt="Descrição da imagem">
				</div>
				<span class="mensagem">Registros dos Alunos </span>
			</a>
		</div>

        <!-- Quarto bloco - Horário dos administradores -->
        <div class="caixinha-container">
			<a href="horarioAdm/horarioADM.php" style="text-decoration: none;">
				<div class="caixinha">
					<img src="IMG/horarioADM.png" alt="Descrição da imagem">
				</div>
				<span class="mensagem">Horário dos administradores</span>
			</a>
		</div>
	</div>
	


	<!-- VINCULA O ARQUIVO JAVASCRIPT EXTERNO "SCRIPT.JS" AO DOCUMENTO HTML -->
    <script src="index.js"></script>
</body>
</html>

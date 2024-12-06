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
<!DOCTYPE html> <!-- Declaração do tipo de documento -->
<html lang="pt-br"> <!-- Abertura da tag HTML com atributo de idioma definido para Português Brasileiro -->
<head>
    <!-- DEFINE O CONJUNTO DE CARACTERES COMO UTF-8 -->
    <meta charset="UTF-8"> 

    <!-- CONFIGURA A VISUALIZAÇÃO DA PÁGINA PARA DISPOSITIVOS MÓVEIS -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 

    <!-- DEFINE O TÍTULO DA PÁGINA -->
    <title>ScanView</title> 

    <!-- VINCULA O ARQUIVO CSS EXTERNO "ENVIADO.CSS" AO DOCUMENTO HTML -->
    <link rel="stylesheet" href="enviado.css"> 

    <!-- IMPORTAÇÃO DA FONTE DO GOOGLE -->
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

    </style>
</head>

<body>
    <nav class="navbar">
        <!-- Dropdown -->
        <div class="dropdown">

            <!-- Botão do Dropdown -->
            <button class="dropbtn">☰</button>

            <!-- Conteúdo do Dropdown -->
            <div class="dropdown-content">

                <!-- Item do Dropdown - Início -->
                <div class="dropdown-item">
                    <a href="../../../../../index.php">
                        <img src="IMG/inicio.png" alt="Imagem 1"> <!-- Imagem do item do dropdown -->
                        <span class="dropdown-link">Início</span> <!-- Texto descritivo do item do dropdown -->
                    </a>
                </div>

                <!-- Item do Dropdown - Perfil -->
                <div class="dropdown-item">
                    <a href="../../../../../Perfil_Adm/perfil_adm.php">
                        <img src="IMG/perfil.png" alt="Imagem 2"> <!-- Imagem do item do dropdown -->
                        <span class="dropdown-link">Perfil</span> <!-- Texto descritivo do item do dropdown -->
                    </a>
                </div>

                <!-- Item do Dropdown - E-mail -->
                <div class="dropdown-item">
                    <a href="../../../../../Email_Adm/email_adm.php">
                        <img src="IMG/email.png" alt="Imagem 3"> <!-- Imagem do item do dropdown -->
                        <span class="dropdown-link">E-mail</span> <!-- Texto descritivo do item do dropdown -->
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


        <!-- Adicionando o elemento span para "Categorias" -->
        <span class="categorias">Agendamento de Manutenção</span> 
    </nav>

    <!-- Gradiente de Fundo -->
    <div class="diagonal-gradient"></div> 

    <!-- Link para retornar à página de registro -->
    <a href="../../agendamentoC2.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img"> 
    </a>

    <!-- Imagem de sucesso -->
    <img src="IMG/aprovado.png" class="enviado-img"> <!-- Imagem exibindo sucesso -->
    <span class="mensagem">Manutenção Agendada com sucesso!<br>
    1 dia antes de realizá-la você será notificado através do seu inbox.</span></br>

    <!-- VINCULA O ARQUIVO JAVASCRIPT EXTERNO "ENVIADO.JS" AO DOCUMENTO HTML -->
    <script src="enviado.js"></script> 
</body>
</html>

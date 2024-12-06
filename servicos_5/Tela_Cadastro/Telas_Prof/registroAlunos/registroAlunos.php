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

$stmt->close();
$conn->close();
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
    <link rel="stylesheet" href="registroAlunos.css">
    <!-- IMPORTAÇÃO DA FONTE DO GOOGLE -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins em Negrito*/
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins normal*/
    
        /* Estilos para a tabela de alunos */
        .alunos-table {
            width: 700px; /* Define a largura da tabela */
            margin-left: 32%;
            margin-top: 60px; /* Adiciona margem superior de 60px */
            border-collapse: collapse; /* Colapsa as bordas da tabela */
            background-color: #ffffff; /* Define a cor de fundo da tabela */
            border-radius: 45px; /* Adiciona bordas arredondadas */
            overflow: hidden; /* Esconde o overflow */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Adiciona uma sombra */
        }

        /* Estilos para as células da tabela */
        .alunos-table th,
        .alunos-table td {
            padding: 25px; /* Adiciona preenchimento interno de 12px */
            text-align: center; /* Centraliza o texto */
        }

        /* Estilos para os cabeçalhos da tabela */
        .alunos-table th {
            background-color: #003185; /* Define a cor de fundo dos cabeçalhos */
            font-weight: bold; /* Define o peso da fonte como negrito */
            font-size: 18px; /* Define o tamanho da fonte */
            color: white; /* Define a cor do texto */
        }

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
            left: 780px;
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
                    <a href="../index.php">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <!-- Item do Dropdown - Perfil -->
                <div class="dropdown-item">
                    <a href="../Perfil_Prof/perfil_prof.php">
                        <img src="IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <!-- Item do Dropdown - E-mail -->
                <div class="dropdown-item">
                    <a href="../Email_prof/email_prof.php">
                        <img src="IMG/email.png" alt="Imagem 3">
                        <span class="dropdown-link">E-mail</span>
                    </a>
                </div>
            </div>
        </div>
            <div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($prof['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($prof['Email']); ?></div>
			</div>

        <!-- Adicionando o elemento span para "Categorias" -->
        <span class="categorias">Registro de Alunos</span>
    </nav>
    <!-- Gradiente de Fundo -->
    <div class="diagonal-gradient"></div> 

        <a href="../index.php" class="voltar-link">
            <img src="IMG/voltar.png" class="voltar-img">
        </a>

        <div class="container">
        <table class="alunos-table">
            <thead>
                <tr>
                    <th>Nº do Computador</th>
                    <th>Nomes dos Alunos</th>
                    <th>Data</th>
                    <th>Curso</th>
                </tr>
            </thead>
            <tbody>
                <!-- As linhas serão preenchidas pelo PHP -->
                <?php include 'view_computers.php'; ?>
            </tbody>
        </table>
    </div>

    <script src="registroAlunos.js"></script>
</body>
</html>

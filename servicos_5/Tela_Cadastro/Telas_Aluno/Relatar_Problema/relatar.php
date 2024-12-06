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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="relatar.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins em Negrito*/
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins normal*/
    
    .custom-button {
        position: absolute; /* posicionamento absoluto */
        background-color: #66CAFF;
        color: rgb(0, 0, 0);
        padding: 5px 50px;
        border: none;
        border-radius: 10px;
        font-size: 10px;
        cursor: pointer;
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-style: normal;
        font-size: 20px;
        text-align: center;
        top: 105%; /* coloca a caixa no centro vertical */
        left: 44%; /* coloca a caixa no centro horizontal */
        transition: background-color 0.3s ease;
    }

    .custom-button:hover {
        background-color: #003185;
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
        left: -820px;
        transform: translate(-50%, -50%); /* Centraliza horizontal e verticalmente */
        white-space: nowrap; /* Evita que o texto quebre em linhas */
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../index.php"><img src="IMG/inicio.png" alt="Início"><span class="dropdown-link">Início</span></a>
                </div>
                <div class="dropdown-item">
                    <a href="../Perfil_Aluno/perfil_aluno.php"><img src="IMG/perfil.png" alt="Perfil"><span class="dropdown-link">Perfil</span></a>
                </div>
                <div class="dropdown-item">
                    <a href="../Email_Aluno/Email_aluno.php"><img src="IMG/email.png" alt="E-mail"><span class="dropdown-link">E-mail</span></a>
                </div>
            </div>
            <span class="categorias">Relatar Problemas</span>
        </div>
			<div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($aluno['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($aluno['Email']); ?></div>
				</div>
    </nav>
    <div class="diagonal-gradient"></div>

    <!-- Formulário de Problemas -->
    <form action="processa_problema.php" method="POST">
        <div class="input-container">
            <div class="input-group">
                <img src="IMG/perfilamarelo.png" alt="User Icon" class="input-icon">
                <input type="text" name="nome" placeholder="Nome" required>
            </div>
            <div class="input-group">
                <img src="IMG/lab.png" alt="Building Icon" class="input-icon">
                <input type="text" name="laboratorio" placeholder="Laboratório" required>
            </div>
            <div class="input-group">
                <img src="IMG/pc.png" alt="Laptop Icon" class="input-icon">
                <input type="text" name="computador" placeholder="Computador Usado" required>
            </div>
        </div>

        <!-- Checkboxes Dinâmicos -->
        <div class="flexbox-background">
            <div class="checkbox-container">
                <?php
                    // Exemplo de itens do problema. Substituir com consulta ao banco de dados.
                    $itens_problema = ["Monitor não liga", "Teclado não funciona", "Mouse desconectado", "Problema de rede", "Outros"];
                    foreach ($itens_problema as $index => $item) {
                        echo '<div class="checkbox-item">';
                        echo '<input type="checkbox" name="problema[]" id="item' . $index . '" value="' . $item . '">';
                        echo '<label for="item' . $index . '" class="checkbox-label">' . $item . '</label>';
                        echo '</div>';
                    }
                ?>
            </div>
        </div>

        <!-- Botão de Enviar -->
        <button type="submit" class="custom-button">Enviar</button>
    </form>

    <!-- Voltar -->
    <a href="../index.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>

    <script src="relatar.js"></script>
</body>
</html>

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


// Captura o número do computador, se existir na URL
$computador_numero = isset($_GET['computador']) ? $_GET['computador'] : '';

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tela de Agendamento</title>
    <link rel="stylesheet" href="AgendaManutencao2.css">
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
            width: 30px;
            height: auto;
            position: absolute;
            left: 30px;
            top: -230px;
            transform: translateY(-50%);
        }

        .container {
            background: linear-gradient(to bottom right, rgb(0, 0, 0), #003185);
            padding: 40px;
            border-radius: 10px;
            border: 2px solid #FFFFFF;
            text-align: center;
            width: 90%;
            max-width: 600px;
            margin: -650px auto;
        }

        .mini-container {
            width: 50px;
            height: 50px;
            background-color: #FFFFFF;
            text-align: center;
            font-size: 30px;
            color: #000000;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 210px auto 0;
        }

        .computador {
            color: #ffde59;
            text-align: center;
            font-size: 20px;
            position: relative;
            top: 180px;
            left: 50%;
            transform: translateX(-50%);
            width: fit-content;
            z-index: 10;
        }

        button {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #66CAFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        buttonEnviar:hover {
            background-color: #003185;
        }

    </style>
</head>
<body>
<nav class="navbar">
        <div class="dropdown">
            <buttonDrop class="dropbtn">☰</buttonDrop>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../../../index.php">
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

    <div class="diagonal-gradient"></div>

    <div class="container">
        <form action="processa_agendamento.php" method="POST">
            <!-- Passando o computador_id para o form -->
            <input type="hidden" name="computador_id" value="<?php echo htmlspecialchars($computador_numero); ?>">

            <div class="options">
                <div class="option">
                    <label for="data">
                        <img src="IMG/agenda.png" alt="Ícone da agenda"> Data
                    </label>
                    <input type="date" id="data" name="data" required>
                </div>
                <div class="option">
                    <label for="laboratorio">
                        <img src="IMG/porta.png" alt="Ícone de porta"> Laboratório
                    </label>
                    <input type="text" id="laboratorio" name="laboratorio" placeholder="Digite o laboratório" required>
                </div>
                <div class="option">
                    <label for="hora">
                        <img src="IMG/relogio.png" alt="Ícone de relógio"> Hora
                    </label>
                    <input type="time" id="hora" name="hora" required>
                </div>
            </div>
            <button type="submit">Enviar</button>
        </form>
    </div>

    <div class="computador">
        Computador
    </div>

    <div class="mini-container">
        <?php echo htmlspecialchars($computador_numero); ?>
    </div>

    <a href="../FinalAgenda2/finalAgenda2.php?computador=<?php echo urlencode($computador_numero); ?>" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>
    <script src="script.js"></script>
</body>
</html>

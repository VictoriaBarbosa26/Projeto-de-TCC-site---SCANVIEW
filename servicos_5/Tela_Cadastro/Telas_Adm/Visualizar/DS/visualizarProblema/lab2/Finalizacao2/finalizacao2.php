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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="finalizacao2.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap">
    <style>
        .flexbox-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;  /* Alinha ao topo da tela */
            position: absolute;
            top: 10px;  /* Ajuste o valor de "top" para subir a caixa */
            left: 150px;
            width: 100vw;
            height: 100vh;
            z-index: 10;
            display: none; 
            transform: translateX(-50%); /* Para centralizar o elemento horizontalmente */
        }

        .flexbox-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }


        h2 {
            font-family: 'Poppins', sans-serif;
            margin-bottom: 15px;
        }

        textarea {
            width: 90%;
            height: 100px;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            resize: none;
        }

        .button-container {
            display: flex;
            justify-content: space-around;
        }

        .custom-button {
            background-color: #66CAFF;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .custom-button:hover {
            background-color: #003185;
        }


        .voltar-img {
            width: 30px; /* Tamanho inicial para telas grandes */
            height: auto; /* Mantém a proporção da imagem */
            position: absolute; /* Posicionamento absoluto */
            left: 30px; /* Distância fixa da esquerda */
            top: 70px; /* Posição inicial conforme solicitado */
            transform: translateY(-50%); /* Centraliza verticalmente */
        }
    
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
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../../index.php">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="PERFIL.HTML">
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
        </div>
			<div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($adm['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($adm['Email']); ?></div>
			</div>
        <span class="categorias">Visualizar Problemas</span>
    </nav>
    
    <a href="../visualizarSeq2.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>
    <div class="diagonal-gradient">
        <div class="content">
            <form action="resolver_problemas.php" method="POST">
                <div class="problem-list">
                    <?php
                    // Conexão com o banco de dados
                    $conexao = new mysqli("localhost", "root", "", "ScanView");

                    if ($conexao->connect_error) {
                        die("Conexão falhou: " . $conexao->connect_error);
                    }

                    // Obtém o ID do computador da URL
                    $id_computador = isset($_GET['id_computador']) ? intval($_GET['id_computador']) : 0;

                    // Consulta para obter os problemas associados ao computador
                    $sql = "SELECT p.ID_Problema, p.Descricao 
                            FROM Problemas p
                            WHERE p.ID_Computador = ?";

                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param("i", $id_computador);
                    $stmt->execute();
                    $resultado = $stmt->get_result();

                    // Exibição dos problemas
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo '<div class="problem-item">';
                            echo '<input type="checkbox" id="problem' . $row['ID_Problema'] . '" name="problemas[]" value="' . $row['ID_Problema'] . '">';
                            echo '<label for="problem' . $row['ID_Problema'] . '">' . htmlspecialchars($row['Descricao']) . '</label>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Nenhum problema relatado.</p>';
                    }

                    // Fecha a conexão
                    $conexao->close();
                    ?>
                </div>
            </form>

            <!-- Novo botão para exibir o flexbox -->
            <button id="toggleFlexboxButton" class="custom-button">Problema Resolvido</button>
            
            <div id="flexbox" class="flexbox-container">
                <div class="flexbox-content">
                    <h2>Descrição da Manutenção</h2>
                    <textarea id="problemDescription" placeholder="Descreva o que foi feito na manutenção aqui..."></textarea>
                    <input type="date" id="dataResolucao" required> <!-- Novo campo para a data -->
                    <div id="selectedProblems"></div> <!-- Aqui será exibido os problemas selecionados -->
                    <div class="button-container">
                        <button id="sendButton" class="custom-button">Enviar</button>
                        <button id="cancelButton" class="custom-button">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="script.js"></script> <!-- Inclui o JavaScript externo -->

</body>
</html>

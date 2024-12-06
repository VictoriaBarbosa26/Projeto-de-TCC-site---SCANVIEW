<?php
session_start();

// Verificar se o administrador está logado
if (!isset($_SESSION['adm_id'])) {
    echo "Você não está logado. Redirecionando para a página de login...";
    header("refresh:3;url=../../Login/Login.php");
    exit();
}

// Definir a conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter o ID da mensagem passada pela URL
$mensagem_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar se o ID foi passado
if ($mensagem_id > 0) {
    // Consultar a mensagem específica pelo ID
    $sql = "SELECT * FROM mensagens WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $conn->error);
    }

    $stmt->bind_param("i", $mensagem_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $mensagem = $result->fetch_assoc();
    } else {
        echo "Mensagem não encontrada.";
        exit();
    }
} else {
    echo "ID inválido.";
    exit();
}

// Recuperar os dados do administrador logado
$adm_id = $_SESSION['adm_id'];
$sql = "SELECT Nome_Completo, Email FROM Administradores WHERE ID_Adm = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Erro ao preparar a consulta: ' . $conn->error);
}

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
?>








<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Mensagem</title>
    <link rel="stylesheet" href="recados.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /* Fonte Poppins em Negrito */
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /* Fonte Poppins normal */
    
        .mensagem-container {
            display: flex;
            flex-direction: column;
            justify-content: center;   
            align-items: center;      
            height: 100vh;             
            width: 70%;              
            position: relative;        
            padding: 20px;             
            box-sizing: border-box;    
            top: -10px;
            left: 300px;
        }

        .mensagem {
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
            width: 50%;
            text-align: left; /* Alinhando o texto à esquerda */
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Alinhando os itens ao topo */
            margin-bottom: 200px; /* Espaçamento entre a mensagem e a área de resposta */
            }

        .responder {
            width: 50%; /* Mantém a largura do formulário igual ao da mensagem */
            text-align: center;
        }

		/* Ícone de perfil e informações */
		.Perfil-Momentaneo .info .nome {
            position: absolute;
			font-size: 16px;
			font-weight: bold;
            left: -570px;
            top: -20px;
		}

		.Perfil-Momentaneo .info .email {
            position: absolute;
			font-size: 14px;
			color: #ccc;
            left: -570px;
            top: 5px;
		}
        
        .Perfil-Momentaneo img {
            position: absolute;
            width: 80px;
            height: auto;
            border-radius: 50%;
            object-fit: cover;
            left: -660px;
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
            left: 930px;
            transform: translate(-50%, -50%); /* Centraliza horizontal e verticalmente */
            white-space: nowrap; /* Evita que o texto quebre em linhas */
        }

        .email-img {
            position: absolute;
            width: 40px;
            height: auto;
            object-fit: cover;
            left: 590px;
            top: 370px;
        }
        

        .responder-container {
            display: none; /* Inicialmente escondido */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Centraliza na tela */
            background-color: rgba(0, 0, 0, 0.8); /* Fundo semi-transparente */
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
            width: 60%; /* Largura do container */
            max-width: 500px; /* Tamanho máximo */
            text-align: left;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .responder-link {
            cursor: pointer;
            color: #2EDBD3; /* Cor de destaque */
            font-size: 18px;
            font-weight: bold;
        }

        .responder-container .destinatario {
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .responder-container label {
            font-size: 14px;
            font-weight: normal;
        }

        .responder-container textarea {
            width: 90%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: none;
        }

        .responder-container button {
            padding: 10px 20px;
            background-color: #2EDBD3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .responder-link {
            position: absolute;
            cursor: pointer; /* Altera o cursor para a mãozinha */
            color: white; /* Cor do "Responder" */
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            transition: color 0.3s ease-in-out; /* Efeito suave para a cor */
            top: 500px;
            left: 850px;
        }

        .marcar-como-lida {
            position: absolute;
            cursor: pointer; /* Altera o cursor para a mãozinha */
            color: white; /* Cor do "Responder" */
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            transition: color 0.3s ease-in-out; /* Efeito suave para a cor */
            top: 540px;
            left: 820px;
        }

        .responder-link:hover {
            color: #FFDE59; /* Cor do "Responder" quando passar o mouse */
        }

        .marcar-como-lida {
            color: #2EDBD3; /* Cor do "Marcar como lida" */
        }

        .marcar-como-lida:hover {
            color: #FFDE59; /* Cor do "Marcar como lida" quando passar o mouse */
        }

        /* Botão de voltar */
        .voltar-link {
            display: inline-block;
            margin-top: 20px;
            color: #2EDBD3;
            text-decoration: none;
            font-size: 16px;
        }


        
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../index.php">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../../Perfil_Adm/perfil_adm.php">
                        <img src="IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../../Email_adm/email_adm.php">
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
        </div>
        <span class="categorias">Detalhes da Mensagem</span>
    </nav>

    <!-- Gradiente de Fundo -->
    <div class="diagonal-gradient"></div>

    <!-- Botão de Voltar -->
    <a href="../email_adm.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img" alt="Voltar">
    </a>

    <div class="mensagem-container">
        <!-- Exibe a mensagem selecionada -->
        <div class="mensagem">
            <h3>De: <?php echo htmlspecialchars($mensagem['remetente']); ?></h3>
            <p><strong>Assunto:</strong> <?php echo htmlspecialchars($mensagem['assunto']); ?></p>
            <p><strong>Conteúdo:</strong><br><?php echo nl2br(htmlspecialchars($mensagem['conteudo'])); ?></p>
            <p><strong>Data de envio:</strong> <?php echo htmlspecialchars($mensagem['data_envio']); ?></p>
        </div>

        <!-- Link para exibir o formulário de resposta -->
        <span class="responder-link" onclick="toggleResponder()">Responder</span>
        <span class="marcar-como-lida" onclick="marcarComoLida()">Marcar como lida</span>
        
        <!-- Container para exibir a resposta -->
        <div class="responder-container" id="responderContainer">
            <div class="destinatario">
                Para: <?php echo htmlspecialchars($mensagem['remetente']); ?>
            </div>
            <form method="POST" action="resposta.php">
                <input type="hidden" name="mensagem_id" value="<?php echo $mensagem['id']; ?>">
                <label for="resposta">Sua Resposta:</label>
                <textarea name="resposta" required></textarea>
                <button type="submit">Enviar Resposta</button>
            </form>

        </div>
    </div>

    <!-- Imagem do Email -->
    <img src="IMG/email.png" class="email-img">

    <script>
        // Função para alternar a exibição do formulário de resposta
        function toggleResponder() {
            const responderContainer = document.getElementById('responderContainer');
            if (responderContainer.style.display === "none" || responderContainer.style.display === "") {
                responderContainer.style.display = "block"; // Torna visível
            } else {
                responderContainer.style.display = "none"; // Torna invisível
            }
        }

        // Assegure que o container comece invisível ao carregar
        document.getElementById('responderContainer').style.display = 'none';

        function marcarComoLida() {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "marcar_como_lida.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Mensagem marcada como lida!');
                    // Remover a mensagem da tela
                    document.querySelector('.mensagem-container').style.display = 'none';
                }
            };
            xhr.send("mensagem_id=<?php echo $mensagem['id']; ?>");
        }

    </script>



<script>
        function toggleResponder() {
            const responderContainer = document.getElementById('responderContainer');
            if (responderContainer.style.display === "none" || responderContainer.style.display === "") {
                responderContainer.style.display = "block"; 
            } else {
                responderContainer.style.display = "none"; 
            }
        }

        function marcarComoLida() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "marcar_como_lida.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert('Mensagem marcada como lida!');
                // Redirecionar para a página anterior
                window.history.back(); // Voltar para a página anterior no histórico do navegador
            }
    };
    xhr.send("mensagem_id=<?php echo $mensagem['id']; ?>");
}

    </script>

    <script src="recados.js"></script> 


</body>
</html>
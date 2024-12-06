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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="perfil.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

        /*Botão salvar modificações e sair*/
        .custom-button {
        position: absolute; /* Define a posição como absoluta */
        background-color: #66CAFF; /* Define a cor de fundo */
        color: black; /* Define a cor do texto */
        padding: 5px 80px; /* Define o preenchimento */
        border: none; /* Remove a borda */
        border-radius: 15px; /* Arredonda os cantos */
        font-size: 10px; /* Define o tamanho da fonte */
        cursor: pointer; /* Define o cursor como pointer (mãozinha) */
        font-family: "Poppins", sans-serif; /* Define a fonte */
        font-weight: 500; /* Define o peso da fonte */
        font-style: normal; /* Define o estilo da fonte */
        font-size: 20px; /* Define o tamanho da fonte */
        text-align: center; /* Centraliza o texto */
        top: 135%; /* Posiciona 115% do topo */
        left: 45%; /* Posiciona 43% da esquerda */
        transition: background-color 0.3s ease; /* Define uma transição suave para a cor de fundo */
        }

        /* Estilo para o botão personalizado ao passar o mouse */
        .custom-button:hover {
        background-color: #003185; /* Define a cor de fundo ao passar o mouse */
        }


    </style>
</head>
<body>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="INICIO.HTML">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="perfil_prof.php">
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
            <div class="Perfil-Momentaneo">
                <div class="perfil-texto">Perfil</div>
                <div class="profile-icon" onclick="document.getElementById('fileInput').click();">
                    <img id="imgFotoPerfil" alt="Sua Imagem" src="IMG/icon_momentaneo.png"> <!-- Adicione uma imagem padrão -->
                </div>
                <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="previewImage(event)">
            </div>
        </div>
    </nav>
    <div class="diagonal-gradient"></div>

    
    <div class="info-container">
        <div class="nome" contenteditable="true" id="nome">
            <?php echo htmlspecialchars($prof['Nome_Completo']); ?>
    </div>
    </div>

    <div class="info-container2">
        <div class="cargo">Professor</div>
    </div>

    <div class="info-container3">
        <div class="email" contenteditable="true" id="email">
            <?php echo htmlspecialchars($prof['Email']); ?>
        </div>
    </div>
   
    <div class="info-container4">
        <div class="local">ETEC Professora Ilza Nascimento Pintus</div>
    </div>

    <div class="info-container5">
        <div class="senha" contenteditable="true" id="senha">
            <?php echo htmlspecialchars($prof['Senha']); ?>
        </div>
    </div>

    
    
    <a href="../index.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>



    <img src="IMG/perfil.png" class="perfil-img">
    <img src="IMG/chave.png" class="chave-img">
    <img src="IMG/email.png" class="email-img">
    <img src="IMG/localizacao.png" class="local-img">
    <img src="IMG/olho.png" class="olho-img">




    <!-- Botões -->
    <button class="salvar-button" id="salvar-button">Salvar Modificações</button>
    <a href="logout.php"><button class="custom-button">Sair</button></a>
    <div class="diagonal-gradient"></div>
    <script src="perfil_prof.js"></script>
</body>
</html>
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


// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
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

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $disciplina = $_POST['disciplina'];
    $data = $_POST['data'];
    $lab = $_POST['lab'];
    $horario = $_POST['horario'];
    $curso = $_POST['curso'];  // Corrigido para usar 'curso' com 'c' minúsculo


    // Inserir dados na tabela Registro_Aulas
    $sql = "INSERT INTO registro_aulas (Disciplina, Data, Lab, Horario, Curso) VALUES ('$disciplina', '$data', '$lab', '$horario', '$curso')";

    if ($conn->query($sql) === TRUE) {
        echo "Novo registro criado com sucesso!";
        // Redirecionar para a página de registros após a inserção
        header("Location: enviado/enviado.php"); 
        exit;
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}
if (isset($_POST['curso'])) {
    $curso = $_POST['curso'];
} else {
    $curso = '';  // Ou algum valor padrão, ou trate o erro de forma apropriada
}
?>






<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="novoRegistro.css"> <!-- Ajuste o caminho aqui -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        .custom-button {
            background-color: #66CAFF;
            color: rgb(0, 0, 0);
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 20px; /* Tamanho do texto do botão */
            cursor: pointer;
            text-align: center;
            display: block; /* Permite centralizar com margens automáticas */
            margin: 20px auto; /* Centraliza horizontalmente */
            transition: background-color 0.3s ease;
        }
        
        .custom-button:hover {
            background-color: #003185;
        }   

        .input-container img {
            width: 80px; /* Tamanho da imagem */
            height: 80px; /* Tamanho da imagem */
        }
        /* Estilos para a imagem "Voltar" */
        .voltar-img {
            width: 30px; /* Ajuste a largura conforme necessário */
            height: auto; /* Altura automática para manter a proporção */
            position: absolute; /* Posicionamento absoluto */
            left: 30px; /* Distância da esquerda da tela */
            top: -355px; /* Posicionamento abaixo da imagem do ícone de perfil */
            transform: translateY(-50%); /* Centraliza verticalmente */
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

    <div class="diagonal-gradient"></div>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../../index.php"><img src="IMG/inicio.png" alt="Imagem 1"><span class="dropdown-link">Início</span></a>
                </div>
                <div class="dropdown-item">
                    <a href="../../Perfil_Prof/perfil_prof.php"><img src="IMG/perfil.png" alt="Imagem 2"><span class="dropdown-link">Perfil</span></a>
                </div>
                <div class="dropdown-item">
                    <a href="../../Email_Prof/email_prof.php"><img src="IMG/email.png" alt="Imagem 3"><span class="dropdown-link">E-mail</span></a>
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
        <span class="categorias">Novo Registro de Aula</span>
    </nav>

    <form method="POST" action="">
        <div class="input-container">
            <img src="IMG/livros.png" alt="Icone">
            <input type="text" name="disciplina" placeholder="Disciplina Aplicada" required>
        </div>
        <div class="images-container">
            <div class="image-item">
                <img src="IMG/agenda.png" alt="Imagem 1"><br>
                <input type="date" name="data" required>
            </div>
            <div class="image-item">
                <img src="IMG/porta.png" alt="Imagem 2"><br>
                <input type="text" name="lab" placeholder="Laboratório" required>
            </div>
            <div class="image-item">
                <img src="IMG/horarioADM.png" alt="Imagem 3"><br>
                <input type="time" name="horario" required>
            </div>
            <div class="image-item">
                <img src="IMG/curso.png" alt="Imagem 4"><br>
                <input type="text" name="curso" placeholder="Curso" required>
            </div>
        </div>
        <button type="submit" class="custom-button">Enviar</button>
    </form>
    
    
    <a href="../registrarAulas.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>

    <script src="novoRegistro.js"></script>
</body>
</html>

<?php
$conn->close();
?>

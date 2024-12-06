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


if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Consulta para obter os horários dos administradores junto com os nomes
$sql = "SELECT h.*, a.Nome_Completo FROM horarios_adm h
        JOIN Administradores a ON h.adm_id = a.ID_Adm";
$result = $conn->query($sql);

// Consulta para obter todos os cursos
$cursos = [];
$cursos_query = "SELECT DISTINCT Curso FROM Alunos";
$result = $conn->query($cursos_query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cursos[] = $row['Curso'];
    }
}

// Fechar conexão
$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="selecionarCurso.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins em Negrito*/
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins normal*/

        /* Estilos para o texto "Selecione-Curso" */
        .Selecione-Curso {
            color: #ffde59; /* Cor do texto */
            font-size: 35px; /* Tamanho da fonte */
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: normal;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            margin: 100px 0;
        }

        .voltar-img {
            width: 30px; /* Tamanho inicial para telas grandes */
            height: auto; /* Mantém a proporção da imagem */
            position: absolute; /* Posicionamento absoluto */
            left: 30px; /* Distância fixa da esquerda */
            top: -190px; /* Posição inicial conforme solicitado */
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
                <a href="../Perfil_Adm/perfil_adm.php">
                    <img src="IMG/perfil.png" alt="Imagem 2">
                    <span class="dropdown-link">Perfil</span>
                </a> 
            </div>
            <!-- Item do Dropdown - E-mail -->
            <div class="dropdown-item">
                <a href="../Email_Adm/Email_adm.php">
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
					<div class="nome"><?php echo htmlspecialchars($adm['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($adm['Email']); ?></div>
			</div>

    <span class="categorias">Visualizar Problemas</span>
</nav>
<div class="diagonal-gradient"></div>
<a href="../index.php" class="back-button"></a>

<!-- Div para Selecionar Curso -->
<div class="Selecione-Curso">
    <Selecione-Curso>SELECIONE UM CURSO</Selecione-Curso>
</div>

<?php
// Exibe todas as caixinhas de curso, sem ocultá-las
echo '<div class="container">';

// Define o mapeamento de cursos com seus respectivos links
$caixinhas = [
    'ADM' => 'ADM/visualizarProblema/visualisarProblema.php',
    'AUT' => 'AUT/visualizarProblema/visualisarProblema.php',
    'DS'  => 'DS/visualizarProblema/visualisarProblema.php'
];

// Exibe todas as caixinhas de cursos com base no nome do curso
foreach ($caixinhas as $curso => $url) {
    echo "<div class='caixinha-container'>
            <a href='$url?curso=$curso'> 
                <div class='caixinha'>
                    <h1>$curso</h1>
                </div>
            </a>
          </div>";
}

echo '</div>';
?>

<a href="../index.php" class="voltar-link">
    <img src="IMG/voltar.png" class="voltar-img">
</a>

<script src="script.js"></script>
</body>
</html>

<?php
// Conectar ao banco de dados
$conn = new mysqli("localhost", "root", "", "ScanView");

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber os dados do formulário
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $laboratorio = isset($_POST['laboratorio']) ? $_POST['laboratorio'] : null;
    $computador = isset($_POST['computador']) ? $_POST['computador'] : null;
    $professor = isset($_POST['professor']) ? $_POST['professor'] : null;

    // Verificar se todos os campos obrigatórios estão preenchidos
    if ($nome && $laboratorio && $computador && $professor) {
        // Verificar se o aluno existe pelo nome
        $sql_aluno = "SELECT ID_Aluno FROM Alunos WHERE Nome_Completo = ?";
        $stmt_aluno = $conn->prepare($sql_aluno);
        $stmt_aluno->bind_param("s", $nome);
        $stmt_aluno->execute();
        $stmt_aluno->store_result();

        if ($stmt_aluno->num_rows === 0) {
            die("Erro: Nome do aluno não encontrado.");
        }

        $stmt_aluno->bind_result($id_aluno);
        $stmt_aluno->fetch();
        $stmt_aluno->close();

        // Inserir dados na tabela Computadores
        $sql_professor = "INSERT INTO Computadores_Professores (Laboratorio, Computador, ID_Aluno, ID_Professor) VALUES (?, ?, ?, ?)";
        $stmt_professor = $conn->prepare($sql_professor);
        $stmt_professor->bind_param("ssii", $laboratorio, $computador, $id_aluno, $professor);

// Se o formulário foi enviado com sucesso
if ($stmt_professor->execute()) {
    // Redireciona para a página de sucesso
    header("Location: enviado/enviado.php");
    exit();
} else {
    // Se ocorreu um erro
    header("Location: erro.html");
    exit();
}

        
        $stmt_professor->close();
        } else {
            echo "<span style='color: #fff; font-weight: 400;'>Todos os campos são obrigatórios.</span>";
        }
}

$conn->close();
?>


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
    <title>Registrar Computador</title>
    <link rel="stylesheet" href="registro.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins em Negrito*/
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins normal*/
        /* Remover o fundo branco do botão da lixeira e centralizar o ícone */
        .lixeira-button {
            background: none; /* Remover o fundo */
            border: none; /* Remover a borda */
            padding: 0; /* Remover o preenchimento */
            display: flex; /* Usar flexbox para alinhar o conteúdo */
            justify-content: center; /* Centraliza horizontalmente */
            align-items: center; /* Centraliza verticalmente */
            cursor: pointer; /* Torna o ícone clicável */
        }

        /* Estilos para o ícone da lixeira */
        .lixeira-icon {
            width: 70px; /* Tamanho do ícone */
            height: 40px; /* Tamanho do ícone */
            position: absolute;
            transform: translate(-420%, 350%); /* Centraliza horizontal e verticalmente */
        }

        /* Estilos para o botão personalizado */
        .custom-button {
            position: absolute; /* Posicionamento absoluto */
            background-color: #66CAFF; /* Cor de fundo do botão */
            color: rgb(0, 0, 0); /* Cor do texto */
            padding: 5px 50px; /* Adiciona preenchimento */
            border: none; /* Remove a borda */
            border-radius: 10px; /* Adiciona bordas arredondadas */
            font-size: 10px; /* Tamanho da fonte */
            cursor: pointer; /* Altera o cursor para indicar que é clicável */
            font-family: "Poppins", sans-serif; /* Define a fonte */
            font-weight: 500; /* Define o peso da fonte */
            font-style: normal; /* Define o estilo da fonte */
            font-size: 20px; /* Tamanho da fonte */
            text-align: center; /* Centraliza o texto */
            top: 145%; /* Posiciona no centro verticalmente */
            left: 38%; /* Posiciona no centro horizontalmente */
            transition: background-color 0.3s ease; /* Transição suave */
        }

        .custom-button:hover {
            background-color: #003185; /* Cor de fundo ao passar o mouse */
        }   

        select, option {
            font-family: 'Poppins', sans-serif;
            width: 60%; /* A largura é 100%, igual aos outros campos de entrada */
            padding: 7px; /* O mesmo preenchimento que os inputs */
            border: 1px solid #ccc; /* A mesma borda */
            border-radius: 13px; /* O mesmo arredondamento das bordas */
            font-size: 11px; /* Tamanho da fonte, igual aos inputs */
            background-color: #fff; /* Cor de fundo */
            color: #000; /* Cor do texto */
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
                    <a href="../index.php">
                        <img src="IMG/inicio.png" alt="Imagem 1">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../Perfil_Aluno/perfil_Aluno.php">
                        <img src="IMG/perfil.png" alt="Imagem 2">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../Email_Aluno/email_Aluno.php">
                        <img src="IMG/email.png" alt="Imagem 3">
                        <span class="dropdown-link">E-mail</span>
                    </a>
                </div>
            </div>
        </div>
			<div class="Perfil-Momentaneo">
				<img src="IMG/icon_momentaneo.png" alt="Sua Imagem">
				<div class="info">
					<div class="nome"><?php echo htmlspecialchars($aluno['Nome_Completo']); ?></div>
					<div class="email"><?php echo htmlspecialchars($aluno['Email']); ?></div>
			</div>
        <span class="categorias">Registrar Computador</span>
    </nav>

    <div class="diagonal-gradient"></div>
    
    <div class="container">
        <form class="form-container" action="registro.php" method="POST">
            <div class="input-box">
                <img src="IMG/nome.png" alt="Ícone Nome">
                <input type="text" name="nome" placeholder="Nome do Aluno" required>
            </div>
            <div class="input-box">
                <img src="IMG/lab.png" alt="Ícone Laboratório">
                <input type="text" name="laboratorio" placeholder="Laboratório" required>
            </div>
            <div class="input-box">
                <img src="IMG/pc.png" alt="Ícone Computador Usado">
                <input type="text" name="computador" placeholder="Computador Usado" required>
            </div>
            
            <div class="input-box">
                <!-- Imagem ao lado da seleção do professor -->
                <img src="IMG/prof.png" alt="Ícone Professor" class="input-box">
                <select name="professor" id="professor" required>
                    <option value="" disabled selected>Selecione o Professor</option>
                    <?php
                    // Conectar ao banco de dados
                    $conn = new mysqli("localhost", "root", "", "ScanView");

                    // Verificar a conexão
                    if ($conn->connect_error) {
                        die("Conexão falhou: " . $conn->connect_error);
                    }

                    // Consultar professores
                    $sql = "SELECT ID_Prof, Nome_Completo FROM Professor";
                    $result = $conn->query($sql);

                    // Verificar se a consulta retornou resultados
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["ID_Prof"] . "'>" . $row["Nome_Completo"] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>Nenhum professor encontrado</option>";
                    }

                    $conn->close();
                    ?>
                </select>
            </div>
            
            <button type="submit" class="custom-button">Enviar</button>
            
            <!-- Ícone de Lixo para Limpar o Formulário -->
            <button type="button" class="lixeira-button" onclick="limparFormulario()">
                <img src="IMG/Lixeira.png" alt="Ícone Lixo" class="lixeira-icon">
            </button>

        </form>

        <a href="../index.php" class="voltar-link">
            <img src="IMG/voltar.png" class="voltar-img">
        </a>
    </div>

    <script>
        // Função para limpar o formulário
        function limparFormulario() {
            var form = document.querySelector('.form-container');
            form.reset();
        }
    </script>

    <script src="registro.js"></script>
</body>
</html>


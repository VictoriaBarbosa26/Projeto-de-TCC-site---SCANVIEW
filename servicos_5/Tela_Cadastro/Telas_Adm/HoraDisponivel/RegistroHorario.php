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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos foram preenchidos
    if (isset($_POST['data']) && isset($_POST['hora']) && isset($_POST['hora_saida'])) {
        $adm_id = 1; // O ID do administrador deve ser dinâmico, ajuste conforme necessário
        $data = $_POST['data']; // Data da entrada
        $hora = $_POST['hora']; // Hora da entrada
        $hora_saida = $_POST['hora_saida']; // Hora da saída

        // Verifica se já existe uma entrada registrada para aquele dia
        $check_sql = "SELECT * FROM horarios_adm WHERE adm_id = ? AND data = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $adm_id, $data);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Se já existe, atualiza a hora de saída
            $sql = "UPDATE horarios_adm SET horario_fim = ? WHERE adm_id = ? AND data = ? AND horario_fim IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sis", $hora_saida, $adm_id, $data);
            
            if ($stmt->execute()) {
                // Redireciona para outra página após o sucesso
                header("Location: enviado/enviado.php"); 
                exit();
            } else {
                echo "Erro ao registrar saída: " . $stmt->error;
            }
        } else {
            // Se não existe, insere a nova entrada
            $sql = "INSERT INTO horarios_adm (adm_id, data, horario_inicio, horario_fim) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $adm_id, $data, $hora, $hora_saida);
            
            if ($stmt->execute()) {
                // Redireciona para outra página após o sucesso
                header("Location: enviado/enviado.php"); 
                exit();
            } else {
                echo "Erro ao registrar entrada: " . $stmt->error;
            }
        }

        $stmt->close();
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="RegistroHorario.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        .submit-button {
            padding: 10px 20px;
            background-color: #66CAFF;
            color: black;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .submit-button:hover {
            background-color: #003185;
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

        .categorias {
            color: #2EDBD3;
            font-size: 25px;
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: normal;
            position: absolute;
            top: 50%;
            left: 950px;
            transform: translate(-50%, -50%);
            white-space: nowrap;
        }

        .voltar-img {
            width: 30px;
            height: auto;
            position: fixed;
            left: -630px;
            top: -130px;
            z-index: 1000;
        }

        .input-container {
            margin-top: 2%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .input-container input {
            padding: 8px;
            font-size: 14px;
            width: 100%;
            max-width: 300px;
        }

        .input-container label {
            margin-bottom: 8px;
            font-size: 18px;
        }

        .images-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .image-item {
            text-align: center;
            margin: 0 10px;
        }

        .images-container img {
            width: 80px;
            height: 80px;
            margin-bottom: 8px;
        }

        .images-container input {
            margin-top: 10px;
            padding: 5px;
            font-size: 14px;
            width: 100px;
        }

        .images-container .image-item span {
            display: block;
            font-size: 14px;
            color: #333;
            margin-top: 5px;
            text-align: center;
        }

        @media (max-width: 600px) {
            .input-container {
                width: 90%;
            }

            .images-container {
                flex-direction: column;
            }

            .image-item {
                margin: 10px 0;
            }

            .custom-button {
                width: 100%;
            }
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
                    <a href="../../Email_Adm/email_adm.php">
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
        <span class="categorias">Horário de disponibilidade</span>
    </nav>

    <div class="diagonal-gradient"></div>

    <main>
        <section class="content">
            <div class="computer-info">
                <div class="computer-id">Registro de Entrada e Saída</div>
            </div>

            <form method="POST" action="">
                <div class="images-container">
                    <div class="image-item">
                        <img src="IMG/agenda.png" alt="Imagem 1"><br>
                        <input type="date" name="data" required>
                        <span>Data</span>
                    </div>

                    <div class="image-item">
                        <img src="IMG/relogio.png" alt="Relógio Entrada"><br>
                        <input type="time" name="hora" required>
                        <span>Entrada</span>
                    </div>

                    <div class="image-item">
                        <img src="IMG/relogio.png" alt="Relógio Saída"><br>
                        <input type="time" name="hora_saida" required>
                        <span>Saída</span>
                    </div>
                </div>

                <button type="submit" class="submit-button">Enviar</button>
            </form>

            <a href="../index.php" class="voltar-link">
                <img src="IMG/voltar.png" class="voltar-img" alt="Voltar">
            </a>
        </section>
    </main>

    <script src="../index.js"></script>
</body>
</html>

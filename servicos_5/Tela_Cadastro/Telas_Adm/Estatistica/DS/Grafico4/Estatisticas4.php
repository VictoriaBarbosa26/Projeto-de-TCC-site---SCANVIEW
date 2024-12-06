<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ScanView";

try {
    // Usando PDO para conectar ao banco de dados
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Lógica para buscar os 5 problemas mais frequentes no laboratório
$problemas = [];
$stmt = $pdo->prepare("
    SELECT Descricao, COUNT(*) as total
    FROM Problemas
    GROUP BY Descricao
    ORDER BY total DESC
    LIMIT 5
");
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizando os dados para o gráfico
$nomesProblemas = [];
$dadosProblemas = [];
foreach ($resultados as $resultado) {
    $nomesProblemas[] = $resultado['Descricao'];
    
    $problemasPorMes = [];
    for ($i = 1; $i <= 12; $i++) {
        $stmtMes = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM Problemas
            WHERE Descricao = :descricao AND MONTH(Data_Registro) = :mes
        ");
        $stmtMes->execute(['descricao' => $resultado['Descricao'], 'mes' => $i]);
        $resultadoMes = $stmtMes->fetch(PDO::FETCH_ASSOC);
        
        $problemasPorMes[] = (int)$resultadoMes['total'];
    }
    $dadosProblemas[] = $problemasPorMes;
}

// Verifique se a variável da sessão está definida
if (!isset($_SESSION['adm_id'])) {
    echo "Você não está logado. Redirecionando para a página de login...";
    header("refresh:3;url=../../Login/Login.php"); // Redireciona após 3 segundos
    exit();
}

// Recuperar os dados do administrador logado
$adm_id = $_SESSION['adm_id'];
$stmt = $pdo->prepare("SELECT Nome_Completo, Email, Senha FROM Administradores WHERE ID_Adm = ?");
$stmt->execute([$adm_id]);
$adm = $stmt->fetch(PDO::FETCH_ASSOC);

if ($adm) {
    // Dados do administrador encontrados
} else {
    echo "Administrador não encontrado.";
    exit();
}
?>




<!-- HTML do gráfico -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="Estatisticas4.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        canvas {
            width: 1300px; /* Largura do gráfico */
            height: 1000px; /* Altura do gráfico */
            background-color: rgba(255, 255, 255, 0); /* Fundo totalmente transparente */
            display: block; /* Para garantir que o canvas se comporte como um bloco */
            margin: 0 auto; /* Centraliza horizontalmente */
            top: 50px;
            position: absolute;
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
        <span class="categorias">Estatísticas de Hardware</span>
    </nav>

    <div class="diagonal-gradient">

        <div class="chart-container"> 
            <canvas id="myChart"></canvas> 
        </div>

        <a href="../estatistica.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>

        <script>
            // Dados do gráfico
            const ctx = document.getElementById('myChart').getContext('2d');
            const data = {
                labels: <?php echo json_encode(["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"]); ?>,
                datasets: [
                    <?php
                    foreach ($dadosProblemas as $index => $problema) {
                        echo "{
                            label: " . json_encode($nomesProblemas[$index]) . ",
                            data: " . json_encode($problema) . ",
                            borderColor: randomColor(),
                            borderWidth: 2,
                            backgroundColor: 'rgba(62, 149, 205, 0.2)',
                            fill: true
                        }" . ($index < count($dadosProblemas) - 1 ? "," : "");
                    }
                    ?>
                ]
            };

            function randomColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                        },
                        title: {
                            display: true,
                            text: 'Problemas Registrados por Mês'
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Quantidade de Problemas'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Meses'
                            }
                        }
                    }
                }
            };

            const myChart = new Chart(ctx, config);
        </script>
    </div>

    <script src="../script.js"></script>
</body>
</html>

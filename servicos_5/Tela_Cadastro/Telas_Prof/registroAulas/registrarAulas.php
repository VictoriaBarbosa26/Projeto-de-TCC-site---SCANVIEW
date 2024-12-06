<?php
$servername = "localhost";
$username = "root"; // Altere conforme necessário
$password = ""; // Altere conforme necessário
$dbname = "ScanView";


$conn = new mysqli($servername, $username, $password, $dbname);

session_start();

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

// Processar a exclusão de registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aulaExcluir'])) {
    $aulasParaExcluir = $_POST['aulaExcluir'];
    foreach ($aulasParaExcluir as $idAula) {
        $sqlDelete = "DELETE FROM Registro_Aulas WHERE ID_Aula = ?";
        $stmt = $conn->prepare($sqlDelete);
        $stmt->bind_param("i", $idAula);
        $stmt->execute();
        $stmt->close();
    }
}

// Inicializar a consulta
$sql = "SELECT ID_Aula, Data, Disciplina, Curso FROM Registro_Aulas";

// Filtrar por data se a data for enviada
if (isset($_GET['data']) && !empty($_GET['data'])) {
    $dataFiltrada = $_GET['data'];
    $sql .= " WHERE Data = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dataFiltrada);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="registroAulas.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins em Negrito*/
	@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /*Fonte poppins normal*/
    
    .botoes-topo {
        position: absolute;
        top: 150px;
        right: 180px;
        display: flex;
        align-items: center;
    }

    .botoes-topo img {
        width: 40px;
        height: 40px;
        margin-left: 10px;
        cursor: pointer;
        z-index: 1;
    }

    .data {
        font-size: 18px; /* Tamanho da fonte da data */
        font-weight: bold; /* Fonte em negrito */
        margin-right: 30px; /* Espaçamento à direita */
        color: white;
    }
    .descricao {
        font-size: 18px; /* Tamanho da fonte da descrição */
        margin-right: 30px; /* Espaçamento à direita */
        top: 400px;
        color: white;
    }
    .curso {
        font-size: 18px; /* Tamanho da fonte da descrição */
        margin-right: 17px; /* Espaçamento à direita */
        top: 400px;
        color: white;
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
    
    <div class="diagonal-gradient"></div>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../index.php"><img src="IMG/inicio.png" alt="Imagem 1"><span class="dropdown-link">Início</span></a>
                </div>
                <div class="dropdown-item">
                    <a href="../Perfil_Prof/perfil_prof.php"><img src="IMG/perfil.png" alt="Imagem 2"><span class="dropdown-link">Perfil</span></a>
                </div>
                <div class="dropdown-item">
                    <a href="../Email_Prof/email_prof.php"><img src="IMG/email.png" alt="Imagem 3"><span class="dropdown-link">E-mail</span></a>
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
        <span class="categorias">Registro de Aulas</span>
    </nav>


        <form method="post" id="formExcluir">
        <div class="atividades">
            <ul class="lista-atividades">
                <?php
                if ($result->num_rows > 0) {
                    // Exibir os registros de aulas
                    while($row = $result->fetch_assoc()) {
                        $dataFormatada = date("d/m/Y", strtotime($row['Data']));
                        echo "<li>
                            <div class='imagem'><img src='IMG/agenda.png' alt='Imagem de TCC'></div>
                            <div class='data'>" . $dataFormatada . "</div>
                            <div class='descricao'>Aula de: " . $row['Disciplina'] . "</div>
                            <div class='curso' style='color: #fff;'>Curso: " . $row['Curso'] . "</div>
                            <input type='checkbox' name='aulaExcluir[]' value='" . $row['ID_Aula'] . "'>
                        </li>";
                    }

                } else {
                    echo "<li style='color: white; font-weight: 400; transform: translateX(380%);'>Nenhuma aula registrada.</li>";
                }
                ?>
            </ul>
        </div>

        <button type="submit" class="excluir-registro" style="display:none;">Excluir Selecionados</button>
    </form>

    <a href="novoRegistro/novoRegistro.php">
        <button class="adicionar-registro">
            <img src="IMG/agenda.png" alt="Ícone de Adicionar">
            Adicionar novo registro
        </button>
    </a>

    <a href="../index.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img">
    </a>


    <!-- Botões de imagem no canto superior direito -->
    <div class="botoes-topo">
        <img src="IMG/lixeira.png" alt="Botão 1" onclick="confirmarExclusao();">
        <img src="IMG/filtrar.png" alt="Botão 2" onclick="mostrarFiltroData()">
    </div>

<!-- Formulário para filtrar por data -->
<div id="filtroData" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background: linear-gradient(to bottom, #f0f0f0, #d9d9d9); padding:20px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); z-index: 1000;">
    <span style="cursor:pointer; float:right; font-size: 20px; color: #888;" onclick="mostrarFiltroData()">✖</span>
    <label for="data">Filtrar por data:</label>
    <div style="display: flex; flex-direction: column; align-items: center; margin-top: 10px;">
        <input type="date" id="data" name="data" onchange="mostrarBotaoFiltrar()">
        <button id="botaoFiltrar" type="button" style="display:none; background-color: #66caff; color: white; border: none; padding: 10px 15px; text-align: center; text-decoration: none; font-size: 16px; cursor: pointer; border-radius: 4px; margin-top: 10px; width: 100%; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#4CAF50'" onmouseout="this.style.backgroundColor='#003185'" onclick="filtrarPorData()">Filtrar</button>


    </div>
</div>

<script src="registroAulas.js"></script>
<script>
        // Função para mostrar o filtro
        function mostrarFiltroData() {
            const filtro = document.getElementById('filtroData');
            filtro.style.display = filtro.style.display === 'none' ? 'block' : 'none';
        }

        // Função para mostrar o botão de filtrar
        function mostrarBotaoFiltrar() {
            const botao = document.getElementById('botaoFiltrar');
            botao.style.display = 'block'; // Mostra o botão ao selecionar uma data
        }

        // Função para confirmar a exclusão
        function confirmarExclusao() {
            const checkboxes = document.querySelectorAll('input[name="aulaExcluir[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Selecione ao menos uma aula para excluir.');
                return;
            }
            const confirmacao = confirm('Deseja realmente excluir os registros selecionados?');
            if (confirmacao) {
                document.getElementById('formExcluir').submit(); // Envia o formulário para excluir
            }
        }

        // Função para filtrar por data
        function filtrarPorData() {
            const data = document.getElementById('data').value;
            if (data) {
                window.location.href = "?data=" + data; // Redireciona para a página com a data selecionada
            }
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>


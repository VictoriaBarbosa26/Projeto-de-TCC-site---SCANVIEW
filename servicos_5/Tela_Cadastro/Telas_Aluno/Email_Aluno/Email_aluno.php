<?php
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

// Enviar mensagem se o botão "Enviar" for clicado
if (isset($_POST['enviar'])) {
    // Escapando os dados de entrada
    $destinatario = mysqli_real_escape_string($conn, $_POST['destinatario']);
    $assunto = mysqli_real_escape_string($conn, $_POST['assunto']);
    $conteudo = mysqli_real_escape_string($conn, $_POST['mensagem']);

    // Verificação de campos vazios
    if (empty($destinatario) || empty($assunto) || empty($conteudo)) {
        $_SESSION['mensagem'] = "Todos os campos devem ser preenchidos!";
    } else {
        // Verificar se o destinatário existe na tabela Alunos, Professores ou Administradores
        $sql_verificar_destinatario = "SELECT Email FROM Alunos WHERE Email = ? UNION SELECT Email FROM Professor WHERE Email = ? UNION SELECT Email FROM Administradores WHERE Email = ?";
        $stmt_verificar = $conn->prepare($sql_verificar_destinatario);
        $stmt_verificar->bind_param("sss", $destinatario, $destinatario, $destinatario);
        $stmt_verificar->execute();
        $result_destinatario = $stmt_verificar->get_result();

        // Se o destinatário não existir, mostrar um erro
        if ($result_destinatario->num_rows == 0) {
            $_SESSION['mensagem'] = "Destinatário não encontrado!";
        } else {
            // Inserir a mensagem na tabela com status "não lida"
            $sql = "INSERT INTO mensagens (remetente, destinatario, assunto, conteudo, status) VALUES (?, ?, ?, ?, 'não lida')";
            $stmt = $conn->prepare($sql);
            // Usar o email do usuário logado como remetente
            $stmt->bind_param("ssss", $_SESSION['usuario_email'], $destinatario, $assunto, $conteudo);

            if ($stmt->execute()) {
                $_SESSION['mensagem'] = "Mensagem enviada com sucesso!";
            } else {
                $_SESSION['mensagem'] = "Erro ao enviar mensagem: " . $conn->error;
            }

            $stmt->close();
        }
        $stmt_verificar->close();
        
        // Redirecionar para a mesma página para evitar reenvio
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Sempre usar exit após um redirecionamento
    }
}

// Buscar mensagens recebidas
$email = $_SESSION['usuario_email'];
$mensagens = []; // Inicializa o array de mensagens
if (!empty($email)) {
    // Alterar a consulta para buscar mensagens "não lidas"
    $sql = "SELECT * FROM mensagens WHERE destinatario = ? AND status = 'não lida' ORDER BY id DESC"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Adiciona cada mensagem ao array de mensagens
        $mensagem_id = $row['id']; // ID da mensagem

        // Agora buscamos as respostas relacionadas a essa mensagem
        $sql_respostas = "SELECT * FROM respostas WHERE mensagem_id = ? ORDER BY data_resposta ASC";
        $stmt_respostas = $conn->prepare($sql_respostas);
        $stmt_respostas->bind_param("i", $mensagem_id);
        $stmt_respostas->execute();
        $result_respostas = $stmt_respostas->get_result();

        $respostas = [];
        while ($resposta = $result_respostas->fetch_assoc()) {
            $respostas[] = $resposta;
        }

        // Adiciona a mensagem e as respostas ao array de mensagens
        $row['respostas'] = $respostas;
        $mensagens[] = $row;

        $stmt_respostas->close();
    }

    $stmt->close();
} else {
    echo "<script>alert('Usuário não está logado.');</script>";
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

$conn->close();

?>




<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanView</title>
    <link rel="stylesheet" href="Email.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /* Fonte Poppins em Negrito */
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'); /* Fonte Poppins normal */
    
        /* Estilos para cada balão de mensagem */
        .mensagem-balao {
            background-color: #fff;
            padding: 10px;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 70%;
            text-align: center;
        }
        .mensagem-box {
            position: absolute;
            right: 20px;
            top: 19%;
            z-index: 1000;
        }
        .escrever-mensagem {
            font-size: 18px;
            cursor: pointer;
            background-color: #66CAFF;
            color: #003185;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            position: absolute;
            left: -190px;
            top: -30px;
        }
        .caixa-mensagem {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .caixa-mensagem h3 {
            margin: 0;
        }
        .caixa-mensagem label {
            display: block;
        }
        .caixa-mensagem input,
        .caixa-mensagem textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Estilos do balão de mensagem */
        .mensagem-balao {
            background-color: #fff;
            padding: 10px;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 70%;
            position: relative; /* Necessário para posicionar o ícone de email fora do balão */
            text-align: center;
            display: flex;
            align-items: center; /* Alinha verticalmente o conteúdo */
            flex-direction: column; /* Para garantir que os itens fiquem empilhados */
        }

        /* Estilo da imagem de olho fora do balão */
        .mensagem-balao .view-img {
            width: 40px;  /* Ajuste o tamanho da imagem conforme necessário */
            height: 40px;
            position: absolute;  /* Posiciona a imagem fora do balão */
            left: 700px;  /* Ajusta a posição à esquerda do balão */
            top: 50%;  /* Alinha verticalmente no meio do balão */
            transform: translateY(-50%); /* Ajusta para centralizar verticalmente */
            cursor: pointer; /* Cursor para indicar que a imagem é clicável */
        }


        /* Estilo da imagem de e-mail fora do balão */
        .mensagem-balao img {
            width: 40px;  /* Ajuste o tamanho da imagem conforme necessário */
            height: 30px;
            position: absolute;  /* Posiciona a imagem fora do balão */
            left: -60px;  /* Ajusta a posição à esquerda do balão */
            top: 50%;  /* Alinha verticalmente no meio do balão */
            transform: translateY(-50%); /* Ajusta para centralizar verticalmente */
            cursor: pointer; /* Cursor para indicar que a imagem é clicável */
        }



        /* Container para o botão "Ver Respostas" */
        .ver-respostas-container {
            margin-top: 10px;
            display: flex;
            justify-content: center; /* Centraliza o botão */
            width: 100%; /* Garante que o botão tenha toda a largura do balão */
        }

        /* Estilo do botão "Ver Respostas" */
        .toggle-respostas {
            background-color: #66CAFF;
            color: black;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .toggle-respostas:hover {
            background-color: #85857b; /* Efeito de hover para o botão */
        }

        /* Estilos do container de respostas (ocultando por padrão) */
        .respostas-container {
            display: none;
            margin-top: 10px;
            transition: max-height 0.3s ease-out;
        }

        /* Classe aberta para mostrar as respostas */
        .respostas-container.open {
            display: block;
        }

        .mensagem-balao.resposta {
            background-color: #f0f8ff;
            margin-bottom: 10px;
            width: 70%;
            padding: 10px;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: #003185;
        }

        .view-icon {
            font-size: 20px;
            color: #003185;
            text-decoration: none;
            margin-left: 10px;
        }

        /* Ícone de perfil e informações */
		.Perfil-Momentaneo .info .nome {
            position: absolute;
			font-size: 16px;
			font-weight: bold;
            left: -20px;
            top: -20px;
		}

		.Perfil-Momentaneo .info .email {
            position: absolute;
			font-size: 14px;
			color: #ccc;
            left: -20px;
            top: 5px;
		}
        
        .Perfil-Momentaneo img {
            position: absolute;
            width: 80px;
            height: auto;
            border-radius: 50%;
            object-fit: cover;
            left: -130px;
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="dropdown">
            <button class="dropbtn">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-item">
                    <a href="../index.php">
                        <img src="IMG/inicio.png" alt="Início">
                        <span class="dropdown-link">Início</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="../Perfil_Aluno/perfil_aluno.php">
                        <img src="IMG/perfil.png" alt="Perfil">
                        <span class="dropdown-link">Perfil</span>
                    </a>
                </div>
                <div class="dropdown-item">
                    <a href="Email_aluno.php">
                        <img src="IMG/email.png" alt="E-mail">
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
        </div>
        <span class="categorias">Inbox</span>
    </nav>
    <div class="diagonal-gradient"></div>
    
    <a href="../index.php" class="voltar-link">
        <img src="IMG/voltar.png" class="voltar-img" alt="Voltar">
    </a>

    <div class="mensagem-container">
    <?php if (empty($mensagens)): ?>
        <p style="color: white; position: relative; left: 50%; transform: translateX(-215%);">Sem recados no momento</p>
    <?php else: ?>
        <?php foreach ($mensagens as $mensagem): ?>
            <div class="mensagem-balao">
                <img src="IMG/email.png" alt="Email">
                <a href="Recados_alunos/recados.php?id=<?php echo htmlspecialchars($mensagem['id']); ?>">
                    <img src="IMG/olho.png" alt="Ver mensagem" class="view-img" data-id="<?php echo $mensagem['id']; ?>">
                </a>
                <p><strong>Assunto:</strong> <?php echo htmlspecialchars($mensagem['assunto']); ?></p>

                <!-- Verificar se existem respostas antes de exibir o botão -->
                <?php if (!empty($mensagem['respostas'])): ?>
                    <!-- Botão para exibir as respostas (centralizado abaixo do assunto) -->
                    <div class="ver-respostas-container">
                        <button class="toggle-respostas" data-id="<?php echo $mensagem['id']; ?>">Ver Respostas</button>
                    </div>

                    <div class="respostas-container" id="respostas-<?php echo $mensagem['id']; ?>">
                        <?php foreach ($mensagem['respostas'] as $resposta): ?>
                            <div class="mensagem-balao resposta">
                                <p><strong>Resposta de <?php echo htmlspecialchars($mensagem['remetente']); ?>:</strong></p>
                                <p><?php echo htmlspecialchars($resposta['resposta']); ?></p>
                                <p><strong>Data da resposta:</strong> <?php echo htmlspecialchars($resposta['data_resposta']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

    <!-- Caixa de mensagem para escrever uma nova mensagem -->
    <div class="mensagem-box">
        <button class="escrever-mensagem">✚</button>
        <div class="caixa-mensagem">
            <h3>Nova Mensagem</h3>
            <form method="POST" action="">
                <label for="destinatario">Destinatário:</label>
                <input type="text" id="destinatario" name="destinatario" placeholder="Insira o destinatário" required>
                <label for="assunto">Assunto:</label>
                <input type="text" id="assunto" name="assunto" placeholder="Insira o assunto" required>
                <label for="mensagem">Mensagem:</label>
                <textarea id="mensagem" name="mensagem" placeholder="Escreva sua mensagem" required></textarea>
                <button type="submit" name="enviar">Enviar</button>
                <button type="button" id="cancelar">Cancelar</button>
            </form>
        </div>
    </div>
    <script>
        // Script para alternar a visibilidade das respostas
        document.querySelectorAll('.toggle-respostas').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var containerRespostas = document.getElementById('respostas-' + id);

                if (containerRespostas.classList.contains("open")) {
                    containerRespostas.classList.remove("open");
                    this.textContent = "Ver Respostas";
                } else {
                    containerRespostas.classList.add("open");
                    this.textContent = "Esconder Respostas";
                }
            });
        });
        // JavaScript para controlar a exibição da caixa de mensagem
        document.querySelector('.escrever-mensagem').addEventListener('click', function() {
            document.querySelector('.caixa-mensagem').style.display = 'block';
        });

        document.querySelector('#cancelar').addEventListener('click', function() {
            document.querySelector('.caixa-mensagem').style.display = 'none';
        });
    </script>
    
    <script src="Email.js"></script> 

</body>
</html>

		
		

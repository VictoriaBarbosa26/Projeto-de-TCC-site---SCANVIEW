<?php
session_start(); // Certifique-se de iniciar a sessão

$servername = "localhost";
$username = "root"; // Altere conforme necessário
$password = ""; // Altere conforme necessário
$dbname = "ScanView";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Inicializa a variável de mensagem de erro
$error_message = '';

// Checar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar dados do formulário
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $cargo = $_POST["cargo"];
    $rm = isset($_POST["rm"]) ? $_POST["rm"] : NULL;
    $numero_identificacao = isset($_POST["numero_identificacao"]) ? $_POST["numero_identificacao"] : NULL;
    $curso = isset($_POST["curso"]) ? $_POST["curso"] : NULL; // Novo campo curso

    // Verificar se o número de identificação é válido para administradores e professores
    if ($cargo == "adm" || $cargo == "prof") {
        $stmt = $conn->prepare("SELECT * FROM Identificacao_Pendentes WHERE Numero_Identificacao = ? AND Cargo = ?");
        $stmt->bind_param("is", $numero_identificacao, $cargo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Número de identificação inválido
            $error_message = "Número de identificação inválido para o cargo selecionado.";
        }
    }

    // Preparar a consulta SQL e o URL de redirecionamento com base no cargo e curso
    if ($error_message == '') {
        if ($cargo == "aluno") {
            $sql = "INSERT INTO Alunos (Nome_Completo, Email, Senha, RM, Curso) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiss", $nome, $email, $senha, $rm, $curso);
            $redirect_url = "Telas_Aluno/index.php";
        } elseif ($cargo == "adm") {
            $sql = "INSERT INTO Administradores (Nome_Completo, Email, Senha, Numero_Identificacao) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nome, $email, $senha, $numero_identificacao);
            $redirect_url = "Telas_Adm/index.php";
        } elseif ($cargo == "prof") {
            $sql = "INSERT INTO Professor (Nome_Completo, Email, Senha, Numero_Identificacao) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nome, $email, $senha, $numero_identificacao);
            $redirect_url = "Telas_Prof/index.php";
        } else {
            $error_message = "Cargo inválido.";
        }

        if ($error_message == '') {
            // Executar a consulta SQL
            if ($stmt->execute()) {
                // Se o cadastro for de um ADMINISTRADOR, recupera o ID do administrador recém-criado e salva na sessão
                if ($cargo == "adm") {
                    $id_adm = $conn->insert_id; // O ID do último registro inserido
                    $_SESSION['adm_id'] = $id_adm; // Armazenar o ID do administrador na sessão
                    $_SESSION['usuario_email'] = $email; // Armazenar o email do administrador na sessão
                } 
                // Se o cadastro for de um ALUNO, recupera o ID do aluno recém-criado e salva na sessão
                elseif ($cargo == "aluno") {
                    $id_aluno = $conn->insert_id; // O ID do último registro inserido
                    $_SESSION['aluno_id'] = $id_aluno; // Armazenar o ID do aluno na sessão
                    $_SESSION['usuario_email'] = $email; // Armazenar o email do aluno na sessão
                }
                // Se o cadastro for de um PROFESSOR, recupera o ID do professor recém-criado e salva na sessão
                elseif ($cargo == "prof") {
                    $id_prof = $conn->insert_id; // O ID do último registro inserido
                    $_SESSION['prof_id'] = $id_prof; // Armazenar o ID do professor na sessão
                    $_SESSION['usuario_email'] = $email; // Armazenar o email do professor na sessão
                }

                // Redirecionar para a página correspondente
                header("Location: $redirect_url");
                exit();
            } else {
                $error_message = "Erro ao cadastrar: " . $stmt->error;
            }
        }
    }
}

// Fechar conexão
$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="cadastro.css">
    <link rel="stylesheet" href="notification.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Estilo para a mensagem de erro */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            font-size: 16px;
            display: none; 
        }
        /* Exibe a mensagem quando houver um erro */
        .error-message.show {
            display: block;
        }
        #curso-container {
            display: block; /* Exibe o contêiner para ver se a fonte está sendo aplicada */
        }
        .form-group input[type="submit"] {
            margin-bottom: 2%;
            margin-top: 10px; /* Adiciona espaçamento superior ao botão */
            background-color: #003185;
            border-color: #003185;
            color: white;
            padding: 14px 20px;
            font-family: "Poppins", sans-serif;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
        }
        .container {
            background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 50, 133, 0.7), rgba(0, 0, 0, 0.7)); /* Degradê de preto para azul com transparência */
            border: 1px solid white; /* Borda branca ao redor do contêiner */
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative; /* Use 'relative' em vez de 'absolute' para evitar problemas de layout */
            width: 90%; /* O container ocupa a largura total da tela */
            max-width: 600px; /* Definir uma largura máxima para não esticar muito em telas grandes */
            min-width: 200px; /* Definir uma largura mínima para não encolher demais */
            color: white;
            border-radius: 10px;
            max-height: 900px;
        }
    </style>
</head>
<body>
    <img class="logo" src="IMG/logo.png" alt="Logo">
    <div class="container">
        <h2>CADASTRAR-SE</h2>
        <form id="cadastro-form" action="cadastro.php" method="post">
            <section>
                <!-- Campos do formulário -->
                <div class="form-group">
                    <img src="IMG/perfil.png" class="icon" alt="perfil">
                    <input type="text" id="nome" name="nome" placeholder="" required>
                    <label for="nome" class="input-label">Nome</label>
                </div>
                <div class="form-group">
                    <img src="IMG/email.png" class="icon" alt="email">
                    <input type="email" id="email" name="email" placeholder="" required>
                    <label for="email" class="input-label">Email Institucional</label>
                </div>
                <div class="form-group">
                    <img src="IMG/senha.png" class="icon" alt="senha">
                    <input type="password" id="senha" name="senha" placeholder="" required>
                    <label for="senha" class="input-label">Senha</label>
                </div>
                <div class="form-group">
                    <img src="IMG/cargo.png" class="icon" alt="cargo">
                    <select id="cargo" name="cargo" required>
                        <option value="" disabled selected>Selecione seu cargo</option>
                        <option value="adm">Administrador</option>
                        <option value="prof">Professor</option>
                        <option value="aluno">Aluno</option>
                    </select>
                </div>
                <!-- Campos adicionais para RM, número de identificação e curso -->
                <div class="form-group1" id="rm-container" style="display: none; top: -45px;">
                    <img src="IMG/num_ident.png" class="identi" alt="identi">
                    <input type="text" id="rm" name="rm" placeholder="">
                    <label for="rm" class="input-labelRM">RM</label>
                </div>
                <div class="form-group1" id="numero-identificacao-container" style="display: none;">
                    <img src="IMG/num_ident.png" class="identi" alt="identi">
                    <input type="text" id="numero_identificacao" name="numero_identificacao" placeholder="">
                    <label for="numero_identificacao" class="input-labelRM">Nº de Identificação</label>
                </div>
                <div class="form-group1" id="curso-container" style="display: none; position: relative; top: -65px;">
                    <img src="IMG/curso.png" class="curso" alt="curso">
                    <select id="curso" name="curso" required>
                        <option value="" disabled selected>Selecione seu curso</option>
                        <option value="ADM">Administração</option>
                        <option value="AUT">Automação Industrial</option>
                        <option value="DS">Desenvolvimento de Sistemas</option>
                    </select>
                </div>

                <!-- Mensagem de erro -->
                <div id="error-message" class="error-message <?php echo $error_message ? 'show' : ''; ?>">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>

                <div class="form-group">
                    <input type="submit" id="cadastrar-btn" value="Cadastrar">
                </div>
            </section>
        </form>
    </div>

    <div id="notification" class="notification" style="display: none;">
        <button class="close-button" onclick="closeNotification()">x</button>
        <div class="notification-content">
            <h3>TERMOS DE PRIVACIDADE E SEGURANÇA SCAN VIEW</h3>
            <p>
                Ao utilizar a plataforma Scan View, você concorda com os seguintes termos de segurança e privacidade:
                <ul>
                    <li>Seus dados pessoais são armazenados de forma segura e são acessíveis somente por você e pela equipe autorizada.</li>
                    <li>Os dados coletados são utilizados exclusivamente para fins administrativos e acadêmicos, de acordo com as leis vigentes.</li>
                    <li>A plataforma se compromete a proteger suas informações contra acesso não autorizado e vazamentos.</li>
                    <li>Você tem o direito de acessar, corrigir ou excluir suas informações pessoais a qualquer momento.</li>
                </ul>
            </p>
            <label>
                <input type="checkbox" id="accept-checkbox"> Li e aceito os termos de segurança e privacidade.
            </label>
            <button id="accept-button" disabled>Confirmar</button>
        </div>
    </div>

    <script src="scriptCad.js"></script>
</body>
</html>

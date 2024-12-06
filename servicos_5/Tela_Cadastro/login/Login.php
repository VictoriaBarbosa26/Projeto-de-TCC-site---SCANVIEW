<?php
session_start(); // Inicia a sessão

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

// Inicializa as variáveis de mensagem de erro e sucesso
$error_message = '';
$success_message = '';

// Checar se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    // Capturar dados do formulário de login
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Consultar os dados do usuário com base no email
    $stmt = $conn->prepare("
        SELECT 'Aluno' AS tipo, ID_Aluno AS ID, Email, Senha, RM, NULL AS Numero_Identificacao FROM Alunos WHERE Email = ? 
        UNION 
        SELECT 'Administrador' AS tipo, ID_Adm AS ID, Email, Senha, NULL AS RM, Numero_Identificacao FROM Administradores WHERE Email = ? 
        UNION 
        SELECT 'Professor' AS tipo, ID_Prof AS ID, Email, Senha, NULL AS RM, Numero_Identificacao FROM Professor WHERE Email = ?
    ");
    $stmt->bind_param("sss", $email, $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verificar a senha
        if ($user['Senha'] == $senha) {
            // Armazenar o ID do aluno, administrador ou professor na sessão
            if ($user['tipo'] == 'Aluno') {
                $_SESSION['aluno_id'] = $user['ID']; // Armazenar o ID do aluno
                $_SESSION['usuario_email'] = $email; // Armazenar o email do aluno
                $redirect_url = "../Telas_Aluno/index.php";
            } elseif ($user['tipo'] == 'Administrador') {
                $_SESSION['adm_id'] = $user['ID']; // Armazenar o ID do administrador
                $_SESSION['usuario_email'] = $email; // Armazenar o email do administrador
                $redirect_url = "../Telas_Adm/index.php";
            } elseif ($user['tipo'] == 'Professor') {
                $_SESSION['prof_id'] = $user['ID']; // Armazenar o ID do professor
                $_SESSION['usuario_email'] = $email; // Armazenar o email do professor
                $redirect_url = "../Telas_Prof/index.php";
            }

            // Redirecionar para a página apropriada
            header("Location: $redirect_url");
            exit();
        } else {
            $error_message = "Senha incorreta.";
        }
    } else {
        $error_message = "Email não encontrado.";
    }
}

// Checar se o formulário de recuperação de senha foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["recover"])) {
  // Captura os dados do formulário de recuperação
  $email_recuperacao = $_POST["email"];
  $nova_senha = $_POST["nova_senha"];
  $confirmar_senha = $_POST["confirmar_senha"];
  
  // Verifica se as senhas coincidem
  if ($nova_senha == $confirmar_senha) {
      
      // Atualiza a senha no banco de dados para Alunos (sem criptografia)
      $stmt = $conn->prepare("UPDATE Alunos SET Senha = ? WHERE Email = ?");
      $stmt->bind_param("ss", $nova_senha, $email_recuperacao);
      if ($stmt->execute()) {
          $success_message = "Senha alterada com sucesso!";
      } else {
          $error_message = "Erro ao alterar a senha para Aluno. Tente novamente.";
      }

      // Atualiza a senha no banco de dados para Administradores (sem criptografia)
      $stmt = $conn->prepare("UPDATE Administradores SET Senha = ? WHERE Email = ?");
      $stmt->bind_param("ss", $nova_senha, $email_recuperacao);
      if ($stmt->execute()) {
          $success_message = "Senha alterada com sucesso!";
      } else {
          $error_message = "Erro ao alterar a senha para Administrador. Tente novamente.";
      }

      // Atualiza a senha no banco de dados para Professores (sem criptografia)
      $stmt = $conn->prepare("UPDATE Professor SET Senha = ? WHERE Email = ?");
      $stmt->bind_param("ss", $nova_senha, $email_recuperacao);
      if ($stmt->execute()) {
          $success_message = "Senha alterada com sucesso!";
      } else {
          $error_message = "Erro ao alterar a senha para Professor. Tente novamente.";
      }

  } else {
      $error_message = "As senhas não coincidem.";
  }
}

// Fechar conexão
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css"> 
  <style>
      /* Estilos adicionais para o formulário de recuperação */
      #recover-form {
        display: none; /* Começa oculto */
        position: fixed; /* Fixar na tela */
        top: 50%; /* Centraliza verticalmente */
        left: 50%; /* Centraliza horizontalmente */
        transform: translate(-50%, -50%); /* Ajuste final para centralização */
        width: 100%;
        max-width: 400px; /* Define um tamanho máximo para o formulário */
        padding: 20px;
        background-image: linear-gradient(to bottom, #000000, #003185, #000000); /* Degradê de preto para azul */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        z-index: 10000; 
        border: 1px solid white; /* Borda branca ao redor do contêiner */
      }

      /* Opcional: Para cobrir o fundo e dar o efeito de modal */
      #recover-form-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente */
        z-index: 999; /* Fica atrás do formulário */
      }
      
      .alterar-senha-button {
        background-color: #66CAFF; /* Altere para a cor desejada */
        color: white; /* Cor do texto */
        border: none; /* Remover borda */
        padding: 10px 20px; /* Tamanho do botão */
        font-size: 16px; /* Tamanho da fonte */
        border-radius: 5px; /* Bordas arredondadas */
        cursor: pointer; /* Cursor de clique */
        transition: background-color 0.3s ease; /* Transição suave para a mudança de cor */
      }

      .alterar-senha-button:hover {
        background-color: #85857B; /* Cor ao passar o mouse (hover) */
      }

      .container {
        background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 50, 133, 0.7), rgba(0, 0, 0, 0.7)); /* Degradê de preto para azul com transparência */
        border: 1px solid white; /* Borda branca ao redor do contêiner */
        padding: 100px;
        position: absolute;
        border-radius: 10px;
        width: 300px;
        height: 300px;
        text-align: center;
        color: white;
      }


  </style>
</head>
<body>
  <span class="logo"><img src="IMG/logo.png" alt="Logo"></span>
  <div class="container">
    <h1>LOGIN</h1>
    <form action="login.php" method="post">
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="Digite seu email" value="" required> <!-- Não persiste o email -->
    <span class="icon"><img src="IMG/email.png" alt="Envelope"></span>
  </div>
  <div class="form-group">
    <label for="password">Senha:</label>
    <input type="password" id="password" name="senha" placeholder="Digite sua senha" value="" required> <!-- Não persiste a senha -->
    <span class="icon"><img src="IMG/senha.png" alt="Cadeado"></span>
  </div>
  <!-- Mensagem de erro exibida aqui -->
  <div id="error-message" class="error-message" style="<?php echo $error_message ? 'display: block;' : 'display: none;'; ?>">
    <?php echo htmlspecialchars($error_message); ?>
  </div>
  <div id="success-message" class="success-message" style="<?php echo $success_message ? 'display: block;' : 'display: none;'; ?>">
    <?php echo htmlspecialchars($success_message); ?>
  </div>
  <button type="submit" name="login">Entrar</button>
</form>

    <div class="forgot">
      <a href="javascript:void(0);" onclick="showRecoverForm()">Esqueceu sua senha?</a>
    </div>
    <div class="create-account">
      <a href="../cadastro.php">Criar Conta</a>
    </div>

    <!-- Overlay para o fundo -->
    <div id="recover-form-overlay" style="display: none;"></div>

    <!-- Formulário de recuperação de senha -->
    <div id="recover-form">
      <h2>Recuperar Senha</h2>
      <form action="login.php" method="post">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" placeholder="Digite seu email" required>
        </div>
        <div class="form-group">
          <label for="nova_senha">Nova Senha:</label>
          <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" required>
        </div>
        <div class="form-group">
          <label for="confirmar_senha">Confirmar Nova Senha:</label>
          <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua nova senha" required>
        </div>
        <div id="error-message-recover" class="error-message" style="<?php echo $error_message ? 'display: block;' : 'display: none;'; ?>">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
        <button type="submit" name="recover" class="alterar-senha-button">Alterar Senha</button>
      </form>
    </div>
  </div>

  <script>
    function showRecoverForm() {
      document.getElementById("recover-form").style.display = "block";
      document.getElementById("recover-form-overlay").style.display = "block";
    }

    // Fecha o formulário de recuperação ao clicar no overlay
    document.getElementById("recover-form-overlay").onclick = function() {
      document.getElementById("recover-form").style.display = "none";
      document.getElementById("recover-form-overlay").style.display = "none";
    };
  </script>
</body>
</html>
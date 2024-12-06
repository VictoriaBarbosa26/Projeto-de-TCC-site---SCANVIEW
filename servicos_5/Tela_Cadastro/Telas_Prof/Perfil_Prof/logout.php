<?php
session_start();
// Limpa todas as variáveis da sessão
$_SESSION = array();

// Se você quiser destruir a sessão também, faça isso:
session_destroy();

// Redireciona para a página de login
header("Location: ../../login/Login.php");
exit();
?>

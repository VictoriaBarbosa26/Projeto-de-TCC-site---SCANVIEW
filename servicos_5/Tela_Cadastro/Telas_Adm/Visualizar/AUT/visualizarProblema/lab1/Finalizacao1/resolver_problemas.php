<?php
// Conexão com o banco de dados
$conexao = new mysqli("localhost", "root", "", "ScanView");

if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}

// Verifica se problemas foram marcados como resolvidos
if (isset($_POST['problemas'])) {
    $problemas_resolvidos = json_decode($_POST['problemas']);
    $descricao_solucao = $_POST['descricao_solucao'];
    $data_solucao = $_POST['data_solucao'];

    $ids_resolvidos = [];

    foreach ($problemas_resolvidos as $id_problema) {
        // Insere a descrição e a data na tabela Solucoes
        $sql = "INSERT INTO Solucoes (ID_Problema, Descricao, DataResolucao) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iss", $id_problema, $descricao_solucao, $data_solucao);
        if ($stmt->execute()) {
            $ids_resolvidos[] = $id_problema; // Adiciona o ID à lista de resolvidos
        }
    }

    // Retorna os IDs dos problemas resolvidos como JSON
    echo json_encode($ids_resolvidos);
} else {
    echo json_encode([]);
}

$conexao->close();
?>

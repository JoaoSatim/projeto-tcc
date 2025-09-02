<?php
require_once '../conexaohost/conexao.php';
session_start();

if(!isset($_GET['id_venda'])){
    die("ID da venda não informado.");
}

$id_venda = intval($_GET['id_venda']);

// Atualizar status para 'aprovada'
$sql = "UPDATE vendas SET status = 'aprovada' WHERE id_venda = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_venda);

if($stmt->execute()){
    echo "Venda aprovada com sucesso!";
}else{
    echo "Erro ao aprovar venda: " . $conn->error;
}
?>

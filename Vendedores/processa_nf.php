<?php
require_once '../conexaohost/conexao.php';
session_start();

// Receber dados da venda
$numero_venda = $_POST['numero_venda'];
$nome = $_POST['nome'];
$tipo_cpf_cnpj = $_POST['tipo_cpf_cnpj'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$cep = $_POST['cep'];
$responsavel = $_POST['responsavel_entrega'];

// Iniciar transação
$conn->begin_transaction();

try {
    // Inserir venda
    $sqlVenda = "INSERT INTO vendas (numero_venda, nome, tipo_cpf_cnpj, telefone, endereco, cep, responsavel_entrega)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlVenda);
    $stmt->bind_param("issssss", $numero_venda, $nome, $tipo_cpf_cnpj, $telefone, $endereco, $cep, $responsavel);
    $stmt->execute();
    $idVenda = $stmt->insert_id;

    // Inserir itens
    $produtos = $_POST['produto'];
    $quantidades = $_POST['quantidade'];
    $unidades = $_POST['unidade'];
    $tipos = $_POST['tipo'];
    $valoresUnit = $_POST['valor_unitario'];
    $valoresTot = $_POST['valor_total'];

    $sqlItem = "INSERT INTO itens_venda (id_venda, id_produto, quantidade, unidade, tipo, valor_unitario, valor_total)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);

    for ($i = 0; $i < count($produtos); $i++) {
        $idProduto = $produtos[$i];
        $qtd = $quantidades[$i];
        $unidade = $unidades[$i];
        $tipo = $tipos[$i];
        $valorUnit = $valoresUnit[$i];
        $valorTot = $valoresTot[$i];

        $stmtItem->bind_param("iidssdd", $idVenda, $idProduto, $qtd, $unidade, $tipo, $valorUnit, $valorTot);
        $stmtItem->execute();
    }

    // Confirmar
    $conn->commit();

    echo "<script>alert('Venda salva com sucesso!'); window.location.href='vendas_sacaria.php';</script>";

} catch (Exception $e) {
    $conn->rollback();
    die("Erro ao salvar venda: " . $e->getMessage());
}

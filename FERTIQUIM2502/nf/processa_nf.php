<?php
$conn = new mysqli("localhost", "root", "", "fertiquim1");

// Verifica conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Recebe os dados da NF
$numero_nf = $_POST['numero_nf'];
$nome_fantasia = $_POST['nome_fantasia'];
$cnpj = $_POST['cnpj'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$cep = $_POST['cep'];
$responsavel_entrega = $_POST['responsavel_entrega'];
$cpf_responsavel = $_POST['cpf_responsavel'];

// Insere a nota fiscal na tabela nf_pendente
$stmt = $conn->prepare("INSERT INTO nf_pendente 
    (numero_nf, nome_fantasia, cnpj, telefone, endereco, cep, responsavel_entrega, cpf_responsavel) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssss", $numero_nf, $nome_fantasia, $cnpj, $telefone, $endereco, $cep, $responsavel_entrega, $cpf_responsavel);

if (!$stmt->execute()) {
    die("Erro ao salvar a NF: " . $stmt->error);
}

$nf_id = $stmt->insert_id; // pega o ID da NF inserida
$stmt->close();

// Fertilizantes recebidos
$nomes = $_POST['fertilizante_nome'];
$quantidades = $_POST['fertilizante_quantidade'];
$unidades = $_POST['fertilizante_unidade'];

// Insere fertilizantes na tabela fertilizantes_pendentes
$stmt_fert = $conn->prepare("INSERT INTO fertilizantes_pendentes (nf_id, nome, quantidade, unidade) VALUES (?, ?, ?, ?)");

foreach ($nomes as $index => $nome) {
    $quantidade = $quantidades[$index];
    $unidade = $unidades[$index];

    $stmt_fert->bind_param("isds", $nf_id, $nome, $quantidade, $unidade);
    if (!$stmt_fert->execute()) {
        die("Erro ao inserir fertilizante: " . $stmt_fert->error);
    }
}

$stmt_fert->close();
$conn->close();

// Redireciona de volta para a página de NF pendente
header("Location: pendente.php");
exit;
?>

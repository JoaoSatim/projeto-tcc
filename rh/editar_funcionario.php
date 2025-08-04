<?php
$conn = new mysqli("localhost", "root", "", "fertiquim");
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

// Verifica se veio via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit("Acesso proibido");
}

// Verifica se todos os dados existem antes de usar
$campos_necessarios = ['id', 'cpf', 'data_nascimento', 'endereco', 'numero_casa', 'cep', 'uf', 'salario'];
foreach ($campos_necessarios as $campo) {
    if (!isset($_POST[$campo])) {
        http_response_code(400);
        exit("Erro: Campo '$campo' não recebido");
    }
}

// Pega os dados
$id = $_POST['id'];
$cpf = $_POST['cpf'];
$data = $_POST['data_nascimento'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero_casa'];
$cep = $_POST['cep'];
$uf = $_POST['uf'];
$salario = $_POST['salario'];

// Atualiza no banco
$sql = "UPDATE cadastro_funcionario SET 
    cpf = ?, 
    data_nascimento = ?, 
    endereco = ?, 
    numero_casa = ?, 
    cep = ?, 
    uf = ?, 
    salario = ? 
    WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssdi", $cpf, $data, $endereco, $numero, $cep, $uf, $salario, $id);
$stmt->execute();

echo $stmt->affected_rows > 0 ? "sucesso" : "erro";
?>
    
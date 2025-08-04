<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fertiquim1';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
}
$cadastro_sucesso = false;

if (isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];
    $usuario = $_POST['usuario'];
    $matricula = $_POST ['matricula'];
    $funcao = $_POST['funcao'];

    $result = mysqli_query($conn, "INSERT INTO usuarios (nome, usuario, senha, matricula, funcao) VALUES ('$nome', '$usuario', '$senha', ''$matricula, '$funcao')");

    if ($result) {
        $cadastro_sucesso = true;
    } else {
        echo "<script>alert('Erro ao cadastrar: " . mysqli_error($conexao) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Funcionário</title>
    <link rel="stylesheet" href="../css/estilologin.css">
</head>
<body>
    <div class="login-container">
        <h2>Registrar Funcionário</h2>
        <form action="" method="POST">
            <label for="matricula">Nº Matricula</label>
            <input type="text" id="matricula" name="matricula" required>

            <label for="funcao">Função</label>
            <input type="text" id="funcao" name="funcao" required>
        
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required>

            <label for="usuario">Usuário</label>
            <input type="text" id="usuario" name="usuario" required>
            
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            
            
            <input type="submit" name="submit" id="submit" value="Registrar">
        </form>

        <p style="margin-top: 15px;">
            <a href="../pglogin/pglogin.php">← Voltar para a página inicial</a>
        </p>
    </div>

    <?php if ($cadastro_sucesso): ?>
        <script>
            alert("Usuário cadastrado com sucesso!");
        </script>
    <?php endif; ?>

    <script src="script.js"></script>
</body>
</html>
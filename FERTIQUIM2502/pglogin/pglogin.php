<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fertiquim';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['username'];
    $senha = $_POST['password'];
    $matricula = $_POST['matricula'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND matricula = ?");
    $stmt->bind_param("ss", $usuario, $matricula);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $dados = $resultado->fetch_assoc();

        // Comparação simples (ideal: usar password_hash no futuro)
        if ($senha === $dados['senha']) {
            $_SESSION['usuario'] = $dados['nome'];

            // Redireciona conforme matrícula
            if ($dados['matricula'] == '2501') {
                header("Location: ../../pginicial/pginicial.php");
                exit;
            } elseif ($dados['matricula'] == '2502') {
                header("Location: ../pginicial/pginicial.php");
                exit;
            } elseif ($dados['matricula'] == '1707') {
                header("Location: pagina1707.php");
                exit;
            } else {
                // Se quiser pode deixar uma página padrão ou mensagem de erro
                header("Location: ../pginicial/pginicial.php");
                exit;
            }
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário ou matrícula não encontrados.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/estilologin.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
        <form method="POST" action="">
            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" name="matricula" required>

            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>

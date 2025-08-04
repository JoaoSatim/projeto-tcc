<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fertiquim1';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

// Consulta o estoque
$result = $conn->query("SELECT * FROM estoque_fertilizantes ORDER BY data_atualizacao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualizar Estoque - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
  <header>
    <h1>FERTIQUIM Fertilizantes</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="../estoque/estoque.php">Estoque</a>
      <a href="#">Orçamentos</a>
      <a href="#">Lançamentos</a>
      <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
  </header>

  <div class="container">
    <h2 class="titulo">Estoque Atual</h2>
    <table class="tabela">
      <thead>
        <tr>
          <th>Nome do Produto</th>
          <th>Quantidade</th>
          <th>Unidade</th>
          <th>Atualizado em</th>
          <th>Usuário</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['nome_produto']; ?></td>
            <td><?php echo $row['quantidade']; ?></td>
            <td><?php echo $row['unidade']; ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?></td>
            <td><?php echo $row['usuario']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <footer>
    &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
  </footer>
</body>
</html>

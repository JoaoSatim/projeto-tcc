<?php
session_start(); // Inicia a sessão para acessar o nome do usuário

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fertiquim1';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die('Erro de conexão: ' . $conn->connect_error);
}


if (isset($_POST['adicionar'])) {
  $nome = $_POST['nome'];
  $quantidade = $_POST['quantidade'];
  $unidade = $_POST['unidade'];
  $usuario = $_SESSION['usuario'];

  $conn->query("INSERT INTO estoque_fertilizantes (nome_produto, quantidade, unidade, data_atualizacao, usuario) 
                VALUES ('$nome', $quantidade, '$unidade', NOW(), '$usuario')");
}

if (isset($_GET['remover'])) {
  $id = $_GET['remover'];
  $conn->query("DELETE FROM estoque_fertilizantes WHERE id = $id");
}

$result = $conn->query("SELECT * FROM estoque_fertilizantes ORDER BY data_atualizacao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estoque - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
  <header>
    <h1>Fertiquim - Estoque de Fertilizantes</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="../nf/inserir.php">Inserir NF's</a>
      <a href="../nf/consultar.php">Consultar NF's</a>
      <a href="#">Nf's Pendente</a>

    </nav>
  </header>

  <div class="container">
    <h2 class="titulo">Adicionar Fertilizante</h2>
    <form method="post" class="formulario">
      <input type="text" name="nome" placeholder="Nome do fertilizante" required>
      <input type="number" name="quantidade" placeholder="Quantidade" required step="0.01">
      <input type="text" name="unidade" placeholder="Unidade (ex: kg, L)" required>
      <button type="submit" name="adicionar">Adicionar</button>
    </form>

    <h2 class="titulo">Estoque Atual</h2>
    <table class="tabela">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Quantidade</th>
          <th>Unidade</th>
          <th>Atualizado em</th>
          <th>Usuário</th>
          <th>Ações</th>
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
            <td>
              <a href="?remover=<?php echo $row['id']; ?>" onclick="return confirm('Remover este fertilizante?')">Remover</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <footer>
    &copy; 2025 Fertiquim Fertilizantes
  </footer>
</body>
</html>

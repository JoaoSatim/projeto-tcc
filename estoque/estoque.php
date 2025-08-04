<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fertiquim';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die('Erro de conexão: ' . $conn->connect_error);
}

if (isset($_POST['adicionar'])) {
  $nome = $_POST['nome'];
  $quantidade = $_POST['quantidade'];
  $unidade = $_POST['unidade'];
  $tipo = $_POST['tipo'];
  $usuario = $_SESSION['usuario'];

  $conn->query("INSERT INTO estoque_fertilizantes (nome_produto, quantidade, unidade, tipo, data_atualizacao, usuario) 
                VALUES ('$nome', $quantidade, '$unidade', '$tipo', NOW(), '$usuario')");
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
      <a href="../inventario/inv.php">Inventário</a>
      <a href="../estoque/estoque.php">Controle</a>
      <a href="../nf/inserir.php">Inserir NF's</a>
      <a href="../nf/consultar.php">Consultar NF's</a>
      <a href="../nf/pendente.php">NF's Pendente</a>  
      <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
  </header>

  <div class="container">
    <h2 class="titulo">Adicionar Material Manualmente</h2>
    <form method="post" class="formulario">
      <input type="text" name="nome" placeholder="Nome do Material" required>
      <input type="number" name="quantidade" placeholder="Quantidade" required step="0.01">
      <input type="text" name="unidade" placeholder="Unidade (ex: kg, L)" required>
      
      <select name="tipo" required>
                <option value="">Selecione</option>
                <option value="Material de informática">Material de informática</option>
                <option value="Ferramentas">Ferramentas</option>
                <option value="Matéria-prima">Matéria-prima</option>
                <option value="Material de Escritorio">Material de Escritório</option>
                <option value="EPI">EPi's</option>
      </select>

      <button type="submit" name="adicionar">Adicionar</button>
    </form>

    <h2 class="titulo">Estoque Atual</h2>
    <table class="tabela">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Quantidade</th>
          <th>Unidade</th>
          <th>Tipo</th>
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
            <td><?php echo $row['tipo']; ?></td>
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

  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
</body>
</html>

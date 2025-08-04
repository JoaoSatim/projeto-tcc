<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

// Conexão com o banco
$conn = new mysqli("localhost", "root", "", "fertiquim");
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

// Processamento do formulário
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funcionario_id = $_POST['funcionario_id'];
    $item_id = $_POST['item_id'];
    $quantidade = intval($_POST['quantidade']);

    $res = $conn->query("SELECT quantidade FROM estoque_fertilizantes WHERE id = $item_id");
    $estoque = $res->fetch_assoc();

    if ($estoque && $estoque['quantidade'] >= $quantidade) {
        $conn->query("INSERT INTO inventario_funcionario (funcionario_id, item_id, quantidade) VALUES ($funcionario_id, $item_id, $quantidade)");
        $conn->query("UPDATE estoque_fertilizantes SET quantidade = quantidade - $quantidade WHERE id = $item_id");
        $mensagem = "<p style='color:green;'>Item entregue com sucesso!</p>";
    } else {
        $mensagem = "<p style='color:red;'>Quantidade insuficiente no estoque.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventário de Funcionário - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .form-entrega {
      max-width: 600px;
      background: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .form-entrega label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    .form-entrega select,
    .form-entrega input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
    }
    .form-entrega button {
      padding: 10px 15px;
    }
    .mensagem {
      margin: 10px 0;
      font-weight: bold;
    }
  </style>
</head>
<body>

<header>
  <h1>FERTIQUIM Fertilizantes</h1>
  <nav>
    <a href="../pginicial/pginicial.php">Início</a>
    <a href="inv.php">Inventário</a>
    <a href="../estoque/estoque.php">Controle</a>
    <a href="../nf/inserir.php">Inserir NF's</a>
    <a href="../nf/consultar.php">Consultar NF's</a>
    <a href="../nf/pendente.php">NF's Pendente</a>  
    <a href="../pglogin/pglogin.php">Sair</a>
  </nav>
</header>

<div class="container">
  <h2 class="titulo">Entrega de Itens para Funcionário</h2>

  <?php if (!empty($mensagem)) echo "<div class='mensagem'>$mensagem</div>"; ?>

  <form method="POST" class="form-entrega">
    <label for="funcionario_id">Funcionário:</label>
    <select name="funcionario_id" required>
      <option value="">-- Selecione --</option>
      <?php
      $funcs = $conn->query("SELECT id, nome FROM cadastro_funcionario");
      while ($f = $funcs->fetch_assoc()):
      ?>
        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="item_id">Item do Estoque:</label>
    <select name="item_id" required>
      <option value="">-- Selecione --</option>
      <?php
      $items = $conn->query("SELECT id, nome_produto, quantidade FROM estoque_fertilizantes");
      while ($i = $items->fetch_assoc()):
      ?>
        <option value="<?= $i['id'] ?>">
          <?= htmlspecialchars($i['nome_produto']) ?> (<?= $i['quantidade'] ?> disponíveis)
        </option>
      <?php endwhile; ?>
    </select>

    <label for="quantidade">Quantidade a Entregar:</label>
    <input type="number" name="quantidade" min="1" required>

    <button type="submit">Entregar</button>
  </form>
</div>

<footer>
  &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
</footer>
  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
</body>
</html>

<?php
require_once '../conexaohost/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

$funcionario_id = $_GET['funcionario_id'] ?? null;
$funcionarios = $conn->query("SELECT id, nome FROM cadastro_funcionario");

$itens_inventario = [];

if ($funcionario_id) {
    $stmt = $conn->prepare("
        SELECT ef.nome_produto, inv.quantidade, inv.data_entrega 
        FROM inventario_funcionario AS inv
        JOIN estoque_fertilizantes AS ef ON inv.item_id = ef.id
        WHERE inv.funcionario_id = ?
        ORDER BY inv.data_entrega DESC
    ");
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $itens_inventario = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Consulta de Inventário - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .form-consulta {
      max-width: 600px;
      margin-bottom: 30px;
    }
    .form-consulta label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    .form-consulta select {
      width: 100%;
      padding: 8px;
    }
    .form-consulta button {
      margin-top: 10px;
      padding: 8px 12px;
    }
    .tabela {
      width: 100%;
      border-collapse: collapse;
    }
    .tabela th, .tabela td {
      border: 1px solid #ccc;
      padding: 8px;
    }
  </style>
</head>
<body>

<?php include '../base/estoque.php'; ?>

<div class="container">
  <h2 class="titulo">Consulta de Inventário por Funcionário</h2>

  <form method="GET" class="form-consulta">
    <label for="funcionario_id">Selecione o Funcionário:</label>
    <select name="funcionario_id" required>
      <option value="">-- Escolha --</option>
      <?php while ($f = $funcionarios->fetch_assoc()): ?>
        <option value="<?= $f['id'] ?>" <?= ($funcionario_id == $f['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($f['nome']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Consultar</button>
  </form>

  <?php if ($funcionario_id): ?>
    <?php if ($itens_inventario->num_rows > 0): ?>
      <table class="tabela">
        <thead>
          <tr>
            <th>Item</th>
            <th>Quantidade</th>
            <th>Data da Entrega</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $itens_inventario->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['nome_produto']) ?></td>
              <td><?= htmlspecialchars($row['quantidade']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($row['data_entrega'])) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Nenhum item encontrado para este funcionário.</p>
    <?php endif; ?>
  <?php endif; ?>
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

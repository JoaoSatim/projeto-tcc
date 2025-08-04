<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "fertiquim");
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

$funcionario_id = $_GET['funcionario_id'] ?? null;
$funcionarios = $conn->query("SELECT id, nome FROM cadastro_funcionario");

// Editar inventário e atualizar estoque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
    $id = intval($_POST['editar_id']);
    $nova_qtd = intval($_POST['quantidade']);
    $justificativa = $conn->real_escape_string($_POST['justificativa']);
    $usuario = $_SESSION['usuario'];

    // Buscar quantidade atual e item_id
    $res = $conn->query("SELECT quantidade, item_id FROM inventario_funcionario WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $dados = $res->fetch_assoc();
        $qtd_atual = $dados['quantidade'];
        $item_id = $dados['item_id'];

        $diferenca = $nova_qtd - $qtd_atual;

        // Atualizar o estoque com base na diferença
        $conn->query("
            UPDATE estoque_fertilizantes 
            SET quantidade = quantidade - $diferenca
            WHERE id = $item_id
        ");

        // Atualizar inventário com justificativa
        $conn->query("
            UPDATE inventario_funcionario 
            SET 
                quantidade = $nova_qtd,
                justificativa = '$justificativa',
                usuario_responsavel = '$usuario',
                data_alteracao = NOW()
            WHERE id = $id
        ");
    }
}

// Remover item do inventário e retornar quantidade ao estoque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_id'])) {
    $id = intval($_POST['remover_id']);

        // Remove do inventário
        $conn->query("DELETE FROM inventario_funcionario WHERE id = $id");
    }

// Consulta inventário do funcionário
$itens_inventario = [];
if ($funcionario_id) {
    $stmt = $conn->prepare("
        SELECT inv.id, inv.quantidade, inv.data_entrega, ef.nome_produto 
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
    .form-inline input[type="number"] {
      width: 60px;
    }
    .form-inline textarea {
      width: 100%;
      height: 50px;
    }
    .form-inline button {
      margin-right: 5px;
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
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $itens_inventario->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['nome_produto']) ?></td>
              <td>
                <form method="POST" class="form-inline">
                  <input type="hidden" name="editar_id" value="<?= $row['id'] ?>">
                  <input type="number" name="quantidade" value="<?= $row['quantidade'] ?>" required>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($row['data_entrega'])) ?></td>
              <td>
                  <textarea name="justificativa" placeholder="Justificativa" required></textarea>
                  <button type="submit">Salvar</button>
                </form>
                <form method="POST" onsubmit="return confirm('Tem certeza que deseja remover este item?');" style="display:inline;">
                  <input type="hidden" name="remover_id" value="<?= $row['id'] ?>">
                  <button type="submit" style="background-color:red;color:white;">Remover</button>
                </form>
              </td>
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

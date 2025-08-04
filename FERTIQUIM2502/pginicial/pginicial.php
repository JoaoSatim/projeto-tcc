<?php
// Conexão com banco
$host = "localhost";
$user = "root";
$pass = "";
$db = "fertiquim1";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$sql = "SELECT SUM(quantidade) AS total_estoque FROM estoque_fertilizantes";
$result = $conn->query($sql);

$totalEstoque = 0;
if ($result && $row = $result->fetch_assoc()) {
    $totalEstoque = $row['total_estoque'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fertiquim - Controle de Estoque</title>
  <link rel="stylesheet" href="../css/estilo.css" />
</head>
<body>
  <header>
    <h1>FERTIQUIM Fertilizantes</h1>
    <nav>
      <a href="#" onclick="navigateTo('index.php')">Início</a>
      <a href="../estoque/estoque.php">Estoque</a>
      <a href="#">Orçamentos</a>
      <a href="#">Lançamentos</a>
      <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
  </header>

  <div class="container">
    <h2 class="titulo">Visão Geral do Estoque</h2>
    <div class="cards">
      <div class="card">
        <h2>Estoque Total</h2>
        <p><?php echo number_format($totalEstoque, 2, ',', '.'); ?> T</p>
      </div>
      <div class="card">
        <h2>Entradas no mês</h2>
        <p>******</p>
      </div>
      <div class="card">
        <h2>Saídas no mês</h2>
        <p>******</p>
      </div>
    </div>

    <div class="actions">
      <a href="../estoque/estoque.php">+ Cadastrar Fertilizante</a>
      <a href="../estoque/estoquevisu.php">📦 Ver Estoque</a>
      <a href="../nf/inserir.php">➕ Inserir NF's</a>
      <a href="../nf/consultar.php">🔍 Consultar NF</a>
    </div>
  </div>

  <footer>
    &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
  </footer>
</body>
</html>

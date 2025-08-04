<?php
// Se for usar algo dinâmico, mantenha o PHP, se não, pode tirar
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fertiquim - Sistema</title>
  <link rel="stylesheet" href="../css/estilo.css" />
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
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
      <a href="inserir.php" class="card inserirmaterial-card">
        <h2></h2>
      </a>
      <a href="consultar.php" class="card consultarinventario-card">
        <h2></h2>
      </a>
      <a href="editar.php" class="card editarinventario-card">
        <h2></h2>
      </a>

    </div>
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

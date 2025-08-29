<?php

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
  <?php include '../base/cabecalho.php'; ?>
  <div class="container">
    <h2 class="titulo">Painel Principal</h2>
    <div class="cards">
      <a href="../manutencao/manutencao.php"" class="card VendasInternas-card">
        <h2></h2>
      </a>
      <a href="vendas_sacarias.php" class="card VendasExternas-card">
        <h2></h2>
      </a>
        
      <a href="../manutencao/manutencao.php" class="card Clientes-card">
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

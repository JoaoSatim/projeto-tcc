<?php
require_once '../conexaohost/conexao.php';
include '../base/cabecalho.php';

// Buscar clientes
$sql = "SELECT id, tipo_pessoa, nome_razao, cpf_cnpj, rg_ie, data_nascimento, telefone, celular, email, endereco, numero, complemento 
        FROM clientes ORDER BY nome_razao ASC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Consulta de Clientes</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .container { max-width: 900px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f5f5f5; }
    tr.details { display: none; background: #fafafa; }
    .btn {
      display: inline-block; padding: 6px 12px; border-radius: 5px;
      text-decoration: none; color: #fff; font-size: 13px; cursor: pointer;
    }
    .btn-edit { background-color: #2196f3; }
    .btn-delete { background-color: #f44336; }
    .btn-expand { background-color: #4caf50; }
  </style>
  <script>
    function toggleDetails(id) {
      const row = document.getElementById('details-' + id);
      if (row.style.display === 'table-row') {
        row.style.display = 'none';
      } else {
        row.style.display = 'table-row';
      }
    }
  </script>
</head>
<body>
<div class="container">
  <h2>Lista de Clientes</h2>
  
  <table>
    <thead>
      <tr>
        <th>Nome/Razão</th>
        <th>CPF/CNPJ</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php if($res && $res->num_rows > 0): ?>
        <?php while($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['nome_razao']); ?></td>
            <td><?php echo $row['cpf_cnpj']; ?></td>
            <td>
              <button class="btn btn-expand" onclick="toggleDetails(<?php echo $row['id']; ?>)">Expandir</button>
            </td>
          </tr>
          <tr class="details" id="details-<?php echo $row['id']; ?>">
            <td colspan="3">
              <strong>Tipo:</strong> <?php echo $row['tipo_pessoa']; ?><br>
              <strong>RG/IE:</strong> <?php echo $row['rg_ie']; ?><br>
              <strong>Data Nasc.:</strong> <?php echo $row['data_nascimento']; ?><br>
              <strong>Telefone:</strong> <?php echo $row['telefone']; ?><br>
              <strong>Celular:</strong> <?php echo $row['celular']; ?><br>
              <strong>Email:</strong> <?php echo $row['email']; ?><br>
              <strong>Endereço:</strong> <?php echo $row['endereco'] . ', ' . $row['numero']; ?><br>
              <strong>Complemento:</strong> <?php echo $row['complemento']; ?><br><br>

              <a href="editar_cliente.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
              <a href="deletar_cliente.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Deseja realmente excluir este cliente?');">Excluir</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3">Nenhum cliente encontrado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>

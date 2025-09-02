<?php
require_once '../conexaohost/conexao.php';
session_start();

if(!isset($_GET['id_venda'])){
    die("ID da venda não informado.");
}

$id_venda = intval($_GET['id_venda']);

// Buscar dados da venda
$sqlVenda = "SELECT * FROM vendas WHERE id_venda = ?";
$stmtVenda = $conn->prepare($sqlVenda);
$stmtVenda->bind_param("i", $id_venda);
$stmtVenda->execute();
$resVenda = $stmtVenda->get_result();
$venda = $resVenda->fetch_assoc();

if(!$venda){
    die("Venda não encontrada.");
}

// Buscar itens da venda
$sqlItens = "SELECT * FROM itens_venda WHERE id_venda = ?";
$stmtItens = $conn->prepare($sqlItens);
$stmtItens->bind_param("i", $id_venda);
$stmtItens->execute();
$resItens = $stmtItens->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Canhoto Venda #<?php echo $venda['numero_venda']; ?></title>
<style>
  body { font-family: Arial, sans-serif; margin:20px; background:#fff; color:#333; }
  .header { display:flex; align-items:center; justify-content:space-between; border-bottom:3px solid #388e3c; padding-bottom:10px; margin-bottom:20px; }
  .header img { height:60px; }
  .empresa-info { text-align:right; }
  .empresa-info h2 { margin:0; color:#388e3c; }
  .empresa-info p { margin:2px 0; font-size:13px; }

  h3 { color:#388e3c; margin-bottom:5px; }
  .cliente-info, .itens, .footer { margin-bottom:20px; }

  table { width:100%; border-collapse:collapse; }
  table th, table td { border:1px solid #388e3c; padding:8px; text-align:center; font-size:14px; }
  table th { background:#388e3c; color:white; }

  .footer { text-align:center; margin-top:30px; font-size:13px; color:#555; }
  .btn-print { padding:10px 15px; background:#388e3c; color:white; border:none; cursor:pointer; border-radius:5px; font-size:14px; margin-top:10px; }
  .btn-print:hover { opacity:0.85; }
</style>
</head>
<body>

<div class="header">
  <img src="../img/logo.jpg" alt="Logo Fertiquim">
  <div class="empresa-info">
    <h2>FERITIQUIM FERTILIZANTES GOIAS LTDA.</h2>
    <p>Bruno OttO Bergold, 450 Centro</p>
    <p>87270-000 Engenheiro Beltrão - PR</p>
    <p>CNPJ: 59.125.844/0001-01 | IE: 91162002-25</p>
  </div>
</div>

<div class="cliente-info">
  <h3>Informações do Cliente</h3>
  <p><strong>Nome:</strong> <?php echo $venda['cliente']; ?></p>
  <p><strong>CPF/CNPJ:</strong> <?php echo $venda['cpf_cnpj']; ?></p>
  <p><strong>Telefone:</strong> <?php echo $venda['telefone']; ?></p>
  <p><strong>Endereço:</strong> <?php echo $venda['endereco']; ?> | CEP: <?php echo $venda['cep']; ?></p>
  <p><strong>Responsável:</strong> <?php echo $venda['responsavel']; ?></p>
  <p><strong>Data da Venda:</strong> <?php echo date("d/m/Y H:i", strtotime($venda['data_venda'])); ?></p>
</div>

<div class="itens">
  <h3>Itens da Venda</h3>
  <table>
    <thead>
      <tr>
        <th>Produto</th>
        <th>Quantidade</th>
        <th>Unidade</th>
        <th>Tipo</th>
        <th>Valor Unitário (R$)</th>
        <th>Total (R$)</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $totalGeral = 0;
      while($item = $resItens->fetch_assoc()):
        $totalGeral += $item['valor_total'];
      ?>
      <tr>
        <td><?php echo $item['produto']; ?></td>
        <td><?php echo $item['quantidade']; ?></td>
        <td><?php echo $item['unidade']; ?></td>
        <td><?php echo $item['tipo']; ?></td>
        <td><?php echo number_format($item['valor_unitario'],2); ?></td>
        <td><?php echo number_format($item['valor_total'],2); ?></td>
      </tr>
      <?php endwhile; ?>
      <tr>
        <td colspan="5" style="text-align:right; font-weight:bold;">TOTAL GERAL (R$)</td>
        <td style="font-weight:bold;"><?php echo number_format($totalGeral,2); ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="footer">
  <p>Canhoto gerado por Fertiquim Fertilizantes Goiás LTDA. – Obrigado pela preferência!</p>
</div>

<button class="btn-print" onclick="window.print()">Imprimir Canhoto</button>
</body>
</html>

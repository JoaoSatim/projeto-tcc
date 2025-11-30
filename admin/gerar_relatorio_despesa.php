<?php
session_start();
require_once '../conexaohost/conexao.php'; 

if (!isset($conn) && !isset($conexao)) {
    die("Erro: conexão com o banco não encontrada. Verifique o arquivo de conexão.");
}
if (isset($conn) && !isset($conexao)) {
    $conexao = $conn;
}

if (!isset($_SESSION['funcao_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

if (!isset($_GET['data_inicio']) || !isset($_GET['data_fim'])) {
    die("Erro: Intervalo de datas não informado.");
}

$data_inicio = $_GET['data_inicio'];
$data_fim = $_GET['data_fim'];

$sql = "SELECT * FROM entradas 
        WHERE data BETWEEN ? AND ?
        ORDER BY data ASC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ss", $data_inicio, $data_fim);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatório de Entradas</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef2f3;
    margin: 0;
    padding: 0;
}
.container {
    width: 95%;
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    padding: 25px 40px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
header {
    text-align: center;
    border-bottom: 2px solid #2e5d2e;
    padding-bottom: 10px;
    margin-bottom: 25px;
}

header p {
    color: #ccc;
    margin: 4px 0;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 15px;
}
th, td {
    padding: 10px 8px;
    border: 1px solid #ccc;
    text-align: center;
}
th {
    background-color: #2e5d2e;
    color: white;
}
tfoot td {
    font-weight: bold;
}
.botoes {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
}
button, a.voltar {
    background-color: #2e5d2e;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    transition: 0.2s;
}
button:hover, a.voltar:hover {
    background-color: #3b7a3b;
}
@media print {
    body {
        background: white;
    }
    .container {
        box-shadow: none;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .botoes, #menu-admin, footer {
        display: none !important;
    }
    table {
        font-size: 13px;
    }
    header {
        border: none;
        margin-bottom: 10px;
    }
}
</style>
</head>
<body>
    <?php include '../base/administrador.php'; ?>
    <div class="container">
        <header>
            <h1>FERTIQUIM SYSTEM</h1>
            <p>Relatório de Despesas</p>
            <p>Período: <?php echo date("d/m/Y", strtotime($data_inicio)); ?> à <?php echo date("d/m/Y", strtotime($data_fim)); ?></p>
            <p>Emitido em: <?php echo date("d/m/Y H:i"); ?></p>
        </header>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Valor (R$)</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>Criado em</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($row['data'])); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($row['criado_em'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">Nenhuma entrada encontrada neste período.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="botoes">
            <button onclick="imprimirRelatorio()">🖨️ Imprimir Relatório</button>
            <a href="despesas.php" class="voltar">⬅️ Voltar</a>
        </div>
    </div>

    <?php include '../base/rodape.php'; ?>

<script>
function imprimirRelatorio() {
    const conteudo = document.querySelector('.container').innerHTML;
    const janela = window.open('', '', 'width=900,height=650');
    janela.document.write(`
        <html>
        <head>
            <title>Relatório de Entradas</title>
            <style>
                body { font-family: Arial; margin: 40px; }
                h1, h2, h3 { color: #2e5d2e; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: center; font-size: 14px; }
                th { background: #2e5d2e; color: white; }
                footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            ${conteudo}
            <footer><p>FERTIQUIM SYSTEM — Relatório gerado automaticamente em ${new Date().toLocaleString()}</p></footer>
            <script>window.print();<\/script>
        </body>
        </html>
    `);
    janela.document.close();
}
</script>
</body>
</html>

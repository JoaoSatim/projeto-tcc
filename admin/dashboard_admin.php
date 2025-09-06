<?php
session_start();
require_once '../conexaohost/conexao.php';

// Bloqueia acesso para não-admin
if (!isset($_SESSION['funcao_usuario']) || 
    (strtolower($_SESSION['funcao_usuario']) !== 'administrador' && strtolower($_SESSION['funcao_usuario']) !== 'proprietário')) {
    header("Location: ../pginicial/pginicial.php");
    exit;
}

// Filtros
$dataInicio = $_POST['data_inicio'] ?? date('Y-m-01');
$dataFim    = $_POST['data_fim'] ?? date('Y-m-t');
$tipoDespesa = $_POST['tipo_despesa'] ?? 'todas';

// Função helper: somas mensais de receitas ou despesas
function somasMensaisPeriodo(mysqli $conn, string $tabela, string $colData, string $colValor, string $inicio, string $fim, string $colTipo = '', $tipo = ''): array {
    $resultado = [];
    $whereTipo = ($colTipo && $tipo && $tipo !== 'todas') ? " AND $colTipo = '$tipo'" : "";
    $sql = "
        SELECT DATE_FORMAT($colData, '%Y-%m') AS ym, SUM($colValor) AS total
        FROM `$tabela`
        WHERE DATE($colData) BETWEEN '$inicio' AND '$fim' $whereTipo
        GROUP BY ym
        ORDER BY ym;
    ";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $resultado[$row['ym']] = (float)$row['total'];
        }
    }
    return $resultado;
}

// Labels
$labels = [];
$inicioLabels = strtotime($dataInicio);
$fimLabels    = strtotime($dataFim);
$current = $inicioLabels;
while ($current <= $fimLabels) {
    $labels[] = date('Y-m', $current);
    $current = strtotime("+1 month", $current);
}

// Receitas (vendas)
$receitasPorMes = [];
$sqlReceitas = "
    SELECT DATE_FORMAT(v.data_venda, '%Y-%m') AS ym, SUM(iv.valor_total) AS total
    FROM vendas v
    INNER JOIN itens_venda iv ON v.id_venda = iv.id_venda
    WHERE DATE(v.data_venda) BETWEEN '$dataInicio' AND '$dataFim'
    GROUP BY ym
    ORDER BY ym;
";
$resReceitas = $conn->query($sqlReceitas);
if($resReceitas){
    while($row = $resReceitas->fetch_assoc()){
        $receitasPorMes[$row['ym']] = (float)$row['total'];
    }
}

// Despesas gerais (soma de todas as tabelas de despesas)
$despesasAvulsas = somasMensaisPeriodo($conn, 'entradas', 'data', 'valor', $dataInicio, $dataFim, 'tipo', $tipoDespesa);
$despesasCombustivel = somasMensaisPeriodo($conn, 'controle_combustivel', 'data', 'valor', $dataInicio, $dataFim, 'tipo', $tipoDespesa);
$despesasOutros = []; // caso queira adicionar outras tabelas de despesas futuramente

$despesasPorMes = [];
foreach($labels as $ym){
    $a = $despesasAvulsas[$ym] ?? 0;
    $c = $despesasCombustivel[$ym] ?? 0;
    $o = $despesasOutros[$ym] ?? 0;
    $despesasPorMes[$ym] = $a + $c + $o;
}

// Arrays para gráfico
$serieReceitas = $serieDespesas = [];
$totalReceitas = $totalDespesas = 0;
foreach($labels as $ym){
    $r = $receitasPorMes[$ym] ?? 0;
    $d = $despesasPorMes[$ym] ?? 0;
    $serieReceitas[] = $r;
    $serieDespesas[] = $d;
    $totalReceitas += $r;
    $totalDespesas += $d;
}

$saldo = $totalReceitas - $totalDespesas;

function brl($v) { return 'R$ ' . number_format($v, 2, ',', '.'); }

// Buscar tipos de despesa para filtro
$tiposDespesa = [];
$resTipos = $conn->query("SELECT DISTINCT tipo FROM entradas UNION SELECT DISTINCT tipo FROM controle_combustivel ORDER BY tipo");
if($resTipos){
    while($row = $resTipos->fetch_assoc()){
        $tiposDespesa[] = $row['tipo'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Financeiro - Fertiquim</title>
<link rel="stylesheet" href="../css/estilo.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin: 20px 0; }
.card { background: #f9f9f9; border: 1px solid #ddd; border-radius: 10px; padding: 15px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.card h3 { font-size: 16px; margin-bottom: 10px; color: #333; }
.card .val { font-size: 22px; font-weight: bold; color: #2e7d32; }
.card.negativo .val { color: #c62828; }

.painel { background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-top: 20px; }
.painel h4 { margin-bottom: 10px; font-size: 18px; color: #444; }

.charts { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
@media (max-width: 900px) { .charts { grid-template-columns: 1fr; } }

.filtros { margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.filtros label { font-weight: bold; }
.filtros input[type=date], .filtros select { padding:5px; border-radius:5px; border:1px solid #ccc; }
.filtros button { padding:7px 15px; background:#2e7d32; color:white; border:none; border-radius:5px; cursor:pointer; }
.filtros button:hover { opacity:0.85; }
</style>
</head>
<body>
<?php include '../base/administrador.php'; ?>

<div class="container">
<h2 class="titulo">Dashboard Financeiro</h2>

<!-- Formulário filtro -->
<form method="post" class="filtros">
    <label>Data Início:</label>
    <input type="date" name="data_inicio" value="<?= $dataInicio ?>">
    <label>Data Fim:</label>
    <input type="date" name="data_fim" value="<?= $dataFim ?>">
    <label>Tipo de Despesa:</label>
    <select name="tipo_despesa">

        <option value="todas">Todas</option>       
        <option value="Combustível">Combustível</option>
        <option value="Material de Escritório">Material de Escritório</option>
        <option value="Material Elétrico">Material Elétrico</option>
        <option value="Material de Informática">Material de Informática</option>
        <option value="EPI's">EPI's</option>
        <option value="Uniforme">Uniforme</option>
        <option value="Deslocamento Fluvial">Deslocamento Fluvial</option>
        <option value="Pedágio">Pedágio</option>
        <option value="Alimentação">Alimentação</option>
        <option value="Acessórios">Acessórios</option>
        <option value="Reposição Bancária">Reposição Bancária</option>
        <option value="Estacionamento">Estacionamento</option>
        <option value="Ferramentas">Ferramentas</option>
        <option value="Hospedagem">Hospedagem</option>
        <option value="Material de Copa">Material de Copa</option>
        <option value="VR">VR</option>
        <option value="Diária">Diária</option>
        <option value="Locação">Locação</option>
        <option value="Salário">Salário</option>
        




        <?php foreach($tiposDespesa as $t): ?>
            <option value="<?= $t ?>" <?= ($tipoDespesa==$t?'selected':'') ?>><?= $t ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrar</button>
</form>

<div class="cards-grid">
    <div class="card">
        <h3>Entradas (Receitas)</h3>
        <div class="val"><?= brl($totalReceitas) ?></div>
    </div>
    <div class="card">
        <h3>Despesas em Geral</h3>
        <div class="val"><?= brl($totalDespesas) ?></div>
    </div>
    <div class="card <?= $saldo>=0?'':'negativo' ?>">
        <h3>Saldo</h3>
        <div class="val"><?= brl($saldo) ?></div>
    </div>
</div>

<div class="charts">
    <div class="painel">
        <h4>Evolução Mensal</h4>
        <canvas id="chartLinhas"></canvas>
    </div>
    <div class="painel">
        <h4>Composição das Despesas</h4>
        <canvas id="chartRosca"></canvas>
    </div>
</div>

</div>
<?php include '../base/rodape.php'; ?>

<script>
const labels = <?= json_encode($labels) ?>;
const receitas = <?= json_encode($serieReceitas) ?>;
const despesas = <?= json_encode($serieDespesas) ?>;

new Chart(document.getElementById('chartLinhas'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label:'Entradas (Receitas)', data:receitas, borderColor:'#2e7d32', fill:false },
            { label:'Despesas', data:despesas, borderColor:'#c62828', fill:false }
        ]
    },
    options:{ responsive:true }
});

// Rosca de composição de despesas por tipo
<?php
// Somar despesas para rosca
$avulsasTotal = array_sum($despesasAvulsas);
$combustivelTotal = array_sum($despesasCombustivel);
$outrosTotal = array_sum($despesasOutros);
?>
new Chart(document.getElementById('chartRosca'), {
    type:'doughnut',
    data:{
        labels:['Avulsas','Combustível','Outros'],
        datasets:[{
            data:[<?= $avulsasTotal ?>, <?= $combustivelTotal ?>, <?= $outrosTotal ?>],
            backgroundColor:['#ff9800','#03a9f4','#ff5722']
        }]
    },
    options:{ responsive:true, plugins:{ legend:{ position:'bottom' } } }
});
</script>
  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
</body>
</html>

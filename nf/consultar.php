<?php
session_start();
$conn = new mysqli("localhost", "root", "", "fertiquim");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Filtro da busca (agora busca também por CNPJ e nome_fantasia)
$filtro = "";
if (!empty($_GET['busca'])) {
    $busca = $conn->real_escape_string($_GET['busca']);
    $filtro = "WHERE numero_nf LIKE '%$busca%' 
            OR data_confirmacao LIKE '%$busca%' 
            OR cnpj LIKE '%$busca%'
            OR nome_fantasia LIKE '%$busca%'";
}

// Consulta as NFs aceitas
$sql = "SELECT id, numero_nf, nome_fantasia, cnpj, telefone, endereco, cep, data_confirmacao, responsavel_entrega, cpf_responsavel
        FROM nf_aceitas 
        $filtro
        ORDER BY data_confirmacao DESC";
$result = $conn->query($sql);

// Montar um array com fertilizantes por NF
$dados = [];
if ($result && $result->num_rows > 0) {
    while($nf = $result->fetch_assoc()) {
        $nf_id = (int)$nf['id'];
        $fertilizantes = [];
        $res_fert = $conn->query("SELECT nome_produto, quantidade, unidade, tipo FROM fertilizantes_aceitos WHERE nf_id = $nf_id");
        if ($res_fert && $res_fert->num_rows > 0) {
            while($f = $res_fert->fetch_assoc()) {
                $fertilizantes[] = $f;
            }
        }
        $nf['fertilizantes'] = $fertilizantes;
        $dados[] = $nf;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Notas Fiscais Aceitas</title>
<link rel="stylesheet" href="../css/estilo.css">
<style>
    body { font-family: Arial, sans-serif; }
    .container { max-width: 1200px; margin: auto; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .search-box { margin-top: 20px; }
    .expand-btn { background-color: #2196F3; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; }
    .detalhes-nf td { padding: 0; }
    .detalhes-conteudo {
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 15px;
        margin: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        line-height: 1.6;
    }
</style>
<script>
function toggleExpand(id) {
    var content = document.getElementById('expand_' + id);
    if(content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'table-row';
    } else {
        content.style.display = 'none';
    }
}
</script>
</head>
<body>
<header>
    <h1>Notas Fiscais Aceitas</h1>
    <nav>
    <a href="../pginicial/pginicial.php">Início</a>
    <a href="../inventario/inv.php">Inventário</a>
    <a href="../estoque/estoque.php">Controle</a>
    <a href="../nf/inserir.php">Inserir NF's</a>
    <a href="../nf/consultar.php">Consultar NF's</a>
    <a href="../nf/pendente.php">NF's Pendente</a>  
    <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
</header>

<div class="container">
    <form method="get" class="search-box">
        <input type="text" name="busca" placeholder="DATA/NOME/CNPJ" value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php if (count($dados) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número NF</th>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>CEP</th>
                    <th>Data Confirmação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dados as $nf): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($nf['id']); ?></td>
                        <td><?php echo htmlspecialchars($nf['numero_nf']); ?></td>
                        <td><?php echo htmlspecialchars($nf['nome_fantasia']); ?></td>
                        <td><?php echo htmlspecialchars($nf['cnpj']); ?></td>
                        <td><?php echo htmlspecialchars($nf['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($nf['endereco']); ?></td>
                        <td><?php echo htmlspecialchars($nf['cep']); ?></td>
                        <td><?php echo htmlspecialchars($nf['data_confirmacao']); ?></td>
                        <td>
                            <button class="expand-btn" onclick="toggleExpand(<?php echo $nf['id']; ?>)">⬇️</button>
                        </td>
                    </tr>
                    <tr id="expand_<?php echo $nf['id']; ?>" class="detalhes-nf" style="display:none;">
                        <td colspan="9">
                            <div class="detalhes-conteudo">
                                <strong>Conteúdo da NF:</strong><br>
                                Número: <?php echo htmlspecialchars($nf['numero_nf']); ?><br>
                                Nome Fantasia: <?php echo htmlspecialchars($nf['nome_fantasia']); ?><br>
                                CNPJ: <?php echo htmlspecialchars($nf['cnpj']); ?><br>
                                Telefone: <?php echo htmlspecialchars($nf['telefone']); ?><br>
                                Endereço: <?php echo htmlspecialchars($nf['endereco']); ?><br>
                                CEP: <?php echo htmlspecialchars($nf['cep']); ?><br>
                                Data de Confirmação: <?php echo htmlspecialchars($nf['data_confirmacao']); ?><br><br>

                                <strong>Responsável pela conferência:</strong> <?php echo htmlspecialchars($nf['responsavel_entrega']); ?><br>
                                

                                <strong>Materiais nesta NF:</strong><br>
                                <?php if (!empty($nf['fertilizantes'])): ?>
                                    <ul>
                                    <?php foreach($nf['fertilizantes'] as $f): ?>
                                        <li>
                                            <?php echo htmlspecialchars($f['nome_produto']); ?> — 
                                            Quantidade: <?php echo htmlspecialchars($f['quantidade']); ?> 
                                            <?php echo htmlspecialchars($f['unidade']); ?> 
                                            — Tipo: <?php echo htmlspecialchars($f['tipo']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    Nenhum fertilizante encontrado.
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma nota fiscal aceita encontrada.</p>
    <?php endif; ?>
</div>
 <footer>
    &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
  <?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao_usuario'])): ?>
    <div class="usuario-logado">
      <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
    </div>
  <?php endif; ?>
  </footer>
</body>
</html>
<?php $conn->close(); ?>

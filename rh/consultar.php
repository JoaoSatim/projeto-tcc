<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$resultado = $conn->query("SELECT * FROM cadastro_funcionario ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Funcionários - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .tabela-funcionarios {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .tabela-funcionarios th, .tabela-funcionarios td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    .tabela-funcionarios th {
      background-color: #f5f5f5;
    }

    .detalhes-funcionario {
      background-color: #f9f9f9;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      display: none;
    }

    .btn-expandir, .btn-acao {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 6px 12px;
      cursor: pointer;
      border-radius: 4px;
      margin-right: 5px;
    }

    .btn-remover {
      background-color: #e74c3c;
    }

    .usuario-logado {
      position: fixed;
      bottom: 10px;
      left: 15px;
      font-size: 14px;
      color: #555;
      background-color: #f1f1f1;
      padding: 6px 10px;
      border-radius: 5px;
    }

    .acoes {
      margin-top: 10px;
    }

    input {
      margin-bottom: 5px;
      padding: 5px;
      width: 250px;
    }
  </style>

  <script>
    function toggleDetalhes(id) {
      const el = document.getElementById('detalhes-' + id);
      el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function salvarEdicao(event, id) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      formData.append('id', id);

      fetch('editar_funcionario.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(text => {
        if (text.includes('sucesso')) {
          alert('Dados atualizados com sucesso!');
        } else {
          alert('Erro ao atualizar os dados.');
        }
      })
      .catch(() => alert('Erro na comunicação com o servidor.'));
    }

    function removerFuncionario(id, nome) {
      if (!confirm('Deseja realmente remover "' + nome + '"?')) return;

      fetch('remover_funcionario.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
      })
      .then(response => response.text())
      .then(text => {
        if (text.includes('sucesso')) {
          alert('Funcionário removido com sucesso.');
          document.getElementById('detalhes-' + id).previousElementSibling.remove();
          document.getElementById('detalhes-' + id).remove();
        } else {
          alert('Erro ao remover o funcionário.');
        }
      })
      .catch(() => alert('Erro na comunicação com o servidor.'));
    }
  </script>
</head>
<body>

<header>
  <h1>FERTIQUIM Fertilizantes</h1>
  <nav>
    <a href="../pginicial/pginicial.php">Início</a>
    <a href="../pglogin/pglogin.php">Sair</a>
  </nav>
</header>

<div class="container">
  <h2 class="titulo">Funcionários Cadastrados</h2>

  <table class="tabela-funcionarios">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Matrícula</th>
        <th>Função</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($f = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($f['nome']) ?></td>
          <td><?= htmlspecialchars($f['matricula_funcionario']) ?></td>
          <td><?= htmlspecialchars($f['funcao']) ?></td>
          <td><button class="btn-expandir" onclick="toggleDetalhes(<?= $f['id'] ?>)">Expandir</button></td>
        </tr>
        <tr id="detalhes-<?= $f['id'] ?>" class="detalhes-funcionario">
          <td colspan="5">
            <form onsubmit="salvarEdicao(event, <?= $f['id'] ?>)">
              <strong>CPF:</strong> <input type="text" name="cpf" value="<?= htmlspecialchars($f['cpf']) ?>"><br>
              <strong>Data de Nascimento:</strong> <input type="date" name="data_nascimento" value="<?= htmlspecialchars($f['data_nascimento']) ?>"><br>
              <strong>Endereço:</strong> <input type="text" name="endereco" value="<?= htmlspecialchars($f['endereco']) ?>"><br>
              <strong>Número:</strong> <input type="text" name="numero_casa" value="<?= htmlspecialchars($f['numero_casa']) ?>"><br>
              <strong>CEP:</strong> <input type="text" name="cep" value="<?= htmlspecialchars($f['cep']) ?>"><br>
              <strong>UF:</strong> <input type="text" name="uf" value="<?= htmlspecialchars($f['uf']) ?>"><br>
              <strong>Salário:</strong> <input type="text" name="salario" value="<?= htmlspecialchars($f['salario']) ?>"><br>

              <div class="acoes">
                <button type="submit" class="btn-acao">Salvar</button>
                <button type="button" class="btn-acao btn-remover" onclick="removerFuncionario(<?= $f['id'] ?>, '<?= htmlspecialchars($f['nome']) ?>')">Remover</button>
              </div>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<footer>
  &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
</footer>

<?php if (isset($_SESSION['nome_usuario'])): ?>
  <div class="usuario-logado">
    <?= htmlspecialchars($_SESSION['nome_usuario']) ?> 
  </div>
<?php endif; ?>

</body>
</html>

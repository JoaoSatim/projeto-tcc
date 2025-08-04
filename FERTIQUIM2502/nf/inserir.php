<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Inserir Nota Fiscal</title>
  <link rel="stylesheet" href="../css/estilo.css">
  <style>
    .form-section {
      background: #fff;
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    .form-section h2 {
      margin-top: 0;
      color: #4e4e4e;
    }
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .form-group {
      flex: 1;
      min-width: 180px;
    }
    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 4px;
    }
    .form-group input {
      width: 100%;
      padding: 6px 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    #fertilizantes .fertilizante {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 10px;
      align-items: flex-end;
    }
    .fertilizante .form-group {
      flex: 1;
      min-width: 150px;
    }
    .remove-btn {
      background-color: red;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      padding: 6px 12px;
      height: 32px;
    }
    .add-btn, input[type="submit"] {
      background-color: #8bc34a;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
      font-size: 14px;
    }
    @media(max-width: 600px) {
      .form-row {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Inserir Nota Fiscal</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="inserir.php">Inserir NF's</a>
      <a href="consultar.php">Consultar NF's</a>
      <a href="pendente.php">Nf's Pendente</a>
    </nav>
  </header>

  <div class="container">
    <form action="processa_nf.php" method="post">
      
      <div class="form-section">
        <h2>Dados da Nota Fiscal</h2>
        <div class="form-row">
          <div class="form-group">
            <label>Número da NF:</label>
            <input type="text" name="numero_nf" required>
          </div>
          <div class="form-group">
            <label>Nome Fantasia:</label>
            <input type="text" name="nome_fantasia" required>
          </div>
          <div class="form-group">
            <label>CNPJ:</label>
            <input type="text" name="cnpj" required>
          </div>
          <div class="form-group">
            <label>Telefone:</label>
            <input type="text" name="telefone" required>
          </div>
          <div class="form-group">
            <label>Endereço:</label>
            <input type="text" name="endereco" required>
          </div>
          <div class="form-group">
            <label>CEP:</label>
            <input type="text" name="cep" required>
          </div>
          <div class="form-group">
            <label>Responsável:</label>
            <input type="text" name="responsavel_entrega" required>
          </div>
          <div class="form-group">
            <label>CPF do Responsável:</label>
            <input type="text" name="cpf_responsavel" required>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2>Fertilizantes Recebidos</h2>
        <div id="fertilizantes">
          <div class="fertilizante">
            <div class="form-group">
              <label>Nome:</label>
              <input type="text" name="fertilizante_nome[]" required>
            </div>
            <div class="form-group">
              <label>Quantidade:</label>
              <input type="number" name="fertilizante_quantidade[]" step="0.01" required>
            </div>
            <div class="form-group">
              <label>Unidade:</label>
              <input type="text" name="fertilizante_unidade[]" required>
            </div>
            <button type="button" class="remove-btn" onclick="removeFertilizante(this)">Remover</button>
          </div>
        </div>
        <button type="button" class="add-btn" onclick="addFertilizante()">+ Adicionar Fertilizante</button>
      </div>

      <input type="submit" value="Salvar Nota Fiscal">
    </form>
  </div>

  <script>
    function addFertilizante() {
      const container = document.getElementById('fertilizantes');
      const div = document.createElement('div');
      div.className = 'fertilizante';
      div.innerHTML = `
        <div class="form-group">
          <label>Nome:</label>
          <input type="text" name="fertilizante_nome[]" required>
        </div>
        <div class="form-group">
          <label>Quantidade:</label>
          <input type="number" name="fertilizante_quantidade[]" step="0.01" required>
        </div>
        <div class="form-group">
          <label>Unidade:</label>
          <input type="text" name="fertilizante_unidade[]" required>
        </div>
        <button type="button" class="remove-btn" onclick="removeFertilizante(this)">Remover</button>
      `;
      container.appendChild(div);
    }

    function removeFertilizante(btn) {
      btn.parentElement.remove();
    }
  </script>
</body>
</html>

<?php

$aviso = "🚀 Nova versão do sistema disponível! 

            *Alguns Ajustes visuais realizados

            *Ajustes de desing!

            *Inserção de Extrato no setor Administrador

            FUTUROS AJUSTES A SEREM REALIZADOS

            *Novos Ajustes Visuais estão sendo implementados 
            com a principal ideia de deixar o sistema mais fluido

            *Ajustes no controle de estoque visual e operacional
            
            *Serão adicionados mais opções de relatórios no estoque 
            dando mais opções de impressão

            *Solicito por gentileza para enviarem sugestões de melhorias
            1x por semana do sistema, o mesmo voltou recentemente a ser atualizado
            desde já agradecido


            * Email para contato : joao.v.s.satim@gmail.com 
            ";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<style>
  .aviso-box {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 280px;
    background: #A7D129;
    color: #333;
    border: 1px solid #225B0B;
    border-radius: 8px;
    padding: 15px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: slideUp 0.6s ease-out;
  }

  .aviso-box h4 {
    margin: 0 0 8px;
    font-size: 15px;
    color: #7a6200;
  }

  .aviso-box .close-btn {
    position: absolute;
    top: 6px;
    right: 10px;
    cursor: pointer;
    font-size: 14px;
    color: #7a6200;
  }

  @keyframes slideUp {
    from { transform: translateY(100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
</style>
</head>
<body>

<div class="aviso-box" id="aviso">
  <span class="close-btn" onclick="document.getElementById('aviso').style.display='none'">✖</span>
  <h4>📌 Aviso</h4>
  <p><?php echo nl2br($aviso); ?></p>
</div>

</body>
</html>

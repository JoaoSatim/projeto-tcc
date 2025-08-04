<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se está logado
if (!isset($_SESSION['nome_usuario']) || !isset($_SESSION['funcao_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// Função para restringir acesso por função
function restringirAcesso(array $funcoesPermitidas) {
    $usuarioFuncao = $_SESSION['funcao_usuario'] ?? '';

    if (!in_array($usuarioFuncao, $funcoesPermitidas)) {
        echo "<script>alert('Acesso negado!'); window.location.href = '../pginicial/pginicial.php';</script>";
        exit;
    }
}
?>

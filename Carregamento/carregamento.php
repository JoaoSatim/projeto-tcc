<?php
session_start();
include('../sessao/verifica_sessao.php');

restringirAcesso(['Carregamento', 'Administrador']);

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fertiquim';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}
?>
<?
require_once '../conexaohost/conexao.php';
session_start();
include('../sessao/verifica_sessao.php');

restringirAcesso(['Financeiro', 'Administrador']);

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../pglogin/pglogin.php');
    exit;
}
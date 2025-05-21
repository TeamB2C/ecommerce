<?php
session_start();

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';
include '../includes/funcoes.php';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $categoria = $_POST['categoria'] ?? '';

    // Validação básica
    if (!empty($nome) && !empty($descricao) && !empty($preco) && !empty($categoria)) {
        if (adicionar_produto($nome, $descricao, $preco, $categoria)) {
            header("Location: painel.php?sucesso=1");
            exit;
        } else {
            header("Location: adicionar.php?erro=1");
            exit;
        }
    } else {
        header("Location: adicionar.php?erro=campos_vazios");
        exit;
    }
} else {
    // Acesso direto ao script sem envio de formulário
    header("Location: painel.php");
    exit;
}

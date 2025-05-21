<?php
session_start();

// Recebe dados do formulário
$produto_id = $_POST['produto_id'] ?? null;
$quantidade = $_POST['quantidade'] ?? null;

if ($produto_id && $quantidade) {
    $quantidade = (int)$quantidade;
    if ($quantidade < 1) {
        $quantidade = 1;
    }

    // Atualiza o carrinho na sessão
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }
}

// Redireciona de volta para a página do carrinho
header('Location: carrinho.php');
exit;

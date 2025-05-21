<?php
session_start();
include '../includes/funcoes.php';

if (isset($_GET['produto_id'])) {
    $produto_id = $_GET['produto_id'];
    remover_do_carrinho($produto_id);
}

// Redireciona de volta ao carrinho
header("Location: carrinho.php");
exit;
?>

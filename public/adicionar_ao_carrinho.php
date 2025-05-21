<?php
include '../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'];

    adicionar_ao_carrinho($produto_id, $quantidade);
    header("Location: carrinho.php");
    exit;
}
?>

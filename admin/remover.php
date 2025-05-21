<?php
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';

// Verifica se o id foi passado via GET
if (isset($_GET['id'])) {
    $produto_id = intval($_GET['id']);

    // Antes de deletar, vamos buscar o nome da imagem para remover o arquivo também
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = :id");
    $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        // Caminho completo da imagem (ajuste se seu caminho for diferente)
        $caminhoImagem = "../assets/images/produtos/" . $produto['imagem'];

        // Deleta o produto do banco
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Remove a imagem do servidor, se existir
            if (file_exists($caminhoImagem)) {
                unlink($caminhoImagem);
            }
            // Redireciona para o painel com sucesso
            header("Location: painel.php?msg=Produto removido com sucesso");
            exit;
        } else {
            header("Location: painel.php?error=Erro ao remover produto");
            exit;
        }
    } else {
        header("Location: painel.php?error=Produto não encontrado");
        exit;
    }
} else {
    header("Location: painel.php");
    exit;
}
?>

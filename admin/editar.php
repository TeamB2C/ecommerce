<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: painel.php");
    exit;
}

$produto_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: painel.php?error=Produto não encontrado");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    $imagemAtual = $produto['imagem'];
    $novoNomeImagem = $imagemAtual;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nomeTemporario = $_FILES['foto']['tmp_name'];
        $nomeOriginal = basename($_FILES['foto']['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];

        if (in_array($extensao, $extensoesPermitidas)) {
            $novoNomeImagem = uniqid() . "." . $extensao;
            $diretorioDestino = "../assets/images/produtos/";

            if (!is_dir($diretorioDestino)) {
                mkdir($diretorioDestino, 0755, true);
            }

            $caminhoImagemCompleto = $diretorioDestino . $novoNomeImagem;

            if (move_uploaded_file($nomeTemporario, $caminhoImagemCompleto)) {
                $imagemAntigaCaminho = $diretorioDestino . $imagemAtual;
                if ($imagemAtual && file_exists($imagemAntigaCaminho)) {
                    unlink($imagemAntigaCaminho);
                }
            } else {
                $erro = "Falha ao fazer upload da nova imagem.";
            }
        } else {
            $erro = "Formato de imagem inválido. Use JPG ou PNG.";
        }
    }

    if (empty($erro)) {
        $stmt = $pdo->prepare("UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, imagem = :imagem WHERE id = :id");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':imagem', $novoNomeImagem);
        $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $sucesso = "Produto atualizado com sucesso.";
            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
            $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
            $stmt->execute();
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $erro = "Erro ao atualizar o produto.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Produto</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff0f5;
            margin: 0;
            padding: 0;
        }

        .pagina-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header h1 {
            color: #e91e63;
            margin-bottom: 10px;
        }

        header a {
            display: inline-block;
            color: #c2185b;
            text-decoration: none;
            background-color: #f8f9fa;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        header a:hover {
            background-color: #e2e6ea;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #e91e63;
        }

        input[type="text"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #e91e63;
            border-radius: 8px;
            font-size: 15px;
        }

        button[type="submit"] {
            margin-top: 25px;
            background-color: #e75480;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #e91e63;
        }

        p {
            margin: 10px 0;
            font-weight: bold;
        }

        .mensagem-sucesso {
            color: green;
        }

        .mensagem-erro {
            color: red;
        }

        img {
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="pagina-container">
    <header>
        <h1>Editar Produto</h1>
        <a href="painel.php">← Voltar ao Painel</a>
    </header>

    <main>
        <?php if ($erro): ?>
            <p class="mensagem-erro"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <p class="mensagem-sucesso"><?php echo htmlspecialchars($sucesso); ?></p>
        <?php endif; ?>

        <form action="editar.php?id=<?php echo $produto_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required />

            <label for="descricao">Descrição:</label>
            <input type="text" name="descricao" id="descricao" value="<?php echo htmlspecialchars($produto['descricao']); ?>" required />

            <label for="preco">Preço:</label>
            <input type="text" name="preco" id="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>" required />

            <label for="categoria">Categoria:</label>
            <input type="text" name="categoria" id="categoria" value="<?php echo isset($produto['categoria']) ? htmlspecialchars($produto['categoria']) : ''; ?>" required />

            <label>Imagem Atual:</label><br />
            <img src="../assets/images/produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="Imagem do Produto" style="max-width: 150px; height: auto;" /><br />

            <label for="foto">Nova Imagem (opcional):</label>
            <input type="file" name="foto" id="foto" accept="image/jpeg, image/png" />

            <button type="submit">Salvar Alterações</button>
        </form>
    </main>
</div>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';
include '../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    $nomeImagem = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nomeTemporario = $_FILES['foto']['tmp_name'];
        $nomeOriginal = basename($_FILES['foto']['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
        if (in_array($extensao, $extensoesPermitidas)) {
            $novoNome = uniqid() . "." . $extensao;
            $diretorioDestino = "../assets/images/produtos/";

            if (!is_dir($diretorioDestino)) {
                mkdir($diretorioDestino, 0755, true);
            }

            $caminhoCompleto = $diretorioDestino . $novoNome;

            if (move_uploaded_file($nomeTemporario, $caminhoCompleto)) {
                $nomeImagem = $novoNome;
            } else {
                $erro = "Erro ao mover a imagem para o diretório.";
            }
        } else {
            $erro = "Formato de imagem inválido. Use JPG ou PNG.";
        }
    } else {
        $erro = "Erro no upload da imagem ou nenhuma imagem enviada.";
    }

    if (!isset($erro)) {
        if (adicionar_produto($nome, $descricao, $preco, $categoria, $nomeImagem)) {
            header("Location: painel.php");
            exit;
        } else {
            $erro = "Erro ao adicionar produto.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adicionar Produto</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(to bottom, #fff0f5, #ffe4e1);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .pagina-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            position: relative;
        }

        h1 {
            font-family: 'Rubik', sans-serif;
            font-size: 2em;
            color: #e75480;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #d6336c;
        }

        input[type="text"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ffb6c1;
            border-radius: 5px;
            background-color: #fffafa;
        }

        button {
            padding: 12px;
            background-color: #e75480;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #ff1493;
        }

        .mensagem-alerta {
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
            font-size: 1em;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: desvanecer 4s ease forwards;
        }

        .mensagem-alerta.erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes desvanecer {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }

        .voltar {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .voltar img {
            width: 30px;
            height: auto;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }

        .footer-sociais {
            list-style: none;
            padding: 0;
            margin: 10px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .footer-sociais li img {
            width: 24px;
            height: auto;
        }

        .footer-bottom {
            margin-top: 10px;
        }

        .footer-p {
            font-size: 0.85em;
        }

        .voltar-painel {
    position: absolute;
    top: 20px;
    left: 20px;
    text-decoration: none;
    font-size: 0.9em;
    color: #d63384;
    background-color: #f8f9fa;
    padding: 6px 12px;
    border-radius: 5px;
    transition: background-color 0.3s;
}



.voltar-painel:hover {
    background-color: #e2e6ea;
}

    </style>
</head>
<body>
    <div class="pagina-container">
        <header>
            <h1>Adicionar Produto</h1>
            <a href="painel.php" class="voltar-painel">← Voltar ao Painel</a>

        </header>
        <main>
            <?php if (isset($erro)): ?>
                <div class="mensagem-alerta erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form action="adicionar.php" method="POST" enctype="multipart/form-data">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required />

                <label for="descricao">Descrição:</label>
                <input type="text" name="descricao" id="descricao" required />

                <label for="preco">Preço:</label>
                <input type="text" name="preco" id="preco" required />

                <label for="categoria">Categoria:</label>
                <input type="text" name="categoria" id="categoria" required />

                <label for="foto">Foto do Produto:</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png" required />

                <button type="submit">Adicionar</button>
            </form>
        </main>
        <footer>
            <div class="footer-conteudo">
                <h3>Um convite de casamento</h3>
                <ul class="footer-sociais">
                    <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram" /></a></li>
                    <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter" /></a></li>
                    <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook" /></a></li>
                    <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn" /></a></li>
                </ul>
            </div>
            <div class="footer-bottom">
                <p class="footer-p">&copy; 2025 Um Convite de Casamento - Todos os direitos reservados</p>
            </div>
        </footer>
    </div>
</body>
</html>

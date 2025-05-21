<?php
session_start();
include '../includes/conexao.php';
include '../includes/funcoes.php';

$usuario_logado = $_SESSION['usuario'] ?? null;
$usuario_is_admin = false;
$usuario_nome = '';
$usuario_imagem = '';

if ($usuario_logado && is_array($usuario_logado)) {
    $usuario_is_admin = ($usuario_logado['tipo'] ?? '') === 'admin';
    $usuario_nome = $usuario_logado['nome'] ?? '';
    $usuario_imagem = $usuario_logado['imagem'] ?? '';
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Produto não encontrado.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->execute(['id' => $id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "Produto não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($produto['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Rubik:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Rubik', sans-serif;
            background: linear-gradient(to bottom, #fff0f5, #ffe4e1);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: #fff !important;
            box-shadow: none !important;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            height: 120px;
        }
        header h1 {
            font-family: 'Rubik', sans-serif;
            color: #e75480;
            font-size: 2.5em;
        }
        .voltar {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .voltar img { width: 30px; }
        .perfil-admin {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .perfil-admin img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .perfil-admin p { margin: 0; }
        .perfil-admin nav a {
            margin-left: 10px;
            color: #e75480;
            text-decoration: none;
        }
        .perfil-admin nav a:hover {
            text-decoration: underline;
        }
        .produto-detalhe-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        .produto-imagem img {
            width: 300px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            margin-bottom: 20px;
        }
        .produto-info {
            max-width: 600px;
            text-align: center;
        }
        .produto-info h1 {
            font-size: 2em;
            color: #e75480;
            margin-bottom: 10px;
        }
        .preco {
            font-size: 1.0em;
            color: #e75480;
            margin-bottom: 20px;
        }
        .descricao {
            font-size: 1.1em;
            margin-bottom: 30px;
            color: #444;
        }
        .form-adicionar {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-linha {
            margin-bottom: 15px;
        }
        .quantidade-input {
            padding: 8px 12px;
            font-size: 1em;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 80px;
        }
        .btn-adicionar {
            background-color: #e75480;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-adicionar:hover {
            background-color: #e75480;
        }
        footer {
            margin-top: auto;
            background-color: #fff;
            text-align: center;
            padding: 30px 20px;
            box-shadow: 0 -4px 8px rgba(0,0,0,0.05);
        }
        footer h3 {
            font-family: 'Rubik', sans-serif;
            font-size: 1.2em;
            color: #777;
            margin-bottom: 15px;
        }
        .footer-sociais {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        .footer-sociais img { width: 30px; }
        .footer-bottom .footer-p {
            font-size: 0.9em;
            color: #777;
        }
        .logo-central {
            order: 2;
            position: static;
            transform: none;
            flex-grow: 0;
            text-align: center;
        }
        .logo-central img {
            transform: scale(0.7) !important;
        }

        .perfil-admin {
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 10px;
}

.perfil-admin {
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 10px;
}

.voltar {
    position: absolute;
    top: 50%;
    left: 20px;
    transform: translateY(-50%);
}

    </style>
</head>
<body>

<div class="pagina-container">
    <header>
        <div class="logo-central">
            <a href="../index.php">
                <img src="../assets/images/sistema/logo01.png" alt="Voltar ao Catálogo" />
            </a>
        </div>

        <div class="voltar">
            <a href="../index.php">
                <img src="../assets/images/sistema/back.png" alt="Voltar ao Catálogo" />
            </a>
        </div>

        <div class="perfil-admin">
            <a href="carrinho.php" title="Carrinho">
                <img src="../assets/images/sistema/carrinho.png" alt="Carrinho" />
            </a>
            <?php if ($usuario_logado): ?>
                <img src="<?php echo !empty($usuario_imagem) ? 'uploads/' . htmlspecialchars($usuario_imagem) : '../assets/images/default.png'; ?>" alt="Perfil" />
                <div>
                    <p><strong><?php echo htmlspecialchars($usuario_nome); ?></strong></p>
                    <nav>
                        <a href="public/perfil.php">Perfil</a>
                        <a href="public/logout.php">Sair</a>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="produto-detalhe-container">
        <div class="produto-imagem">
            <img src="../assets/images/produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
        </div>
        <div class="produto-info">
            <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
            <p class="preco">
                Preço unitário: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br>
                <span id="total-preco">Total: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
            </p>
            <p class="descricao"><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>

            <form class="form-adicionar" action="adicionar_ao_carrinho.php" method="POST">
                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                <div class="form-linha">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" name="quantidade" id="quantidade" value="1" min="1" required class="quantidade-input">
                </div>
                <button type="submit" class="btn-adicionar">Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>

    <footer>
        <h3>Um convite de casamento</h3>
        <ul class="footer-sociais">
            <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram" /></a></li>
            <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter" /></a></li>
            <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook" /></a></li>
            <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn" /></a></li>
        </ul>
        <div class="footer-bottom">
            <p class="footer-p">&copy; 2025 Um Convite de Casamento - Todos os direitos reservados</p>
        </div>
    </footer>
</div>

<!-- Script para atualizar o preço total -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const precoUnitario = <?php echo json_encode($produto['preco']); ?>;
        const inputQuantidade = document.getElementById('quantidade');
        const spanTotal = document.getElementById('total-preco');

        function atualizarPrecoTotal() {
            const quantidade = parseInt(inputQuantidade.value) || 1;
            const total = (precoUnitario * quantidade).toFixed(2);
            spanTotal.textContent = 'Total: R$ ' + total.replace('.', ',');
        }

        inputQuantidade.addEventListener('input', atualizarPrecoTotal);
    });
</script>
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
      <div class="vw-plugin-top-wrapper"></div>
    </div>
  </div>
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
  </script>
</body>
</html>

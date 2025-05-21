<?php
session_start();
include '../includes/conexao.php';
include '../includes/funcoes.php';

$itens_carrinho = obter_itens_carrinho($pdo);
$total_carrinho = calcular_total_carrinho($pdo);

// Verifica se o usuário está logado
$usuario_logado = false;
$usuario_nome = '';
$usuario_imagem = '';

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT nome, imagem FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $usuario_nome = $usuario['nome'];
        $usuario_imagem = $usuario['imagem'] ? $usuario['imagem'] : 'default-avatar.jpg';
    }

    $usuario_logado = true;

    // Limpar o carrinho após finalização da compra
    $stmt = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
}
// Limpar carrinho do banco
$stmt = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = :usuario_id");
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();

// Limpar carrinho da sessão
if (isset($_SESSION['carrinho'])) {
    unset($_SESSION['carrinho']);
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Compra Finalizada - Um Convite de Casamento</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/carrinho.css">

    <style>
        h1 {
            font-family: 'Rubik', sans-serif;
            color: #e75480;
            margin-top: 40px;
        }
        .voltar img {
            width: 30px;
        }
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
        .perfil-admin nav a {
            margin-left: 10px;
            color: #e75480;
            text-decoration: none;
        }
        .perfil-admin nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="carrinho-container">

        <header>
            
            <div class="voltar">
                <a href="carrinho.php" title="Voltar ao Catálogo">
                    <img src="../assets/images/sistema/back.png" alt="Voltar">
                </a>
            </div>

            <div class="perfil-admin">
                <?php if ($usuario_logado): ?>
                    <?php if (!empty($usuario_imagem) && file_exists('../uploads/' . $usuario_imagem)): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($usuario_imagem); ?>" alt="Foto de perfil" />
                    <?php else: ?>
                        <img src="../assets/img/default.png" alt="Foto padrão" />
                    <?php endif; ?>
                    <span>Olá, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong></span>
                    <nav>
                        <a href="../public/perfil.php">Perfil</a>
                        <a href="../public/logout.php">Sair</a>
                    </nav>
                <?php else: ?>
                    <span>Faça login para acessar seu perfil.</span>
                <?php endif; ?>
            </div>
        </header>

        <center><h1>SUA COMPRA FOI FINALIZADA COM SUCESSO!</h1></center>

        <footer style="margin-top: 40px; text-align: center; font-size: 0.85em; color: #999;">
            <h3>Um convite de casamento</h3>
            <ul style="list-style:none; padding:0; margin:10px 0; display:flex; justify-content:center; gap:15px;">
                <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram" style="width:24px;"></a></li>
                <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter" style="width:24px;"></a></li>
                <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook" style="width:24px;"></a></li>
                <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn" style="width:24px;"></a></li>
            </ul>
            <p>&copy; 2025 Um Convite de Casamento - Todos os direitos reservados</p>
        </footer>

    </div>
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

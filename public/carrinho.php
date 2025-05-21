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
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Carrinho de Compras - Um Convite de Casamento</title>

<!-- Fonte elegante e caligráfica -->
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/carrinho.css">



</head>
<body>
<div class="carrinho-container">

    <header>
        <h1>Carrinho de Compras</h1>
        <div class="voltar">
            <a href="../index.php" title="Voltar ao Catálogo">
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

    <main>
        <?php if (empty($itens_carrinho)): ?>
            <p class="mensagem-vazio">Seu carrinho está vazio.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($itens_carrinho as $produto_id => $quantidade): ?>
    <?php
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute(['id' => $produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <tr>
        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
        <td>
            <form action="atualizar_carrinho.php" method="POST" class="form-quantidade">
                <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>">
                <input
                    type="number"
                    name="quantidade"
                    value="<?php echo $quantidade; ?>"
                    min="1"
                    required
                    style="width: 60px;"
                                onchange="this.form.submit()"

                >
            </form>
        </td>
        <td>R$ <?php echo number_format($produto['preco'] * $quantidade, 2, ',', '.'); ?></td>
        <td><a href="remover_do_carrinho.php?produto_id=<?php echo $produto_id; ?>">Remover</a></td>
    </tr>
<?php endforeach; ?>
</tbody>
            </table>
            <p class="total">Total: R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></p>

            <form class="finalizar-form" action="finalizar.php" method="POST">
                <button
                    type="submit"
                    <?php if(empty($itens_carrinho)) echo 'disabled'; ?>
                    onclick="<?php if(empty($itens_carrinho)) echo "alert('Seu carrinho está vazio. Não é possível finalizar a compra.'); return false;"; ?>"
                >
                    Finalizar Compra
                </button>
            </form>
        <?php endif; ?>
    </main>

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

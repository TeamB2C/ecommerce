<?php
session_start();
include('includes/conexao.php');

$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usuario_logado = false;
$usuario_nome = '';
$usuario_imagem = '';
$usuario_is_admin = false;

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT nome, imagem, is_admin FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $usuario_nome = $usuario['nome'];
        $usuario_imagem = $usuario['imagem'] ? $usuario['imagem'] : 'default-avatar.jpg';
        $usuario_is_admin = ($usuario['is_admin'] == 1);
    }

    $usuario_logado = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="assets/images/sistema/carta_fechada.png" type="image">
    <title>Um Convite de Casamento</title>

    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/perfil.css" />
    <link rel="stylesheet" href="assets/css/pagina-container.css" />
    <link rel="stylesheet" href="assets/css/card.css" />
    <link rel="stylesheet" href="assets/css/pagina_inicial.css" />
    <link rel="stylesheet" href="assets/css/menu-lateral.css" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="pagina-container">
        <header>
            <div class="menu-container">
                <div class="hamburger-menu" id="hamburger">
                    <i class='bx bx-menu'></i>
                </div>
            </div>

            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/sistema/logo01.png" alt="Logo da Loja" class="logo-img" />
                </a>
            </div>

            <div class="menu-desktop">
                <?php if ($usuario_is_admin): ?>
                    <a href="admin/painel.php">Painel Admin</a>
                <?php endif; ?>
                <a href="index.php">Catálogo</a>
                <a href="historico_pedidos.php">Histórico de Pedidos</a>
            </div>


            <div class="icones-header">
                <a href="<?php echo $usuario_logado ? 'public/perfil.php' : 'public/login_registro.php'; ?>" class="icone">
                    <i class='bx bx-user'></i>
                </a>
                <div id="sacola-icon" class="icone">
                    <i class='bx bx-shopping-bag'></i>
                    <span class="item-sacola"></span>
                </div>
            </div>
        </header>

        <div class="menu-fundo" id="menuFundo"></div>

        <nav class="menu-lateral" id="menuLateral">
            <?php if ($usuario_logado): ?>
                <?php if ($usuario_is_admin): ?>
                    <a href="admin/painel.php">Painel</a>
                <?php endif; ?>
                <a href="public/perfil.php">Gerenciar Perfil</a>
                <a href="public/logout.php">Sair</a>
            <?php else: ?>
                <a href="public/login_registro.php">Login</a>
                <a href="public/login_registro.php?acao=registrar">Registrar-se</a>
            <?php endif; ?>
        </nav>

        <section class="catalogo">
            <h2 class="titulo">Conheça nossos destaques</h2>
            <div class="conteudo-produto">
                <?php foreach ($produtos as $produto): ?>
                    <div class="produto" onclick="abrirProduto(<?php echo $produto['id']; ?>)">
                        <img src="assets/images/produtos/<?php echo $produto['imagem']; ?>"
                            alt="<?php echo htmlspecialchars($produto['nome']); ?>" />
                        <div class="info-preco-sacola">
                            <?php if ($produto['preco'] > 0): ?>
                                <span class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                            <?php else: ?>
                                <span class="preco indisponivel">Preço indisponível</span>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                        <?php if ($produto['preco'] > 0): ?>
                            <form action="public/adicionar_ao_carrinho.php" method="POST" onsubmit="pararPropagacao(event)">
                                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                                <input type="hidden" name="quantidade" value="1">
                                <button type="submit" class="btn-adicionar-sacola">
                                    Adicionar à sacola
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
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

    <script src="assets/js/menu-lateral.js"></script>
    <script>
        function abrirProduto(id) {
            window.location.href = 'public/produto.php?id=' + id;
        }

        function pararPropagacao(event) {
            event.stopPropagation();
        }
    </script>
</body>

</html>

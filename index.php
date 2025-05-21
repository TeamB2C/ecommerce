<?php
session_start();
include('includes/conexao.php');

// Obtém os produtos do banco
$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se o usuário está logado
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
    <link rel="stylesheet" href="assets/css/footer.css" />
    <link rel="stylesheet" href="assets/css/pagina-container.css" />
    <link rel="stylesheet" href="assets/css/card.css" />
    <link rel="stylesheet" href="assets/css/custom.css" />
    <script src="assets/js/header.js"></script>


    
    <script>
        // Abre a página do produto ao clicar no card, exceto no botão adicionar
        function abrirProduto(id) {
            window.location.href = 'public/produto.php?id=' + id;
        }
        // Evita que clique no botão propague
        function pararPropagacao(event) {
            event.stopPropagation();
        }
    </script>
</head>
<body>
    
    <div class="pagina-container">
        <header>
            <div class="menu-container">
                <div class="hamburger-menu" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div id="menu-hamburguer" class="menu-hamburguer">
                    <?php if ($usuario_logado): ?>
                        <div class="user-info">
                            <?php if ($usuario_is_admin): ?>
                                <a href="admin/painel.php">Painel</a>
                            <?php endif; ?>
                            <a href="public/perfil.php">Gerenciar Perfil</a>
                            <a href="public/logout.php">Sair</a>
                        </div>
                    <?php else: ?>
                        <a href="public/login.php">Login</a>
                        <a href="public/registrar.php">Cadastrar-se</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="logo-central">
                <a href="index.php">
                    <img src="assets/images/sistema/logo01.png" alt="Logo da Loja" class="logo-img" />
                </a>
            </div>

            <div class="perfil-admin">
                <a href="public/carrinho.php" class="icon-carrinho" title="Carrinho">
                    <img src="assets/images/sistema/carrinho.png" alt="Carrinho" class="carrinho-img" />
                </a>

                <?php if ($usuario_logado): ?>
                    <?php if (!empty($usuario_imagem)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($usuario_imagem); ?>" alt="Foto de perfil" />
                    <?php else: ?>
                        <img src="assets/images/default.png" alt="Sem foto" />
                    <?php endif; ?>

                    <div>
                        <p>Bem-vindo, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong></p>
                        <nav>
                            <a href="public/perfil.php">Perfil</a>
                            <a href="public/logout.php">Sair</a>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <main>
            <section>
                <div class="catalogo-header">
                    <h2>Catálogo de Produtos</h2>
                    <form class="catalogo-busca" action="index.php" method="GET">
                    <input 
                    type="text" 
                    name="busca" 
                    placeholder="O que você procura?" 
                    autocomplete="off"
                    value=""
                    />
                    </form>
                </div>


                <div class="produtos">
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto" onclick="abrirProduto(<?php echo $produto['id']; ?>)">
                            <img src="assets/images/produtos/<?php echo $produto['imagem']; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" />
                            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                            <br>
                            
                            <form action="public/adicionar_ao_carrinho.php" method="POST" onsubmit="pararPropagacao(event)" style="position: absolute; bottom: 15px; right: 15px;">
    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
    <input type="hidden" name="quantidade" value="1">
    <center><button type="submit" class="btn-adicionar-carrinho">Adicionar ao Carrinho</button></center>
</form>

                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <footer>
            <div class="footer-conteudo">
                <h3>Um convite de casamento</h3>
                <ul class="footer-sociais">
                    <li><a href="#"><img src="assets/images/sistema/instagram.png" alt="Instagram" /></a></li>
                    <li><a href="#"><img src="assets/images/sistema/twitter.png" alt="Twitter" /></a></li>
                    <li><a href="#"><img src="assets/images/sistema/facebook.png" alt="Facebook" /></a></li>
                    <li><a href="#"><img src="assets/images/sistema/linkedin.png" alt="LinkedIn" /></a></li>
                </ul>
            </div>
            <div class="footer-bottom">
                <p class="footer-p">&copy; 2025 Um Convite de Casamento - Todos os direitos reservados</p>
            </div>
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
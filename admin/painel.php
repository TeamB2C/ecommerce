<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';
include '../includes/funcoes.php';

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT nome, imagem FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $usuario_id);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$produtos = listar_produtos();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel Administrador</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fff0f5;
      color: #333;
    }

    .pagina-container {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background-color: #ffe4ec;
      padding: 20px;
      border-bottom: 2px solid #f8c8dc;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
    }

    .menu-container {
      position: relative;
    }

    .hamburger-menu {
      display: flex;
      flex-direction: column;
      cursor: pointer;
      width: 30px;
      gap: 5px;
    }

    .hamburger-menu span {
      height: 3px;
      background-color: #e91e63;
      border-radius: 2px;
    }

    .menu-hamburguer {
      display: none;
      flex-direction: column;
      background-color: #fddce8;
      position: absolute;
      top: 40px;
      left: 0;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .menu-hamburguer a {
      color: #e91e63;
      text-decoration: none;
      margin: 5px 0;
      font-size: 17px;
    }

    .menu-hamburguer a:hover {
      text-decoration: underline;
    }

    .cabecalho-direita {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    header h1 {
      font-size: 28px;
      color: #e91e63;
    }

    .perfil-admin {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .perfil-admin img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #e91e63;
    }

    .perfil-admin nav a {
      margin: 0 5px;
      color: #e91e63;
      text-decoration: none;
    }

    .perfil-admin nav a:hover {
      text-decoration: underline;
    }

    main {
      flex: 1;
      padding: 30px 20px;
    }

    section h2 {
      color: #e91e63;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #f3c4d3;
    }

    th {
      background-color: #fddce8;
      color: #c2185b;
    }

    .acoes-produto {
      display: flex;
      gap: 10px;
    }

    .btn-link {
      padding: 6px 12px;
      text-decoration: none;
      color: white;
      background-color: #e91e63;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .btn-link:hover {
      background-color: #d81b60;
    }

    .btn-danger {
      background-color: #f44336;
    }

    .btn-danger:hover {
      background-color: #c62828;
    }

    footer {
      background-color: #ffe4ec;
      border-top: 2px solid #f8c8dc;
      padding: 20px;
    }

    .footer-conteudo {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }

    .footer-conteudo h3 {
      color: #777;
    }

    .footer-sociais {
      list-style: none;
      display: flex;
      gap: 15px;
    }

    .footer-sociais li img {
      width: 24px;
      height: 24px;
    }

    .footer-bottom {
      text-align: center;
      margin-top: 10px;
    }

    .footer-p {
      font-size: 14px;
      color: #777;
    }

    @media (max-width: 768px) {
      .cabecalho-direita {
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
      }

      .perfil-admin {
        flex-direction: column;
        align-items: flex-start;
      }

      .menu-hamburguer {
        display: flex;
      }
    }
  </style>

  <script>
    function toggleMenu() {
      const menu = document.getElementById('menu-hamburguer');
      menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
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
          <a href="../index.php">Catálogo</a>
          <a href="adicionar.php">Adicionar novo produto</a>
        </div>
      </div>

      <div class="cabecalho-direita">
        <h1>Painel Administrador</h1>
        <div class="perfil-admin">
          <?php if (!empty($usuario['imagem'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($usuario['imagem']); ?>" alt="Foto de perfil">
          <?php else: ?>
            <img src="../assets/img/default.png" alt="Sem foto">
          <?php endif; ?>
          <div>
            <p>Bem-vindo, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></p>
            <nav>
              <a href="../public/perfil.php">Perfil</a> |
              <a href="../public/logout.php">Sair</a>
            </nav>
          </div>
        </div>
      </div>
    </header>

    <main>
      <section>
        <h2>Produtos</h2>
        <table>
          <tr>
            <th>Nome</th>
            <th>Preço</th>
            <th>Ação</th>
          </tr>
          <?php foreach ($produtos as $produto): ?>
          <tr>
            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
            <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
            <td>
              <div class="acoes-produto">
                <a href="editar.php?id=<?php echo $produto['id']; ?>" class="btn-link">Editar</a>
                <a href="remover.php?id=<?php echo $produto['id']; ?>" class="btn-link btn-danger" onclick="return confirm('Tem certeza que deseja remover este produto?');">Remover</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </section>
    </main>

    <footer>
      <div class="footer-conteudo">
        <h3>Um convite de casamento</h3>
        <ul class="footer-sociais">
          <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram"></a></li>
          <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter"></a></li>
          <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook"></a></li>
          <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn"></a></li>
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

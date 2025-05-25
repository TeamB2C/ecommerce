<?php
session_start();
include_once '../includes/conexao.php';
include_once '../includes/funcoes.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];


    if (login_usuario($email, $senha, $pdo)) {
        // Verifica se o usuário é administrador
        if ($_SESSION['is_admin'] == 1) {
            header("Location: ../admin/painel.php");
        } else {
            header("Location: ../index.php");
        }
        exit;
    } else {
        $erro = "E-mail ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Um Convite de Casamento</title>

    <!-- Fonte elegante e caligráfica -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
 



</head>



<body>
    <div class="login-container">


        <header>
            <h1 class="titulo-login"> Login</h1>
        </header>

        <main>
            <section class="form-section">
                <?php if (isset($erro)): ?>
                    <p class="erro"><?php echo htmlspecialchars($erro); ?></p>
                <?php endif; ?>

                <form action="login.php" method="POST" class="formulario-login">
                    <label for="email"> E-mail:</label>
                    <input type="email" name="email" id="email" required>

                    <label for="senha"> Senha:</label>
                    <input type="password" name="senha" id="senha" required>

                    <button type="submit">Entrar</button>
                </form>
            </section>
        </main>



        <footer>

<div class="registrar-link">
    <a href="registrar.php">Não possui uma conta? Cadastre-se</a>
</div>


            <h3>Um convite de casamento</h3>
            <ul class="footer-sociais">
                <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram"></a></li>
                <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter"></a></li>
                <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook"></a></li>
                <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn"></a></li>
            </ul>
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

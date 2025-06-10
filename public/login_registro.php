<?php
session_start();
include_once '../includes/conexao.php';
include_once '../includes/funcoes.php';

$erro_login = null;
$erro_registro = null;

// === LOGIN ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'login') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (login_usuario($email, $senha, $pdo)) {
        if ($_SESSION['is_admin'] == 1) {
            header("Location: ../admin/painel.php");
        } else {
            header("Location: ../index.php");
        }
        exit;
    } else {
        $_SESSION['erro_login'] = "E-mail ou senha incorretos."; // Define erro na sessão
    }
} else {
    unset($_SESSION['erro_login']); // Limpa erro ao acessar a página normalmente
}

// === REGISTRO ===
if (isset($_POST['acao']) && $_POST['acao'] === 'registrar') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (!$erro_registro) {
        $resultado = registrar_cliente($nome, $email, $senha);

        if ($resultado === true) {
            header("Location: login_registro.php"); // Redireciona para login
            exit;
        } else {
            $erro_registro = $resultado;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario</title>
  <link rel="stylesheet" href="../assets/css/login_e_registro.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <div class="container" id="container">
    
    <!-- Tela de Login (inicialmente visível) -->
    <div class="form-box login">
    <form method="POST" action="login_registro.php">
        <h1>Entrar</h1>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'login' && isset($_SESSION['erro_login'])): ?>
        <p class="erro"><?php echo htmlspecialchars($_SESSION['erro_login']); ?></p>
        <?php unset($_SESSION['erro_login']); ?> <!-- Remove erro após exibição -->
    <?php endif; ?>

        
        <input type="hidden" name="acao" value="login">
        <div class="input-box">
          <input type="email" name="email" placeholder="Email" required>
          <i class="bx bxs-user"></i>
        </div>
        <div class="input-box">
          <input type="password" name="senha" placeholder="Senha" required>
          <i class="bx bxs-lock-alt"></i>
        </div>
        <div class="forgot-link">
          <a href="#">Esqueceu a senha?</a>
        </div>
        <button type="submit" class="btn">Entrar</button>
    </form>
</div>

    <div class="form-box registro">
        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= htmlspecialchars($erro); ?></p>
        <?php endif; ?>

      <form method="POST" action="login_registro.php" enctype="multipart/form-data">
        <h1>Crie sua conta</h1>
        <input type="hidden" name="acao" value="registrar">
        <div class="input-box">
          <input type="text" name="nome" placeholder="Nome" required>
          <i class="bx bxs-user"></i>
        </div>
        <div class="input-box">
          <input type="email" name="email" placeholder="Email" required>
          <i class="bx bxs-envelope"></i>
        </div>
        <div class="input-box">
          <input type="password" name="senha" placeholder="Senha" required>
          <i class="bx bxs-lock-alt"></i>
        </div>
        <button type="submit" class="btn">Registrar</button>
      </form>
    </div>

    <!-- Painel lateral com botões -->
    <div class="toggle-box">
      <!-- Este aparece primeiro (Registrar) -->
      <div class="toggle-panel toggle-left">
        <h1>Bem vindo de volta!</h1>
        <p>Não tem uma conta?</p>
        <button class="btn" id="register">Registrar-se</button>
      </div>

      <!-- Este aparece depois (Entrar) -->
      <div class="toggle-panel toggle-right">
        <h1>Ola, Bem vindo!</h1>
        <p>Ja tem uma conta?</p>
        <button class="btn" id="login">Entrar</button>
      </div>
    </div>
  </div>
<script src="../assets/js/login_registro.js"></script>

</body>
</html>
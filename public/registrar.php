<?php
include '../includes/conexao.php';
include '../includes/funcoes.php';

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $imagem_caminho = 'default-avatar.jpg';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto = $_FILES['foto'];
        $nome_unico = uniqid() . '-' . $foto['name'];
        $destino = "../uploads/" . $nome_unico;
        $tipo_imagem = $foto['type'];

        if ($tipo_imagem == "image/jpeg" || $tipo_imagem == "image/png") {
            if (move_uploaded_file($foto['tmp_name'], $destino)) {
                $imagem_caminho = $nome_unico;
            } else {
                $erro = "Erro ao enviar a imagem.";
            }
        } else {
            $erro = "Apenas imagens JPEG e PNG são permitidas.";
        }
    }

    if (!$erro) {
        $resultado = registrar_cliente($nome, $email, $senha, $imagem_caminho);

        if ($resultado === true) {
            header("Location: login.php");
            exit;
        } else {
            $erro = $resultado;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
    <script src="../assets/js/validacoes.js"></script>
    <link rel="stylesheet" href="../assets/css/registrar.css">

</head>
<body>
    <main>
        <section>
            <h1>Cadastre-se</h1>
            <div class="login-link">
                <a href="login.php">Já tem uma conta? Faça login</a>
            </div>

            <?php if (isset($erro)): ?>
                <p style="color: red;"><?= htmlspecialchars($erro); ?></p>
            <?php endif; ?>

            <form action="registrar.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required onblur="validarNome()">
                <span id="msg_nome"></span>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required onblur="validarEmail()">
                <span id="msg_email"></span>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>

                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required onkeyup="validarSenhas()">
                <span id="msg_senha"></span>

                <label for="foto">Foto de Perfil (opcional):</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png"><br><br>

                <button type="submit">Registrar</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        &copy; <?= date("Y") ?> - Todos os direitos reservados
    </footer>
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

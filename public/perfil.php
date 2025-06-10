<?php
session_start();
include('../includes/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtém os dados do usuário
$stmt = $pdo->prepare("SELECT nome, email, imagem FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $usuario_id);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Atualiza os dados caso o formulário seja enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = $_POST['nome'] ?? '';
    $nova_senha = $_POST['senha'] ?? '';
    $nova_foto = $_FILES['foto'] ?? null;

    $dados_atualizacao = [];

    if (!empty($novo_nome) && $novo_nome != $usuario['nome']) {
        $dados_atualizacao['nome'] = $novo_nome;
    }

    if (!empty($nova_senha)) {
        $dados_atualizacao['senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
    }

    if ($nova_foto && $nova_foto['error'] == 0) {
        $nome_unico = uniqid() . '-' . basename($nova_foto['name']);
        $destino = "../uploads/" . $nome_unico;

        if (move_uploaded_file($nova_foto['tmp_name'], $destino)) {
            $dados_atualizacao['imagem'] = $nome_unico;
        }
    }

    if (!empty($dados_atualizacao)) {
        $set = [];
        foreach ($dados_atualizacao as $campo => $valor) {
            $set[] = "$campo = :$campo";
        }

        $sql = "UPDATE usuarios SET " . implode(", ", $set) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        foreach ($dados_atualizacao as $campo => $valor) {
            $stmt->bindValue(":$campo", $valor);
        }
        $stmt->bindValue(':id', $usuario_id);

        if ($stmt->execute()) {
            if (isset($dados_atualizacao['nome'])) {
                $_SESSION['nome'] = $dados_atualizacao['nome'];
            }
            $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
            header("Location: perfil.php");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar os dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="assets/js/validacoes.js"></script>
    <title>Editar Perfil</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Rubik', sans-serif;
            background: linear-gradient(to bottom, #fff0f5, #ffe4e1);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .pagina-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            max-width: 700px;
            width: 100%;
            position: relative;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        header h1 {
            font-size: 2em;
            color: #e75480;
            font-weight: 600;
        }
        .voltar {
            position: absolute;
            top: 0;
            left: 0;
        }
        .voltar img {
            width: 30px;
            height: auto;
            cursor: pointer;
            transition: filter 0.3s ease;
        }
        .voltar img:hover {
            filter: brightness(0.8);
        }
        main section {
            width: 100%;
        }
        main section h3 {
            font-size: 1.4em;
            color: #e75480;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #b33951;
        }
        form input[type="text"],
        form input[type="password"],
        form input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1em;
            margin-bottom: 12px;
            background-color: #fffafa;
            transition: border-color 0.3s ease;
        }
        form input[type="text"]:focus,
        form input[type="password"]:focus,
        form input[type="file"]:focus {
            outline: none;
            border-color: #e75480;
        }
        form span {
            font-size: 0.9em;
            color: #d33;
            margin-bottom: 10px;
            min-height: 18px;
        }
        form button {
            margin-top: 15px;
            background-color: #e75480;
            color: #fff;
            padding: 14px 0;
            font-size: 1.1em;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #b33951;
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
            color: #999;
        }

        /* Foto perfil */
        .foto-perfil-container {
            position: relative;
            width: 160px;
            margin: 0 auto 20px;
        }
        .foto-perfil-container img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e75480;
        }
        .botao-editar-foto {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .botao-editar-foto img {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        /* Alertas bonitinhos */
        .mensagem-alerta {
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
            max-width: 500px;
            margin: 10px auto 20px auto;
            font-size: 1em;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: desvanecer 4s ease forwards;
        }

        .mensagem-alerta.sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
    </style>
</head>
<body>
    <div class="pagina-container">

        <?php if (isset($_SESSION['sucesso']) || isset($_SESSION['erro'])): ?>
            <div class="mensagem-alerta <?= isset($_SESSION['sucesso']) ? 'sucesso' : 'erro' ?>">
                <?= $_SESSION['sucesso'] ?? $_SESSION['erro']; ?>
            </div>
            <?php unset($_SESSION['sucesso'], $_SESSION['erro']); ?>
        <?php endif; ?>

        <header>
            <h1>Editar Perfil</h1>
            <div class="voltar">
                <a href="../index.php" aria-label="Voltar ao Catálogo">
                    <img src="../assets/images/sistema/back.png" alt="Voltar ao Catálogo" />
                </a>
            </div>
        </header>

        <main>
            <section>
                <h3>Dados do Usuário</h3>

                <form action="perfil.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario();">
                    <!-- FOTO COM BOTÃO DE EDITAR -->
                    <div class="foto-perfil-container">
                        <img id="previewFoto" src="../uploads/<?= htmlspecialchars($usuario['imagem'] ?? 'default.png'); ?>" alt="Foto de Perfil" />
                        <label for="foto" class="botao-editar-foto" title="Editar foto">
                            <img src="../assets/images/sistema/pencil.png" alt="Editar">
                        </label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg, image/png" style="display: none;" onchange="mostrarPreview(event)">
                    </div>

                    <label for="nome">Novo nome:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']); ?>" />
                    <span id="msg_nome"></span>

                    <label for="senha">Nova Senha:</label>
                    <input type="password" id="senha" name="senha" />

                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" onkeyup="validarSenhas()" />
                    <span id="msg_senha"></span>

                    <button type="submit">Atualizar</button>
                </form>
            </section>
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
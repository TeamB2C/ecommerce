<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Inicia a sessão somente se ainda não estiver ativa
}

// Função de registro de cliente (usuário comum)
function registrar_cliente($nome, $email, $senha, $imagem_nome = null) {
    global $pdo;

    // Verifica se o e-mail já está registrado
    $verifica = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $verifica->bindParam(':email', $email, PDO::PARAM_STR);
    $verifica->execute();

    if ($verifica->fetch()) {
        return "E-mail já cadastrado!"; // Retorna mensagem de erro
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Se tiver imagem, insere o campo; se não, deixa NULL
    $sql = "INSERT INTO usuarios (nome, email, senha, is_admin, imagem) VALUES (:nome, :email, :senha, 0, :imagem)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':senha', $senha_hash, PDO::PARAM_STR);
    $stmt->bindParam(':imagem', $imagem_nome, PDO::PARAM_STR);

    return $stmt->execute();
}

// Função de login de usuário genérico (clientes e administradores)
function login_usuario($email, $senha, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['is_admin'] = $usuario['is_admin'];
        $_SESSION['imagem'] = $usuario['imagem']; // ESSENCIAL PARA EXIBIR A FOTO

        return true;
    }

    return false;
}

// Função para adicionar produtos
function adicionar_produto($nome, $descricao, $preco, $categoria, $imagem_nome) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, categoria, imagem) 
                           VALUES (:nome, :descricao, :preco, :categoria, :imagem)");
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
    $stmt->bindParam(':preco', $preco, PDO::PARAM_STR);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
    $stmt->bindParam(':imagem', $imagem_nome, PDO::PARAM_STR);
    return $stmt->execute();
}


// Função para listar produtos
function listar_produtos() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM produtos");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para obter os itens do carrinho
function obter_itens_carrinho() {
    return isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
}

// Função para calcular o total do carrinho
function calcular_total_carrinho($pdo) {
    $total = 0;
    $itens = obter_itens_carrinho();

    foreach ($itens as $produto_id => $quantidade) {
        $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = :id");
        $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            $total += $produto['preco'] * $quantidade;
        }
    }

    return $total;
}

// Função para atualizar os dados do usuário no banco (nome, senha e foto)
function atualizar_usuario($usuario_id, $nome, $senha, $imagem_caminho) {
    global $pdo;
    
    $dados_atualizacao = [];
    
    if ($nome) {
        $dados_atualizacao['nome'] = $nome;
    }
    
    if ($senha) {
        $dados_atualizacao['senha'] = password_hash($senha, PASSWORD_DEFAULT);
    }

    if ($imagem_caminho) {
        $dados_atualizacao['imagem'] = $imagem_caminho;
    }

    $set = [];
    foreach ($dados_atualizacao as $campo => $valor) {
        $set[] = "$campo = :$campo";
    }

    $sql = "UPDATE usuarios SET " . implode(", ", $set) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    foreach ($dados_atualizacao as $campo => $valor) {
        $stmt->bindParam(":$campo", $valor);
    }
    $stmt->bindParam(':id', $usuario_id);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}
function adicionar_ao_carrinho($produto_id, $quantidade) {
    // sua lógica aqui, por exemplo usando sessão
    session_start();
    
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Se o produto já está no carrinho, incrementa
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }
}
function remover_do_carrinho($produto_id) {
    session_start(); // Garante que a sessão está iniciada

    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
}

?>
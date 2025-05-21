// Função para validar e-mail
function validarEmail() {
    var email = document.getElementById('email').value;
    var mensagem = document.getElementById('msg_email');
    var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    if (!regex.test(email)) {
        mensagem.textContent = "Por favor, insira um e-mail válido!";
        mensagem.style.color = "red";
        return false;
    } else {
        mensagem.textContent = "E-mail válido!";
        mensagem.style.color = "green";
        return true;
    }
}

// Função para validar nome
function validarNome() {
    var nome = document.getElementById('nome').value;
    var mensagem = document.getElementById('msg_nome');

    if (nome.trim() === "") {
        mensagem.textContent = "O nome é obrigatório!";
        mensagem.style.color = "red";
        return false;
    } else {
        mensagem.textContent = "Nome válido!";
        mensagem.style.color = "green";
        return true;
    }
}

// Função para validar senhas
function validarSenhas() {
    var senha = document.getElementById('senha').value;
    var confirmarSenha = document.getElementById('confirmar_senha').value;
    var mensagem = document.getElementById('msg_senha');

    if (senha !== confirmarSenha) {
        mensagem.textContent = "As senhas não coincidem!";
        mensagem.style.color = "red";
        return false;
    } else {
        mensagem.textContent = "As senhas coincidem.";
        mensagem.style.color = "green";
        return true;
    }
}

// Função para validar o formulário
function validarFormulario() {
    var validacaoEmail = validarEmail();
    var validacaoNome = validarNome();
    var validacaoSenha = validarSenhas();

    if (!validacaoEmail || !validacaoNome || !validacaoSenha) {
        return false;
    }
    return true;
}
function validarFormulario() {
    var nome = document.getElementById('nome').value;
    var senha = document.getElementById('senha').value;
    var confirmar_senha = document.getElementById('confirmar_senha').value;

    var validado = true;

    // Valida nome
    if (nome.trim() === "") {
        document.getElementById('msg_nome').innerText = "O nome não pode estar vazio!";
        validado = false;
    } else {
        document.getElementById('msg_nome').innerText = "";
    }

    // Se a senha foi preenchida, valida a senha e a confirmação
    if (senha !== "" || confirmar_senha !== "") {
        if (senha !== confirmar_senha) {
            document.getElementById('msg_senha').innerText = "As senhas não coincidem!";
            validado = false;
        } else {
            document.getElementById('msg_senha').innerText = "";
        }
    }

    return validado;
}
window.onload = function() {
    // Verifica se a variável 'sucesso' ou 'erro' está definida e exibe o pop-up
    if (typeof sucesso !== 'undefined' && sucesso !== null) {
        alert(sucesso); // Exibe a mensagem de sucesso
    }

    if (typeof erro !== 'undefined' && erro !== null) {
        alert(erro); // Exibe a mensagem de erro
    }
};

document.addEventListener('DOMContentLoaded', function () {
    // Selecionando o botão do menu hambúrguer e o menu
    const button = document.querySelector('.hamburger-menu');
    const menu = document.getElementById('menu-hamburguer');

    // Adicionando o evento de clique para abrir e fechar o menu
    button.addEventListener('click', function () {
        button.classList.toggle('ativo'); // Anima o ícone de hambúrguer para X
        menu.classList.toggle('ativo');   // Mostra ou esconde o menu
    });
});

function abrirProduto(id) {
window.location.href = 'public/produto.php?id=' + id;
}

function pararPropagacao(event) {
event.stopPropagation();
}

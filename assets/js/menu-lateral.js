const hamburger = document.getElementById('hamburger');
const menuLateral = document.getElementById('menuLateral');
const menuFundo = document.getElementById('menuFundo');

hamburger.addEventListener('click', () => {
  if (menuLateral.style.left === '0px') {
    menuLateral.style.left = '-250px';
    menuFundo.style.display = 'none';
  } else {
    menuLateral.style.left = '0';
    menuFundo.style.display = 'block';
  }
});

menuFundo.addEventListener('click', () => {
  menuLateral.style.left = '-250px';
  menuFundo.style.display = 'none';
});

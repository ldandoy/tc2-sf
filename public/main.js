let moins = document.querySelector('#moins');
let plus = document.querySelector('#plus');
let qty = document.querySelector('#productQty');

moins.addEventListener('click', (event) => {
    if (parseInt(qty.value) > 0) {
        qty.value = parseInt(qty.value)-1;
    } else {
        alert('Vous ne pouvez pas commander une quantité négative');
    }
});

plus.addEventListener('click', (event) => {
    if (parseInt(qty.value) < 50) {
        qty.value = parseInt(qty.value)+1;
    } else {
        alert('Vous ne pouvez pas commander plus de 50 fois le même produit');
    }
});
let moins = document.querySelectorAll('.moins');
let plus = document.querySelectorAll('.plus');
// let qty = document.querySelectorAll('#productQty');

moins.forEach(element => {
    element.addEventListener('click', (event) => {
        let qty = element.parentNode.parentNode.children[1].firstElementChild;
        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value)-1;
        } else {
            alert('Vous ne pouvez pas commander moins d\'un article');
        }
    });
});

plus.forEach(element => {
    element.addEventListener('click', (event) => {
        let qty = element.parentNode.parentNode.children[1].firstElementChild;
        if (parseInt(qty.value) < 50) {
            qty.value = parseInt(qty.value)+1;
        } else {
            alert('Vous ne pouvez pas commander plus de 50 fois le même produit');
        }
    });
});
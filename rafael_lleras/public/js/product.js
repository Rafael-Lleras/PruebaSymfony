
function order_product_by() {
	var value = document.getElementById("select_order_product").value;
	window.location.href = "/products/" + value;
}

function confirm_delete_product(id) {
	var r = confirm("´Si esta seguro de que desea eliminar el producto oprima el boton 'Aceptar'");
	if (r) {
		window.location.href = "/product/delete/" + id;
	}
}

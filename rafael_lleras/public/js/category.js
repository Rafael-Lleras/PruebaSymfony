
function change_state_of_category(id) {
	var active_check = document.getElementById("active_check");
	active_check.disabled = true;
	window.location = '/change_state_of_category/' + id;
}

function order_category_by() {
	var value = document.getElementById("select_order_category").value;
	window.location.href = "/categories/" + value;
}
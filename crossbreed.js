function onStrainSelect(sel, which) {
	if (sel.value == "") {
		document.getElementById(which).disabled = false;
	} else {
		document.getElementById(which).disabled = true;
		document.getElementById(which).value = "";
	}
}

function onCheck(box) {
	row = box.parentNode.parentNode;

	if (box.checked) {
		row.style.textDecoration = "line-through";
		row.style.color = "lightgrey";
	} else {
		row.style.textDecoration = "";
		row.style.color = "black";
	}
}

function onStrainSwap() {
	LS = document.getElementById("LS");
	RS = document.getElementById("RS");
	L = document.getElementById("L");
	R = document.getElementById("R");

	LSv = LS.value;
	RSv = RS.value;
	Lv = L.value;
	Rv = R.value;

	document.getElementById("LS").value = RSv;
	document.getElementById("RS").value = LSv;
	document.getElementById("L").value = Rv;
	document.getElementById("R").value = Lv;

	onStrainSelect(LS, "L");
	onStrainSelect(RS, "R");

	return false;
}

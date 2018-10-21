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

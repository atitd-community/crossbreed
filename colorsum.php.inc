<?php
function colorsum($raw) {
	$K = "";
	$C = "";
	$M = "";
	$Y = "";
	$R = "";
	$G = "";
	$B = "";
	$A = "";
	$O = "";
	$V = "";

	for ($i = 0 ; $i < strlen($raw) ; $i++) {
		switch ($raw[$i]) {
			case 'C':
				$C .= 'C';
				break;
			case 'M':
				$M .= 'M';
				break;
			case 'Y':
				$Y .= 'Y';
				break;
		}
	}

	$C = substr($C, 0, 4);
	$M = substr($M, 0, 4);
	$Y = substr($Y, 0, 4);

	while (!empty($C) && !empty($M) && !empty($Y)) {
		$K .= 'K';
		$C = substr($C, 1);
		$M = substr($M, 1);
		$Y = substr($Y, 1);
	}

	while (!empty($M) && !empty($Y)) {
		$R .= 'R';
		$M = substr($M, 1);
		$Y = substr($Y, 1);
	}

	while (!empty($C) && !empty($Y)) {
		$G .= 'G';
		$C = substr($C, 1);
		$Y = substr($Y, 1);
	}

	while (!empty($C) && !empty($M)) {
		$B .= 'B';
		$C = substr($C, 1);
		$M = substr($M, 1);
	}

	while (!empty($R) && !empty($Y)) {
		$O .= 'O';
		$R = substr($R, 1);
		$Y = substr($Y, 1);
	}

	while (!empty($B) && !empty($C)) {
		$A .= 'A';
		$B = substr($B, 1);
		$C = substr($C, 1);
	}

	while (!empty($B) && !empty($M)) {
		$V .= 'V';
		$B = substr($B, 1);
		$M = substr($M, 1);
	}

	$result = "$V$A$O$R$G$B$C$M$Y$K";

	if (empty($result)) {
		$result = '-';
	}

	return $result;
}
?>

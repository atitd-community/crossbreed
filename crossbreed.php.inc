<?php
function printerrors() {
	global $errors;

	if (!empty($errors)) {
		printf("<div class='error'>%s</div>", $errors);
	}
}

function printparents() {
	global $ltgenome;
	global $rtgenome;

	printgenome('L:' . (!empty($_REQUEST['LS']) ? $_REQUEST['LS'] : 'Other'), $ltgenome, true);
	printgenome('R:' . (!empty($_REQUEST['RS']) ? $_REQUEST['RS'] : 'Other'), $rtgenome, true);
}

function printresults($genomes) {
	foreach ($genomes as $genome) {
		printgenome($_REQUEST['name'], $genome, true);
	}
}

function printknowns() {
	global $known;

	foreach ($known as $name => $genome) {
		printgenome($name, $genome);
	}
}

function listchoices($which) {
	global $known;
	global $inputstate;

	$other = " selected=\"selected\"";

	foreach ($known as $name => $genome) {
		if ($name == $_REQUEST[$which]) {
			$sel = " selected=\"selected\"";
			$other = "";
			$inputstate[$which] = " disabled=\"disabled\"";
		} else {
			$sel = "";
		}

		printf("<option value=\"%s\"%s>%s</option>\n", $name, $sel, $name);
	}

	printf("<option value=\"\"%s>Other</option>\n", $other, $name);
}

function runcross() {
	global $known;
	global $children;
	global $mutations;
	global $ltgenome;
	global $rtgenome;

	$ltgenome = $_REQUEST['L'];
	$rtgenome = $_REQUEST['R'];

	if (empty($ltgenome)) {
		$ltgenome = $known[$_REQUEST['LS']];
	} else {
		foreach ($known as $name => $genome) {
			if ($genome == $ltgenome) {
				$_REQUEST['LS'] == $name;
			}
		}
	}

	if (empty($rtgenome)) {
		$rtgenome = $known[$_REQUEST['RS']];
	} else {
		foreach ($known as $name => $genome) {
			if ($genome == $ltgenome) {
				$_REQUEST['RS'] == $name;
			}
		}
	}

	$llen = strlen($ltgenome);
	$rlen = strlen($rtgenome);
	$clen = floor(($llen + $rlen)/2);

	if (strlen($ltgenome) < strlen($rtgenome)) {
		while (strlen($ltgenome) < strlen($rtgenome)) {
			$ltgenome = " " . $ltgenome . " ";
		}

		if ((($llen + $rlen) % 2) == 1) {
			$ltgenome = substr($ltgenome, 1);
		}

		$max = strlen($rtgenome);
	} else {
		while (strlen($rtgenome) < strlen($ltgenome)) {
			$rtgenome = " " . $rtgenome . " ";
		}

		$max = strlen($ltgenome);
	}

	if ((($llen + $rlen) % 2) == 1) {
		$clen++;
	}

	$ltgenome = rtrim($ltgenome);
	$rtgenome = rtrim($rtgenome);

	for ($i = 0 ; $i < $max ; $i++) {
		$cleft	= trim(substr($ltgenome, 0, $i));
		$cright	= trim(substr($rtgenome, $i));
		$child	= trim($cleft . $cright);

		if (strlen($child) == $clen) {
			if (!in_array($child, $children)) {
				$children[] = $child;
			}

			if ($_REQUEST['mutate'] == 'on') {
				// Gene duplication from left genome

				$mleft = $cleft . substr($cleft, -1, 1);
				$mright = $cright;
				$mchild	= trim($mleft . $mright);

				if (!in_array($mchild, $mutations)) {
					$mutations[] = $mchild;
				}

				// Gene duplication from right genome

				$mleft = $cleft;
				$mright = substr($mright, 0, 1) . $mright;
				$mchild	= trim($mleft . $mright);

				if (!in_array($mchild, $mutations)) {
					$mutations[] = $mchild;
				}

				// Gene deletion from left genome

				$mleft = substr($cleft, 0, -1);
				$mright = $cright;
				$mchild	= trim($mleft . $mright);

				if (!in_array($mchild, $mutations)) {
					$mutations[] = $mchild;
				}

				// Gene deletion from right genome

				$mleft = $cleft;
				$mright = substr($mright, 1);
				$mchild	= trim($mleft . $mright);

				if (!in_array($mchild, $mutations)) {
					$mutations[] = $mchild;
				}
			}
		}
	}

	$mutations = array_diff($mutations, $children);
}
?>

<?php
require_once('colorsum.php.inc');

$known["Bloodheart*"]		= "OUOUOUOU";
$known["Dawn's Blush*"]		= "YYRUYGGROURYYRYUYGGR";
$known["Goldenleaves"]		= "UYGUYGUYGUYG";
$known["Hatch's Bud"]		= "RYYRGGRGGOORGGRGGRURUYUY";
$known["Heart of Darkness"]	= "RUROOUYURUOUYURUROOUYU";
$known["Night Bloom"]		= "ROORYYRGGOORYYRGGRUOURUOUYROYGUGYOR";
$known["Onion Skin"]		= "RYYRYYRGGURUYURUYURUYURUYUOUO";
$known["Pantomime"]			= "YYROYYROYYROYYRO";
$known["Pink Giant"]		= "ROOUROYGUROYGUYYR";
$known["Red Dwarf"]			= "RYYRYYRGGRGGRGGOUOUYUYUYUGYOR";
$known["White Giant"]		= "ROYGUYUYUY";

$inputstate['LS'] = "";
$inputstate['RS'] = "";
$ltgenome = "";
$rtgenome = "";
$errors = "";

$children = array();

function printerrors() {
	global $errors;

	if (!empty($errors)) {
		printf("<div class='error'>%s</div>", $errors);
	}
}

function petalmap($c) {
	switch ($c) {
		case 'O':
			return 'C';
		case 'Y':
			return 'M';
		case 'G':
			return 'Y';
		default:
			return '';
	}
}

function leafmap($c) {
	switch ($c) {
		case 'R':
			return 'C';
		case 'O':
			return 'M';
		case 'Y':
			return 'Y';
		default:
			return '';
	}
}

function printgenome($label, $genome, $checks = false) {
	$upetal	= "";
	$lpetal	= "";
	$leaves	= "";
	$stamen	= "";
	$dwarf	= "";
	$giant	= "";

	$genome = sprintf('K%sK', trim($genome));
	$glen = strlen($genome);

	for ($i = 0 ; $i < $glen ; $i++)
	{
		$quint = substr($genome, $i, 5);
		$c0 = $quint[0];
		$c1 = $quint[1];
		$c2 = $quint[2];

		if ($quint == "ROYGU") {
			$giant .= "G";
		}

		if ($quint == "UGYOR") {
			$dwarf .= "D";
		}

		if ($c0 == 'U') {
			$leaves .= leafmap($c1);
		}

		if ($c1 == 'U') {
			$stamen .= leafmap($c0);
		}

		if (($c0 == 'R') && ($c1 == $c2)) {
			$lpetal .= petalmap($c2);
		}

		if (($c2 == 'R') && ($c0 == $c1)) {
			$upetal .= petalmap($c0);
		}
	}

	printf("<tr>");

	if ($checks) {
		if (($label == "Left") || ($label == "Right")) {
			printf("<th></th>");
		} else {
			printf("<td><input type='checkbox' onchange='onCheck(this);'></td>");
		}
	}

	if (!empty($label)) {
		printf("<td>%s</td>", $label);
	}

	$upetal = colorsum($upetal);
	$lpetal = colorsum($lpetal);
	$leaves = colorsum($leaves);
	$stamen = colorsum($stamen);

	printf("<td>%s</td>", $upetal);
	printf("<td>%s</td>", $lpetal);

	printf("<td>%s</td>", $leaves);
	printf("<td>%s</td>", $stamen);

	printf("<td>%s%s</td>", $dwarf, $giant);

	printf("<td>%d</td>", strlen($genome));

	if (!empty($_REQUEST['target'])) {
		$genome = str_replace($_REQUEST['target'], '<span class="target">' . $_REQUEST['target'] . "</span>", $genome);
	}

	printf("<td>%s</td>", $genome);

	printf("</tr>\n");
}

function printresults() {
	global $children;
	global $ltgenome;
	global $rtgenome;

	printgenome('Left', $ltgenome, true);
	printgenome('Right', $rtgenome, true);

	foreach ($children as $genome) {
		printgenome($_REQUEST['name'], $genome, true);
	}
}

function printknowns() {
	global $known;

	foreach ($known as $name => $genome) {
		printgenome($name, $genome);
	}
}

function validate() {
	global $errors;
	global $known;

	if (!empty($_REQUEST['LS'])) {
		if (is_null($known[$_REQUEST['LS']])) {
			$errors .= "Invalid Left Split Selection<br>";
			$_REQUEST['LS'] = '';
		}
	}

	if (!empty($_REQUEST['RS'])) {
		if (is_null($known[$_REQUEST['RS']])) {
			$errors .= "Invalid Right Split Selection<br>";
			$_REQUEST['RS'] = '';
		}
	}

	$_REQUEST['L'] = strtoupper(preg_replace('/[^A-Za-z]/', '', $_REQUEST['L']));
	$_REQUEST['R'] = strtoupper(preg_replace('/[^A-Za-z]/', '', $_REQUEST['R']));
	$_REQUEST['target'] = strtoupper(preg_replace('/[^A-Za-z]/', '', $_REQUEST['target']));
	$_REQUEST['L'] = trim($_REQUEST['L'], 'K');
	$_REQUEST['R'] = trim($_REQUEST['R'], 'K');
	$_REQUEST['name'] = preg_replace('/[^A-Za-z#0-9]/', '', $_REQUEST['name']);

	if (empty($_REQUEST['name'])) {
		$_REQUEST['name'] = "Player#1";
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
		}
	}
}

validate();
runcross();
?>
<html>
	<head>
		<title>Ashen's Rose Crossbreeding Simulator</title>
		<meta name="robots" value="noindex,nofollow">
		<link rel="canonical" href="<?php echo $_SERVER['SCRIPT_URI']; ?>">
		<link rel="stylesheet" href="crossbreed.css">
		<script lang="text/javascript" src="crossbreed.js"></script>
	</head>
	<body>

	<?php include("crossnav.php.inc"); ?>

	<h3>Ashen's Rose Crossbreed Simulator</h3>

	<p>
	To simulate a Rose crossbreed, select the strain of the bulbs that will go in the left and right splits below and click Generate. Select strain "Other" to input the genome directly (e.g. to cross player-made strains). The simulator will generate a list of possible child genomes, excluding mutation (duplication or subtraction of a gene at the splice point). After doing the actual crossbreed and planting your Rose, you can compare (visually and/or testing) to the generated table to determine which genome(s) match your new Rose strain to target further crossbreeding.
	</p>

	<p>
	NOTE: This tool currently uses old (T6 and earlier) gene color codes. Please use that format until all the mappings to new colors are known and the tool can be updated. Start/End genes (K) are optional. See <a href="https://atitd.wiki/tale8/Flower_Genome_Theories">Flower Genome Theories</a> at the <a href="https://atitd.wiki/">ATITD Wiki</a> for details on ATITD Rose of Ra genomes. The details there as well as the other <a href="https://atitd.wiki/tale8/Guides#Genetics">Genetics Guides</a> are the basis for how this simulator interprets genomes to generate these data tables.
	</p>

	<noscript>
		<p>WARNING: You need javascript for proper functioning of genome entry and for crossing off non-matching children from results.</p>
	</noscript>

	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table cellpadding="3" cellspacing="0">
			<tr>
				<th></th>
				<th>Strain</th>
				<th>Genome</th>
			</tr>
			<tr>
				<th>Left</th>
				<td>
				<select name="LS" onchange="onStrainSelect(this, 'L');">
				<?php listchoices('LS'); ?>
				</select>
				</td>
				<td>
				<input type="text" id="L" name="L" size="100" <?php echo $inputstate['LS']; ?> value="<?php echo $_REQUEST['L']; ?>">
				</td>
			</tr>
			<tr>
				<th>Right</th>
				<td>
				<select name="RS" onchange="onStrainSelect(this, 'R');">
				<?php listchoices('RS'); ?>
				</select>
				</td>
				<td>
				<input type="text" id="R" name="R" size="100" <?php echo $inputstate['RS']; ?> value="<?php echo $_REQUEST['R']; ?>">
				</td>
			</tr>
			<tr>
				<th colspan='2'>Target Sequence</th>
				<td><input type="text" id="target" name="target" size="60" placeholder="Example: ROYGU" value="<?php echo $_REQUEST['target']; ?>"></td>
			</tr>
			<tr>
				<th colspan='2'>Cross</th>
				<td><input type="text" id="name" name="name" size="20" value="<?php echo $_REQUEST['name']; ?>"></td>
			</tr>
		</table>
		<input type="submit" value="Generate" style="margin-top: 1em;">
	</form>

	<p>* I think Bloodheart and Dawn's Blush genomes are swapped, or the images are on the Wikis. Will have to verify these once roses are available.</p>

	<?php printerrors(); ?>

	<?php if (!empty($ltgenome) && !empty($rtgenome)) { ?>
	<h3>Results</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th style='text-align: center;'>X</th><th>Splint</th><th>UP</th><th>LP</th>Lv</th><th>St</th><th>Size</th><th>L</th><th>Genome</th></tr>
		<?php printresults(); ?>
	</table>

	<p>Total of <?php echo count($children); ?> possible children, excluding gene duplication/deletion mutations</p>
	<?php } ?>

	<p>
	If you find this tool useful and want to show your appreciation, I can always (at least for some time) use more Nut's Essence, Khefre's Essence, Geb's Tears, and Revelation Solvents (or mats to make them). :)
	</p>

	<hr>

	<h3>Known Strains</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Name</th><th>UP</th><th>LP</th><th>Lv</th><th>St</th><th>Size</th><th>L</th><th>Genome</th></tr>
		<?php printknowns(); ?>
	</table>

	<p>* I think Bloodheart and Dawn's Blush genomes are swapped, or the images are on the Wikis. Will have to verify these once roses are available.</p>

	<div class='crosskey'>
	<h3>Rose Attributes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Heading</th><th>Definition</th></tr>
		<tr><td>UP</td><td>Upper Petal Color</td></tr>
		<tr><td>LP</td><td>Lower Petal Color</td></tr>
		<tr><td>Lv</td><td>Leaf Color</td></tr>
		<tr><td>St</td><td>Stamen Color</td></tr>
		<tr><td>Size</td><td>List size genes (D = dwarf, G = giant)</td></tr>
		<tr><td>L</td><td>Genome Length</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Petal/Leaf/Stamen Color Key</h3>
	<?php include("colorkey.php.inc"); ?>
	</div>
	</body>
</html>

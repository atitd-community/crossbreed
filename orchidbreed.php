<?php
require_once('colorsum.php.inc');

$known["Amazon Queen"]		= "ILALILALILALIVTPAVPTTLTVLPIV";
$known["Arabian Nights"]	= "ILALILALILALIVPAAIPIILPTVAPVLI";
$known["Avalon"]			= "VLPAVLAVILVPAVILALILALILALI";
$known["Emperor"]			= "VAPTVPAAVTLAVPAAVILALILALILALI";
$known["Hidden in Darkness"]= "TPTPITALILALILTLPTTTLPIPAAPTTLPAAA";
$known["Masquerade"]		= "VVPTTVVTLVLPIVPITATIPVLTVIPVVPTTVVVILALILALIL";
$known["Sabertooth"]		= "VPAALAVILVAPAAVILALILALILALI";
$known["Shangri-La"]		= "TVPIVALPIVILALILALILALIVPTTVLIV";

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

function colormap($c) {
	switch ($c) {
		case 'I':
			return 'C';
		case 'T':
			return 'M';
		case 'A':
			return 'Y';
		default:
			return '';
	}
}

function printgenome($label, $genome, $checks = false) {
	$upetal	= "";
	$mpetal	= "";
	$size	= 0;
	$fpetal	= "";
	$lpetal	= "";
	$stem	= "";
	$leaves	= "";

	$genome = sprintf('R%sR', trim($genome));
	$glen = strlen($genome);

	for ($i = 0 ; $i < $glen ; $i++)
	{
		$quad = substr($genome, $i, 4);
		$c0 = $quad[0];
		$c1 = $quad[1];
		$c2 = $quad[2];
		$c3 = $quad[3];

		if ($quad == "PITA") {
			$size += 1;
		}

		if ($quad == "ATIP") {
			$size -= 1;
		}

		if ($c0 == 'L') {
			$stem .= colormap($c1);
		}

		if ($c1 == 'L') {
			$leaves .= colormap($c0);
		}

		if ($c0 == 'P') {
			$upetal .= colormap($c1);
			if ($c1 == $c2) {
				$mpetal .= colormap($c1);
			}
		}

		if ($c1 == 'P') {
			$fpetal .= colormap($c0);
		}

		if (($c0 == 'L') && ($c1 == 'P')) {
			$lpetal .= colormap($c2);
		}

		if (($c0 == $c1) && ($c1 == $c2)) {
			$lpetal .= colormap($c0);
		}
	}

	printf("<tr>");

	if ($checks) {
		if (preg_match("/^[LR]:.*$/", $label)) {
			printf("<th></th>");
		} else {
			printf("<td><input type='checkbox' onchange='onCheck(this);'></td>");
		}
	}

	if (!empty($label)) {
		printf("<td>%s</td>", $label);
	}

	$upetals = colorsum($upetal);
	$lpetals = colorsum($lpetal);
	$mpetals = colorsum($mpetal);
	$fpetals = colorsum($fpetal);
	$stems = colorsum($stem);
	$leavess = colorsum($leaves);

	printf("<td title=\"%s\">%s</td>", $upetal, $upetals);
	printf("<td title=\"%s\">%s</td>", $lpetal, $lpetals);

	printf("<td title=\"%s\">%s</td>", $mpetal, $mpetals);
	printf("<td title=\"%s\">%s</td>", $fpetal, $fpetals);

	printf("<td title=\"%s\">%s</td>", $stem, $stems);
	printf("<td title=\"%s\">%s</td>", $leaves, $leavess);

	printf("<td>%d</td>", $size);

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

	printgenome('L:' . (!empty($_REQUEST['LS']) ? $_REQUEST['LS'] : 'Other'), $ltgenome, true);
	printgenome('R:' . (!empty($_REQUEST['RS']) ? $_REQUEST['RS'] : 'Other'), $rtgenome, true);

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
	$_REQUEST['L'] = trim($_REQUEST['L'], 'R');
	$_REQUEST['R'] = trim($_REQUEST['R'], 'R');
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
		<title>Ashen's Orchid Crossbreeding Simulator</title>
		<meta name="robots" value="noindex,nofollow">
		<link rel="canonical" href="<?php echo $_SERVER['SCRIPT_URI']; ?>">
		<link rel="stylesheet" href="crossbreed.css">
		<script lang="text/javascript" src="crossbreed.js"></script>
	</head>
	<body>

	<?php include("crossnav.php.inc"); ?>

	<h3>Ashen's Orchid Crossbreed Simulator</h3>

	<p>
	To simulate a Orchid crossbreed, select the strain of the bulbs that will go in the left and right splits below and click Generate. Select strain "Other" to input the genome directly (e.g. to cross player-made strains). The simulator will generate a list of possible child genomes, excluding mutation (duplication or subtraction of a gene at the splice point). After doing the actual crossbreed and planting your Orchid, you can compare (visually and/or testing) to the generated table to determine which genome(s) match your new Orchid strain to target further crossbreeding.
	</p>

	<p>
	NOTE: This tool uses NEW (T8) gene color codes. Start/End genes (R) are optional. See <a href="https://atitd.wiki/tale8/Flower_Genome_Theories">Flower Genome Theories</a> at the <a href="https://atitd.wiki/">ATITD Wiki</a> for details on ATITD Orchid genomes. The details there as well as the other <a href="https://atitd.wiki/tale8/Guides#Genetics">Genetics Guides</a> are the basis for how this simulator interprets genomes to generate these data tables.
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
				<select id="LS" name="LS" onchange="onStrainSelect(this, 'L');">
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
				<select id="RS" name="RS" onchange="onStrainSelect(this, 'R');">
				<?php listchoices('RS'); ?>
				</select>
				</td>
				<td>
				<input type="text" id="R" name="R" size="100" <?php echo $inputstate['RS']; ?> value="<?php echo $_REQUEST['R']; ?>">
				</td>
			</tr>
			<tr>
				<th colspan='2'>Target Sequence</th>
				<td><input type="text" id="target" name="target" size="60" placeholder="Example: ROYG" value="<?php echo $_REQUEST['target']; ?>"></td>
			</tr>
			<tr>
				<th colspan='2'>Cross Name</th>
				<td><input type="text" id="name" name="name" size="20" value="<?php echo $_REQUEST['name']; ?>"></td>
			</tr>
		</table>
		<input type="button" name="swap" value="Swap L/R" onclick="onStrainSwap()">
		<input type="submit" value="Generate" style="margin-top: 1em;">
	</form>

	<?php printerrors(); ?>

	<?php if (!empty($ltgenome) && !empty($rtgenome)) { ?>
	<h3>Results</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th style='text-align: center;'>X</th><th>Splint</th><th>UP</th><th>LP</th><th>MP</th><th>FP</th><th>St</th><th>Lv</th><th>Size</th><th>L</th><th>Genome</th></tr>
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
		<tr><th>Name</th><th>UP</th><th>LP</th><th>MP</th><th>FP</th><th>St</th><th>Lv</th><th>Size</th><th>L</th><th>Genome</th></tr>
		<?php printknowns(); ?>
	</table>

	<div class='crosskey'>
	<h3>Orchid Attributes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Heading</th><th>Definition</th></tr>
		<tr><td>UP</td><td>Upper Petal Color</td></tr>
		<tr><td>LP</td><td>Lower Petal Color</td></tr>
		<tr><td>MP</td><td>Main Petal Color</td></tr>
		<tr><td>FP</td><td>Front Petal Color</td></tr>
		<tr><td>St</td><td>Stem Color</td></tr>
		<tr><td>Lv</td><td>Leaf Color</td></tr>
		<tr><td>Size</td><td>Sum of size genes (-1 = dwarf, 1 = giant)</td></tr>
		<tr><td>L</td><td>Genome Length</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Orchid Phenomes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Phenome</th><th>Effect</th></tr>
		<tr><td>Px</td><td>Upper Petal Color x</td></tr>
		<tr><td>xxx</td><td>Lower Petal Color x</td></tr>
		<tr><td>LPx</td><td>Lower Petal Color x</td></tr>
		<tr><td>Pxx</td><td>Main Petal Color x</td></tr>
		<tr><td>xP</td><td>Front Petal Color x</td></tr>
		<tr><td>Lx</td><td>Stem Color x</td></tr>
		<tr><td>xL</td><td>Leaf Color x</td></tr>
		<tr><td>PITA</td><td>Giant (Size += 1)</td></tr>
		<tr><td>ATIP</td><td>Dwarf (Size -= 1)</td></tr>
		<tr><th>x</th><th>Color</th></tr>
		<tr><td>I</td><td>Cyan</td></tr>
		<tr><td>T</td><td>Magenta</td></tr>
		<tr><td>A</td><td>Yellow</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Petal/Leaf/Stem Color Key</h3>
	<?php include("colorkey.php.inc"); ?>
	</div>
	</body>
</html>

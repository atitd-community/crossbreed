<?php
require_once('colorsum.php.inc');
require_once('crossbreed.php.inc');

$known["Bloodheart*"]		= "ILILILIL";
$known["Dawn's Blush*"]		= "TTPLTAAPILPTTPTLTAAP";
$known["Goldenleaves"]		= "LTALTALTALTA";
$known["Hatch's Bud"]		= "PTTPAAPAAIIPAAPAAPLPLTLT";
$known["Heart of Darkness"]	= "PLPIILTLPLILTLPLPIILTL";
$known["Night Bloom"]		= "PIIPTTPAAIIPTTPAAPLILPLILTPITALATIP";
$known["Onion Skin"]		= "PTTPTTPAALPLTLPLTLPLTLPLTLILI";
$known["Pantomime"]			= "TTPITTPITTPITTPI";
$known["Pink Giant"]		= "PIILPITALPITALTTP";
$known["Red Dwarf"]			= "PTTPTTPAAPAAPAAILILTLTLTLATIP";
$known["White Giant"]		= "PITALTLTLT";

$inputstate['LS'] = "";
$inputstate['RS'] = "";
$ltgenome = "";
$rtgenome = "";
$errors = "";

$children = array();
$mutations = array();

function petalmap($c) {
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

function leafmap($c) {
	switch ($c) {
		case 'P':
			return 'C';
		case 'I':
			return 'M';
		case 'T':
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

	$genome = sprintf('R%sR', trim($genome));
	$glen = strlen($genome);

	for ($i = 0 ; $i < $glen ; $i++)
	{
		$quint = substr($genome, $i, 5);
		$c0 = $quint[0];
		$c1 = $quint[1];
		$c2 = $quint[2];

		if ($quint == "PITAL") {
			$giant .= "G";
		}

		if ($quint == "LATIP") {
			$dwarf .= "D";
		}

		if ($c0 == 'L') {
			$leaves .= leafmap($c1);
		}

		if ($c1 == 'L') {
			$stamen .= leafmap($c0);
		}

		if (($c0 == 'P') && ($c1 == $c2)) {
			$lpetal .= petalmap($c2);
		}

		if (($c2 == 'P') && ($c0 == $c1)) {
			$upetal .= petalmap($c0);
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
	$leavess = colorsum($leaves);
	$stamens = colorsum($stamen);

	printf("<td title=\"%s\">%s</td>", $upetal, $upetals);
	printf("<td title=\"%s\">%s</td>", $lpetal, $lpetals);

	printf("<td title=\"%s\">%s</td>", $leaves, $leavess);
	printf("<td title=\"%s\">%s</td>", $stamen, $stamens);

	printf("<td>%s%s</td>", $dwarf, $giant);

	printf("<td>%d</td>", strlen($genome));

	if (!empty($_REQUEST['target'])) {
		$genome = str_replace($_REQUEST['target'], '<span class="target">' . $_REQUEST['target'] . "</span>", $genome);
	}

	printf("<td>%s</td>", $genome);

	printf("</tr>\n");
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
	To simulate a Rose crossbreed, select the strain of the bulbs that will go in the left and right splits below and click Generate. Select strain "Other" to input the genome directly (e.g. to cross player-made strains). The simulator will generate a list of possible child genomes, with or without mutations (duplication or subtraction of a gene at the splice point). After doing the actual crossbreed and planting your Rose, you can compare (visually and/or testing) to the generated table to determine which genome(s) match your new Rose strain to target further crossbreeding.
	</p>

	<p>
	NOTE: This tool uses T8 gene color codes. Start/End genes (R) are optional. See <a href="https://atitd.wiki/tale11/Flower_Genome_Theories">Flower Genome Theories</a> at the <a href="https://atitd.wiki/">ATITD Wiki</a> for details on ATITD Rose of Ra genomes. The details there as well as the other <a href="https://atitd.wiki/tale11/Guides#Genetics">Genetics Guides</a> are the basis for how this simulator interprets genomes to generate these data tables.
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
				<td><input type="text" id="target" name="target" size="60" placeholder="Example: PITAL" value="<?php echo $_REQUEST['target']; ?>"></td>
			</tr>
			<tr>
				<th colspan='2'>Cross Name</th>
				<td><input type="text" id="name" name="name" size="20" value="<?php echo $_REQUEST['name']; ?>"></td>
			</tr>
		</table>
		<input type="button" name="swap" value="Swap L/R" onclick="onStrainSwap()">
		<input type="submit" value="Generate" style="margin-top: 1em;">
		<input type="checkbox" name="mutate"<?php if ($_REQUEST['mutate']=='on') echo ' checked="checked"'; ?>>Include mutations (25% probability)</input>
	</form>

	<p>* I think Bloodheart and Dawn's Blush genomes are swapped, or the images are on the Wikis. Will have to verify these once roses are available.</p>

	<?php printerrors(); ?>

	<?php if (!empty($ltgenome) && !empty($rtgenome)) { ?>
	<h3>Crossbreed Results</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th style='text-align: center;'><input type='checkbox' onchange='onCheckAll(this);'></th><th>Splint</th><th>UP</th><th>LP</th><th>Lv</th><th>St</th><th>Size</th><th>L</th><th>Genome</th></tr>
		<?php printparents(); ?>
		<?php printresults($children); ?>
		<?php if ($_REQUEST['mutate'] == 'on') { ?>
		<tr><th colspan="9" style="text-align: center;"></th></tr>
		<?php printresults($mutations); ?>
		<?php } ?>
	</table>

	<?php if ($_REQUEST['mutate'] == 'on') { ?>
	<p>Total of <?php echo (count($children) + count($mutations)); ?> possible children (including gene duplication/deletion mutations)</p>
	<?php } else { ?>
	<p>Total of <?php echo count($children); ?> possible children (excluding gene duplication/deletion mutations)</p>
	<?php } ?>
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
	<h3>Rose Phenomes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Phenome</th><th>Effect</th></tr>
		<tr><td>Pxx</td><td>Outer Petal Color x</td></tr>
		<tr><td>xxP</td><td>Inner Petal Color x</td></tr>
		<tr><td>Ly</td><td>Leaf Color y</td></tr>
		<tr><td>yL</td><td>Stamen Color y</td></tr>
		<tr><td>PITAL</td><td>Giant</td></tr>
		<tr><td>LATIP</td><td>Dwarf</td></tr>
		<tr><th>x</th><th>Color</th></tr>
		<tr><td>I</td><td>Cyan</td></tr>
		<tr><td>T</td><td>Magenta</td></tr>
		<tr><td>A</td><td>Yellow</td></tr>
		<tr><th>y</th><th>Color</th></tr>
		<tr><td>P</td><td>Cyan</td></tr>
		<tr><td>I</td><td>Magenta</td></tr>
		<tr><td>T</td><td>Yellow</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Petal/Leaf/Stamen Color Key</h3>
	<?php include("colorkey.php.inc"); ?>
	</div>
	</body>
</html>

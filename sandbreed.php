<?php
require_once('colorsum.php.inc');
require_once('crossbreed.php.inc');

$known["Calliope"]			= "VVIZTTAZLILZIVLPITAZVVIPVTZLAIZAAT";
$known["Clear Skies"]		= "PLPAZZZAPLZZLPAPZVTV";
$known["Cookie Jar"]		= "TLTATLTATZPLZILZPLZILZTLZ";
$known["Corona"]			= "PVZIVZTVZPVZIVZTVZPVZIVZTV";
$known["Love's Touch"]		= "ILIPILIZATIPITAZVIV";
$known["Lemondrop"]			= "TAZTAZTAZTLZTLZTLZ";
$known["Otter's Sunrise"]	= "PITAILILZPITATLTLTIVZIV";
$known["Pluribus' Folly"]	= "VPVPTATATAPAPAPAVPVIVIVIAIAIAPITATIPITAPITA";
$known["Sacrifice"]			= "PITIPCPITIPCPITIPCPITIPC";
$known["Sunshine"]			= "VTVTVTLTLTATA";
$known["Petit Mal"]			= "ATIPLPAPILIATIPTLTATVPVIVTVAI";

$inputstate['LS'] = "";
$inputstate['RS'] = "";
$ltgenome = "";
$rtgenome = "";
$errors = "";

$children = array();
$mutations = array();

function colormap($c) {
	switch ($c) {
		case 'I':
			return 'M';
		case 'T':
			return 'Y';
		case 'P':
			return 'C';
		default:
			return '';
	}
}

function printgenome($label, $genome, $checks = false) {
	$center		= "";
	$ring		= "";
	$size		= 0;
	$olpetal	= "";
	$orpetal	= "";
	$iupetal	= "";
	$ilpetal	= "";

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
			$size -= 3;
		}

		if ($c0 == 'A') {
			$olpetal .= colormap($c1);
		}

		if ($c1 == 'A') {
			$orpetal .= colormap($c0);
		}

		if ($c0 == 'L') {
			$iupetal .= colormap($c1);
		}

		if ($c1 == 'L') {
			$ilpetal .= colormap($c0);
		}

		if ($c0 == 'V') {
			$ring .= colormap($c1);
		}

		if ($c1 == 'V') {
			$center .= colormap($c0);
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

	$centers = colorsum($center);
	$rings = colorsum($ring);
	$olpetals = colorsum($olpetal);
	$orpetals = colorsum($orpetal);
	$iupetals = colorsum($iupetal);
	$ilpetals = colorsum($ilpetal);

	printf("<td title=\"%s\">%s</td>", $center, $centers);
	printf("<td title=\"%s\">%s</td>", $ring, $rings);

	printf("<td title=\"%s\">%s</td>", $olpetal, $olpetals);
	printf("<td title=\"%s\">%s</td>", $orpetal, $orpetals);

	printf("<td title=\"%s\">%s</td>", $iupetal, $iupetals);
	printf("<td title=\"%s\">%s</td>", $ilpetal, $ilpetals);

	printf("<td>%d</td>", $size);

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
		<title>Ashen's Sand Bloom Crossbreeding Simulator</title>
		<meta name="robots" value="noindex,nofollow">
		<link rel="canonical" href="<?php echo $_SERVER['SCRIPT_URI']; ?>">
		<link rel="stylesheet" href="crossbreed.css">
		<script lang="text/javascript" src="crossbreed.js"></script>
	</head>
	<body>

	<?php include("crossnav.php.inc"); ?>

	<h3>Ashen's Sand Bloom Crossbreed Simulator</h3>

	<p>
	To simulate a Sand Bloom crossbreed, select the strain of the bulbs that will go in the left and right splits below and click Generate. Select strain "Other" to input the genome directly (e.g. to cross player-made strains). The simulator will generate a list of possible child genomes, with or without mutations (duplication or subtraction of a gene at the splice point). After doing the actual crossbreed and planting your Sand Bloom, you can compare (visually and/or testing) to the generated table to determine which genome(s) match your new Sand Bloom strain to target further crossbreeding.
	</p>

	<p>
	NOTE: This tool uses T8 gene color codes. Start/End genes (R) are optional. See <a href="https://atitd.wiki/tale11/Flower_Genome_Theories">Flower Genome Theories</a> at the <a href="https://atitd.wiki/">ATITD Wiki</a> for details on ATITD Sand Bloom genomes. The details there as well as the other <a href="https://atitd.wiki/tale11/Guides#Genetics">Genetics Guides</a> are the basis for how this simulator interprets genomes to generate these data tables.
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
				<td><input type="text" id="target" name="target" size="60" placeholder="Example: PITA" value="<?php echo $_REQUEST['target']; ?>"></td>
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

	<?php printerrors(); ?>

	<?php if (!empty($ltgenome) && !empty($rtgenome)) { ?>
	<h3>Crossbreed Results</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th style='text-align: center;'><input type='checkbox' onchange='onCheckAll(this);'></th><th>Splint</th><th>C</th><th>R</th><th>OLP</th><th>ORP</th><th>IUP</th><th>ILP</th><th>Size</th><th>L</th><th>Genome</th></tr>
		<?php printparents(); ?>
		<?php printresults($children); ?>
		<?php if ($_REQUEST['mutate'] == 'on') { ?>
		<tr><th colspan="11" style="text-align: center;"></th></tr>
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
		<tr><th>Name</th><th>C</th><th>R</th><th>OLP</th><th>ORP</th><th>IUP</th><th>ILP</th><th>Size</th><th>L</th><th>Genome</th></tr>
		<?php printknowns(); ?>
	</table>

	<div class='crosskey'>
	<h3>Sand Bloom Attributes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Heading</th><th>Definition</th></tr>
		<tr><td>C</td><td>Center Color</td></tr>
		<tr><td>R</td><td>Ring Color</td></tr>
		<tr><td>OLP</td><td>Outer Left Petal Color</td></tr>
		<tr><td>ORP</td><td>Outer Right Petal Color</td></tr>
		<tr><td>IUP</td><td>Inner Upper Petal Color</td></tr>
		<tr><td>ILP</td><td>Inner Lower Petal Color</td></tr>
		<tr><td>Size</td><td>Sum of size genes (-3 = dwarf, 1 = giant)</td></tr>
		<tr><td>L</td><td>Genome Length</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Sand Bloom Phenomes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Phenome</th><th>Effect</th></tr>
		<tr><td>Ax</td><td>Outer Left Petal Color x</td></tr>
		<tr><td>xA</td><td>Outer Right Petal Color x</td></tr>
		<tr><td>Lx</td><td>Inner Upper Petal Color x</td></tr>
		<tr><td>xL</td><td>Inner Lower Petal Color x</td></tr>
		<tr><td>Vx</td><td>Center Color x</td></tr>
		<tr><td>xV</td><td>Ring Color x</td></tr>
		<tr><td>PITA</td><td>Giant (Size += 1)</td></tr>
		<tr><td>ATIP</td><td>Dwarf (Size -= 3)</td></tr>
		<tr><th>x</th><th>Color</th></tr>
		<tr><td>P</td><td>Cyan</td></tr>
		<tr><td>I</td><td>Magenta</td></tr>
		<tr><td>T</td><td>Yellow</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Center/Petal Color Key</h3>
	<?php include("colorkey.php.inc"); ?>
	</div>
	</body>
</html>

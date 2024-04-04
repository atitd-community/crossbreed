<?php
require_once('colorsum.php.inc');
require_once('crossbreed.php.inc');

$known["Abacua"]			= "PITATPPPTTTIIIATPP";
$known["Asatru"]			= "TATPILITITALTPPLTATPI";
$known["Caodaism"]			= "APTALPITALAATPP";
$known["Candomble "]		= "ITPPTLTIPPALPITALTPPLATPPTLTTTPP";
$known["Candomble Bumble"]	= "ITPPTLTIPPALPITALTPPLATPPTLTTTPPP";
$known["Druidism"]			= "TATPILITITALAATPP";
$known["Druze"]				= "APLITITALITPPT";
$known["Macumba"]			= "TATPILAPTALAPLTIPPA";
$known["Palo Mayombe"]		= "IPPATLAATPPLPITA";
$known["Romani Nostrum"]	= "AATPPPLIIIPILTATPPLPITA";
$known["Santeria"]			= "TIPPALIPPATLAPTALTPPIA";
$known["Satanism"]			= "APLPITALTPPLIIIPI";
$known["Umbanda Olanda"]	= "PAAIILTPPPLPAAIILPAAIILAATPPP";
$known["Voodoo"]			= "PITALITITALITPPT";
$known["Wicca"]				= "TPPIALIPPATLAPLPITA";


$inputstate['LS'] = "";
$inputstate['RS'] = "";
$ltgenome = "";
$rtgenome = "";
$errors = "";

$children = array();
$mutations = array();

function printgenome($label, $genome, $checks = false) {
	$grass = "";
	$sand = "";
	$dirt = "";
	$clay = "";
	$unk1 = "";
	$unk2 = "";
	$unk3 = "";
	$unk4 = "";

	$genome = sprintf('K%sK', trim($genome));
	$glen = strlen($genome);

	for ($i = 0 ; $i < $glen ; $i++)
	{
		$doub = substr($genome, $i, 2);
		$trip = substr($genome, $i, 3);
		$quad = substr($genome, $i, 4);
		$quint = substr($genome, $i, 5);
		$sext = substr($genome, $i, 6);
		$sept = substr($genome, $i, 7);
		$c0 = $quint[0];
		$c1 = $quint[1];
		$c2 = $quint[2];

		if ($quint == "ITITA") {
			$grass .= "G";
		}

		if ($sept == "IPPATLA") {
			$sand .= "S";
		}

		if ($doub == "AP") {
			$dirt .= "D";
		}

		if ($trip == "PPP") {
			$clay .= "C";
		}

		if ($quad == "ATPP") {
			$unk1 .= "?";
		}

		if ($trip == "PPT") {
			$unk2 .= "?";
		}

		if ($quad == "PITA") {
			$unk3 .= "?";
		}

		if ($quint == "TATPI") {
			$unk4 .= "?";
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

	printf("<td>%s</td>", (!empty($grass) ? $grass : "-"));
	printf("<td>%s</td>", (!empty($sand) ? $sand : "-"));
	printf("<td>%s</td>", (!empty($dirt) ? $dirt : "-"));
	printf("<td>%s</td>", (!empty($clay) ? $clay : "-"));
	printf("<td>%s</td>", (!empty($unk1) ? $unk1 : "-"));
	printf("<td>%s</td>", (!empty($unk2) ? $unk2 : "-"));
	printf("<td>%s</td>", (!empty($unk3) ? $unk3 : "-"));
	printf("<td>%s</td>", (!empty($unk4) ? $unk4 : "-"));

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
		<title>Ashen's Wheat Crossbreeding Simulator</title>
		<meta name="robots" value="noindex,nofollow">
		<link rel="canonical" href="<?php echo $_SERVER['SCRIPT_URI']; ?>">
		<link rel="stylesheet" href="crossbreed.css">
		<script lang="text/javascript" src="crossbreed.js"></script>
	</head>
	<body>

	<?php include("crossnav.php.inc"); ?>

	<h3>Ashen's Wheat Crossbreed Simulator</h3>

	<p>
	To simulate a Wheat crossbreed, select the strains of wheat that will go in the left and right splits below and click Generate. Select strain "Other" to input the genome directly (e.g. to cross player-made strains). The simulator will generate a list of possible child genomes, with or without mutations (duplication or subtraction of a gene at the splice point). After doing the actual crossbreed and planting your wheat, you can compare (through testing) to the generated table to determine which genome(s) match your new wheat strain to target further crossbreeding.
	</p>

	<p>
	NOTE: This tool uses T8 gene color codes. Start/End genes (R) are optional. See [TBD] at the <a href="https://atitd.wiki/">ATITD Wiki</a> for details on ATITD Wheat genomes. The details there as well as the other <a href="https://atitd.wiki/tale11/Guides#Genetics">Genetics Guides</a> are the basis for how this simulator interprets genomes to generate these data tables.
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
				<td><input type="text" id="target" name="target" size="60" placeholder="Example: OYYG" value="<?php echo $_REQUEST['target']; ?>"></td>
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
		<tr><th style='text-align: center;'><input type='checkbox' onchange='onCheckAll(this);'></th><th>Splint</th><th>A</th><th>C</th><th>G</th><th>K</th><th>Q</th><th>S</th><th>V</th><th>H</th><th>L</th><th>Genome</th></tr>
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
		<tr><th>Name</th><th>A</th><th>C</th><th>G</th><th>K</th><th>Q</th><th>S</th><th>V</th><th>H</th><th>L</th><th>Genome</th></tr>
		<?php printknowns(); ?>
	</table>

	<div class='crosskey'>
	<h3>Wheat Attributes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Heading</th><th>Definition</th></tr>
		<tr><td>Grass</td><td>Wheat will grow on grass</td></tr>
		<tr><td>Sand</td><td>Wheat will grow on sand</td></tr>
		<tr><td>Dirt</td><td>Wheat will grow on dirt</td></tr>
		<tr><td>Clay</td><td>Wheat will grow on clay</td></tr>
		<tr><td>Unk1</td><td>Unknown</td></tr>
		<tr><td>Unk2</td><td>Unknown</td></tr>
		<tr><td>Unk3</td><td>Unknown</td></tr>
		<tr><td>Unk4</td><td>Unknown</td></tr>
		<tr><td>L</td><td>Genome Length</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Wheat Phenomes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Phenome</th><th>Effect</th></tr>
		<tr><td>ITITA</td><td></td>Grass</tr>
		<tr><td>IPPATLA</td><td></td>Sand</tr>
		<tr><td>AP</td><td></td>Dirt</tr>
		<tr><td>TPI</td><td></td>Clay</tr>
		<tr><td>PTTP</td><td>Unknown</td></tr>
		<tr><td>PPP</td><td>Unknown</td></tr>
		<tr><td>AAAPI</td><td>Unknown</td></tr>
		<tr><td>ATP</td><td>Unknown</td></tr>
	</table>

	</div>
	</body>
</html>

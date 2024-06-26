<?php
require_once('crossbreed.php.inc');

$known["Constitution Peak"]		= "KPITATATATKIPPIKI";
$known["Jacob's Field*"]		= "ATATKIITATATKITPPIATAT";
$known["Nile Green*"]			= "IPAPATATATAPATIPTTPAPITTIPP";
$known["Old Dog"]				= "PIATATATATIPIPAPT";
$known["Old Egypt"]				= "PTTIATATIPATIPTAIATIPATPPPAAPITAPPTIITATP";
$known["Sinai Sage"]			= "KPKIPTKPATATATKIPTIPIKI";
$known["Sunset Pond"]			= "KPKIPTKPATATATKIPTIPIKIKPKP";
$known["Symphony Ridge Gold"]	= "KPKPATATKPKIPTKPIPTKPATATKTKIKIKIKPKIKP";
$known["Wayfarer's Wisk"]		= "IPPTIPAATTPPATPPATPPATPPATAATTVPPIPTTIPPTIPKPAATTKITKPTIPPTIPVPPIVPPI";

$inputstate['LS'] = "";
$inputstate['RS'] = "";
$ltgenome = "";
$rtgenome = "";
$errors = "";

$children = array();
$mutations = array();

function printgenome($label, $genome, $checks = false) {
	$weeds	= 5;
	$waters	= 0;
	$nplus	= 'N';
	$nminus	= 'N';
	$flax	= 0;
	$rotten	= 0;
	$seeds	= 0;
	$twp = false;

	$genome = sprintf('R%sR', trim($genome));
	$glen = strlen($genome);

	for ($i = 0 ; $i < $glen ; $i++)
	{
		$dupl = substr($genome, $i, 2);
		$trip = substr($genome, $i, 3);
		$quad = substr($genome, $i, 4);
		$sixt = substr($genome, $i, 6);

		if ($dupl == 'PI') {
			$seeds++;
		}

		if ($dupl == 'IP') {
			$weeds--;
		}

		if ($dupl == 'TP') {
			$tpw = !$tpw;
		}

		if ($quad == 'ATAT') {
			$flax++;
		}

		if ($sixt == 'ATATAT') {
			$waters++;
			$weeds--;
		}

		if ($quad == 'PPAT') {
			$rotten++;
		}

		if ($trip == 'PIT') {
			$nplus = 'Y';
		}

		if ($trip == 'API') {
			$nminus = 'Y';
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

	$weeds = max($weeds, 1);

	printf("<td>%d</td>", $waters);
	printf("<td>%d</td>", $weeds);

	printf("<td%s>%d</td>", ($tpw ? " class=\"hilite\"" : ""), ($weeds + ($tpw ? 1 : 0)));

	printf("<td>%d</td>", $flax);
	printf("<td>%d</td>", $rotten);
	printf("<td>%d</td>", $seeds);

	printf("<td>%s</td>", $nplus);
	printf("<td>%s</td>", $nminus);

	$actions = $weeds + $waters;
	$flaxtot = $flax + $rotten;

	if ($actions > 0) {
		printf("<td>%0.1f</td>", $flaxtot / $actions);
	} else {
		printf("<td>NaN</td>");
	}

	printf("<td>%d</td>", strlen($genome));

	if (!empty($_REQUEST['target'])) {
		$genome = str_replace($_REQUEST['target'], '<span class="target">' . $_REQUEST['target'] . "</span>", $genome);
	}

	$genome = str_replace("TP", '<span class="hilite">TP</span>', $genome);

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
		<title>Ashen's Flax Crossbreeding Simulator</title>
		<meta name="robots" value="noindex,nofollow">
		<link rel="canonical" href="<?php echo $_SERVER['SCRIPT_URI']; ?>">
		<link rel="stylesheet" href="crossbreed.css">
		<script lang="text/javascript" src="crossbreed.js"></script>
	</head>
	<body>

	<?php include("crossnav.php.inc"); ?>

	<h3>Ashen's Flax Crossbreed Simulator</h3>

	<p>
	To simulate a Flax crossbreed, select the strain of the seeds that will go in the left and right splits below and click Generate. Select strain "Other" to input the genome directly (e.g. to cross player-made strains). The simulator will generate a list of possible child genomes, with or without mutations (duplication or subtraction of a gene at the splice point). After doing the actual crossbreed and planting your Flax, you can compare (by testing) to the generated table to determine which genome(s) match your new Flax strain to target further crossbreeding.
	</p>

	<p>
	NOTE: This tool uses T8 gene color codes. Start/End genes (R) are optional. See <a href="https://atitd.wiki/tale11/Flax_Genome_Theory">Flax Genome Theory</a> at the <a href="https://atitd.wiki/">ATITD Wiki</a> for details on ATITD Flax genomes. The details there as well as the other <a href="https://atitd.wiki/tale11/Guides#Genetics">Genetics Guides</a> are the basis for how this simulator interprets genomes to generate these data tables.
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
				<td><input type="text" id="target" name="target" size="60" placeholder="Example: ATATAT" value="<?php echo $_REQUEST['target']; ?>"></td>
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

	<p>* Jacob's Field and Nile Green require one more weeding than reported here under "Weed" (current theory). Suspect something is missing from theory: weed default is supposedly 5 and theory says nothing about increasing weeds. Let me know if you know what's up. I <em>think</em> the sequence <code>TP</code> might be responsible.</p>

	<?php printerrors(); ?>

	<?php if (!empty($ltgenome) && !empty($rtgenome)) { ?>
	<h3>Crossbreed Results</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th style='text-align: center;'><input type='checkbox' onchange='onCheckAll(this);'></th><th>Splint</th><th>Water</th><th>Weed</th><th>TPW</th><th>Flax</th><th>Rotn</th><th>Seed</th><th>N+</th><th>N-</th><th>F/A</th><th>L</th><th>Genome</th></tr>
		<?php printparents(); ?>
		<?php printresults($children); ?>
		<?php if ($_REQUEST['mutate'] == 'on') { ?>
		<tr><th colspan="13" style="text-align: center;"></th></tr>
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
		<tr><th>Name</th><th>Water</th><th>Weed</th><th>TPW</th><th>Flax</th><th>Rotn</th><th>Seed</th><th>N+</th><th>N-</th><th>F/A</th><th>L</th><th>Genome</th></tr>
		<?php printknowns(); ?>
	</table>

	<p>* Jacob's Field and Nile Green require one more weeding than reported here under "Weed" (current theory). Suspect something is missing from theory: weed default is supposedly 5 and theory says nothing about increasing weeds. Let me know if you know what's up. I <em>think</em> the sequence <code>TP</code> might be responsible.</p>

	<div class='crosskey'>
	<h3>Flax Attributes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Heading</th><th>Definition</th><th>Default</th></tr>
		<tr><td>Water</td><td>Number of "Weed and Water" steps</td><td>0</td></tr>
		<tr><td>Weed</td><td>Number of "Weed" steps (current theory)</td><td>5</td></tr>
		<tr><td class="hilite">TPW</td><td>Number of "Weed" steps (TP theory)</td><td>5</td></tr>
		<tr><td>Flax</td><td>Flax yield per bed (excl. pyramid bonus)</td><td>0</td></tr>
		<tr><td>Rotn</td><td>Rotten Flax yield per bed (excl. pyramid bonus)</td><td>0</td></tr>
		<tr><td>Seed</td><td>Seed yield per seeding</td><td>0</td></tr>
		<tr><td>N+</td><td>Resistant to High Nitrogen</td><td>N</td></tr>
		<tr><td>N-</td><td>Resistant to Low Nitrogen</td><td>N</td></tr>
		<tr><td>F/A</td><td>Flax Per Action ((Flax+Rotn)/(Water+Weed))</td><td>N</td></tr>
		<tr><td>L</td><td>Genome Length</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Flax Phenomes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Phenome</th><th>Effect</th></tr>
		<tr><td>PI</td><td>+1 Seed Yield</td></tr>
		<tr><td>IP</td><td>-1 "Weed" steps</td></tr>
		<tr><td>ATAT</td><td>+1 Flax yield</td></tr>
		<tr><td>PPAT</td><td>+1 Rotten Flax yield</td></tr>
		<tr><td>ATATAT</td><td>+1 "Water" -1 "Weed" steps</td></tr>
		<tr><td>PIT</td><td>N+ Resistance</td></tr>
		<tr><td>API</td><td>N- Resistance</td></tr>
		<tr><td class="hilite">TP</td><td><em>MAYBE</em> +1 Weed, canceled out by 2nd TP?</td></tr>
	</table>
	</div>

	<div class='crosskey'>
	<h3>Gene Color Codes</h3>

	<table cellpadding="3" cellspacing="0">
		<tr><th>Tale8</th><th>Tale7</th><th>Tale6</tH</tr>
		<tr><td>[A]mber</td><td>A</td><td>G</td></tr>
		<tr><td>[P]ink</td><td>G</td><td>R</td></tr>
		<tr><td>Blac[K]</td><td>K</td><td>-</td></tr>
		<tr><td>[I]ndigo</td><td>I</td><td>O</td></tr>
		<tr><td>[T]urquoise</td><td>M</td><td>Y</td></tr>
		<tr><td>[R]ed</td><td>W</td><td>K</td></tr>
	</table>
	</div>

	</body>
</html>

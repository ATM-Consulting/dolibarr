<?php
/**
 * Script "Alias" de signature en ligne (Mode Hybride : Redirection OU Proxy)
 */

// --- CHARGEMENT ENVIRONNEMENT ---
$res = @include_once __DIR__.'/../../master.inc.php';
if (!$res) {
	$res = @include_once __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Failed to load Dolibarr environment');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/signature.lib.php';
global $conf, $db, $langs, $dolibarr_main_url_root;

// --- PARAMÈTRES ---
$type = GETPOST('type', 'alpha');
$ref = GETPOST('ref', 'alphanohtml');

if (empty($type) || empty($ref)) {
	dol_print_error(0, 'Missing parameters');
	exit;
}

$obj = null;
$db->begin();
$internal_type = '';

// --- CHARGEMENT OBJETS ---
if ($type == 'propale' || $type == 'proposal') {
	$internal_type = 'proposal';
	include_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	$obj = new Propal($db);
	$res = $obj->fetch(0, $ref);
} elseif ($type == 'contrat' || $type == 'contract') {
	$internal_type = 'contract';
	include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
	$obj = new Contrat($db);
	$res = $obj->fetch(0, $ref);
} elseif ($type == 'fichinter') {
	$internal_type = 'fichinter';
	include_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
	$obj = new Fichinter($db);
	$res = $obj->fetch(0, $ref);
} elseif ($type == 'expedition' || $type == 'shipping') {
	$internal_type = 'expedition';
	include_once DOL_DOCUMENT_ROOT.'/expedition/class/expedition.class.php';
	$obj = new Expedition($db);
	$res = $obj->fetch(0, $ref);
}

if (empty($obj) || $res <= 0) {
	dol_print_error($db, 'Object not found or error fetching ' . $ref);
	$db->rollback();
	exit;
}

// --- CALCUL URL SÉCURISÉE ---
// On génère l'URL interne (Mode=0) pour être prêt au cas où c'est le proxy
$full_secure_url = getOnlineSignatureUrl(0, $internal_type, $ref, 0, $obj);

if (strpos($full_secure_url, 'http') !== 0) {
	dol_syslog("Failed to generate secure URL: " . $full_secure_url, LOG_ERR);
	dol_print_error(0, 'Error while generating the signature link.');
	$db->rollback();
	exit;
}
$db->commit();

$redirect_url = getOnlineSignatureUrl(0, $internal_type, $ref, 1, $obj); // 1 = URL Externe
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Signature de document - ATM</title>
	<style>
		/* On enlève les marges pour que l'iframe prenne 100% de l'écran */
		body, html {
			margin: 0;
			padding: 0;
			height: 100%;
			overflow: hidden; /* Empêche le double scrollbar */
		}
		iframe {
			width: 100%;
			height: 100%;
			border: none;
		}
	</style>
</head>
<body>
<iframe src="<?php echo htmlspecialchars($redirect_url); ?>"></iframe>
</body>
</html>

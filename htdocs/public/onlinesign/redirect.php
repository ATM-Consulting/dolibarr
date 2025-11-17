<?php
/**
 * Script "Alias" de signature en ligne (mode PHP Proxy)
 * Ce script ne redirige pas le client. Il va chercher le contenu
 * de la page de signature et l'affiche comme si c'était le sien.
 */

// --- CHARGEMENT DE L'ENVIRONNEMENT DOLIBARR ---
$res = @include_once __DIR__.'/../../master.inc.php';
if (!$res) {
	$res = @include_once __DIR__.'/../../main.inc.php';
}
if (!$res) {
	die('Failed to load Dolibarr environment');
}

// Charger la librairie de signature
require_once DOL_DOCUMENT_ROOT.'/core/lib/signature.lib.php';

// Importer les variables globales de Dolibarr dans ce script
global $conf, $db, $langs, $dolibarr_main_url_root;


// --- RÉCUPÉRATION DES PARAMÈTRES (de la règle Apache) ---
$type = GETPOST('type', 'alpha');
$ref = GETPOST('ref', 'alphanohtml');

if (empty($type) || empty($ref)) {
	dol_print_error(0, 'Missing parameters');
	exit;
}

$obj = null;
$db->begin();

$internal_type = ''; // ex: 'proposal'

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

$full_secure_url = getOnlineSignatureUrl(0, $internal_type, $ref, 0, $obj);

if (strpos($full_secure_url, 'http') !== 0) {
	dol_syslog("Failed to generate secure URL: " . $full_secure_url, LOG_ERR);
	dol_print_error(0, 'Error while generating the signature link.');
	$db->rollback();
	exit;
}

$db->commit();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $full_secure_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, DOL_DATA_ROOT . '/sessions/cookie_proxy.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, DOL_DATA_ROOT . '/sessions/cookie_proxy.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$output = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200 || $output === false) {
	dol_print_error(0, 'Failed to fetch signature page content internally. Code: '.$http_code);
	exit;
}


// --- DÉBUT DE LA CORRECTION DES LIENS ---

// 1. Récupérer le nom de domaine public (ex: "signature-propal...")
$public_domain_name = rtrim(getDolGlobalString(strtoupper($internal_type).'_SIGNATURE_ALIAS_URL'), '/');
$public_domain_name = preg_replace('#^https?://#', '', $public_domain_name); // Nettoyer au cas où

if (empty($public_domain_name)) {
	$public_domain_name = $_SERVER['HTTP_HOST'];
}

// 2. Définir les chaînes à corriger
$bad_strings = array(
	'href="' . $public_domain_name . '/',
	'src="' . $public_domain_name . '/',
	'action="' . $public_domain_name . '/'
);

// 3. Définir les chaînes correctes (avec https://)
$good_strings = array(
	'href="https://' . $public_domain_name . '/',
	'src="https://' . $public_domain_name . '/',
	'action="https://' . $public_domain_name . '/'
);

// 4. Réparer le HTML
$output = str_replace($bad_strings, $good_strings, $output);

// 5. Réparer aussi les liens relatifs standards (ex: href="/theme...")
$output = str_replace(
	array('href="/', 'src="/', 'action="/'),
	array('href="https://'.$public_domain_name.'/', 'src="https://'.$public_domain_name.'/', 'action="https://'.$public_domain_name.'/'),
	$output
);

// --- FIN DE LA CORRECTION DES LIENS ---


// Envoyer le contenu modifié au client
print $output;
exit;

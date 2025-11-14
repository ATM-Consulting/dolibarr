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
require_once DOL_DOCUMENT_ROOT.'/core/lib/onlinesign.lib.php';

// --- RÉCUPÉRATION DES PARAMÈTRES (de la règle Apache) ---
$type = GETPOST('type', 'alpha');
$ref = GETPOST('ref', 'alphanohtml');

if (empty($type) || empty($ref)) {
	dol_print_error(0, 'Missing parameters');
	exit;
}

$obj = null;
$db->begin();

// --- CHARGER L'OBJET CORRESPONDANT ---
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

// --- GÉNÉRATION DE L'URL SÉCURISÉE INTERNE ---
// Mode=0 (vraie URL), localorexternal=0 (URL interne/localhost)
$full_secure_url = getOnlineSignatureUrl(0, $internal_type, $ref, 0, $obj); // 0 = URL INTERNE !

if (strpos($full_secure_url, 'http') !== 0) {
	dol_syslog("Failed to generate secure URL: " . $full_secure_url, LOG_ERR);
	dol_print_error(0, 'Error while generating the signature link.');
	$db->rollback();
	exit;
}

$db->commit();

// --- PROXY PHP : RÉCUPÉRER ET AFFICHER LE CONTENU ---
// Utilise cURL pour récupérer le contenu de la page de signature
// en "backend", sans que le client ne le voie.

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $full_secure_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// Gérer les cookies pour que la session (ex: pour le token CSRF) fonctionne
curl_setopt($ch, CURLOPT_COOKIEJAR, DOL_DATA_ROOT . '/sessions/cookie_proxy.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, DOL_DATA_ROOT . '/sessions/cookie_proxy.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Suivre les redirections internes
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Important si le SSL interne est auto-signé
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$output = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200 || $output === false) {
	dol_print_error(0, 'Failed to fetch signature page content internally. Code: '.$http_code);
	exit;
}

// AFFICHE LE HTML DE LA PAGE DE SIGNATURE
// Problème : Les liens relatifs (CSS, JS, AJAX) dans ce HTML seront cassés.
// Nous devons les réécrire pour qu'ils pointent vers le domaine public.

// 1. Trouver l'URL de base interne (ex: https://doliboardtest-dlb...)
$internal_base_url = $conf->url_root;
// 2. Définir l'URL de base publique (ex: https://signature-propal...)
//    -> Idéalement, à mettre dans la conf Dolibarr (ex: PROPAL_SIGNATURE_ALIAS_URL)
$public_base_url = rtrim(getDolGlobalString(strtoupper($internal_type).'_SIGNATURE_ALIAS_URL'), '/');

// Si la conf est vide, on essaie de deviner
if (empty($public_base_url)) {
	// ATTENTION: C'est fragile. Mieux vaut configurer la constante.
	$public_base_url = 'https://' . $_SERVER['HTTP_HOST'];
}

// Remplacer les chemins relatifs/internes par des chemins absolus publics
// Ex: /core/ajax/onlineSign.php -> https://signature-propal.../core/ajax/onlineSign.php
$output = str_replace(
	array('href="/', 'src="/', 'action="/'),
	array('href="'.$public_base_url.'/', 'src="'.$public_base_url.'/', 'action="'.$public_base_url.'/'),
	$output
);
// Remplacer les chemins internes complets s'ils existent
$output = str_replace($internal_base_url, $public_base_url, $output);


// Envoyer le contenu modifié au client
print $output;
exit;

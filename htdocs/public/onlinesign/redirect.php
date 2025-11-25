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


// --- DÉCISION : PROXY OU REDIRECTION ? ---

// On construit le nom de la constante (ex: PROPOSAL_SIGNATURE_USE_PROXY_MODE)
$const_proxy_name = strtoupper($internal_type) . '_SIGNATURE_USE_PROXY_MODE';
$use_proxy = getDolGlobalString($const_proxy_name);


if ($use_proxy) {
	// ====================================================
	// CAS 1 : MODE PROXY (cURL)
	// ====================================================

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $full_secure_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);

	// Gestion cookies (Passe-plat session)
	$cookie_file = DOL_DATA_ROOT . '/sessions/cookie_proxy_'.session_id().'.txt';
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	curl_close($ch);

	if ($http_code != 200 || $output === false) {
		dol_print_error(0, 'Failed to fetch content via proxy. Code: '.$http_code);
		exit;
	}

	// Séparation Header/Body
	$header = substr($output, 0, $header_size);
	$body = substr($output, $header_size);

	// Forward des cookies
	$headers_lines = explode("\r\n", $header);
	foreach ($headers_lines as $line) {
		if (stripos($line, 'Set-Cookie:') === 0) {
			header($line, false);
		}
	}

	// Réécriture des liens (CSS/JS/AJAX)
	$internal_base_url = rtrim($dolibarr_main_url_root, '/');
	$public_domain_name = rtrim(getDolGlobalString(strtoupper($internal_type).'_SIGNATURE_ALIAS_URL'), '/');
	$public_domain_name = preg_replace('#^https?://#', '', $public_domain_name);

	if (empty($public_domain_name)) {
		$public_domain_name = $_SERVER['HTTP_HOST'];
	}
	$public_base_url = 'https://' . $public_domain_name;

	// Remplacements
	$body = str_replace(
		array('href="/', 'src="/', 'action="/'),
		array('href="'.$public_base_url.'/', 'src="'.$public_base_url.'/', 'action="'.$public_base_url.'/'),
		$body
	);
	$body = str_replace($internal_base_url, $public_base_url, $body);
	// Correctif double URL
	$body = str_replace(
		array('href="'.$public_domain_name.'/', 'src="'.$public_domain_name.'/', 'action="'.$public_domain_name.'/'),
		array('href="'.$public_base_url.'/', 'src="'.$public_base_url.'/', 'action="'.$public_base_url.'/'),
		$body
	);

	print $body;
	exit;

} else {
	// ====================================================
	// CAS 2 : MODE STANDARD (Redirection)
	// ====================================================
	$redirect_url = getOnlineSignatureUrl(0, $internal_type, $ref, 1, $obj); // 1 = URL Externe
	header("Location: " . $redirect_url);
	exit;
}

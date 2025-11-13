<?php
/**
 * Script de redirection pour alias de signature en ligne
 */
$res = @include_once __DIR__.'/../../master.inc.php';
if (!$res) {
	$res = @include_once __DIR__.'/../../main.inc.php'; // Pour les anciennes versions
}
if (!$res) {
	die('Failed to load Dolibarr environment');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/onlinesign.lib.php';

$type = GETPOST('type', 'alpha');
$ref = GETPOST('ref', 'regex:/^[a-zA-Z0-9\-]+$/'); // Sécurisation basique

if (empty($type) || empty($ref)) {
	dol_print_error(0, 'Missing parameters');
	exit;
}

$obj = null;
$db->begin();

$internal_type = '';
if ($type == 'propale' || $type == 'proposal') {
	$internal_type = 'proposal';
	include_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
	$obj = new Propal($db);
	$res = $obj->fetch(0, $ref);
}

if (empty($obj) || $res <= 0) {
	dol_print_error($db, 'Object not found or error fetching ' . $ref);
	$db->rollback();
	exit;
}

$full_secure_url = getOnlineSignatureUrl(0, $internal_type, $ref, 1, $obj);

if (strpos($full_secure_url, 'http') !== 0) {
	// La fonction a retourné une erreur (ex: 'Invalid parameter...')
	dol_syslog("Failed to generate secure URL: " . $full_secure_url, LOG_ERR);
	dol_print_error(0, 'Error while generating the signature link.');
	$db->rollback();
	exit;
}
$db->commit();
header('Location: ' . $full_secure_url);
exit;

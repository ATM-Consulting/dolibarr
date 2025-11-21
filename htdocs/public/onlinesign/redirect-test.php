<?php
/* Copyright (C) 2025      ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file       htdocs/public/onlinesign/redirect.php
 * \ingroup    core
 * \brief      Script to redirect alias URLs to the real secured online signature URL
 */

// Load Dolibarr environment
$res = @include_once __DIR__.'/../../master.inc.php';
if (!$res) {
    $res = @include_once __DIR__.'/../../main.inc.php';
}
if (!$res) {
    die('Failed to load Dolibarr environment');
}

// Load signature library
require_once DOL_DOCUMENT_ROOT.'/core/lib/signature.lib.php'; // ou onlinesign.lib.php selon la version

global $conf, $db, $langs;

// Get parameters
$type = GETPOST('type', 'alpha');
$ref = GETPOST('ref', 'alphanohtml');

if (empty($type) || empty($ref)) {
    httponly_accessforbidden('Missing parameters');
}

$obj = null;
$internal_type = '';

// Load object depending on type
if ($type == 'propale' || $type == 'proposal') {
    $internal_type = 'proposal';
    if (isModEnabled('propal')) {
        include_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
        $obj = new Propal($db);
    }
} elseif ($type == 'contrat' || $type == 'contract') {
    $internal_type = 'contract';
    if (isModEnabled('contrat')) {
        include_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
        $obj = new Contrat($db);
    }
} elseif ($type == 'fichinter') {
    $internal_type = 'fichinter';
    if (isModEnabled('ficheinter')) {
        include_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
        $obj = new Fichinter($db);
    }
} elseif ($type == 'expedition' || $type == 'shipping') {
    $internal_type = 'expedition';
    if (isModEnabled('expedition')) {
        include_once DOL_DOCUMENT_ROOT.'/expedition/class/expedition.class.php';
        $obj = new Expedition($db);
    }
}

// Fetch object
if (is_object($obj)) {
    $res = $obj->fetch(0, $ref);
    if ($res <= 0) {
        httponly_accessforbidden('Object not found');
    }
} else {
    httponly_accessforbidden('Type not supported or module not enabled');
}

// Generate the full secure URL
// Mode 0 = True URL, LocalOrExternal 1 = External URL
$full_secure_url = getOnlineSignatureUrl(0, $internal_type, $ref, 1, $obj);

if (strpos($full_secure_url, 'http') !== 0) {
    // Error during generation (e.g. security token missing)
    dol_syslog("Failed to generate secure URL: " . $full_secure_url, LOG_ERR);
    die('Error: ' . $full_secure_url);
}

// Standard HTTP Redirect
header("Location: " . $full_secure_url);
exit;

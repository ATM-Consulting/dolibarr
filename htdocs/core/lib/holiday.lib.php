<?php
/* Copyright (C) 2006-2011  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2022       Frédéric France         <frederic.france@netlogic.fr>
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
 * or see https://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/lib/holiday.lib.php
 *		\brief      base functions for holiday
 */

/**
 *  Return array head with list of tabs to view object information
 *
 *  @param	Object	$object         Holiday
 *  @return array           		head
 */
function holiday_prepare_head($object)
{
	global $db, $langs, $conf, $user;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/holiday/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Leave");
	$head[$h][2] = 'card';
	$h++;

	// Attachments
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	// BACKPORT of https://github.com/Dolibarr/dolibarr/pull/38641 — remove when the upstream PR is merged and this file is updated
	$upload_dir = $conf->holiday->multidir_output[$object->entity].'/'.get_exdir(0, 0, 0, 1, $object, 'holiday');
	// END BACKPORT
	/*
	    [ATM-MDV] Début divergence ATM à retirer lors de la MDV v23+ :
	    à partir de la v23 le stockage bascule sur un dossier propre par référence
	    ('holiday' retiré de $arrayforoldpath dans get_exdir()), le filtrage par préfixe
	    devient donc inutile. Remplacer les 2 lignes suivantes par la version upstream :
	    $nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
	*/
	$allfilesbadge = dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$');
	$nbFiles = count(holidayFilterOwnedFiles($allfilesbadge, $object));
	/* [ATM-MDV] Fin divergence ATM */
	$nbLinks = Link::count($db, $object->element, $object->id);
	$head[$h][0] = DOL_URL_ROOT.'/holiday/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles + $nbLinks) > 0) {
		$head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
	}
	$head[$h][2] = 'documents';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'holiday', 'add', 'core');

	$head[$h][0] = DOL_URL_ROOT.'/holiday/info.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'holiday', 'add', 'external');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'holiday', 'remove');

	return $head;
}


/*
    [ATM-MDV] Début divergence ATM à retirer lors de la MDV v23+ :
    fonction spécifique ATM. En v23 le stockage bascule sur un dossier propre par
    référence, le filtrage par préfixe n'a plus lieu d'être : supprimer toute cette
    fonction ainsi que ses appels dans holiday_prepare_head() et holiday/document.php.
*/
/**
 *  Filter a file list to keep only the attachments belonging to a given holiday.
 *
 *  Holiday attachments are stored using get_exdir() with a 2-level path forged from the
 *  object id (see $arrayforoldpath in get_exdir()), so several holiday requests can share
 *  the same physical directory. Each file is saved prefixed with the holiday reference
 *  (e.g. "CP2401-0007-file.pdf"), or with the provisional reference "(PROVxx)" while the
 *  request is still a draft. Filtering on that prefix isolates the files owned by $object
 *  and avoids showing attachments from other requests sharing the same directory.
 *
 *  @param	array	$filearray	File list as returned by dol_dir_list() (each entry has a 'name' key)
 *  @param	Holiday	$object		Holiday object owning the files
 *  @return	array				Filtered and re-indexed file list (empty array if none match)
 */
function holidayFilterOwnedFiles($filearray, $object)
{
	if (!is_array($filearray) || empty($filearray)) {
		return [];
	}

	$refprefix = dol_sanitizeFileName($object->ref);
	$provprefix = dol_sanitizeFileName('(PROV'.$object->id.')');

	return array_values(array_filter($filearray, function ($f) use ($refprefix, $provprefix) {
		return strncmp($f['name'], $refprefix.'-', strlen($refprefix) + 1) === 0
			|| strncmp($f['name'], $provprefix.'-', strlen($provprefix) + 1) === 0;
	}));
}
/* [ATM-MDV] Fin divergence ATM */


/**
 *  Return array head with list of tabs to view object information
 *
 *  @return array           		head
 */
function holiday_admin_prepare_head()
{
	global $db, $langs, $conf, $user;

	$extrafields = new ExtraFields($db);
	$extrafields->fetch_name_optionals_label('holiday');

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/admin/holiday.php';
	$head[$h][1] = $langs->trans("Setup");
	$head[$h][2] = 'holiday';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'holiday_admin');

	$head[$h][0] = DOL_URL_ROOT.'/admin/holiday_extrafields.php';
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = $extrafields->attributes['holiday']['count'];
	if ($nbExtrafields > 0) {
		$head[$h][1] .= '<span class="badge marginleftonlyshort">'.$nbExtrafields.'</span>';
	}
	$head[$h][2] = 'attributes';
	$h++;

	complete_head_from_modules($conf, $langs, null, $head, $h, 'holiday_admin', 'remove');

	return $head;
}

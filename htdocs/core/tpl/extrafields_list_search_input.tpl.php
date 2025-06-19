<?php

print '<!-- extrafields_list_search_input.tpl.php -->'."\n";

// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf)) {
	print "Error, template page can't be called as URL";
	exit(1);
}

if (empty($extrafieldsobjectkey) && is_object($object)) {
	$extrafieldsobjectkey = $object->table_element;
}

// Loop to show all columns of extrafields for the search title line
if (!empty($extrafieldsobjectkey)) {	// $extrafieldsobject is the $object->table_element like 'societe', 'socpeople', ...
	if (!empty($extrafields->attributes[$extrafieldsobjectkey]['label']) && is_array($extrafields->attributes[$extrafieldsobjectkey]['label']) && count($extrafields->attributes[$extrafieldsobjectkey]['label'])) {
		if (empty($extrafieldsobjectprefix)) {
			$extrafieldsobjectprefix = 'ef.';
		}
		if (empty($search_options_pattern)) {
			$search_options_pattern = 'search_options_';
		}

		foreach ($extrafields->attributes[$extrafieldsobjectkey]['label'] as $key => $val) {
			if (!empty($arrayfields[$extrafieldsobjectprefix.$key]['checked'])) {
				if ($extrafields->attributes[$extrafieldsobjectkey]['type'][$key] == 'separate') {
					continue;
				}
				$cssclass = $extrafields->getAlignFlag($key, $extrafieldsobjectkey);
				$typeofextrafield = $extrafields->attributes[$extrafieldsobjectkey]['type'][$key];
				print '<td class="liste_titre'.($cssclass ? ' '.$cssclass : '').'">';
				$tmpkey = preg_replace('/'.$search_options_pattern.'/', '', $key);
				if (in_array($typeofextrafield, array('varchar', 'mail', 'ip', 'url', 'int', 'double')) && empty($extrafields->attributes[$extrafieldsobjectkey]['computed'][$key])) {
					$searchclass = '';
					if (in_array($typeofextrafield, array('varchar', 'mail', 'ip', 'url'))) {
						$searchclass = 'searchstring';
					}
					if (in_array($typeofextrafield, array('int', 'double'))) {
						$searchclass = 'searchnum';
					}
					print '<input class="flat'.($searchclass ? ' '.$searchclass : '').'" size="4" type="text" name="'.$search_options_pattern.$tmpkey.'" value="'.dol_escape_htmltag((empty($search_array_options[$search_options_pattern.$tmpkey]) ? '' : $search_array_options[$search_options_pattern.$tmpkey])).'">';
				} elseif (in_array($typeofextrafield, array('date', 'datetime', 'timestamp'))) {
					$morecss = '';
					$preselectedvalues = (empty($search_array_options[$search_options_pattern.$tmpkey]) ? '' : $search_array_options[$search_options_pattern.$tmpkey]);
					// Here $preselectedvalues can be an array('start'=>int, 'end'=>int) or an int
					print $extrafields->showInputField($key, $preselectedvalues, '', '', $search_options_pattern, $morecss, 0, $extrafieldsobjectkey, 1);
				} else {
					// for the type as 'checkbox', 'chkbxlst', 'sellist' we should use code instead of id (example: I declare a 'chkbxlst' to have a link with dictionnairy, I have to extend it with the 'code' instead 'rowid')
					$morecss = '';
					/*
					* SPE SGP
					* Gestion de la recherche multiple sur les extrafields multiselect
					*/
					$infos = img_picto(addslashes($langs->trans('UseANDHelp')), 'info_black');
					$checkbox_title = dol_htmlentities($langs->trans('UseANDHelp'), ENT_QUOTES);

					/*
					* SPE SGP
					* Gestion de la recherche multiple sur les extrafields multiselect
					*/
					if(in_array($typeofextrafield, array('checkbox', 'chkbxlst'))) {
						$behaviour = GETPOST($search_options_pattern.$tmpkey.'_AND', 'bool') ? 'checked' : '';

						print '<span id="toIncludeCheckbox_'.$tmpkey.'" style="color: white">'.$langs->trans('ToInclude').'&nbsp;<input type="checkbox" name="'.$search_options_pattern.$tmpkey.'_AND" ';
						// La case reste cochée si la croix (suppression des filtres) n'a pas été activée
						if (! (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha'))){
							print $behaviour;
						}
						print ' title="' . $checkbox_title . '"/>';
						print $infos.'</span>';
					}
					if (in_array($typeofextrafield, array('link', 'sellist', 'text', 'html'))) {
						$morecss = 'maxwidth200';
					}
					print $extrafields->showInputField($key, (!isset($search_array_options[$search_options_pattern.$tmpkey]) ? '' : $search_array_options[$search_options_pattern.$tmpkey]), '', '', $search_options_pattern, $morecss, 0, $extrafieldsobjectkey, 1);
				}
				print '</td>';
			}
		}
	}
}

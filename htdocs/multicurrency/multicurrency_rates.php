<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *      \file       htdocs/multicurrency/multicurrency_rates.php
 *        \ingroup    multicurrency
 *        \brief      Shows an exchange rate editor
 */

$res = @include("../main.inc.php");                // For root directory

/**
 * @var User $user
 * @var DoliDB $db
 */

//require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
//require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
//require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT . '/multicurrency/class/multicurrency.class.php';
dol_include_once('/multicompany/class/actions_multicompany.class.php', 'ActionsMulticompany');
/** @var Translate $langs */
$langs->loadLangs(array(
					  "errors",
					  "admin",
					  "main",
					  "multicurrency"));


if (!$user->admin) {
	accessforbidden();
}

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
/** @var HookManager $hookmanager */
$hookmanager->initHooks(array('multicurrency_rates'));

// Load translation files required by the page

$action = GETPOST('action', 'alpha') ? GETPOST('action', 'alpha') : 'view';

// column definition
$TVisibleColumn = array(
	'rate.date_sync'
	=> array('callback' => 'Date'),
	'rate.rate'
	=> array('callback' => 'Number'),
	'currency.code'
	=> array('callback' => 'CurrencyCode'),
	//		'rate.entity'
	//			=> array('callback' => 'Entity'),
);

/*
 * Actions
 */
_completeColumns($db, $TVisibleColumn);
_handleActions($db, $TVisibleColumn);
exit;

function _handleActions($db, $TVisibleColumn)
{
	global $langs;
	$action = GETPOST('action', 'alpha');
	if (empty($action)) $action = 'view';

	$callbackName = '_action' . _camel($action);
	if (!function_exists($callbackName)) {
		setEventMessages($langs->trans('UnknownAction', $action), array(), 'errors');
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
	call_user_func($callbackName, $db, $TVisibleColumn);
}

/**
 * @param DoliDB $db
 * @param array $TVisibleColumn
 * @param string $mode
 * @param int|null $targetId ID of the row targeted for edition, deletion, etc.
 */
function _mainView($db, $TVisibleColumn, $mode = 'view', $targetId = NULL)
{
	global $langs;
	$title = $langs->trans('CurrencyRateSetup');
	$limit = 1000;

	$TSQLFilter = array();
	foreach ($TVisibleColumn as $colSelect => $colParam) {
		if (isset($colParam['filter_value']) && !empty($colParam['filter_value'])) {
			$cbName = '_getSQLFilter' . ucfirst($colParam['callback']);
			if (function_exists($cbName)) {
				$sqlFilter = call_user_func($cbName, $db, $colParam);
			} else {
				$sqlFilter = ' AND ' . $colParam['name'] . ' = '
							 . '"' . $db->escape($colParam['filter_value']) . '"';
			}
			$TSQLFilter[] = $sqlFilter;
		}
	}

	$sql = /** @lang SQL */
		'SELECT rate.rowid, ' . join(', ', array_keys($TVisibleColumn)) . ' FROM ' . MAIN_DB_PREFIX . 'multicurrency_rate rate'
		. ' LEFT JOIN ' . MAIN_DB_PREFIX . 'multicurrency currency ON rate.fk_multicurrency = currency.rowid'
		. ' WHERE rate.entity IN (' . getEntity('multicurrency') . ')'
		. (count($TSQLFilter) ? join('', $TSQLFilter) : '')
		. ' ORDER BY rate.date_sync DESC'
		. ' LIMIT ' . intval($limit);
	$resql = $db->query($sql);
	if (!$resql) {
		setEventMessages($db->lasterror, array(), 'errors');
		$num_rows = 0;
	} else {
		$num_rows = $db->num_rows($resql);
	}

	llxHeader();
	echo load_fiche_titre($title);

	echo '<style>'
		 . 'button.like-link {'
		 . '  border: none;'
		 . '  padding: 0;'
		 . '  background: inherit;'
		 . '  cursor: pointer;'
		 . '}'
		 . 'col.small-col {'
		 . '  width: 5%'
		 . '}'
		 . '</style>';

	echo '<table class="noborder centpercent">';
	echo '<colgroup>'
		 . '<col span="' . count($TVisibleColumn) . '">'
		 . '<col class="small-col" span="1">'
		 . '</colgroup>';


	// Formulaire des filtres de recherche
	echo '<thead>';
	echo '<tr id="filter-row" class="liste_titre_filter">';
	foreach ($TVisibleColumn as $colSelect => $colParam) {
		echo '<td class="liste_titre ' . $colParam['name'] . '">';
		echo _getCellContent(
			$colParam['filter_value'],
			$colParam,
			'search',
			'form-filter'
		);
		echo '</td>';
	}
	echo '<td class="liste_titre actions">'
		 . '<form method="get" id="form-filter" action="' . $_SERVER["PHP_SELF"] . '">'
		 . '<input type="hidden" name="token" value="' . newToken() . '" />'
		 . '<button class="like-link" name="action" value="filter">'
		 . '<span class="fa fa-search" >&nbsp;</span>'
		 . '</button>'
		 . '<button class="like-link" name="action" value="remove_filters">'
		 . '<span class="fa fa-remove" >&nbsp;</span>'
		 . '</button>'
		 . '</form>'
		 . '</td>';
	echo '</tr>';

	// En-têtes de colonnes
	echo '<tr class="liste_titre" id="title-row">';
	foreach ($TVisibleColumn as $colSelect => $colParam) {
		echo '<th class="liste_titre ' . $colParam['name'] . '">';
		echo $langs->trans('Multicurrency' . _camel(ucfirst($colParam['name'])));
		echo '</th>';
	}
	echo '<th class="liste_titre actions"></th>';
	echo '</tr>';
	echo '</thead>';

	// formulaire d'ajout ('new')
	echo '<tbody>';
	echo '<tr id="row-add-new">';
	foreach ($TVisibleColumn as $colSelect => $colParam) {
		echo '<td class="' . $colParam['name'] . '">';
		// show an empty input
		echo _getCellContent('', $colParam, 'new', 'form-add-new');
		echo '</td>';
	}
	// entire form is inside cell because HTML does not allow forms inside tables unless they are inside cells
	echo '<td>'
		 . '<form method="post" id="form-add-new" action="' . $_SERVER["PHP_SELF"] . '">'
		 . _formHiddenInputs($TVisibleColumn)
		 . '<button class="button" name="action" value="add">'
		 . $langs->trans('Add')
		 . '</button>'
		 . '</form>'
		 . '</td>';
	echo '</tr>';
	echo '</tbody>';

	// lignes
	echo '<tbody>';
	if (!$num_rows) {
		echo '<tr>';
		$colspan = count($TVisibleColumn);
		$colspan += 1; // account for the action column
		echo '<td colspan="' . $colspan . '">' . $langs->trans('NoResults') . '</td>';
		echo '</tr>';
	}
	for ($i = 0; $i < $num_rows; $i++) {
		$obj = $db->fetch_object($resql);
		$objId = intval($obj->rowid);
		$row_is_in_edit_mode = ($mode === 'modify' && $objId === $targetId);
		$form_update_name = "form-update-" . $objId;
		if (!$obj) {
			break;
		}
		echo '<tr id="row-' . intval($obj->rowid) . '">';
		foreach ($TVisibleColumn as $colSelect => $colParam) {
			$rawValue = $obj->{$colParam['name']};
			$displayMode = 'view';
			if ($row_is_in_edit_mode) {
				$displayMode = 'modify';
			}
			$cellContent = _getCellContent($rawValue, $colParam, $displayMode, $form_update_name);
			echo '<td class="' . $colParam['name'] . '">' . $cellContent . '</td>';
		}

		echo '<td class="actions">';
		// save form (for the row in edit mode)
		if ($row_is_in_edit_mode) {
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" id="' . $form_update_name . '" style="display: inline;">'
				 . _formHiddenInputs($TVisibleColumn)
				 . '<input type="hidden" name="id" value="' . $objId . '">'
				 . '<input type="hidden" name="action" value="update">'
				 . '<input type="submit" class="button" value="'
				 . htmlspecialchars($langs->trans('Save'), ENT_QUOTES)
				 . '" />'
				 . '</form>';
		} // edit + delete buttons (for rows not in edit mode)
		else {
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" id="form-edit-' . $objId . '" style="display: inline;">'
				 . _formHiddenInputs($TVisibleColumn)
				 . '<input type="hidden" name="id" value="' . $objId . '">'
				 . '<input type="hidden" name="action" value="modify">'
				 . '<button class="like-link">'
				 . img_picto('edit', 'edit')
				 . '</button>'
				 . '</form>';
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" id="form-delete-' . $objId . '" style="display: inline;">'
				 . _formHiddenInputs($TVisibleColumn)
				 . '<input type="hidden" name="id" value="' . $objId . '">'
				 . '<input type="hidden" name="action" value="delete">'
				 . '<button class="like-link">'
				 . img_picto('delete', 'delete')
				 . '</button>'
				 . '</form>';
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';


	// End of page
	llxFooter();
	$db->close();
}

/**
 * Calls a specialized callback depending on $colParam['callback'] (or a default one
 * if not set or found) to return a representation of $rawValue depending on $mode:
 *
 * @param mixed $rawValue A raw value (as returned by the SQL handler)
 * @param array $colParam Information about the kind of value (date, price, etc.)
 * @param string $mode 'view',   => returns the value for end user display
 *                             'modify', => returns a form to modify the value
 *                             'new',    => returns a form to put the value in a new record
 *                             'raw',    => does nothing (returns the raw value)
 *                             'text'    => returns a text-only version of the value
 *                                          (for text-only exports etc.)
 * @param string|null $formId HTML id of the form on which to attach the input in
 *                             'modify' and 'new' modes
 * @return string
 */
function _getCellContent($rawValue, $colParam, $mode = 'view', $formId = NULL)
{
	if ($mode === 'raw') return $rawValue;
	$callback = _cellContentCallbackName($colParam);
	return call_user_func($callback, $rawValue, $mode, $colParam['name'], $formId);
}

/**
 * @param $rawValue
 * @param string $mode
 * @param string $inputName
 * @return string
 * @see _getCellContent()
 */
function _getCellDefault($rawValue, $mode = 'view', $inputName = '', $formId = NULL)
{
	switch ($mode) {
		case 'view':
			return dol_escape_htmltag($rawValue);
		case 'modify':
		case 'new':
			$inputAttributes = array(
				'value' => $rawValue,
				'name' => $inputName,
			);
			if ($formId !== NULL) {
				$inputAttributes['form'] = $formId;
			}
			return _tagWithAttributes('input', $inputAttributes);
		case 'raw':
			return $rawValue;
		case 'text':
			return strip_tags($rawValue);
		case 'search':
			return '<input name="search_' . $inputName . '"'
				   . ' value="' . htmlspecialchars(GETPOST('search_' . $inputName), ENT_QUOTES) . '"'
				   . ' form="' . $formId . '"'
				   . ' />';
	}
	return $rawValue;
}

/**
 * @param $rawValue
 * @param string $mode
 * @param string $inputName
 * @return string
 * @see _getCellContent()
 */
function _getCellDate($rawValue, $mode = 'view', $inputName = '', $formId = NULL)
{
	global $db;
	switch ($mode) {
		case 'view':
			$tms = $db->jdate($rawValue);
			$dateFormat = '%d/%m/%Y %H:%M';
			$dateFormat = '';
			return dol_print_date($tms, $dateFormat);
		case 'modify':
		case 'new':
			$inputAttributes = array(
				'type' => 'date',
				'value' => preg_replace('/^(.*?) .*/', '$1', $rawValue),
				'name' => $inputName,
			);
			if ($formId !== NULL) {
				$inputAttributes['form'] = $formId;
			}
			return _tagWithAttributes('input', $inputAttributes);
		case 'raw':
			return $rawValue;
		case 'text':
			return strip_tags($rawValue);
		case 'search':
			$select = _tagWithAttributes('select', array(
				'form' => $formId,
				'name' => 'search_' . $inputName
			));
			$y = intval(dol_print_date(dol_now(), '%Y'));
			$emptyOptParams = array('value' => '');
			if (empty($rawValue)) {
				$emptyOptParams['selected'] = 'selected';
			}
			$options = array(_tagWithAttributes('option', $emptyOptParams));
			$options += array_map(function ($i) use ($rawValue) {
				$optParams = array('value' => $i);
				if ($rawValue == $i) $optParams['selected'] = 'selected';
				return _tagWithAttributes('option', $optParams) . $i . '</option>';
			}, range($y - 10, $y + 1));
			return $select . join("\n", $options) . '</select>';
	}
	return $rawValue;
}

/**
 * @param $rawValue
 * @param string $mode
 * @param string $inputName
 * @return string
 * @see _getCellContent()
 */
function _getCellNumber($rawValue, $mode = 'view', $inputName = '', $formId = NULL)
{
	switch ($mode) {
		case 'view':
			return price($rawValue);
		case 'modify':
		case 'new':
			$inputAttributes = array(
				'value' => $rawValue,
				'name' => $inputName,
				'placeholder' => '0,00',
				'pattern' => '\d+(?:[.,]\d+)?',
				'required' => 'required',
			);
			if ($formId !== NULL) {
				$inputAttributes['form'] = $formId;
			}
			return _tagWithAttributes('input', $inputAttributes);
		case 'raw':
			return $rawValue;
		case 'text':
			return strip_tags($rawValue);
		case 'search':
			return '<input name="search_' . $inputName . '"'
				   . ' value="' . htmlspecialchars(GETPOST('search_' . $inputName), ENT_QUOTES) . '"'
				   . ' form="' . $formId . '"'
				   . ' />';
	}
	return $rawValue;
}

/**
 * @param $rawValue
 * @param string $mode
 * @param string $inputName
 * @return string
 */
function _getCellCurrencyCode($rawValue, $mode = 'view', $inputName = '', $formId = NULL)
{
	global $db, $langs;
	if ($formId) $formId = htmlspecialchars($formId, ENT_QUOTES);
	$form = new Form($db);
	switch ($mode) {
		case 'view':
		case 'modify': // 'modify' because the currency code is read-only
			return $langs->cache_currencies[$rawValue]['label'] . ' (' . $langs->getCurrencySymbol($rawValue) . ')';
		case 'new':
			$select = $form->selectMultiCurrency($rawValue, $inputName, 1);
			if ($formId) {
				// add form attribute to the output of selectCurrency
				$select = preg_replace(
					'/^<select /i',
					'<select form="' . $formId . '" ',
					$select
				);
			}
			return $select;
		case 'raw':
			return $rawValue;
		case 'text':
			return strip_tags($rawValue);
		case 'search':
			$select = $form->selectMultiCurrency($rawValue, 'search_' . $inputName, 1);
			if ($formId) {
				// add form attribute to the output of selectCurrency
				$select = preg_replace(
					'/^<select /i',
					'<select form="' . $formId . '" ',
					$select
				);
			}
			return $select;
	}
	return $rawValue;
}

/**
 * @param array $colParam
 * @return string
 */
function _cellContentCallbackName($colParam)
{
	global $langs;
	$cellContentCallback = '_getCellDefault';
	// possible override in column definition
	if (isset($colParam['callback'])) {
		$cbName = $colParam['callback'];
		// mandatory function name prefix: '_getCell' (some day, the callback may come from untrusted input)
		if (strpos($cbName, '_getCell') !== 0) $cbName = '_getCell' . $cbName;
		if (function_exists($cbName)) {
			$cellContentCallback = $cbName;
		} else {
			_setEventMessageOnce(
				$langs->trans('ErrorCallbackNotFound', $cbName),
				'warnings'
			);
		}
	}
	return $cellContentCallback;
}

/**
 * Returns an opening (or self-closing) tag with the (escaped) requested attributes
 *
 * Example: _tagWithAttributes('input', ['name' => 'test', 'value' => '"hello"'])
 *          => '<input name="test" value="&quot;nothing&quot;" />'
 *
 *
 * @param string $tagName
 * @param array $TAttribute [$attrName => $attrVal]
 * @return string
 */
function _tagWithAttributes($tagName, $TAttribute)
{
	$selfClosing = in_array($tagName, array('area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'));
	$tag = '<' . $tagName;
	foreach ($TAttribute as $attrName => $attrVal) {
		$tag .= ' ' . $attrName . '="' . str_replace("\n", "&#10;", htmlspecialchars($attrVal, ENT_QUOTES)) . '"';
	}
	$tag .= $selfClosing ? ' />' : ' >';
	return $tag;
}

/**
 * Returns the name of the column in the object returned by DoliDB::fetch_object
 *
 * Example:
 *   $colSelect = 'abcd'               => 'abcd' // no table name, no alias
 *                'table.xyz AS abcd'  => 'abcd' // with table name
 *                'table.abcd'         => 'abcd' // with table name and alias
 *                'xyz AS abcd'        => 'xyz AS abcd' // not handled: alias without table name
 * @param string $colSelect
 * @return string
 */
function _columnAlias($colSelect)
{
	// the regexp replacement does this:
	//     'table.abcd AS efgh' => 'efgh'
	// regexp explanation:
	//     '.*\.`?'     => not captured:  anything, then a dot, then an optional backtick;
	//     '([^ `]+)`?' => capture 1:     anything that doesn't have a space or a backtick (then an optional, uncaptured backtick)
	//     '(?:.....)?' => non-capturing: makes whatever is inside the parentheses optional
	//     '\s+as\s+`?' => not captured:  whitespace, then 'AS', then whitespace, then an optional backtick
	//     '([^ `]+)'   => capture 2:     anything that doesn't have a space or a backtick
	return preg_replace_callback(
		'/^.*\.`?([^ `]+)`?(?:\s+as\s+`?([^ `]+)`?)?/i',
		function ($m) {
			return isset($m[2]) ? $m[2] : $m[1];
		},
		$colSelect
	);
}

/**
 * Returns $str in camel case ("snake_case_versus_camel_case" => 'snakeCaseVersusCamelCase')
 * @param $str
 * @return string|string[]|null
 */
function _camel($str)
{
	return preg_replace_callback('/_(.)?/', function ($m) {
		return ucfirst($m[1]);
	}, $str);
}


/**
 * Default: view all currency rates
 * @param DoliDB $db
 */
function _actionView($db, $TVisibleColumn)
{
	_mainView($db, $TVisibleColumn, 'view', intval(GETPOST('id', 'int')));
}

function _actionFilter($db, $TVisibleColumn)
{
	_mainView($db, $TVisibleColumn);
}

function _actionRemoveFilters($db, $TVisibleColumn)
{
	foreach ($TVisibleColumn as $colSelect => &$colParam) {
		if (isset($colParam['filter_value'])) {
			unset($colParam['filter_value']);
		}
	}
	unset($colParam);
	_mainView($db, $TVisibleColumn);
}

/**
 * Add a new currency rate
 * @param DoliDB $db
 */
function _actionAdd($db, $TVisibleColumn)
{
	global $langs, $conf;
	$dateSync = GETPOST('date_sync', 'alpha');
	$rate = GETPOST('rate', 'int');
	$code = GETPOST('code', 'aZ09');
	$entity = intval($conf->entity);
	$multiCurrency = new MultiCurrency($db);
	$resfetch = $multiCurrency->fetch(null, $code);
	if ($resfetch <= 0) {
		setEventMessages($langs->trans('MulticurrencyErrorCurrencyCodeNotFound', $code), array(), 'errors');
	} else {
		$mcRate = new CurrencyRate($db);
		$mcRate->date_sync = $dateSync;
		$mcRate->rate = $rate;
		$mcRate->entity = $entity;
		$rescreate = $mcRate->create($multiCurrency->id);
		if ($rescreate <= 0) {
			setEventMessages($langs->trans('MulticurrencyErrorCouldNotCreateRate', $rate, $code), array(), 'errors');
		}
	}
	_mainView($db, $TVisibleColumn, 'view');
}

/**
 * Show a currency rate in edit mode
 * @param DoliDB $db
 */
function _actionModify($db, $TVisibleColumn)
{
	$id = intval(GETPOST('id', 'int'));
	_mainView($db, $TVisibleColumn, 'modify', $id);
}

/**
 * Saves a currency rate
 * @param $db
 */
function _actionUpdate($db, $TVisibleColumn)
{
	global $langs;
	$id = intval(GETPOST('id', 'int'));
	$dateSync = GETPOST('date_sync', 'alpha');
	$rate = GETPOST('rate', 'int');
	$mcRate = new CurrencyRate($db);
	$resfetch = $mcRate->fetch($id);
	if ($resfetch <= 0) {
		setEventMessages($langs->trans('MulticurrencyErrorCouldNotFetchRate', $id), array(), 'errors');
	} else {
		$mcRate->date_sync = $dateSync;
		$mcRate->rate = $rate;
		$resupdate = $mcRate->update();
		if ($resupdate <= 0) {
			setEventMessages($langs->trans($db->lasterror), array(), 'errors');
		} else {
			setEventMessages($langs->trans('MulticurrencyRateSaved'), array(), 'mesgs');
		}
	}

	_mainView($db, $TVisibleColumn);
}

/**
 * Show a confirm form prior to deleting a currency rate
 * @param DoliDB $db
 */
function _actionDelete($db, $TVisibleColumn)
{
	global $langs;
	global $delayedhtmlcontent;
	$id = intval(GETPOST('id', 'int'));
	$form = new Form($db);
	$formParams = array(
		'id' => $id,
		'token' => newToken(),
	);
	foreach ($TVisibleColumn as $colSelect => $colParam) {
		if (isset($colParam['filter_value'])) {
			$formParams['search_' . $colParam['name']] = $colParam['filter_value'];
		}
	}
	if (isset($page)) $formParams['page'] = $page;
	$formParams = http_build_query($formParams);
	$delayedhtmlcontent .= $form->formconfirm(
		$_SERVER["PHP_SELF"] . '?' . $formParams,
		$langs->trans('DeleteLine'),
		$langs->trans('ConfirmDeleteLine'),
		'confirm_delete',
		'',
		0,
		1
	);
	_mainView($db, $TVisibleColumn, 'view');
}

/**
 * Delete a currency rate
 * @param DoliDB $db
 */
function _actionConfirmDelete($db, $TVisibleColumn)
{
	global $langs;
	$id = intval(GETPOST('id', 'int'));
	if (empty($id)) {
		setEventMessages($langs->trans('WrongID'), array(), 'errors');
	} else {
		$mcRate = new CurrencyRate($db);
		$resfetch = $mcRate->fetch($id);
		if ($resfetch <= 0) {
			setEventMessages($langs->trans('MulticurrencyErrorCouldNotFetchRate', $id), array(), 'errors');
		} else {
			$resdelete = $mcRate->delete();
			if ($resdelete <= 0) {
				setEventMessages($db->lasterror, array(), 'errors');
			} else {
				setEventMessages($langs->trans('MulticurrencyRateDeleted'), array(), 'mesgs');
			}
		}
	}
	_mainView($db, $TVisibleColumn, 'view');
}

/**
 * Calls setEventMessages only if $message is not already stored for display
 *
 * @param string $message
 * @param string $level 'errors', 'mesgs', 'warnings'
 */
function _setEventMessageOnce($message, $level = 'errors')
{
	if (!in_array($message, $_SESSION['dol_events'][$level])) {
		setEventMessages($message, array(), $level);
	}
}

/**
 * Completes the column definition array with values from HTTP query
 * @param DoliDB $db
 * @param array $TVisibleColumn
 */
function _completeColumns($db, &$TVisibleColumn)
{
	foreach ($TVisibleColumn as $colSelect => &$colParam) {
		$colParam['name'] = _columnAlias($colSelect);
		if (GETPOSTISSET('search_' . $colParam['name'])) {
			$searchValue = GETPOST('search_' . $colParam['name']);
			if (empty($searchValue)) continue;
			$colParam['filter_value'] = $searchValue;
		}
	}
	unset($colParam);
//	$confirm = GETPOST('confirm', 'alpha');
//	$listoffset = GETPOST('listoffset');
//	$listlimit = GETPOST('listlimit') > 0 ?GETPOST('listlimit') : 1000; // To avoid too long dictionaries
//	$sortfield = GETPOST("sortfield", 'alpha');
//	$sortorder = GETPOST("sortorder", 'alpha');
//	$page = GETPOST("page", 'int');
//	if (empty($page) || $page == -1) { $page = 0; }     // If $page is not defined, or '' or -1
//	$offset = $listlimit * $page;
//	$pageprev = $page - 1;
//	$pagenext = $page + 1;
}

/**
 * @param DoliDB $db
 * @param array $colParam
 * @return string
 */
function _getSQLFilterNumber($db, $colParam)
{
	$filterVal = $colParam['filter_value'];
	// apply price2num to every part of the string delimited by '<' or '>'
	$filterVal = join(
		'',
		array_map(
			'price2num',
			preg_split(
				'/([><])/',
				$filterVal,
				-1,
				PREG_SPLIT_DELIM_CAPTURE
			)
		)
	);
	$sqlFilter = natural_search($colParam['name'], $filterVal, 1);
	return $sqlFilter;
}

function _getSQLFilterDate($db, $colParam)
{
	$year = intval($colParam['filter_value']);
	$yearPlusOne = ($year + 1) . '-01-01 00:00:00';
	$year .= '-01-01 00:00:00';
	$sqlFilter = ' AND (rate.date_sync > "' . $year . '"'
				 . ' AND rate.date_sync < "' . ($yearPlusOne) . '")';
	return $sqlFilter;
}

/**
 * Returns the hidden <input/> fields that need to be attached to all
 * forms (such as search parameters).
 *
 * @param $TVisibleColumn
 * @return string
 */
function _formHiddenInputs($TVisibleColumn)
{
	$ret = '';
	foreach ($TVisibleColumn as $colSelect => $colParam) {
		if (isset($colParam['filter_value'])) {
			$ret .= "\n" . _tagWithAttributes('input', array(
					'type' => 'hidden',
					'name' => 'search_' . $colParam['name'],
					'value' => $colParam['filter_value'],
				));
		}
	}
	$ret .= "\n" . _tagWithAttributes('input', array(
			'type' => 'hidden',
			'name' => 'token',
			'value' => newToken()
		));
	return $ret;
}

<?php
/* Copyright (C) 2026		ATM Consulting		<support@atm-consulting.fr>
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
 *      \file       test/phpunit/ExtraFieldsTest.php
 *		\ingroup    test
 *      \brief      PHPUnit test
 *		\remarks	To run this script as CLI:  phpunit filename.php
 */

global $conf,$user,$langs,$db;
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/core/class/extrafields.class.php';
require_once dirname(__FILE__).'/CommonClassTest.class.php';


/**
 * Class for PHPUnit tests
 *
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 * @remarks	backupGlobals must be disabled to have db,conf,user and lang not erased.
 */
class ExtraFieldsTest extends CommonClassTest
{
	/**
	 * Data provider for testShowInputFieldOnListIgnoresTheObjectIdFilter
	 *
	 * @return array<string,array{0:string}>
	 */
	public static function listTypeWithObjectIdFilterProvider()
	{
		return array(
			'sellist' => array('sellist'),
			'chkbxlst' => array('chkbxlst'),
		);
	}

	/**
	 * A list view has no current object, so a '(column:=:$ID$)' filter must not reach the SQL.
	 *
	 * @param	string	$type	Extrafield type
	 * @return	void
	 *
	 * @dataProvider listTypeWithObjectIdFilterProvider
	 */
	public function testShowInputFieldOnListIgnoresTheObjectIdFilter($type)
	{
		global $db, $hookmanager;

		if (!is_object($hookmanager)) {
			include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
			$hookmanager = new HookManager($db);
		}

		$extrafields = new ExtraFields($db);
		$elementtype = 'societe';
		$code = 'countryofobject';
		$extrafields->attributes[$elementtype]['type'][$code] = $type;
		$extrafields->attributes[$elementtype]['label'][$code] = $code;
		$extrafields->attributes[$elementtype]['size'][$code] = '';
		$extrafields->attributes[$elementtype]['default'][$code] = '';
		$extrafields->attributes[$elementtype]['computed'][$code] = '';
		$extrafields->attributes[$elementtype]['unique'][$code] = 0;
		$extrafields->attributes[$elementtype]['required'][$code] = 0;
		$extrafields->attributes[$elementtype]['perms'][$code] = '';
		$extrafields->attributes[$elementtype]['langfile'][$code] = '';
		$extrafields->attributes[$elementtype]['list'][$code] = '1';
		$extrafields->attributes[$elementtype]['totalizable'][$code] = 0;
		$extrafields->attributes[$elementtype]['help'][$code] = '';
		$extrafields->attributes[$elementtype]['alwayseditable'][$code] = 0;
		$extrafields->attributes[$elementtype]['param'][$code] = array('options' => array('c_country:label:rowid::(rowid:=:$ID$)' => null));

		$savedphpself = $_SERVER['PHP_SELF'] ?? null;
		$_SERVER['PHP_SELF'] = '/societe/list.php';
		try {
			$out = $extrafields->showInputField($code, '', '', '', 'search_', '', 0, $elementtype, 1);
		} finally {
			$_SERVER['PHP_SELF'] = $savedphpself;
		}

		print __METHOD__." type=".$type." out=".$out."\n";
		$this->assertStringNotContainsString('Error', $out);
		$this->assertMatchesRegularExpression('/<option[^>]* value="1"/', $out);
	}

	/**
	 * Data provider for testIsEmptyValue
	 *
	 * @return array<string,array{0:mixed,1:string,2:bool}>
	 */
	public static function isEmptyValueProvider()
	{
		return array(
			'int null' => array(null, 'int', true),
			'int empty string' => array('', 'int', true),
			'int zero string' => array('0', 'int', false),
			'int zero' => array(0, 'int', false),
			'int value' => array('5', 'int', false),
			'double zero string' => array('0', 'double', false),
			'double zero decimal' => array('0.00', 'double', false),
			'price zero string' => array('0', 'price', false),
			'varchar zero string' => array('0', 'varchar', true),
			'varchar value' => array('abc', 'varchar', false),
			'select empty' => array('0', 'select', true),
			'sellist zero' => array('0', 'sellist', true),
			'link minus one' => array('-1', 'link', true),
		);
	}

	/**
	 * Zero is a valid value for a mandatory numeric extrafield.
	 *
	 * @param	mixed	$value		Value to test
	 * @param	string	$type		Extrafield type
	 * @param	bool	$expected	Expected result
	 * @return	void
	 *
	 * @dataProvider isEmptyValueProvider
	 */
	public function testIsEmptyValue($value, $type, $expected)
	{
		$this->assertSame($expected, ExtraFields::isEmptyValue($value, $type));
	}
}

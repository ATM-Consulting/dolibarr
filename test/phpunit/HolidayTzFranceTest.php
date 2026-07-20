<?php
/* Copyright (C) 2026 Adrien Raze <adrien.raze@atm-consulting.fr>
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
 *      \file       test/phpunit/HolidayTzFranceTest.php
 *		\ingroup    test
 *      \brief      PHPUnit test for the Holiday class in a positive-offset timezone
 *		\remarks	To run this script as CLI:  php -d date.timezone='Europe/Paris' phpunit HolidayTzFranceTest.php
 */

global $conf,$user,$langs,$db;
//define('TEST_DB_FORCE_TYPE','mysql');	// This is to force using mysql driver
//require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/holiday/class/holiday.class.php';
require_once dirname(__FILE__).'/../../htdocs/core/lib/date.lib.php';
require_once dirname(__FILE__).'/CommonClassTest.class.php';

$langs->load("dict");

if (empty($user->id)) {
	print "Load permissions for admin user nb 1\n";
	$user->fetch(1);
	$user->loadRights();
}

$conf->global->MAIN_DISABLE_ALL_MAILS = 1;


/**
 * Class for PHPUnit tests
 *
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 * @remarks	backupGlobals must be disabled to have db,conf,user and lang not erased.
 */
class HolidayTzFranceTest extends CommonClassTest
{
	/**
	 * setUpBeforeClass
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void
	{
		global $conf,$user,$langs,$db;

		if (getServerTimeZoneString() != 'Europe/Paris' && getServerTimeZoneString() != 'Europe/Berlin') {
			print "\n".__METHOD__." This PHPUnit test can be launched manually only onto a server with PHP timezone set to TZ=Europe/Paris, not a TZ=".getServerTimeZoneString().".\n";
			print "You can launch the test from command line with:\n";
			print "php -d date.timezone='Europe/Paris' phpunit HolidayTzFranceTest.php\n";
			die(1);
		}

		$db->begin();	// This is to have all actions inside a transaction even if test launched without suite.

		print __METHOD__."\n";
	}

	/**
	 * Regression test: validate() must count open days from the GMT date, not the server-timezone date.
	 *
	 * Scenario: a leave for Friday 2025-05-02 (a working day) in Europe/Paris (UTC+2 in May).
	 * Local midnight maps to 2025-05-01 22:00 UTC. Public holidays (LABORDAY1 = 1 May) are
	 * defined on UTC dates, so num_open_day() must be called with the GMT date. If the server
	 * timezone date is used (the historical bug), the Friday is read as 1 May => 0 open day,
	 * so the negative-balance guard is skipped and the request is wrongly allowed.
	 *
	 * With a 0 balance and a leave type blocking negative balances, a 1 open day request
	 * must be rejected by validate() (return -1).
	 *
	 * @return void
	 */
	public function testValidateUsesGmtDateForOpenDays()
	{
		global $conf,$user,$langs,$db,$mysoc;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$langs->load('holiday');

		// The bug only shows up for a country where 1 May is a public holiday.
		$mysoc->country_code = 'FR';

		// Enable the guard so that validate() runs the num_open_day() based balance check.
		$conf->global->HOLIDAY_DISALLOW_NEGATIVE_BALANCE = 1;

		// Create a dedicated leave type that blocks any request making the balance negative.
		$sql = "INSERT INTO ".$db->prefix()."c_holiday_types (entity, code, label, affect, delay, newbymonth, block_if_negative, active)";
		$sql .= " VALUES (".((int) $conf->entity).", 'TESTTZFR', '".$db->escape('Test TZ France')."', 1, 0, 0, 1, 1)";
		$resql = $db->query($sql);
		$this->assertNotFalse($resql, 'Failed to create the test leave type');
		$typeid = (int) $db->last_insert_id($db->prefix()."c_holiday_types");

		// getDictionaryValue() caches the dictionary: reset the cache so the new type is visible.
		unset($conf->cache['dictvalues_c_holiday_types']);

		// Set a balance of 0 for this user and this leave type.
		$sql = "INSERT INTO ".$db->prefix()."holiday_users (fk_user, fk_type, nb_holiday)";
		$sql .= " VALUES (".((int) $user->id).", ".((int) $typeid).", 0)";
		$resql = $db->query($sql);
		$this->assertNotFalse($resql, 'Failed to set the test balance');

		$localobject = new Holiday($db);
		$localobject->fk_user = $user->id;
		$localobject->fk_type = $typeid;
		$localobject->fk_validator = $user->id;
		$localobject->status = Holiday::STATUS_VALIDATED;
		$localobject->date_debut = dol_mktime(0, 0, 0, 5, 2, 2025, 'tzserver');
		$localobject->date_fin = dol_mktime(0, 0, 0, 5, 2, 2025, 'tzserver');
		$localobject->date_debut_gmt = dol_mktime(0, 0, 0, 5, 2, 2025, 'gmt');
		$localobject->date_fin_gmt = dol_mktime(0, 0, 0, 5, 2, 2025, 'gmt');

		$result = $localobject->validate($user);

		print __METHOD__." result=".$result."\n";

		// 1 open day requested with a 0 balance => the request must be blocked (return -1).
		// Before the fix, the Friday was read as 1 May (public holiday) => 0 open day and the guard was skipped.
		$this->assertEquals(-1, $result, 'Leave on Friday 2025-05-02 (1 open day) with a 0 balance must be blocked');
		$this->assertEquals('LeaveRequestCreationBlockedBecauseBalanceIsNegative', $localobject->error, 'The block reason must be the negative balance');
	}
}

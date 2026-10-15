<?php
/* Copyright (C) 2026       ATM Consulting          <support@atm-consulting.fr>
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
 *      \file       test/phpunit/FactureSituationDeltaAmountTest.php
 *      \ingroup    test
 *      \brief      PHPUnit test of the amount of a situation invoice line for the current situation only (INVOICE_USE_SITUATION = 1)
 *      \remarks    To run this script as CLI:  phpunit filename.php
 */

global $conf,$user,$langs,$db;
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/compta/facture/class/facture.class.php';
require_once dirname(__FILE__).'/../../htdocs/societe/class/societe.class.php';
require_once dirname(__FILE__).'/CommonClassTest.class.php';

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
class FactureSituationDeltaAmountTest extends CommonClassTest
{
	/**
	 * @var mixed
	 */
	private $savedSituationMode;

	/**
	 * setUp
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		global $conf, $invoicecache;

		parent::setUp();
		$this->savedSituationMode = $conf->global->INVOICE_USE_SITUATION ?? null;
		$conf->global->INVOICE_USE_SITUATION = 1;
		$invoicecache = array();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		global $conf;

		if ($this->savedSituationMode === null) {
			unset($conf->global->INVOICE_USE_SITUATION);
		} else {
			$conf->global->INVOICE_USE_SITUATION = $this->savedSituationMode;
		}
		parent::tearDown();
	}

	/**
	 * Create a situation invoice holding one line at the given cumulated progress
	 *
	 * @param	int		$socid			Thirdparty id
	 * @param	int		$cycleRef		Situation cycle ref
	 * @param	int		$counter		Situation counter
	 * @param	float	$percent		Cumulated progress of the line
	 * @param	int		$prevLineId		Line of the previous situation
	 * @return	Facture
	 */
	private function createSituation(int $socid, int $cycleRef, int $counter, float $percent, int $prevLineId): Facture
	{
		global $db, $user;

		$invoice = new Facture($db);
		$invoice->initAsSpecimen();
		$invoice->lines = array();
		$invoice->socid = $socid;
		$invoice->type = Facture::TYPE_SITUATION;
		$invoice->situation_cycle_ref = $cycleRef;
		$invoice->situation_counter = $counter;
		$this->assertGreaterThan(0, $invoice->create($user), (string) $invoice->error);
		$this->assertGreaterThan(0, $invoice->addline('Phase', 12942.30, 1, 20, 0, 0, 0, 0, '', '', 0, 0, 0, 'HT', 0, 0, -1, 0, '', 0, 0, null, 0, '', array(), $percent, $prevLineId), (string) $invoice->error);
		$invoice->fetch($invoice->id);

		return $invoice;
	}

	/**
	 * Lines of a situation must add up to the invoice total, not to a progress ratio of the cumulated amount.
	 *
	 * @return void
	 */
	public function testDeltaAmountAddsUpToInvoiceTotal(): void
	{
		global $conf, $db, $user;

		$thirdparty = new Societe($db);
		$thirdparty->initAsSpecimen();
		$thirdparty->name = 'SituationDeltaAmountTest '.mt_rand(0, 99999);
		$thirdparty->code_client = -1;
		$thirdparty->fournisseur = 0;
		$thirdparty->code_fournisseur = -1;
		$this->assertGreaterThan(0, $thirdparty->create($user), $thirdparty->error.implode(" ", $thirdparty->errors));

		$cycleRef = 9000 + mt_rand(0, 999);
		$first = $this->createSituation($thirdparty->id, $cycleRef, 1, 25, 0);
		$second = $this->createSituation($thirdparty->id, $cycleRef, 2, 50, (int) $first->lines[0]->id);

		$this->assertEquals(3235.58, $first->lines[0]->getSituationDeltaAmount($first->id, 'total_ht'));
		$this->assertEquals(3235.57, $second->lines[0]->getSituationDeltaAmount($second->id, 'total_ht'));
		$this->assertEquals((float) $second->total_ht, $second->lines[0]->getSituationDeltaAmount($second->id, 'total_ht'));
		$this->assertEquals((float) $second->total_tva, $second->lines[0]->getSituationDeltaAmount($second->id, 'total_tva'));
		$this->assertSame(0.0, $second->lines[0]->getSituationDeltaAmount($second->id, 'rowid'));

		$conf->global->INVOICE_USE_SITUATION = 2;
		$this->assertEquals((float) $second->lines[0]->total_ht, $second->lines[0]->getSituationDeltaAmount($second->id, 'total_ht'));
	}
}

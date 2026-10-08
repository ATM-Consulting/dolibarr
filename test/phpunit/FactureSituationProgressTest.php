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
 *      \file       test/phpunit/FactureSituationProgressTest.php
 *      \ingroup    test
 *      \brief      PHPUnit test of the progress of situation invoice lines in progressive mode (INVOICE_USE_SITUATION = 2)
 *      \remarks    To run this script as CLI:  phpunit filename.php
 */

global $conf,$user,$langs,$db;
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/compta/facture/class/facture.class.php';
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
class FactureSituationProgressTest extends CommonClassTest
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
		$conf->global->INVOICE_USE_SITUATION = 2;
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
	 * Insert an invoice of a situation cycle
	 *
	 * @param int $type   Invoice type
	 * @param int $cycle  Situation cycle
	 * @param int $status Invoice status
	 * @return int        Invoice id
	 */
	private function insertInvoice(int $type, int $cycle, int $status): int
	{
		$db = $this->savdb;
		$resql = $db->query("SELECT MIN(rowid) as socid FROM ".$db->prefix()."societe");
		$obj = $db->fetch_object($resql);
		$db->free($resql);

		$sql = "INSERT INTO ".$db->prefix()."facture (ref, entity, fk_soc, datec, datef, type, fk_statut, situation_cycle_ref, situation_counter, situation_final)";
		$sql .= " VALUES ('".$db->escape(uniqid('PHPUNITSIT'))."', 1, ".((int) $obj->socid).", '".$db->idate(dol_now())."', '".$db->idate(dol_now())."', ".$type.", ".$status.", ".$cycle.", 1, 0)";
		$this->assertTrue((bool) $db->query($sql), (string) $db->lasterror().' '.$sql);

		return (int) $db->last_insert_id($db->prefix().'facture');
	}

	/**
	 * Insert a line of a situation cycle
	 *
	 * @param int       $invoiceId Invoice id
	 * @param float     $percent   situation_percent of the line
	 * @param int|null  $prevId    Previous line
	 * @return int                 Line id
	 */
	private function insertLine(int $invoiceId, float $percent, ?int $prevId): int
	{
		$db = $this->savdb;
		$sql = "INSERT INTO ".$db->prefix()."facturedet (fk_facture, description, qty, subprice, tva_tx, total_ht, total_tva, total_ttc, product_type, situation_percent, fk_prev_id)";
		$sql .= " VALUES (".$invoiceId.", 'Lot A', 1, 1000, 0, ".(10 * $percent).", 0, ".(10 * $percent).", 1, ".$percent.", ".($prevId ? $prevId : 'null').")";
		$this->assertTrue((bool) $db->query($sql), (string) $db->lasterror().' '.$sql);

		return (int) $db->last_insert_id($db->prefix().'facturedet');
	}

	/**
	 * Progress before a new line of $invoiceId linked to $prevId
	 *
	 * @param int $invoiceId Invoice holding the new line
	 * @param int $prevId    Previous line
	 * @return float
	 */
	private function previousProgress(int $invoiceId, int $prevId): float
	{
		$line = new FactureLigne($this->savdb);
		$line->fk_prev_id = $prevId;

		return (float) $line->getAllPrevProgress($invoiceId);
	}

	/**
	 * A credit note brought back to 50 % on a line invoiced at 60 % credits the 10 % left, as a negative amount
	 *
	 * @return void
	 */
	public function testPartialCreditNoteCreditsTheDifference()
	{
		$db = $this->savdb;
		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 30, null);
		$s2 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l2 = $this->insertLine($s2, 30, $l1);
		$creditNoteId = $this->insertInvoice(Facture::TYPE_CREDIT_NOTE, $cycle, Facture::STATUS_DRAFT);
		$lineId = $this->insertLine($creditNoteId, 0, $l2);
		$db->query("UPDATE ".$db->prefix()."facturedet SET subprice = -1000 WHERE rowid = ".$lineId);

		$creditNote = new Facture($db);
		$creditNote->fetch($creditNoteId);
		$creditNote->update_percent($creditNote->lines[0], 50, false);

		$line = new FactureLigne($db);
		$line->fetch($lineId);
		$this->assertEquals(10, $line->situation_percent);
		$this->assertEquals(-100, $line->total_ht);
	}

	/**
	 * After a removal from cycle, the new cycle does not inherit the progress of the old one
	 *
	 * @return void
	 */
	public function testPreviousProgressStopsAtCycleBoundary()
	{
		$oldCycle = 900000 + mt_rand(1, 99999);
		$newCycle = $oldCycle + 100000;
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $oldCycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 30, null);
		$removed = $this->insertInvoice(Facture::TYPE_SITUATION, $newCycle, Facture::STATUS_CLOSED);
		$lRemoved = $this->insertLine($removed, 30, $l1);

		$this->assertEquals(0, $this->previousProgress($removed, $l1), 'line of the removed invoice');
		$nextNew = $this->insertInvoice(Facture::TYPE_SITUATION, $newCycle, Facture::STATUS_DRAFT);
		$this->assertEquals(30, $this->previousProgress($nextNew, $lRemoved), 'next situation of the new cycle');
		$nextOld = $this->insertInvoice(Facture::TYPE_SITUATION, $oldCycle, Facture::STATUS_DRAFT);
		$this->assertEquals(30, $this->previousProgress($nextOld, $l1), 'next situation of the old cycle');
	}

	/**
	 * Set the status of an invoice and reset the invoice cache
	 *
	 * @param int $invoiceId Invoice id
	 * @param int $status    Invoice status
	 * @return void
	 */
	private function setStatus(int $invoiceId, int $status): void
	{
		global $invoicecache;

		$db = $this->savdb;
		$this->assertTrue((bool) $db->query("UPDATE ".$db->prefix()."facture SET fk_statut = ".$status." WHERE rowid = ".$invoiceId));
		$invoicecache = array();
	}

	/**
	 * Legacy mode: a draft total credit note of a situation is rated like the validated one
	 *
	 * @return void
	 */
	public function testLegacyDraftTotalCreditNoteKeepsFullRatio()
	{
		global $conf;

		$conf->global->INVOICE_USE_SITUATION = 1;
		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 60, null);
		$creditNoteId = $this->insertInvoice(Facture::TYPE_CREDIT_NOTE, $cycle, Facture::STATUS_DRAFT);
		$lineId = $this->insertLine($creditNoteId, 60, $l1);

		$line = new FactureLigne($this->savdb);
		$line->fetch($lineId);
		$this->assertEquals(0, $line->get_prev_progress($creditNoteId), 'draft');
		$this->assertEquals(1, $line->getSituationRatio(), 'draft');

		$this->setStatus($creditNoteId, Facture::STATUS_VALIDATED);
		$this->assertEquals(0, $line->get_prev_progress($creditNoteId), 'validated');
		$this->assertEquals(1, $line->getSituationRatio(), 'validated');
	}

	/**
	 * Progressive mode: the progress of a draft credit note deducts the credit note itself, like the validated one
	 *
	 * @return void
	 */
	public function testDraftCreditNoteDeductsItself()
	{
		$db = $this->savdb;
		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 30, null);
		$s2 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l2 = $this->insertLine($s2, 30, $l1);
		$creditNoteId = $this->insertInvoice(Facture::TYPE_CREDIT_NOTE, $cycle, Facture::STATUS_DRAFT);
		$lineId = $this->insertLine($creditNoteId, 10, $l2);
		$db->query("UPDATE ".$db->prefix()."facturedet SET subprice = -1000, total_ht = -100, total_ttc = -100 WHERE rowid = ".$lineId);

		$line = new FactureLigne($db);
		$line->fetch($lineId);
		$this->assertEquals(50, $line->getAllPrevProgress($creditNoteId), 'draft');
		$this->assertEquals(20, $line->get_prev_progress($creditNoteId), 'draft, last situation only');
		$s3 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_DRAFT);
		$this->assertEquals(60, $this->previousProgress($s3, $l2), 'another invoice ignores the draft credit note');

		$creditNote = new Facture($db);
		$creditNote->fetch($creditNoteId);
		$creditNote->update_percent($creditNote->lines[0], 50, false);
		$line->fetch($lineId);
		$this->assertEquals(10, $line->situation_percent, 'saving the same progress again');
		$creditNote->fetch($creditNoteId);
		$creditNote->update_percent($creditNote->lines[0], 40, false);
		$line->fetch($lineId);
		$this->assertEquals(20, $line->situation_percent, 'saving a lower progress');
		$this->assertEquals(-200, $line->total_ht, 'saving a lower progress');

		$this->setStatus($creditNoteId, Facture::STATUS_VALIDATED);
		$this->assertEquals(40, $line->getAllPrevProgress($creditNoteId), 'validated');
		$this->assertEquals(40, $this->previousProgress($s3, $l2), 'next situation after the validated credit note');
	}

	/**
	 * A credit note line pointing to a line removed from its cycle has no previous progress to credit
	 *
	 * @return void
	 */
	public function testCreditNoteOnLineOutOfCycleHasNoPreviousProgress()
	{
		$db = $this->savdb;
		$cycle = 900000 + mt_rand(1, 99999);
		$removed = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle + 100000, Facture::STATUS_VALIDATED);
		$lRemoved = $this->insertLine($removed, 30, null);
		$creditNoteId = $this->insertInvoice(Facture::TYPE_CREDIT_NOTE, $cycle, Facture::STATUS_DRAFT);
		$lineId = $this->insertLine($creditNoteId, 10, $lRemoved);
		$db->query("UPDATE ".$db->prefix()."facturedet SET subprice = -1000 WHERE rowid = ".$lineId);

		$line = new FactureLigne($db);
		$line->fetch($lineId);
		$this->assertEquals(0, $line->getAllPrevProgress($creditNoteId, true, true));

		$creditNote = new Facture($db);
		$creditNote->fetch($creditNoteId);
		$creditNote->update_percent($creditNote->lines[0], 0, false);
		$line->fetch($lineId);
		$this->assertEquals(0, $line->situation_percent);
	}

	/**
	 * The progress before a credit note never deducts it, whatever its status
	 *
	 * @return void
	 */
	public function testProgressBeforeCreditNoteExcludesItself()
	{
		global $conf;

		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 60, null);
		$creditNoteId = $this->insertInvoice(Facture::TYPE_CREDIT_NOTE, $cycle, Facture::STATUS_VALIDATED);
		$lineId = $this->insertLine($creditNoteId, 10, $l1);
		$other = $this->insertInvoice(Facture::TYPE_CREDIT_NOTE, $cycle, Facture::STATUS_VALIDATED);
		$this->insertLine($other, 5, $l1);

		$line = new FactureLigne($this->savdb);
		$line->fetch($lineId);
		$this->assertEquals(45, $line->getAllPrevProgress($creditNoteId), 'mode 2, after');
		$this->assertEquals(55, $line->getAllPrevProgress($creditNoteId, true, true), 'mode 2, before');
		$conf->global->INVOICE_USE_SITUATION = 1;
		$this->assertEquals(45, $line->get_prev_progress($creditNoteId), 'mode 1, after');
		$this->assertEquals(55, $line->get_prev_progress($creditNoteId, true, true), 'mode 1, before');

		$this->setStatus($creditNoteId, Facture::STATUS_ABANDONED);
		$this->assertEquals(45, $line->get_prev_progress($creditNoteId), 'abandoned, its own view');
		$this->assertEquals(55, $line->get_prev_progress($creditNoteId, true, true), 'abandoned, before');
		$this->assertEquals(55, $this->previousProgress($s1, $l1), 'abandoned, ignored by another invoice');
	}
	/**
	 * Validate an invoice and return its situation_final
	 *
	 * @param int $invoiceId Invoice id
	 * @return int
	 */
	private function validateAndGetFinal(int $invoiceId): int
	{
		global $conf, $mysoc, $user;

		if (!is_object($mysoc)) {
			$mysoc = new Societe($this->savdb);
			$mysoc->setMysoc($conf);
		}
		$invoice = new Facture($this->savdb);
		$this->assertEquals(1, $invoice->fetch($invoiceId));
		$this->assertGreaterThan(0, $invoice->validate($user), $invoice->error.' '.implode(',', $invoice->errors));
		$invoice->fetch($invoiceId);

		return (int) $invoice->situation_final;
	}

	/**
	 * Progressive mode: a cycle completed by 33.33 + 33.33 + 33.34 is final
	 *
	 * @return void
	 */
	public function testThirdsCompleteTheCycle()
	{
		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 33.33, null);
		$s2 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l2 = $this->insertLine($s2, 33.33, $l1);
		$s3 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_DRAFT);
		$this->insertLine($s3, 33.34, $l2);

		$this->assertEquals(1, $this->validateAndGetFinal($s3));
	}

	/**
	 * Progressive mode: float noise on the cumul of the deltas still reaches 100 %
	 *
	 * @return void
	 */
	public function testCumulWithFloatNoiseIsFinal()
	{
		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 50, null);
		$s2 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_DRAFT);
		$this->insertLine($s2, 50.000000004, $l1);

		$this->assertEquals(100, FactureLigne::roundSituationProgress(100.000000004));
		$this->assertEquals(1, $this->validateAndGetFinal($s2));
	}

	/**
	 * Progressive mode: entering the previous cumul again is not below it
	 *
	 * @return void
	 */
	public function testPreviousCumulCanBeEnteredAgain()
	{
		$cycle = 900000 + mt_rand(1, 99999);
		$s1 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l1 = $this->insertLine($s1, 0.1, null);
		$s2 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_VALIDATED);
		$l2 = $this->insertLine($s2, 0.2, $l1);
		$s3 = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_DRAFT);

		$previous = $this->previousProgress($s3, $l2);
		$entered = (float) price2num('0.3', FactureLigne::SITUATION_PROGRESS_DECIMALS);
		$this->assertTrue($entered < $previous, 'raw float cumul is above the entered value');
		$this->assertFalse($entered < FactureLigne::roundSituationProgress($previous));
		$this->assertFalse($entered > FactureLigne::roundSituationProgress($previous));
	}

	/**
	 * Legacy mode: situation_final still requires situation_percent strictly equal to 100
	 *
	 * @return void
	 */
	public function testLegacyFinalIsUnchanged()
	{
		global $conf;

		$conf->global->INVOICE_USE_SITUATION = 1;
		$cycle = 900000 + mt_rand(1, 99999);
		$noisy = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle, Facture::STATUS_DRAFT);
		$this->insertLine($noisy, 99.999999999, null);
		$this->assertEquals(0, $this->validateAndGetFinal($noisy));

		$full = $this->insertInvoice(Facture::TYPE_SITUATION, $cycle + 100000, Facture::STATUS_DRAFT);
		$this->insertLine($full, 100, null);
		$this->assertEquals(1, $this->validateAndGetFinal($full));
	}
}

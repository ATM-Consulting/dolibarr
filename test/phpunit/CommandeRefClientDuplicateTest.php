<?php
/* Copyright (C) 2026 ATM Consulting <contact@atm-consulting.fr>
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
 *      \file       test/phpunit/CommandeRefClientDuplicateTest.php
 *      \ingroup    test
 *      \brief      PHPUnit test of the customer order duplicate ref_client control
 *      \remarks    To run this script as CLI:  phpunit --no-configuration CommandeRefClientDuplicateTest.php
 *      \remarks    UPSTREAM PR #40155 - whole file, drop it if the target version ships the PR
 */

global $conf, $user, $langs, $db;
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';

if (empty($user->id)) {
	$user->fetch(1);
	$user->getrights();
}
$conf->global->MAIN_DISABLE_ALL_MAILS = 1;

/**
 * Class for PHPUnit tests
 *
 * Does not extend CommonClassTest on purpose: that base class reads DOL_DATA_ROOT/dolibarr.log
 * at each setUp and errors out when the instance has no log file yet.
 *
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 */
class CommandeRefClientDuplicateTest extends PHPUnit\Framework\TestCase
{
	/** @var int */
	private static $socid1 = 0;

	/** @var int */
	private static $socid2 = 0;

	/**
	 * Open a transaction rolled back at the end of the suite: no test data is ever committed
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void
	{
		global $db;

		$db->begin();

		$sql = "SELECT rowid FROM ".$db->prefix()."societe";
		$sql .= " WHERE client > 0 AND status = 1 AND entity IN (".getEntity('societe').")";
		$sql .= " ORDER BY rowid ASC";
		$sql .= $db->plimit(2);

		$resql = $db->query($sql);
		$socids = array();
		while ($obj = $db->fetch_object($resql)) {
			$socids[] = (int) $obj->rowid;
		}
		$db->free($resql);

		self::$socid1 = isset($socids[0]) ? $socids[0] : 0;
		self::$socid2 = isset($socids[1]) ? $socids[1] : 0;
	}

	/**
	 * Roll back every record created by the suite
	 *
	 * @return void
	 */
	public static function tearDownAfterClass(): void
	{
		global $db;

		$db->rollback();
	}

	/**
	 * Skip the whole suite when the instance has not enough active customers to build the fixtures
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		if (empty(self::$socid1) || empty(self::$socid2)) {
			$this->markTestSkipped('Needs at least 2 active customers on the instance');
		}
	}

	/**
	 * Create a customer order
	 *
	 * @param	int		$socid			Third party id
	 * @param	string	$ref_client		Customer ref
	 * @return	Commande				Created order, id is <= 0 when creation was refused
	 */
	private function createOrder(int $socid, string $ref_client): Commande
	{
		global $db, $user;

		$order = new Commande($db);
		$order->socid = $socid;
		$order->date = dol_now();
		$order->ref_client = $ref_client;
		$order->id = $order->create($user);

		return $order;
	}

	/**
	 * A customer ref not used yet must be accepted
	 *
	 * @return void
	 */
	public function testCreateWithFreeRefClientIsAccepted(): void
	{
		$order = $this->createOrder(self::$socid1, 'PHPUNIT-FREE-001');

		$this->assertGreaterThan(0, $order->id, 'create() KO: '.$order->error);
	}

	/**
	 * Same customer ref on the same third party must be refused, and the error must name the conflicting order
	 *
	 * @return void
	 */
	public function testCreateWithDuplicateRefClientIsRefused(): void
	{
		$first = $this->createOrder(self::$socid1, 'PHPUNIT-DUP-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid1, 'PHPUNIT-DUP-001');

		$this->assertLessThan(0, $second->id, 'Duplicate ref_client should have been refused');
		$this->assertStringContainsString($first->ref, $second->error);
		$this->assertStringContainsString('PHPUNIT-DUP-001', $second->error);
	}

	/**
	 * The control is scoped to the third party: the same customer ref on another customer stays legitimate
	 *
	 * @return void
	 */
	public function testCreateWithSameRefClientOnAnotherThirdPartyIsAccepted(): void
	{
		$first = $this->createOrder(self::$socid1, 'PHPUNIT-OTHERSOC-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid2, 'PHPUNIT-OTHERSOC-001');

		$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);
	}

	/**
	 * An empty customer ref is never a duplicate, whatever the number of orders without one
	 *
	 * @return void
	 */
	public function testCreateWithEmptyRefClientIsAlwaysAccepted(): void
	{
		$first = $this->createOrder(self::$socid1, '');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid1, '');

		$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);
	}

	/**
	 * A customer ref made only of spaces must be treated as empty
	 *
	 * @return void
	 */
	public function testCreateWithBlankRefClientIsAlwaysAccepted(): void
	{
		$first = $this->createOrder(self::$socid1, '   ');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid1, '   ');

		$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);
	}

	/**
	 * Inline edition of the customer ref must be refused when the value is already used elsewhere
	 *
	 * @return void
	 */
	public function testSetRefClientToAnUsedValueIsRefused(): void
	{
		global $db, $user;

		$first = $this->createOrder(self::$socid1, 'PHPUNIT-SETREF-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid1, 'PHPUNIT-SETREF-002');
		$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);

		$order = new Commande($db);
		$this->assertGreaterThan(0, $order->fetch($second->id));

		$this->assertLessThan(0, $order->set_ref_client($user, 'PHPUNIT-SETREF-001'));
		$this->assertStringContainsString($first->ref, $order->error);

		$reloaded = new Commande($db);
		$reloaded->fetch($second->id);
		$this->assertEquals('PHPUNIT-SETREF-002', $reloaded->ref_client, 'Refused edition must not have been written');
	}

	/**
	 * Saving an order without touching its customer ref must stay possible, including on a legacy duplicate
	 *
	 * @return void
	 */
	public function testSetRefClientToItsOwnValueIsAccepted(): void
	{
		global $db, $user;

		$first = $this->createOrder(self::$socid1, 'PHPUNIT-SELF-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$order = new Commande($db);
		$this->assertGreaterThan(0, $order->fetch($first->id));

		$this->assertGreaterThan(0, $order->set_ref_client($user, 'PHPUNIT-SELF-001'));
	}

	/**
	 * The API update path is covered too
	 *
	 * @return void
	 */
	public function testUpdateWithDuplicateRefClientIsRefused(): void
	{
		global $db, $user;

		$first = $this->createOrder(self::$socid1, 'PHPUNIT-UPD-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid1, 'PHPUNIT-UPD-002');
		$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);

		$order = new Commande($db);
		$this->assertGreaterThan(0, $order->fetch($second->id));
		$this->fillMandatoryExtrafields($order);

		// Positive control: without it a failure below could come from any other update() rule
		$order->ref_client = 'PHPUNIT-UPD-003';
		$this->assertGreaterThan(0, $order->update($user), 'update() KO: '.$order->error);

		$order->ref_client = 'PHPUNIT-UPD-001';
		$this->assertLessThan(0, $order->update($user));
		$this->assertStringContainsString($first->ref, $order->error);

		$reloaded = new Commande($db);
		$reloaded->fetch($second->id);
		$this->assertEquals('PHPUNIT-UPD-003', $reloaded->ref_client, 'Refused update must not have been written');
	}

	/**
	 * Feed the mandatory extrafields of the instance so that update() is not refused for an unrelated reason
	 *
	 * @param	Commande	$order	Order to complete
	 * @return	void
	 */
	private function fillMandatoryExtrafields(Commande $order): void
	{
		global $db;

		$sql = "SELECT name, param FROM ".$db->prefix()."extrafields";
		$sql .= " WHERE elementtype = 'commande' AND fieldrequired = 1 AND entity IN (".getEntity('extrafields').")";

		$resql = $db->query($sql);
		while ($obj = $db->fetch_object($resql)) {
			if (!empty($order->array_options['options_'.$obj->name])) {
				continue;
			}
			$options = jsonOrUnserialize($obj->param);
			$order->array_options['options_'.$obj->name] = (is_array($options) && !empty($options['options']))
				? (string) array_key_first($options['options'])
				: '1';
		}
		$db->free($resql);
	}

	/**
	 * A placeholder value listed in ORDER_REF_CLIENT_DUPLICATE_ALLOWED_VALUES stays repeatable
	 *
	 * @return void
	 */
	public function testExemptedRefClientIsAlwaysAccepted(): void
	{
		global $conf;

		$savedvalue = getDolGlobalString('ORDER_REF_CLIENT_DUPLICATE_ALLOWED_VALUES');
		$conf->global->ORDER_REF_CLIENT_DUPLICATE_ALLOWED_VALUES = 'BPA DEVIS,PHPUNIT-EXEMPT';

		try {
			$first = $this->createOrder(self::$socid1, 'PHPUNIT-EXEMPT');
			$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

			$second = $this->createOrder(self::$socid1, 'PHPUNIT-EXEMPT');
			$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);

			// Matching is case and spacing insensitive
			$third = $this->createOrder(self::$socid1, '  phpunit-exempt  ');
			$this->assertGreaterThan(0, $third->id, 'create() KO: '.$third->error);

			// A value outside the list is still controlled
			$fourth = $this->createOrder(self::$socid1, 'PHPUNIT-NOTEXEMPT');
			$this->assertGreaterThan(0, $fourth->id, 'create() KO: '.$fourth->error);
			$fifth = $this->createOrder(self::$socid1, 'PHPUNIT-NOTEXEMPT');
			$this->assertLessThan(0, $fifth->id, 'Duplicate ref_client should have been refused');
		} finally {
			$conf->global->ORDER_REF_CLIENT_DUPLICATE_ALLOWED_VALUES = $savedvalue;
		}
	}

	/**
	 * A refused creation must leave no row behind and must not shift the order numbering
	 *
	 * @return void
	 */
	public function testRefusedCreationLeavesNoTrace(): void
	{
		global $db, $user;

		$first = $this->createOrder(self::$socid1, 'PHPUNIT-NOTRACE-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$soc = new Societe($db);
		$soc->fetch(self::$socid1);
		$countbefore = $this->countOrders();
		$nextrefbefore = $first->getNextNumRef($soc);

		$refused = $this->createOrder(self::$socid1, 'PHPUNIT-NOTRACE-001');
		$this->assertLessThan(0, $refused->id);

		$this->assertEquals($countbefore, $this->countOrders(), 'A refused creation must not insert any row');
		$this->assertEquals($nextrefbefore, $first->getNextNumRef($soc), 'A refused creation must not consume a ref');
	}

	/**
	 * Orders already in duplicate before the control existed must stay usable
	 *
	 * @return void
	 */
	public function testLegacyDuplicateStaysManipulable(): void
	{
		global $db, $user;

		$first = $this->createOrder(self::$socid1, 'PHPUNIT-LEGACY-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$second = $this->createOrder(self::$socid1, 'PHPUNIT-LEGACY-FREE');
		$this->assertGreaterThan(0, $second->id, 'create() KO: '.$second->error);
		$this->forceRefClientInDatabase($second->id, 'PHPUNIT-LEGACY-001');

		$order = new Commande($db);
		$this->assertGreaterThan(0, $order->fetch($second->id));
		$this->assertEquals('PHPUNIT-LEGACY-001', $order->ref_client);

		// Everything that does not write ref_client keeps working on a legacy duplicate
		$this->assertGreaterThan(0, $order->set_date($user, dol_now()), 'set_date() KO: '.$order->error);
		$this->assertGreaterThan(0, $order->set_date_livraison($user, dol_now()), 'set_date_livraison() KO: '.$order->error);

		// And the duplicate remains fixable
		$this->assertGreaterThan(0, $order->set_ref_client($user, 'PHPUNIT-LEGACY-FIXED'), 'set_ref_client() KO: '.$order->error);

		$reloaded = new Commande($db);
		$reloaded->fetch($second->id);
		$this->assertEquals('PHPUNIT-LEGACY-FIXED', $reloaded->ref_client);
	}

	/**
	 * Count the customer orders visible on the entity
	 *
	 * @return int
	 */
	private function countOrders(): int
	{
		global $db;

		$sql = "SELECT COUNT(*) AS nb FROM ".$db->prefix()."commande WHERE entity IN (".getEntity('commande').")";
		$resql = $db->query($sql);
		$obj = $db->fetch_object($resql);
		$db->free($resql);

		return (int) $obj->nb;
	}

	/**
	 * Write ref_client without going through the control, to rebuild a pre-existing duplicate
	 *
	 * @param	int		$id				Order id
	 * @param	string	$ref_client		Customer ref to force
	 * @return	void
	 */
	private function forceRefClientInDatabase(int $id, string $ref_client): void
	{
		global $db;

		$sql = "UPDATE ".$db->prefix()."commande SET ref_client = '".$db->escape($ref_client)."'";
		$sql .= " WHERE rowid = ".((int) $id);

		$this->assertNotFalse($db->query($sql), (string) $db->lasterror());
	}

	/**
	 * The detection method is scoped to the third party and excludes the order being edited
	 *
	 * @return void
	 */
	public function testGetOrderUsingSameRefClient(): void
	{
		global $db;

		$first = $this->createOrder(self::$socid1, 'PHPUNIT-LOOKUP-001');
		$this->assertGreaterThan(0, $first->id, 'create() KO: '.$first->error);

		$order = new Commande($db);

		$this->assertEquals($first->ref, $order->getOrderUsingSameRefClient('PHPUNIT-LOOKUP-001', self::$socid1));
		$this->assertEquals('', $order->getOrderUsingSameRefClient('PHPUNIT-LOOKUP-001', self::$socid1, $first->id));
		$this->assertEquals('', $order->getOrderUsingSameRefClient('PHPUNIT-LOOKUP-001', self::$socid2));
		$this->assertEquals('', $order->getOrderUsingSameRefClient('PHPUNIT-UNKNOWN-999', self::$socid1));
		$this->assertEquals('', $order->getOrderUsingSameRefClient('', self::$socid1));
		$this->assertEquals('', $order->getOrderUsingSameRefClient('PHPUNIT-LOOKUP-001', 0));
	}
}

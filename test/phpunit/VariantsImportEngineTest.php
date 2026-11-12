<?php
/* Copyright (C) 2026 ATM Consulting          <contact@atm-consulting.fr>
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
 *      \file       test/phpunit/VariantsImportEngineTest.php
 *      \ingroup    test
 *      \brief      PHPUnit test of the variants import run through the 23.0 CSV import engine
 *      \remarks    To run this script as CLI:  phpunit filename.php
 */

global $conf,$user,$langs,$db;
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/product/class/product.class.php';
require_once dirname(__FILE__).'/../../htdocs/imports/class/import.class.php';
require_once dirname(__FILE__).'/../../htdocs/core/modules/import/import_csv.modules.php';
require_once dirname(__FILE__).'/../../htdocs/waf.inc.php';	// testSqlAndScriptInject() is called by the import engine and only loaded by main.inc.php
require_once dirname(__FILE__).'/CommonClassTest.class.php';

if (empty($user->id)) {
	print "Load permissions for admin user nb 1\n";
	$user->fetch(1);
	$user->loadRights();
}


// BACKPORT ATM Dolibarr#40167 - this whole file covers the 23.0 import engine glue, remove when upgrading to 25+
/**
 * Class for PHPUnit tests
 *
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 * @remarks	backupGlobals must be disabled to have db,conf,user and lang not erased.
 */
class VariantsImportEngineTest extends CommonClassTest
{
	/**
	 * Columns of the test file for the combinations dataset
	 */
	const COMBINATION_COLUMNS = array(
		1 => 'pac.fk_product_parent',
		2 => 'pac.fk_product_child',
		3 => 'pac.variation_price',
		4 => 'pac.variation_price_percentage',
		5 => 'pac.variation_weight',
	);

	/**
	 * @var string[]	Files written by the tests, removed by tearDown()
	 */
	private $tmpfiles = array();

	/**
	 * setUpBeforeClass
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void
	{
		global $conf, $db;

		if (!isModEnabled('variants')) {
			$conf->modules['variants'] = 'variants';
		}
		if (empty($conf->variants->enabled)) {
			if (!isset($conf->variants)) {
				$conf->variants = new stdClass();
			}
			$conf->variants->enabled = 1;
		}

		$db->begin();

		print __METHOD__."\n";
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		foreach ($this->tmpfiles as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
		$this->tmpfiles = array();

		parent::tearDown();
	}

	/**
	 * Create a product and give it the same price on every price level
	 *
	 * @param	string	$ref	Product ref
	 * @return	Product			Created product
	 */
	private function createProduct($ref)
	{
		global $user;

		$product = new Product($this->savdb);
		$product->ref = $ref;
		$product->label = 'Label of '.$ref;
		$product->description = 'Description of '.$ref;
		$product->type = Product::TYPE_PRODUCT;
		$product->status = 1;
		$product->status_buy = 1;
		$product->price_base_type = 'HT';
		$product->price = 100;
		$product->tva_tx = 20;
		$product->weight = 2;
		$this->assertGreaterThan(0, $product->create($user), 'create '.$ref.' '.$product->errorsToString());

		if (getDolGlobalString('PRODUIT_MULTIPRICES')) {
			for ($level = 1; $level <= getDolGlobalInt('PRODUIT_MULTIPRICES_LIMIT'); $level++) {
				$this->assertGreaterThan(0, $product->updatePrice(100, 'HT', $user, 20, 0, $level), 'level '.$level.' price of '.$ref);
			}
		}

		return $product;
	}

	/**
	 * Return the selling price of a product, the one of the first level with multiprices
	 *
	 * @param	int		$id		Product id
	 * @return	float			Price
	 */
	private function fetchPrice($id)
	{
		$product = new Product($this->savdb);
		$this->assertGreaterThan(0, $product->fetch($id), 'fetch product '.$id);

		return (float) (getDolGlobalString('PRODUIT_MULTIPRICES') ? $product->multiprices[1] : $product->price);
	}

	/**
	 * Write a CSV file made of the given lines and return its path
	 *
	 * @param	string[]	$lines	Lines of the file, without line ending
	 * @param	string		$prefix	Bytes written before the first line
	 * @return	string				Path of the file
	 */
	private function writeCsv($lines, $prefix = '')
	{
		$file = tempnam(sys_get_temp_dir(), 'phpunitvariants');
		file_put_contents($file, $prefix.implode("\n", $lines)."\n");
		$this->tmpfiles[] = $file;

		return $file;
	}

	/**
	 * Import every line of a CSV file into the combinations dataset, as imports/import.php does
	 *
	 * @param	string		$file			Path of the file
	 * @param	int			$simulation		1 to run as the simulation step
	 * @return	array<int,array<array{lib:string,type:string}>>		Errors per line number
	 */
	private function importCombinations($file, $simulation = 0)
	{
		global $user;

		$objimport = new Import($this->savdb);
		$objimport->load_arrays($user, 'variants_combination');
		$this->assertEquals('variants_combination', $objimport->array_import_code[0], 'the combinations dataset is declared');

		$obj = new ImportCsv($this->savdb, 'variants_combination');
		$obj->separator = ';';
		$obj->importissimulation = $simulation;
		$this->assertGreaterThan(0, $obj->import_open_file($file), 'open '.$file);

		$errors = array();
		$linenb = 0;
		$importid = dol_print_date(dol_now(), '%Y%m%d%H%M%S');
		while (($record = $obj->import_read_record()) !== false) {
			$linenb++;
			$obj->import_insert($record, self::COMBINATION_COLUMNS, $objimport, count(self::COMBINATION_COLUMNS), $importid, array('pac.fk_product_parent', 'pac.fk_product_child'));
			if (count($obj->errors)) {
				$errors[$linenb] = $obj->errors;
			}
		}
		$obj->import_close_file();

		return $errors;
	}

	/**
	 * An imported combination gets the price and weight of its parent plus the impacts.
	 *
	 * @return void
	 */
	public function testCsvImportReconcilesTheVariant()
	{
		$parent = $this->createProduct('PHPUNITENG_P1');
		$child = $this->createProduct('PHPUNITENG_C1');

		$errors = $this->importCombinations($this->writeCsv(array('ref:PHPUNITENG_P1;ref:PHPUNITENG_C1;5;0;1')));

		$this->assertEmpty($errors, 'import errors '.json_encode($errors));
		$this->assertEquals(105, $this->fetchPrice($child->id), 'price of the variant is the price of the parent plus the impact');
		$tmpchild = new Product($this->savdb);
		$tmpchild->fetch($child->id);
		$this->assertEquals(3, (float) $tmpchild->weight, 'weight of the variant is the weight of the parent plus the impact');
	}

	/**
	 * A line refused by the trigger is reported as an error of that line.
	 *
	 * @return void
	 */
	public function testCsvImportReportsTheTriggerRefusal()
	{
		global $langs;

		$langs->load('products');
		$this->createProduct('PHPUNITENG_P2');

		$errors = $this->importCombinations($this->writeCsv(array('ref:PHPUNITENG_P2;ref:PHPUNITENG_P2;0;0;0')));

		$this->assertArrayHasKey(1, $errors, 'the line is in error');
		$messages = array_column($errors[1], 'lib');
		$this->assertContains($langs->trans('ErrorVariantChildIsParent'), $messages, 'the message of the trigger reaches the screen');
		$this->assertContains('TRIGGER', array_column($errors[1], 'type'), 'the error is typed as a trigger error');
	}

	/**
	 * The simulation step runs no trigger, as the import engine of Dolibarr 24.0.
	 *
	 * @return void
	 */
	public function testSimulationRunsNoTrigger()
	{
		$this->createProduct('PHPUNITENG_P3');
		$child = $this->createProduct('PHPUNITENG_C3');
		$pricebefore = $this->fetchPrice($child->id);

		$errors = $this->importCombinations($this->writeCsv(array('ref:PHPUNITENG_P3;ref:PHPUNITENG_C3;5;0;1')), 1);

		$this->assertEmpty($errors, 'simulation errors '.json_encode($errors));
		$this->assertEquals($pricebefore, $this->fetchPrice($child->id), 'the variant is not reconciled during the simulation');
	}

	/**
	 * Only the variant tables run a trigger from the import engine.
	 *
	 * @return void
	 */
	public function testOtherTablesRunNoTrigger()
	{
		global $user, $langs, $conf;

		$obj = new ImportCsv($this->savdb, 'produit_1');
		$method = new ReflectionMethod($obj, 'runImportLineTrigger');
		$method->setAccessible(true);

		$this->assertEquals(0, $method->invoke($obj, MAIN_DB_PREFIX.'product', 'insert', 1, 'x', $user, $langs, $conf), 'llx_product runs no trigger');
		$this->assertEquals(0, $method->invoke($obj, MAIN_DB_PREFIX.'product_attribute', 'insert', 1, 'x', $user, $langs, $conf), 'llx_product_attribute runs no trigger');
	}
}

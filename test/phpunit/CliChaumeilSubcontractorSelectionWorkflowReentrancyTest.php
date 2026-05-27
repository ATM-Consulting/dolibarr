<?php
declare(strict_types=1);

/* Copyright (C) 2026 ATM Consulting <support@atm-consulting.fr> */

global $conf, $user, $langs, $db;
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/comm/propal/class/propal.class.php';
require_once dirname(__FILE__).'/../../htdocs/custom/clichaumeil/class/Subcontracting/CliChaumeilSubcontractorSelectionWorkflow.class.php';
require_once dirname(__FILE__).'/CommonClassTest.class.php';

if (empty($user->id)) {
    $user->fetch(1);
    $user->loadRights();
}
$conf->global->MAIN_DISABLE_ALL_MAILS = 1;

/**
 * Tests for CliChaumeilSubcontractorSelectionWorkflow re-entrancy guard.
 *
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 */
class CliChaumeilSubcontractorSelectionWorkflowReentrancyTest extends CommonClassTest
{
    /**
     * Return a ReflectionProperty targeting the private static $isRunning flag.
     *
     * @return ReflectionProperty
     */
    private static function getIsRunningProperty(): ReflectionProperty
    {
        $prop = new ReflectionProperty(CliChaumeilSubcontractorSelectionWorkflow::class, 'isRunning');
        $prop->setAccessible(true);
        return $prop;
    }

    /**
     * Reset the static flag after each test so tests remain independent.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        self::getIsRunningProperty()->setValue(null, false);
        parent::tearDown();
    }

    /**
     * The flag must be false when no execution is in progress.
     *
     * @return void
     */
    public function testIsRunningIsFalseByDefault(): void
    {
        $prop = self::getIsRunningProperty();
        self::assertFalse($prop->getValue(null));
    }

    /**
     * When $isRunning is already true (= we are inside a first execute() call),
     * a second call must return an empty array immediately without doing any work.
     *
     * @return void
     */
    public function testExecuteReturnsEmptyArrayWhenAlreadyRunning(): void
    {
        global $db, $conf, $langs, $user;

        $prop = self::getIsRunningProperty();
        $prop->setValue(null, true); // Simulate being inside a primary execute() call

        $workflow = new CliChaumeilSubcontractorSelectionWorkflow($db, $conf, $langs);
        $parent = new Propal($db); // Minimal object — execute() will return before using it

        $result = $workflow->execute($parent, 99999, $user);

        self::assertSame([], $result, 'execute() must return [] when the re-entrancy guard is active');
        // $isRunning stays true here: the primary caller (which set it) has not finished yet.
        // tearDown() will reset it.
    }

    /**
     * After a re-entrant call returns [], the flag must still be true
     * (the primary execute() will reset it via its own finally block).
     *
     * @return void
     */
    public function testFlagRemainsSetAfterGuardSkip(): void
    {
        global $db, $conf, $langs, $user;

        $prop = self::getIsRunningProperty();
        $prop->setValue(null, true);

        $workflow = new CliChaumeilSubcontractorSelectionWorkflow($db, $conf, $langs);
        $parent   = new Propal($db);

        $workflow->execute($parent, 99999, $user);

        self::assertTrue(
            $prop->getValue(null),
            '$isRunning must remain true after a re-entrant call — only the primary execute() resets it via finally'
        );
    }
}

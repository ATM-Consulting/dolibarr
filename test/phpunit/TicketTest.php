<?php
/* Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2023 Alexandre Janniaux   <alexandre.janniaux@gmail.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
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
 *      \file       test/unit/TicketTest.php
 *		\ingroup    test
 *      \brief      PHPUnit test
 *		\remarks	To run this script as CLI:  phpunit filename.php
 */

global $conf,$user,$langs,$db;
//define('TEST_DB_FORCE_TYPE','mysql'); // This is to force using mysql driver
//require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../htdocs/user/class/usergroup.class.php';
require_once dirname(__FILE__).'/../../htdocs/ticket/class/ticket.class.php';
// @ATM-PATCH BEGIN backport-tests — tests of the ticket message hooks and of the recipients recorded on the message event
// grep: git grep '@ATM-PATCH' -- htdocs/ test/
require_once dirname(__FILE__).'/../../htdocs/core/class/html.form.class.php';
require_once dirname(__FILE__).'/../../htdocs/core/class/hookmanager.class.php';
require_once dirname(__FILE__).'/../../htdocs/contact/class/contact.class.php';
require_once dirname(__FILE__).'/../../htdocs/societe/class/societe.class.php';
require_once dirname(__FILE__).'/../../htdocs/comm/action/class/actioncomm.class.php';
// @ATM-PATCH END backport-tests
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
class TicketTest extends CommonClassTest
{
	/**
	 * testTicketCreate
	 *
	 * @return	int
	 */
	public function testTicketCreate()
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		// Try to create one with bad values
		$localobject = new Ticket($db);
		$localobject->initAsSpecimen();
		$localobject->ref = '';
		$result = $localobject->create($user);

		print __METHOD__." result=".$result."\n";
		$this->assertEquals(-3, $result, $localobject->error.join(',', $localobject->errors));

		// Try to create one with correct values
		$localobject = new Ticket($db);
		$localobject->initAsSpecimen();
		$result = $localobject->create($user);

		print __METHOD__." result=".$result."\n";
		$this->assertGreaterThan(0, $result, $localobject->error.join(',', $localobject->errors));

		return $result;
	}

	/**
	 * testTicketFetch
	 *
	 * @param	int		$id		Id of ticket
	 * @return	int
	 *
	 * @depends	testTicketCreate
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketFetch($id)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$localobject = new Ticket($db);
		$result = $localobject->fetch($id);

		print __METHOD__." id=".$id." result=".$result."\n";
		$this->assertGreaterThan(0, $result);

		return $localobject;
	}

	/**
	 * testTicketmarkAsRead
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketmarkAsRead($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$result = $localobject->markAsRead($user);
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject;
	}

	/**
	 * testTicketsetProject
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketsetProject($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$project_id = 1;

		$result = $localobject->setProject($project_id);
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject;
	}

	/**
	 * testTicketsetContract
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketsetContract($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$contract_id = 1;

		$result = $localobject->setContract($contract_id);
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject;
	}

	/**
	 * testTicketsetProgression
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketsetProgression($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$percent = 80;

		$result = $localobject->setProgression($percent);
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject;
	}

	/**
	 * testTicketassignUser
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketassignUser($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$user_id_to_assign = 1;

		$result = $localobject->assignUser($user, $user_id_to_assign);
		;
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject;
	}

	/**
	 * testTicketassignUserOther
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketassignUserOther($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$user_id_to_assign = 2;

		$result = $localobject->assignUser($user, $user_id_to_assign);
		;
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject;
	}

	/**
	 * testTicketclose
	 *
	 * @param	Ticket		$localobject		Ticket
	 * @return	int
	 *
	 * @depends	testTicketFetch
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketclose($localobject)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$result = $localobject->close($user);
		print __METHOD__." id=".$localobject->id." result=".$result."\n";

		$this->assertGreaterThan(0, $result);
		return $localobject->id;
	}


	/**
	 * testTicketDelete
	 *
	 * @param	int		$id		Id of ticket
	 * @return	int
	 *
	 * @depends	testTicketclose
	 * The depends says test is run only if previous is ok
	 */
	public function testTicketDelete($id)
	{
		global $conf,$user,$langs,$db;
		$conf = $this->savconf;
		$user = $this->savuser;
		$langs = $this->savlangs;
		$db = $this->savdb;

		$localobject = new Ticket($db);
		$result = $localobject->fetch($id);
		$result = $localobject->delete($user);

		print __METHOD__." id=".$id." result=".$result."\n";
		$this->assertGreaterThan(0, $result);
		return $result;
	}

	// @ATM-PATCH BEGIN backport-tests — tests of the ticket message hooks and of the recipients recorded on the message event
	// grep: git grep '@ATM-PATCH' -- htdocs/ test/
	/**
	 * testNewMessagePublicRecordsUnionOfInternalAndExternalMails
	 *
	 * @return	void
	 */
	public function testNewMessagePublicRecordsUnionOfInternalAndExternalMails()
	{
		$fixture = $this->createMessageFixture();
		try {
			$result = $this->postMessage($fixture, array(
				'private_message' => '0',
				'receiver_multiselect' => '1',
				'receiver' => array($fixture['externalEmail'], strtoupper($fixture['internalEmail'])),
			));
			$event = $this->fetchMessageEvent($fixture['ticket']->id);
		} finally {
			$this->deleteMessageFixture($fixture);
		}

		$this->assertGreaterThan(0, $result['return']);
		$this->assertCount(3, $result['sent'], json_encode($result['sent']));
		$this->assertSame(array($fixture['externalEmail'], $fixture['internalEmail']), $this->emailsOf((string) $event->email_to));
	}

	/**
	 * testNewMessagePrivateRecordsInternalMail
	 *
	 * @return	void
	 */
	public function testNewMessagePrivateRecordsInternalMail()
	{
		$fixture = $this->createMessageFixture();
		try {
			$result = $this->postMessage($fixture, array('private_message' => '1'));
			$event = $this->fetchMessageEvent($fixture['ticket']->id);
		} finally {
			$this->deleteMessageFixture($fixture);
		}

		$this->assertCount(1, $result['sent'], json_encode($result['sent']));
		$this->assertSame('TICKET_MSG_PRIVATE_SENTBYMAIL', $event->code);
		$this->assertSame(array($fixture['internalEmail']), $this->emailsOf((string) $event->email_to));
	}

	/**
	 * testUpdateSendtoccTicketMessageReplacesInternalCc
	 *
	 * @return	void
	 */
	public function testUpdateSendtoccTicketMessageReplacesInternalCc()
	{
		$ccHook = new class () {
			/** @var string */
			public $error = '';
			/** @var string[] */
			public $errors = array();
			/** @var string[] */
			public $results = array();
			/** @var string[] */
			public $mailtypes = array();

			/**
			 * Replace the CC of the internal mail.
			 *
			 * @param	array<string,mixed>	$parameters	Hook parameters
			 * @return	int
			 */
			public function updateSendtoccTicketMessage($parameters)
			{
				$this->mailtypes[] = $parameters['mailtype'];
				if ($parameters['mailtype'] !== 'internal') {
					return 0;
				}
				$this->results = array('cc-hook@example.invalid');
				return 1;
			}
		};

		$fixture = $this->createMessageFixture();
		try {
			$result = $this->postMessage($fixture, array('private_message' => '1'), $ccHook);
			$event = $this->fetchMessageEvent($fixture['ticket']->id);
		} finally {
			$this->deleteMessageFixture($fixture);
		}

		$this->assertSame(array('internal'), $ccHook->mailtypes);
		$this->assertCount(1, $result['sent'], json_encode($result['sent']));
		$this->assertSame('cc-hook@example.invalid', $result['sent'][0]['cc']);
		$this->assertSame('cc-hook@example.invalid', $event->email_tocc);
	}

	/**
	 * testShowActionsMessagingWithoutHookAddsNothing
	 *
	 * @return	void
	 */
	public function testShowActionsMessagingWithoutHookAddsNothing()
	{
		$fixture = $this->createMessageFixture();
		try {
			$this->createTimelineEvent($fixture['ticket']);
			$withoutHook = $this->renderTimeline($fixture['ticket']);
			$withHook = $this->renderTimeline($fixture['ticket'], $this->newTimelineHook());
		} finally {
			$this->deleteMessageFixture($fixture);
		}

		$this->assertStringContainsString('Ticket message hooks test', $withoutHook);
		$this->assertStringNotContainsString('phpunit-after-message', $withoutHook);
		$this->assertSame($withoutHook, str_replace('<span class="phpunit-after-message"></span>', '', $withHook));
	}

	/**
	 * testShowActionsMessagingAppendsHookOutput
	 *
	 * @return	void
	 */
	public function testShowActionsMessagingAppendsHookOutput()
	{
		$timelineHook = $this->newTimelineHook();
		$fixture = $this->createMessageFixture();
		try {
			$this->createTimelineEvent($fixture['ticket']);
			$out = $this->renderTimeline($fixture['ticket'], $timelineHook);
		} finally {
			$this->deleteMessageFixture($fixture);
		}

		$this->assertSame(1, substr_count($out, '<span class="phpunit-after-message"></span>'));
		$this->assertSame(array('timeline-to@example.invalid'), $timelineHook->recipients);
	}

	/**
	 * Hook object printing a marker under each timeline message.
	 *
	 * @return	object
	 */
	private function newTimelineHook()
	{
		return new class () {
			/** @var string */
			public $error = '';
			/** @var string[] */
			public $errors = array();
			/** @var ?string */
			public $resprints;
			/** @var string[] */
			public $recipients = array();

			/**
			 * Print a marker under the message.
			 *
			 * @param	array<string,mixed>	$parameters	Hook parameters
			 * @return	int
			 */
			public function printActionMessagingAfterMessage($parameters)
			{
				$this->recipients[] = (string) $parameters['histo']['msg_to'];
				$this->resprints = '<span class="phpunit-after-message"></span>';
				return 0;
			}
		};
	}

	/**
	 * Create a thirdparty, a ticket with one external contact and one internal user contact, both with an email.
	 *
	 * @return	array{thirdparty:Societe,ticket:Ticket,contact:Contact,internal:User,externalEmail:string,internalEmail:string}
	 */
	private function createMessageFixture()
	{
		global $db, $user;

		$thirdparty = new Societe($db);
		$thirdparty->name = 'TicketMessageHooksTest';
		$this->assertGreaterThan(0, $thirdparty->create($user, 1), $thirdparty->error.implode(',', $thirdparty->errors));

		$ticket = new Ticket($db);
		$ticket->initAsSpecimen();
		$ticket->ref = 'TIMH-'.uniqid();
		$ticket->track_id = uniqid('tmh');
		$ticket->fk_soc = $thirdparty->id;
		$ticket->fk_user_assign = 0;
		$ticket->origin_email = '';
		$ticketId = $ticket->create($user, 1);
		$this->assertGreaterThan(0, $ticketId, $ticket->error.implode(',', $ticket->errors));
		$ticket->fetch($ticketId);

		$contact = new Contact($db);
		$contact->lastname = 'TicketMessageHooksExternal';
		$contact->email = 'external-msg-hooks@example.invalid';
		$this->assertGreaterThan(0, $contact->create($user, 1), $contact->error.implode(',', $contact->errors));

		$internal = new User($db);
		$internal->login = 'tmhinternal'.uniqid();
		$internal->lastname = 'TicketMessageHooksInternal';
		$internal->email = 'internal-msg-hooks@example.invalid';
		$this->assertGreaterThan(0, $internal->create($user, 1), $internal->error.implode(',', $internal->errors));

		$fixture = array('thirdparty' => $thirdparty, 'ticket' => $ticket, 'contact' => $contact, 'internal' => $internal, 'externalEmail' => $contact->email, 'internalEmail' => $internal->email);
		// add_contact() also returns 0 for an unknown contact type, so only > 0 proves the link.
		$this->assertGreaterThan(0, $ticket->add_contact($contact->id, 'SUPPORTCLI', 'external', 1));
		$this->assertGreaterThan(0, $ticket->add_contact($internal->id, 'SUPPORTTEC', 'internal', 1));

		return $fixture;
	}

	/**
	 * Post a back-office message by email on the fixture ticket; a hook stands in for the mail transport.
	 *
	 * @param	array{ticket:Ticket}	$fixture	Fixture
	 * @param	array<string,mixed>		$post		POST fields added to the message ones
	 * @param	?object					$extraHook	Hook object registered next to the mail sink
	 * @return	array{return:int,sent:array<array{to:string,cc:string}>}
	 */
	private function postMessage($fixture, $post, $extraHook = null)
	{
		global $conf, $user, $hookmanager;

		$sink = new class () {
			/** @var string */
			public $error = '';
			/** @var string[] */
			public $errors = array();
			/** @var array<array{to:string,cc:string}> */
			public $sent = array();

			/**
			 * Record the mail instead of sending it.
			 *
			 * @param	array<string,mixed>	$parameters	Hook parameters
			 * @param	CMailFile			$mail		Mail
			 * @return	int
			 */
			public function sendMail($parameters, $mail)
			{
				$this->sent[] = array('to' => (string) $mail->addr_to, 'cc' => (string) $mail->addr_cc);
				return 1;
			}
		};

		// Only the sink answers the 'mail' hook, so CMailFile::sendfile() returns true without sending anything.
		$ticket = $fixture['ticket'];
		$savedHookManager = $hookmanager;
		$savedHookParts = $conf->modules_parts['hooks'];
		$savedConsts = array();
		$consts = array('MAIN_DISABLE_ALL_MAILS' => '0', 'TICKET_DISABLE_ALL_MAILS' => '', 'TICKET_NOTIFICATION_EMAIL_FROM' => 'from@example.invalid', 'TICKET_NOTIFICATION_ALSO_MAIN_ADDRESS' => '0', 'MAIN_MAIL_FORCE_SENDTO' => '');
		foreach ($consts as $name => $value) {
			$savedConsts[$name] = isset($conf->global->$name) ? $conf->global->$name : null;
			$conf->global->$name = $value;
		}
		$hookmanager = new HookManager($ticket->db);
		$hookmanager->hooks['mail']['phpunitmailsink'] = $sink;
		$hookmanager->hooksSorted['mail']['1:phpunitmailsink'] = $sink;
		if ($extraHook !== null) {
			$hookmanager->hooks['ticketcard']['phpunitextrahook'] = $extraHook;
			$hookmanager->hooksSorted['ticketcard']['1:phpunitextrahook'] = $extraHook;
		}
		$conf->modules_parts['hooks'] = array();

		$_POST = array_merge(array(
			'track_id' => $ticket->track_id,
			'message' => 'Ticket message hooks test',
			'send_email' => '1',
		), $post);
		$action = 'add_message';
		try {
			$return = $ticket->newMessage($user, $action, 1, 0);
		} finally {
			$_POST = array();
			$hookmanager = $savedHookManager;
			$conf->modules_parts['hooks'] = $savedHookParts;
			foreach ($savedConsts as $name => $value) {
				if ($value === null) {
					unset($conf->global->$name);
				} else {
					$conf->global->$name = $value;
				}
			}
		}

		return array('return' => $return, 'sent' => $sink->sent);
	}

	/**
	 * Create a message event with recipients on a ticket.
	 *
	 * @param	Ticket	$ticket	Ticket
	 * @return	int
	 */
	private function createTimelineEvent($ticket)
	{
		global $db, $user;

		$event = new ActionComm($db);
		$event->type_code = 'AC_OTH_AUTO';
		$event->code = 'TICKET_MSG_SENTBYMAIL';
		$event->label = 'Ticket message hooks test';
		$event->note_private = 'Ticket message hooks test body';
		$event->elementtype = 'ticket';
		$event->fk_element = $ticket->id;
		$event->socid = $ticket->fk_soc;
		$event->datep = dol_now();
		$event->percentage = -1;
		$event->userownerid = $user->id;
		$event->userassigned = array($user->id => array('id' => $user->id, 'transparency' => 0));
		$event->email_to = 'timeline-to@example.invalid';
		$actionId = $event->create($user, 1);
		$this->assertGreaterThan(0, $actionId, $event->error.implode(',', $event->errors));

		return $actionId;
	}

	/**
	 * Render the timeline of a ticket with no module hook, plus $hookObject when given.
	 *
	 * @param	Ticket	$ticket		Ticket
	 * @param	?object	$hookObject	Hook object
	 * @return	string
	 */
	private function renderTimeline($ticket, $hookObject = null)
	{
		global $conf, $langs, $db, $form, $hookmanager;

		$langs->load('ticket');
		$form = new Form($db);
		$savedHookManager = $hookmanager;
		$savedHookParts = $conf->modules_parts['hooks'];
		$hookmanager = new HookManager($db);
		if ($hookObject !== null) {
			$hookmanager->hooks['phpunit']['phpunittimeline'] = $hookObject;
			$hookmanager->hooksSorted['phpunit']['1:phpunittimeline'] = $hookObject;
		}
		$conf->modules_parts['hooks'] = array();
		try {
			return (string) show_actions_messaging($conf, $langs, $db, $ticket, null, 1, '', 'done', array('search_agenda_label' => ''));
		} finally {
			$hookmanager = $savedHookManager;
			$conf->modules_parts['hooks'] = $savedHookParts;
		}
	}

	/**
	 * Read the message event of a ticket.
	 *
	 * @param	int		$ticketId	Ticket id
	 * @return	object
	 */
	private function fetchMessageEvent($ticketId)
	{
		global $db;

		$sql = "SELECT code, email_to, email_tocc FROM ".$db->prefix()."actioncomm WHERE elementtype = 'ticket' AND fk_element = ".((int) $ticketId);
		$resql = $db->query($sql);
		$this->assertNotFalse($resql, (string) $db->lasterror());
		$this->assertSame(1, $db->num_rows($resql));
		$row = $db->fetch_object($resql);
		$db->free($resql);

		return $row;
	}

	/**
	 * Lowercase sorted email addresses of a recorded recipient list.
	 *
	 * @param	string	$recipients		"email" or "Name <email>" entries, comma-separated
	 * @return	string[]
	 */
	private function emailsOf($recipients)
	{
		$emails = array();
		foreach (explode(',', $recipients) as $entry) {
			$emails[] = strtolower(trim(preg_match('/<([^>]+)>/', $entry, $reg) ? $reg[1] : $entry));
		}
		sort($emails);

		return $emails;
	}

	/**
	 * Delete the fixture of createMessageFixture() and the events of its ticket.
	 *
	 * @param	array{thirdparty:Societe,ticket:Ticket,contact:Contact,internal:User}	$fixture	Fixture
	 * @return	void
	 */
	private function deleteMessageFixture($fixture)
	{
		global $db, $user;

		$sql = "SELECT id FROM ".$db->prefix()."actioncomm WHERE elementtype = 'ticket' AND fk_element = ".((int) $fixture['ticket']->id);
		$resql = $db->query($sql);
		$ids = array();
		while ($resql && ($obj = $db->fetch_object($resql))) {
			$ids[] = (int) $obj->id;
		}
		foreach ($ids as $id) {
			$event = new ActionComm($db);
			if ($event->fetch($id) > 0) {
				$event->delete($user, 1);
			}
		}
		// Linked-object triggers of local modules read $ticket->thirdparty.
		$fixture['ticket']->fetch_thirdparty();
		$fixture['ticket']->delete($user, 1);
		$fixture['contact']->delete($user, 1);
		$fixture['internal']->delete($user);
		$fixture['thirdparty']->delete($fixture['thirdparty']->id, $user, 0);
	}
	// @ATM-PATCH END backport-tests
}

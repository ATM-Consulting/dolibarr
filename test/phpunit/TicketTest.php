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
// @ATM-PATCH BEGIN ticket-mail-recipients-trace — tests of the private message recipients and of their trace
// grep: git grep '@ATM-PATCH' -- htdocs/ test/
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once dirname(__FILE__).'/../../htdocs/contact/class/contact.class.php';
require_once dirname(__FILE__).'/../../htdocs/societe/class/societe.class.php';
require_once dirname(__FILE__).'/../../htdocs/comm/action/class/actioncomm.class.php';
// @ATM-PATCH END ticket-mail-recipients-trace
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

	// @ATM-PATCH BEGIN ticket-mail-recipients-trace — tests of the private message recipients and of their trace
	// grep: git grep '@ATM-PATCH' -- htdocs/ test/
	/**
	 * testGetPostedMessageRecipientsPrivateDropsExternal
	 *
	 * @return	void
	 */
	public function testGetPostedMessageRecipientsPrivateDropsExternal()
	{
		global $db;

		$_POST = array('receiver_multiselect' => 1, 'receiver_private' => array('int@x.fr', 'client@y.fr'), 'sendto' => 'free@z.fr');

		$ticket = new Ticket($db);
		$result = $ticket->getPostedMessageRecipients(array('int@x.fr'));
		$_POST = array();

		$this->assertSame(array('int@x.fr', 'free@z.fr'), array_keys($result['to']));
	}

	/**
	 * testGetPostedMessageRecipientsDedupCc
	 *
	 * @return	void
	 */
	public function testGetPostedMessageRecipientsDedupCc()
	{
		global $db;

		$_POST = array('receiver_multiselect' => 1, 'receivercc_multiselect' => 1, 'receivercc' => array('A@x.fr'), 'sendtocc' => 'a@x.fr, b@x.fr');

		$ticket = new Ticket($db);
		$result = $ticket->getPostedMessageRecipients();
		$_POST = array();

		$this->assertSame('A@x.fr,b@x.fr', $result['cc']);
	}

	/**
	 * testRecordMessageRecipientsWritesColumns
	 *
	 * @return	void
	 */
	public function testRecordMessageRecipientsWritesColumns()
	{
		global $db, $user;

		$actionId = $this->createTicketMessageEventFixture();

		$ticket = new Ticket($db);
		$result = $ticket->recordMessageRecipients($actionId, array('a@x.fr' => 'Ann <a@x.fr>'), 'Carl <c@x.fr>');

		$event = new ActionComm($db);
		$event->fetch($actionId);
		$event->delete($user, 1);

		$this->assertSame(1, $result, (string) $ticket->error);
		$this->assertSame('a@x.fr', $event->email_to);
		$this->assertSame('c@x.fr', $event->email_tocc);
	}

	/**
	 * testRecordMessageRecipientsDedupUnion
	 *
	 * @return	void
	 */
	public function testRecordMessageRecipientsDedupUnion()
	{
		global $db, $user;

		$actionId = $this->createTicketMessageEventFixture();

		$ticket = new Ticket($db);
		$ticket->recordMessageRecipients($actionId, array('A@x.fr' => 'Ann <A@x.fr>', 'b@x.fr' => 'b@x.fr', 'a@x.fr' => 'a@x.fr'), 'c@x.fr,C@x.fr');

		$event = new ActionComm($db);
		$event->fetch($actionId);
		$event->delete($user, 1);

		$this->assertSame('A@x.fr, b@x.fr', $event->email_to);
		$this->assertSame('c@x.fr', $event->email_tocc);
	}

	/**
	 * testRecordMessageRecipientsKeepsWholeAddressesWithin255
	 *
	 * @return	void
	 */
	public function testRecordMessageRecipientsKeepsWholeAddressesWithin255()
	{
		global $db, $user;

		$actionId = $this->createTicketMessageEventFixture();

		$sendTo = array();
		for ($i = 10; $i < 40; $i++) {
			$sendTo['recipient'.$i.'@example.invalid'] = 'recipient'.$i.'@example.invalid';
		}
		$ticket = new Ticket($db);
		$result = $ticket->recordMessageRecipients($actionId, $sendTo, implode(',', array_keys($sendTo)));

		$event = new ActionComm($db);
		$event->fetch($actionId);
		$event->delete($user, 1);

		$this->assertSame(1, $result, (string) $ticket->error);
		foreach (array($event->email_to, $event->email_tocc) as $stored) {
			$this->assertLessThanOrEqual(255, strlen($stored));
			$this->assertStringStartsWith('recipient10@example.invalid, ', $stored);
			foreach (explode(', ', $stored) as $email) {
				$this->assertArrayHasKey($email, $sendTo);
			}
		}
	}

	/**
	 * testNewMessagePrivateWithoutRecipientSendsNothing
	 *
	 * @return	void
	 */
	public function testNewMessagePrivateWithoutRecipientSendsNothing()
	{
		global $langs;

		$fixture = $this->createTicketWithExternalContactFixture();
		$result = $this->postTicketMessage($fixture['ticket'], array(
			'private_message' => '1',
			'receiver_private_multiselect' => '1',
		));
		$row = $this->fetchTicketMessageEvent($fixture['ticket']->id);
		$this->deleteTicketFixture($fixture);

		$this->assertSame(1, $result['return']);
		$this->assertContains($langs->trans('ErrorMailRecipientIsEmptyForSendTicketMessage'), $result['events']['warnings'] ?? array());
		$this->assertSame(array(), $result['sent'], 'A mail send was attempted');
		$this->assertSame('TICKET_MSG_PRIVATE_SENTBYMAIL', $row->code);
		$this->assertEmpty($row->email_to);
	}

	/**
	 * testNewMessagePrivateDropsPostedExternalAddress
	 *
	 * @return	void
	 */
	public function testNewMessagePrivateDropsPostedExternalAddress()
	{
		global $langs;

		$fixture = $this->createTicketWithExternalContactFixture();
		$result = $this->postTicketMessage($fixture['ticket'], array(
			'private_message' => '1',
			'receiver_private_multiselect' => '1',
			'receiver_private' => array($fixture['externalEmail']),
		));
		$row = $this->fetchTicketMessageEvent($fixture['ticket']->id);
		$this->deleteTicketFixture($fixture);

		$this->assertContains($langs->trans('ErrorMailRecipientIsEmptyForSendTicketMessage'), $result['events']['warnings'] ?? array());
		$this->assertSame(array(), $result['sent'], 'The external address was mailed');
		$this->assertEmpty($row->email_to);
	}

	/**
	 * testNewMessagePublicRecordsUnionOfInternalAndExternalSends
	 *
	 * @return	void
	 */
	public function testNewMessagePublicRecordsUnionOfInternalAndExternalSends()
	{
		global $db, $user;

		$fixture = $this->createTicketWithExternalContactFixture();

		$internal = new User($db);
		$internal->login = 'pmrinternal'.uniqid();
		$internal->lastname = 'PrivateMailTestInternal';
		$internal->email = 'shared@example.invalid';
		$internalId = $internal->create($user, 1);
		$this->assertGreaterThan(0, $internalId, $internal->error.implode(',', $internal->errors));
		$this->assertGreaterThan(0, $fixture['ticket']->add_contact($internalId, 'SUPPORTTEC', 'internal', 1));

		$result = $this->postTicketMessage($fixture['ticket'], array(
			'private_message' => '0',
			'receiver_multiselect' => '1',
			'receiver' => array($fixture['externalEmail'], 'SHARED@example.invalid'),
		), 0);
		$row = $this->fetchTicketMessageEvent($fixture['ticket']->id);
		$this->deleteTicketFixture($fixture);
		$internal->delete($user);

		$this->assertCount(3, $result['sent'], json_encode($result['sent']));
		$this->assertSame('shared@example.invalid, '.$fixture['externalEmail'], $row->email_to);
	}

	/**
	 * Create a message event on a ticket with recipients written straight to the columns.
	 *
	 * @param	Ticket	$ticket	Ticket
	 * @param	string	$code	Event code
	 * @param	string	$to		email_to
	 * @param	string	$cc		email_tocc
	 * @return	int
	 */
	private function createTimelineMessageEvent($ticket, $code, $to, $cc = '')
	{
		global $db;

		$actionId = $this->createTicketMessageEventFixture();
		$sql = "UPDATE ".MAIN_DB_PREFIX."actioncomm SET code = '".$db->escape($code)."', elementtype = 'ticket', fk_element = ".((int) $ticket->id);
		$sql .= ", email_to = '".$db->escape($to)."', email_tocc = '".$db->escape($cc)."' WHERE id = ".((int) $actionId);
		$this->assertTrue((bool) $db->query($sql), (string) $db->lasterror());

		return $actionId;
	}

	/**
	 * Render the timeline of a ticket holding one event, then delete the fixtures.
	 *
	 * @param	string	$code		Event code
	 * @param	string	$to			email_to
	 * @param	string	$cc			email_tocc
	 * @param	int		$viewerSocid	socid of the viewing user
	 * @return	string
	 */
	private function renderTimelineWithMessage($code, $to, $cc = '', $viewerSocid = 0)
	{
		global $conf, $langs, $db, $user, $form;

		$langs->load('ticket');
		$form = new Form($db);
		$fixture = $this->createTicketWithExternalContactFixture();
		$actionId = 0;
		$previousSocid = $user->socid;
		try {
			$actionId = $this->createTimelineMessageEvent($fixture['ticket'], $code, $to, $cc);
			$user->socid = $viewerSocid;
			$out = show_actions_messaging($conf, $langs, $db, $fixture['ticket'], null, 1, '', 'done', array('search_agenda_label' => ''));
		} finally {
			$user->socid = $previousSocid;
			if ($actionId > 0) {
				$event = new ActionComm($db);
				$event->fetch($actionId);
				$event->delete($user, 1);
			}
			$this->deleteTicketFixture($fixture);
		}

		return $out;
	}

	/**
	 * testShowActionsMessagingPrintsRecipients
	 *
	 * @return	void
	 */
	public function testShowActionsMessagingPrintsRecipients()
	{
		global $langs;

		$out = $this->renderTimelineWithMessage('TICKET_MSG_PRIVATE_SENTBYMAIL', 'a@x.fr, b@x.fr', 'c@x.fr');

		$this->assertStringContainsString($langs->trans('TicketMessageSentTo'), $out);
		$this->assertStringContainsString('a@x.fr, b@x.fr', $out);
		$this->assertStringContainsString('c@x.fr', $out);
	}

	/**
	 * testShowActionsMessagingEscapesRecipients
	 *
	 * @return	void
	 */
	public function testShowActionsMessagingEscapesRecipients()
	{
		$out = $this->renderTimelineWithMessage('TICKET_MSG', '<b>x</b>@x.fr');

		$this->assertStringNotContainsString('<b>', $out);
		$this->assertStringContainsString('x@x.fr', $out);
	}

	/**
	 * testShowActionsMessagingIgnoresNonTicketEvent
	 *
	 * @return	void
	 */
	public function testShowActionsMessagingIgnoresNonTicketEvent()
	{
		global $langs;

		$out = $this->renderTimelineWithMessage('AC_EMAIL', 'a@x.fr');

		$this->assertStringContainsString('Ticket message recipients test', $out);
		$this->assertStringNotContainsString($langs->trans('TicketMessageSentTo'), $out);
	}

	/**
	 * testShowActionsMessagingHidesRecipientsFromExternalUser
	 *
	 * @return	void
	 */
	public function testShowActionsMessagingHidesRecipientsFromExternalUser()
	{
		global $langs;

		$out = $this->renderTimelineWithMessage('TICKET_MSG_PRIVATE_SENTBYMAIL', 'a@x.fr', '', 1);

		$this->assertStringContainsString('Ticket message recipients test', $out);
		$this->assertStringNotContainsString($langs->trans('TicketMessageSentTo'), $out);
	}

	/**
	 * Create a ticket message agenda event, the shape newMessage() records recipients on.
	 *
	 * @return	int
	 */
	private function createTicketMessageEventFixture()
	{
		global $db, $user;

		$event = new ActionComm($db);
		$event->type_code = 'AC_OTH_AUTO';
		$event->code = 'TICKET_MSG_PRIVATE_SENTBYMAIL';
		$event->label = 'Ticket message recipients test';
		$event->elementtype = 'ticket';
		$event->fk_element = 0;
		$event->datep = dol_now();
		$event->percentage = -1;
		$event->userownerid = $user->id;
		$event->userassigned = array($user->id => array('id' => $user->id, 'transparency' => 0));
		$actionId = $event->create($user, 1);
		$this->assertGreaterThan(0, $actionId, $event->error.implode(',', $event->errors));

		return $actionId;
	}

	/**
	 * Create a thirdparty, a ticket on it and an external contact of the ticket with an email.
	 *
	 * @return	array{thirdparty:Societe,ticket:Ticket,contact:Contact,externalEmail:string}
	 */
	private function createTicketWithExternalContactFixture()
	{
		global $db, $user;

		// A thirdparty is required: local ticket triggers read $ticket->thirdparty->name.
		$thirdparty = new Societe($db);
		$thirdparty->name = 'PrivateMailTestThirdparty';
		$thirdpartyId = $thirdparty->create($user, 1);
		$this->assertGreaterThan(0, $thirdpartyId, $thirdparty->error.implode(',', $thirdparty->errors));

		$ticket = new Ticket($db);
		$ticket->initAsSpecimen();
		$ticket->ref = 'TIPMR-'.uniqid();
		$ticket->track_id = uniqid('pmr');
		$ticket->fk_soc = $thirdpartyId;
		$ticket->fk_user_assign = 0;
		$ticketId = $ticket->create($user, 1);
		$this->assertGreaterThan(0, $ticketId, $ticket->error.implode(',', $ticket->errors));
		$ticket->fetch($ticketId);
		$ticket->fetch_thirdparty();

		$contact = new Contact($db);
		$contact->lastname = 'PrivateMailTestExternal';
		$contact->email = 'external-private-test@example.invalid';
		$contactId = $contact->create($user, 1);
		$this->assertGreaterThan(0, $contactId, $contact->error.implode(',', $contact->errors));
		$this->assertGreaterThan(0, $ticket->add_contact($contactId, 'SUPPORTCLI', 'external', 1));

		return array('thirdparty' => $thirdparty, 'ticket' => $ticket, 'contact' => $contact, 'externalEmail' => $contact->email);
	}

	/**
	 * Post a back-office message by email, with a hook standing in for the mail transport.
	 *
	 * @param	Ticket				$ticket		Ticket
	 * @param	array<string,mixed>	$post		POST fields added to the message ones
	 * @param	int					$private	$private argument of newMessage()
	 * @return	array{return:int,events:array<string,string[]>,sent:array<array{to:string,cc:string}>}
	 */
	private function postTicketMessage($ticket, $post, $private = 1)
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

		// Only the sink may answer the 'mail' hook, so sendfile() returns true without sending anything.
		$savedHookManager = $hookmanager;
		$savedHookParts = $conf->modules_parts['hooks'];
		$savedDisableMails = getDolGlobalString('MAIN_DISABLE_ALL_MAILS');
		$savedFrom = getDolGlobalString('TICKET_NOTIFICATION_EMAIL_FROM');
		$hookmanager = new HookManager($ticket->db);
		$hookmanager->hooks['mail']['phpunitmailsink'] = $sink;
		$hookmanager->hooksSorted['mail']['1:phpunitmailsink'] = $sink;
		$conf->modules_parts['hooks'] = array();
		$conf->global->MAIN_DISABLE_ALL_MAILS = 0;
		$conf->global->TICKET_NOTIFICATION_EMAIL_FROM = 'from@example.invalid';

		$_SESSION['dol_events'] = array();
		$_POST = array_merge(array(
			'track_id' => $ticket->track_id,
			'message' => 'Message body',
			'send_email' => '1',
			'sendto' => '',
		), $post);
		$action = 'add_message';
		try {
			$return = $ticket->newMessage($user, $action, $private, 0);
		} finally {
			$_POST = array();
			$hookmanager = $savedHookManager;
			$conf->modules_parts['hooks'] = $savedHookParts;
			$conf->global->MAIN_DISABLE_ALL_MAILS = $savedDisableMails;
			$conf->global->TICKET_NOTIFICATION_EMAIL_FROM = $savedFrom;
		}

		return array('return' => $return, 'events' => $_SESSION['dol_events'], 'sent' => $sink->sent);
	}

	/**
	 * Read the message event of a ticket.
	 *
	 * @param	int		$ticketId	Ticket id
	 * @return	object
	 */
	private function fetchTicketMessageEvent($ticketId)
	{
		global $db;

		$sql = "SELECT code, email_to FROM ".MAIN_DB_PREFIX."actioncomm WHERE elementtype = 'ticket' AND fk_element = ".((int) $ticketId);
		$resql = $db->query($sql);
		$row = $db->fetch_object($resql);
		$db->free($resql);
		$this->assertIsObject($row);

		return $row;
	}

	/**
	 * Delete the fixture of createTicketWithExternalContactFixture().
	 *
	 * @param	array{thirdparty:Societe,ticket:Ticket,contact:Contact,externalEmail:string}	$fixture	Fixture
	 * @return	void
	 */
	private function deleteTicketFixture($fixture)
	{
		global $user;

		$fixture['ticket']->fetch_thirdparty();
		$fixture['ticket']->delete($user, 1);
		$fixture['contact']->delete($user, 1);
		$fixture['thirdparty']->delete($fixture['thirdparty']->id, $user, 0);
	}
	// @ATM-PATCH END ticket-mail-recipients-trace
}

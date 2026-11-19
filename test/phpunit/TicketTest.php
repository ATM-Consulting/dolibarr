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
require_once dirname(__FILE__).'/../../htdocs/contact/class/contact.class.php';
require_once dirname(__FILE__).'/../../htdocs/societe/class/societe.class.php';
require_once dirname(__FILE__).'/../../htdocs/comm/action/class/actioncomm.class.php';
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
		$result = $ticket->recordMessageRecipients($actionId, array('a@x.fr' => 'Ann <a@x.fr>'), 'c@x.fr');

		$event = new ActionComm($db);
		$event->fetch($actionId);
		$event->delete($user, 1);

		$this->assertSame(1, $result, (string) $ticket->error);
		$this->assertSame('Ann <a@x.fr>', $event->email_to);
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

		// Internal and external sends merged: each address must be recorded once, whatever its case.
		$ticket = new Ticket($db);
		$ticket->recordMessageRecipients($actionId, array('A@x.fr' => 'Ann <A@x.fr>', 'b@x.fr' => 'b@x.fr', 'a@x.fr' => 'a@x.fr'), 'c@x.fr,C@x.fr');

		$event = new ActionComm($db);
		$event->fetch($actionId);
		$event->delete($user, 1);

		$this->assertSame('Ann <A@x.fr>, b@x.fr', $event->email_to);
		$this->assertSame('c@x.fr', $event->email_tocc);
	}

	/**
	 * testNewMessagePrivateWithoutRecipientSendsNothing
	 *
	 * @return	void
	 */
	public function testNewMessagePrivateWithoutRecipientSendsNothing()
	{
		global $db, $user, $langs;

		$langs->load('ticket');

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

		$contact = new Contact($db);
		$contact->lastname = 'PrivateMailTestExternal';
		$contact->email = 'external-private-test@example.com';
		$contactId = $contact->create($user, 1);
		$this->assertGreaterThan(0, $contactId, $contact->error.implode(',', $contact->errors));
		$this->assertGreaterThan(0, $ticket->add_contact($contactId, 'SUPPORTCLI', 'external', 1));

		$_SESSION['dol_events'] = array();
		$_POST = array(
			'track_id' => $ticket->track_id,
			'message' => 'Private note',
			'private_message' => '1',
			'send_email' => '1',
			'receiver_multiselect' => '1',
			'receiver_private_multiselect' => '1',
			'sendto' => '',
		);
		$action = 'add_message';
		$result = $ticket->newMessage($user, $action, 1, 0);
		$_POST = array();
		$events = $_SESSION['dol_events'];

		$sql = "SELECT code, email_to FROM ".MAIN_DB_PREFIX."actioncomm WHERE elementtype = 'ticket' AND fk_element = ".((int) $ticketId);
		$resql = $db->query($sql);
		$row = $db->fetch_object($resql);
		$db->free($resql);

		$ticket->fetch_thirdparty();
		$ticket->delete($user, 1);
		$contact->delete($user, 1);
		$thirdparty->delete($thirdpartyId, $user, 0);

		$this->assertSame(1, $result);
		$this->assertContains($langs->trans('ErrorMailRecipientIsEmptyForSendTicketMessage'), $events['warnings'] ?? array());
		$this->assertEmpty($events['errors'] ?? array(), 'A mail send was attempted: '.json_encode($events['errors'] ?? array()));
		$this->assertSame('TICKET_MSG_PRIVATE_SENTBYMAIL', $row->code);
		$this->assertEmpty($row->email_to);
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
}

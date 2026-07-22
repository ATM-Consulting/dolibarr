<?php
/* Copyright (C) 2016   Jean-François Ferry     <hello@librethic.io>
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

 use Luracast\Restler\RestException;

require_once DOL_DOCUMENT_ROOT.'/ticket/class/ticket.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/ticket.lib.php';


/**
 * API class for ticket object
 *
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class Tickets extends DolibarrApi
{
	/**
	 * @var array   $FIELDS     Mandatory fields, checked when create and update object
	 */
	public static $FIELDS = array(
		'subject',
		'message'
	);

	/**
	 * @var array   $FIELDS_MESSAGES     Mandatory fields, checked when create and update object
	 */
	public static $FIELDS_MESSAGES = array(
		'track_id',
		'message'
	);

	/**
	 * @var Ticket $ticket {@type Ticket}
	 */
	public $ticket;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $db;
		$this->db = $db;
		$this->ticket = new Ticket($this->db);
	}

	/**
	 * Get properties of a Ticket object.
	 *
	 * Return an array with ticket informations
	 *
	 * @param	int 			$id 		ID of ticket
	 * @param int $contact_list 0: Returned array of contacts/addresses contains all properties, 1: Return array contains just id, -1: Do not return contacts/adddesses
	 * @return 	array|mixed 				Data without useless information
	 *
	 * @throws RestException 401
	 * @throws RestException 403
	 * @throws RestException 404
	 */
	// Backport v24 - b732fd2e
	public function get($id, $contact_list = 1)
	{
		// Backport v24 - b732fd2e
		return $this->getCommon($id, '', '', $contact_list);
	}

	/**
	 * Get properties of a Ticket object from track id
	 *
	 * Return an array with ticket informations
	 *
	 * @param	string  		$track_id 	Tracking ID of ticket
	 * @param   int         	$contact_list	0: Returned array of contacts/addresses contains all properties, 1: Return array contains just id, -1: Do not return contacts/adddesses
	 * @return 	array|mixed 				Data without useless information
	 *
	 * @url GET track_id/{track_id}
	 *
	 * @throws RestException 	401
	 * @throws RestException 	403
	 * @throws RestException 	404
	 */
	// Backport v24 - b732fd2e
	public function getByTrackId($track_id, $contact_list = 1)
	{
		// Backport v24 - b732fd2e
		return $this->getCommon(0, $track_id, '', $contact_list);
	}

	/**
	 * Get properties of a Ticket object from ref
	 *
	 * Return an array with ticket informations
	 *
	 * @param	string  		$ref    	Reference for ticket
	 * @param   int         	$contact_list	0: Returned array of contacts/addresses contains all properties, 1: Return array contains just id, -1: Do not return contacts/adddesses
	 * @return 	array|mixed 				Data without useless information
	 *
	 * @url GET ref/{ref}
	 *
	 * @throws RestException 401
	 * @throws RestException 403
	 * @throws RestException 404
	 */
	// Backport v24 - b732fd2e
	public function getByRef($ref, $contact_list = 1)
	{
		try {
			// Backport v24 - b732fd2e
			return $this->getCommon(0, '', $ref, $contact_list);
		} catch (Exception $e) {
			   throw $e;
		}
	}

	/**
	 * Get properties of a Ticket object
	 * Return an array with ticket informations
	 *
	 * @param	int 			$id 		ID of ticket
	 * @param	string  		$track_id 	Tracking ID of ticket
	 * @param	string  		$ref    	Reference for ticket
	 * @param   int         	$contact_list	0: Returned array of contacts/addresses contains all properties, 1: Return array contains just id, -1: Do not return contacts/adddesses
	 * @return 	array|mixed 				Data without useless information
	 */
	// Backport v24 - b732fd2e
	private function getCommon($id = 0, $track_id = '', $ref = '', $contact_list = 1)
	{
		if (!DolibarrApiAccess::$user->rights->ticket->read) {
			throw new RestException(403);
		}

		// Check parameters
		if (($id < 0) && !$track_id && !$ref) {
			throw new RestException(401, 'Wrong parameters');
		}
		if ($id == 0) {
			$result = $this->ticket->initAsSpecimen();
		} else {
			$result = $this->ticket->fetch($id, $ref, $track_id);
		}
		if (!$result) {
			throw new RestException(404, 'Ticket not found');
		}

		// String for user assigned
		if ($this->ticket->fk_user_assign > 0) {
			$userStatic = new User($this->db);
			$userStatic->fetch($this->ticket->fk_user_assign);
			$this->ticket->fk_user_assign_string = $userStatic->firstname.' '.$userStatic->lastname;
		}

		// Messages of ticket
		$messages = array();
		$this->ticket->loadCacheMsgsTicket();
		if (is_array($this->ticket->cache_msgs_ticket) && count($this->ticket->cache_msgs_ticket) > 0) {
			$num = count($this->ticket->cache_msgs_ticket);
			$i = 0;
			while ($i < $num) {
				if ($this->ticket->cache_msgs_ticket[$i]['fk_user_author'] > 0) {
					$user_action = new User($this->db);
					$user_action->fetch($this->ticket->cache_msgs_ticket[$i]['fk_user_author']);
				}

				// Now define messages
				$messages[] = array(
				'id' => $this->ticket->cache_msgs_ticket[$i]['id'],
				'fk_user_action' => $this->ticket->cache_msgs_ticket[$i]['fk_user_author'],
				'fk_user_action_socid' =>  $user_action->socid,
				'fk_user_action_string' => dolGetFirstLastname($user_action->firstname, $user_action->lastname),
				'message' => $this->ticket->cache_msgs_ticket[$i]['message'],
				'datec' => $this->ticket->cache_msgs_ticket[$i]['datec'],
				'private' => $this->ticket->cache_msgs_ticket[$i]['private']
				);
				$i++;
			}
			$this->ticket->messages = $messages;
		}

		if (!DolibarrApi::_checkAccessToResource('ticket', $this->ticket->id)) {
			throw new RestException(401, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		// Backport v24 START - b732fd2e
		if ($contact_list > -1) {
			// Add external contacts ids
			$tmparray = $this->ticket->liste_contact(-1, 'external', $contact_list);
			if (is_array($tmparray)) {
				$this->ticket->contacts_ids = $tmparray;
			}
			$tmparray = $this->ticket->liste_contact(-1, 'internal', $contact_list);
			if (is_array($tmparray)) {
				$this->ticket->contacts_ids_internal = $tmparray;
			}
		}
		// Backport v24 END - b732fd2e

		return $this->_cleanObjectDatas($this->ticket);
	}

	/**
	 * List tickets
	 *
	 * Get a list of tickets
	 *
	 * @param int       $socid      Filter list with thirdparty ID
	 * @param string	$sortfield	Sort field
	 * @param string	$sortorder	Sort order
	 * @param int		$limit		Limit for list
	 * @param int		$page		Page number
	 * @param string	$sqlfilters Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101') and (t.fk_statut:=:1)"
	 * @param int		$loadcontacts		Load also contacts/addresses (0=No, 1=Yes)
	 *
	 * @return array Array of ticket objects
	 *
	 */
	// Backport v24 - b732fd2e
	public function index($socid = 0, $sortfield = "t.rowid", $sortorder = "ASC", $limit = 100, $page = 0, $sqlfilters = '', $loadcontacts = 0)
	{
		global $db, $conf;

		if (!DolibarrApiAccess::$user->rights->ticket->read) {
			throw new RestException(403);
		}

		$obj_ret = array();

		if (!$socid && DolibarrApiAccess::$user->socid) {
			$socid = DolibarrApiAccess::$user->socid;
		}

		$search_sale = null;
		// If the internal user must only see his customers, force searching by him
		$search_sale = 0;
		if (!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) {
			$search_sale = DolibarrApiAccess::$user->id;
		}

		$sql = "SELECT t.rowid";
		if ((!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) {
			$sql .= ", sc.fk_soc, sc.fk_user"; // We need these fields in order to filter by sale (including the case where the user can only see his prospects)
		}
		$sql .= " FROM ".MAIN_DB_PREFIX."ticket AS t LEFT JOIN ".MAIN_DB_PREFIX."ticket_extrafields AS ef ON (ef.fk_object = t.rowid)"; // Modification VMR Global Solutions to include extrafields as search parameters in the API GET call, so we will be able to filter on extrafields

		if ((!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) {
			$sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc"; // We need this table joined to the select in order to filter by sale
		}

		$sql .= ' WHERE t.entity IN ('.getEntity('ticket', 1).')';
		if ((!DolibarrApiAccess::$user->rights->societe->client->voir && !$socid) || $search_sale > 0) {
			$sql .= " AND t.fk_soc = sc.fk_soc";
		}
		if ($socid > 0) {
			$sql .= " AND t.fk_soc = ".((int) $socid);
		}
		if ($search_sale > 0) {
			$sql .= " AND t.rowid = sc.fk_soc"; // Join for the needed table to filter by sale
		}

		// Insert sale filter
		if ($search_sale > 0) {
			$sql .= " AND sc.fk_user = ".((int) $search_sale);
		}
		// Add sql filters
		if ($sqlfilters) {
			$errormessage = '';
			$sql .= forgeSQLFromUniversalSearchCriteria($sqlfilters, $errormessage);
			if ($errormessage) {
				throw new RestException(400, 'Error when validating parameter sqlfilters -> '.$errormessage);
			}
		}

		$sql .= $this->db->order($sortfield, $sortorder);

		if ($limit) {
			if ($page < 0) {
				$page = 0;
			}
			$offset = $limit * $page;

			$sql .= $this->db->plimit($limit, $offset);
		}

		$result = $this->db->query($sql);
		if ($result) {
			$num = $this->db->num_rows($result);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($result);
				$ticket_static = new Ticket($this->db);
				if ($ticket_static->fetch($obj->rowid)) {
					if ($ticket_static->fk_user_assign > 0) {
						$userStatic = new User($this->db);
						$userStatic->fetch($ticket_static->fk_user_assign);
						$ticket_static->fk_user_assign_string = $userStatic->firstname.' '.$userStatic->lastname;
					}

					// Backport v24 START - b732fd2e
					if ($loadcontacts) {
						// Add external contacts ids
						$tmparray = $ticket_static->liste_contact(-1, 'external', 1);
						if (is_array($tmparray)) {
							$ticket_static->contacts_ids = $tmparray;
						}
						$tmparray = $ticket_static->liste_contact(-1, 'internal', 1);
						if (is_array($tmparray)) {
							$ticket_static->contacts_ids_internal = $tmparray;
						}
					}
					// Backport v24 END - b732fd2e

					$obj_ret[] = $this->_cleanObjectDatas($ticket_static);
				}
				$i++;
			}
		} else {
			throw new RestException(503, 'Error when retrieve ticket list');
		}
		if (!count($obj_ret)) {
			throw new RestException(404, 'No ticket found');
		}
			return $obj_ret;
	}

	/**
	 * Create ticket object
	 *
	 * Optional key "contacts": an array of objects to link on creation, each:
	 *   { "id": int (or "contactid"), "type": string (contact type code), "source": "external"|"internal" (default external) }
	 * The whole creation is atomic: if any contact is invalid, the ticket is not created (rollback + error).
	 * (Backport PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062)
	 *
	 * @param array $request_data   Request datas
	 * @phan-param ?array<string,mixed> $request_data
	 * @phpstan-param ?array<string,mixed> $request_data
	 * @return int  ID of ticket
	 */
	public function post($request_data = null)
	{
		$ticketstatic = new Ticket($this->db);
		if (!DolibarrApiAccess::$user->rights->ticket->write) {
			throw new RestException(401);
		}
		// Check mandatory fields
		$result = $this->_validate($request_data);

		/*
		* START BACKPORT Develop v24 -> PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062
		*/
		// Extract contacts to link after ticket creation (not a Ticket property to assign in the loop below)
		$contacts = array();
		if (isset($request_data['contacts']) && is_array($request_data['contacts'])) {
			$contacts = $request_data['contacts'];
		}
		unset($request_data['contacts']);
		/*
		* END BACKPORT Develop v24 -> PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062
		*/

		foreach ($request_data as $field => $value) {
			$this->ticket->$field = $value;
		}
		if (empty($this->ticket->ref)) {
			$this->ticket->ref = $ticketstatic->getDefaultRef();
		}
		if (empty($this->ticket->track_id)) {
			$this->ticket->track_id = generate_random_id(16);
		}

		/*
		* START BACKPORT Develop v24 -> PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062
		*/
		// Pre-validate contacts (format + type) BEFORE creating the ticket, so a malformed payload
		// does not create a ticket (and fire its creation triggers) only to roll it back afterwards.
		$contactsToLink = array();
		if (!empty($contacts)) {
			foreach ($contacts as $contact) {
				$contactsToLink[] = $this->_normalizeAndValidateContact($contact);
			}
		}

		$this->db->begin();

		/* SPÉ KALI - create the ticket without firing TICKET_CREATE (notrigger=1) so the payload contacts are linked before the creation notification is sent */
		if ($this->ticket->create(DolibarrApiAccess::$user, 1) < 0) {
			$this->db->rollback();
			throw new RestException(500, "Error creating ticket", array_merge(array($this->ticket->error), $this->ticket->errors));
		}
		/* SPÉ KALI - end */

		if (!empty($contactsToLink)) {
			foreach ($contactsToLink as $contact) {
				$result = $this->ticket->add_contact($contact['id'], $contact['type'], $contact['source']);
				// Rolls back the transaction and throws a RestException on any failure
				$this->_addContactResultOrThrow($result, $contact['id'], $contact['type'], $contact['source']);
			}
		}

		/* SPÉ KALI - contacts are now linked: fire TICKET_CREATE so the creation notification reaches the ticket contacts */
		if ($this->ticket->call_trigger('TICKET_CREATE', DolibarrApiAccess::$user) < 0) {
			$this->db->rollback();
			throw new RestException(500, "Error creating ticket", array_merge(array($this->ticket->error), $this->ticket->errors));
		}
		/* SPÉ KALI - end */

		$this->db->commit();
		/*
		* END BACKPORT Develop v24 -> PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062
		*/

		return $this->ticket->id;
	}

	/**
	 * Create ticket object
	 *
	 * @param array $request_data   Request datas
	 * @return int  ID of ticket
	 *
	 */
	public function postNewMessage($request_data = null)
	{
		$ticketstatic = new Ticket($this->db);
		if (!DolibarrApiAccess::$user->rights->ticket->write) {
			throw new RestException(401);
		}
		// Check mandatory fields
		$result = $this->_validateMessage($request_data);

		foreach ($request_data as $field => $value) {
			$this->ticket->$field = $value;
		}
		$ticketMessageText = $this->ticket->message;
		$result = $this->ticket->fetch('', '', $this->ticket->track_id);
		if (!$result) {
			throw new RestException(404, 'Ticket not found');
		}
		$this->ticket->message = $ticketMessageText;
		if (!$this->ticket->createTicketMessage(DolibarrApiAccess::$user)) {
			throw new RestException(500, 'Error when creating ticket');
		}
		return $this->ticket->id;
	}

	/**
	 * Update ticket
	 *
	 * @param int   $id             Id of ticket to update
	 * @param array $request_data   Datas
	 * @return int
	 *
	 */
	public function put($id, $request_data = null)
	{
		if (!DolibarrApiAccess::$user->rights->ticket->write) {
			throw new RestException(401);
		}

		$result = $this->ticket->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Ticket not found');
		}

		if (!DolibarrApi::_checkAccessToResource('ticket', $this->ticket->id)) {
			throw new RestException(401, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		foreach ($request_data as $field => $value) {
			if ($field == 'id') {
				continue;
			}
			if ($field == 'array_options' && is_array($value)) {
				foreach ($value as $index => $val) {
					$this->ticket->array_options[$index] = $this->_checkValForAPI($field, $val, $this->ticket);
				}
				continue;
			}
			$this->ticket->$field = $value;
		}

		if ($this->ticket->update($id, DolibarrApiAccess::$user)) {
			return $this->get($id);
		}

		return false;
	}

	/**
	 * Delete ticket
	 *
	 * @param   int     $id   Ticket ID
	 * @return  array
	 *
	 */
	public function delete($id)
	{
		if (!DolibarrApiAccess::$user->rights->ticket->delete) {
			throw new RestException(401);
		}
		$result = $this->ticket->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Ticket not found');
		}

		if (!DolibarrApi::_checkAccessToResource('ticket', $this->ticket->id)) {
			throw new RestException(401, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		if (!$this->ticket->delete($id)) {
			throw new RestException(500, 'Error when deleting ticket');
		}

		return array(
			'success' => array(
				'code' => 200,
				'message' => 'Ticket deleted'
			)
		);
	}

	/**
	 * Validate fields before create or update object
	 *
	 * @param array $data   Data to validate
	 * @return array
	 *
	 * @throws RestException
	 */
	private function _validate($data)
	{
		$ticket = array();
		foreach (Tickets::$FIELDS as $field) {
			if (!isset($data[$field])) {
				throw new RestException(400, "$field field missing");
			}
			$ticket[$field] = $data[$field];
		}
		return $ticket;
	}

	/**
	 * Validate fields before create or update object message
	 *
	 * @param array $data   Data to validate
	 * @return array
	 *
	 * @throws RestException
	 */
	private function _validateMessage($data)
	{
		$ticket = array();
		foreach (Tickets::$FIELDS_MESSAGES as $field) {
			if (!isset($data[$field])) {
				throw new RestException(400, "$field field missing");
			}
			$ticket[$field] = $data[$field];
		}
		return $ticket;
	}

	/*
	* START BACKPORT Develop v24 -> PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062
	*/
	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Check that an active contact type (code + source) exists for the ticket element.
	 *
	 * @param	string	$type	Contact type code (c_type_contact.code)
	 * @param	string	$source	'internal' or 'external'
	 * @return	bool			True if the active type exists for element 'ticket'
	 */
	private function _ticketContactTypeExists(string $type, string $source): bool
	{
		// phpcs:enable
		$sql = "SELECT tc.rowid";
		$sql .= " FROM ".$this->db->prefix()."c_type_contact as tc";
		$sql .= " WHERE tc.element = 'ticket'";
		$sql .= " AND tc.source = '".$this->db->escape($source)."'";
		$sql .= " AND tc.code = '".$this->db->escape($type)."'";
		$sql .= " AND tc.active = 1";

		$resql = $this->db->query($sql);
		if (!$resql) {
			return false;
		}
		$exists = ($this->db->num_rows($resql) > 0);
		$this->db->free($resql);

		return $exists;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Validate and normalize one contact entry from the API payload.
	 * Called before any DB write, so it throws without rolling back.
	 *
	 * @param	mixed	$contact	Associative array {id|contactid:int, type:string, source?:string}
	 * @phan-param	array{id?:int,contactid?:int,type?:string,source?:string}|mixed	$contact
	 * @return	array				Normalized contact array{id:int,type:string,source:string}
	 * @phan-return	array{id:int,type:string,source:string}
	 * @throws	RestException
	 */
	private function _normalizeAndValidateContact($contact): array
	{
		// phpcs:enable
		if (!is_array($contact)) {
			throw new RestException(400, 'Each element of "contacts" must be an object with id, type and optional source');
		}

		$contactid = 0;
		if (isset($contact['id'])) {
			$contactid = (int) $contact['id'];
		} elseif (isset($contact['contactid'])) {
			$contactid = (int) $contact['contactid'];
		}
		$type = isset($contact['type']) ? trim((string) $contact['type']) : '';
		$source = isset($contact['source']) ? (string) $contact['source'] : 'external';

		if ($contactid <= 0) {
			throw new RestException(400, 'Contact "id" is required and must be a positive integer');
		}
		if ($type === '') {
			throw new RestException(400, 'Contact "type" (contact type code) is required');
		}
		if ($source !== 'internal' && $source !== 'external') {
			throw new RestException(400, 'Contact "source" must be "internal" or "external"');
		}
		// Validate the type upfront so add_contact()'s ambiguous "0" return can only mean "already linked"
		if (!$this->_ticketContactTypeExists($type, $source)) {
			throw new RestException(400, 'Contact type "'.$type.'" not found or inactive for source "'.$source.'"');
		}

		return array('id' => $contactid, 'type' => $type, 'source' => $source);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Map the return code of Ticket::add_contact() to an API response.
	 * Assumes a DB transaction is open: rolls it back and throws on any failure.
	 *
	 * @param	int		$result		Return code of add_contact()
	 * @param	int		$contactid	Contact id that was linked
	 * @param	string	$type		Contact type code
	 * @param	string	$source		'internal' or 'external'
	 * @return	void
	 * @throws	RestException
	 */
	private function _addContactResultOrThrow(int $result, int $contactid, string $type, string $source): void
	{
		// phpcs:enable
		if ($result > 0) {
			return;
		}

		$this->db->rollback();
		if ($result == 0) {
			// Type was validated upfront, so the only remaining 0 case is "already linked"
			throw new RestException(409, 'Contact id='.$contactid.' is already linked to the ticket as source='.$source.' type='.$type);
		} elseif ($result == -1) {
			throw new RestException(404, 'Contact id='.$contactid.' not found');
		} elseif ($result == -3 || $result == -4) {
			throw new RestException(403, 'Not allowed to link contact id='.$contactid);
		}
		throw new RestException(500, 'Error linking contact id='.$contactid, array_merge(array($this->ticket->error), $this->ticket->errors));
	}
	/*
	* END BACKPORT Develop v24 -> PR #39062 - https://github.com/Dolibarr/dolibarr/pull/39062
	*/

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Clean sensible object datas
	 *
	 * @param   Object  $object     Object to clean
	 * @return  Object              Object with cleaned properties
	 *
	 * @todo use an array for properties to clean
	 *
	 */
	protected function _cleanObjectDatas($object)
	{
		// phpcs:enable
		$object = parent::_cleanObjectDatas($object);

		// Other attributes to clean
		$attr2clean = array(
			"contact",
			"contact_id",
			"ref_previous",
			"ref_next",
			"ref_ext",
			"table_element_line",
			"statut",
			"country",
			"country_id",
			"country_code",
			"barcode_type",
			"barcode_type_code",
			"barcode_type_label",
			"barcode_type_coder",
			"mode_reglement_id",
			"cond_reglement_id",
			"cond_reglement",
			"fk_delivery_address",
			"shipping_method_id",
			"modelpdf",
			"fk_account",
			"note_public",
			"note_private",
			"note",
			"total_ht",
			"total_tva",
			"total_localtax1",
			"total_localtax2",
			"total_ttc",
			"fk_incoterms",
			"label_incoterms",
			"location_incoterms",
			"name",
			"lastname",
			"firstname",
			"civility_id",
			"canvas",
			"cache_msgs_ticket",
			"cache_logs_ticket",
			"cache_types_tickets",
			"cache_category_tickets",
			"regeximgext",
			"statuts_short",
			"statuts"
		);
		foreach ($attr2clean as $toclean) {
			unset($object->$toclean);
		}

		// If object has lines, remove $db property
		if (isset($object->lines) && count($object->lines) > 0) {
			$nboflines = count($object->lines);
			for ($i = 0; $i < $nboflines; $i++) {
				$this->_cleanObjectDatas($object->lines[$i]);
			}
		}

		// If object has linked objects, remove $db property
		if (isset($object->linkedObjects) && count($object->linkedObjects) > 0) {
			foreach ($object->linkedObjects as $type_object => $linked_object) {
				foreach ($linked_object as $object2clean) {
					$this->_cleanObjectDatas($object2clean);
				}
			}
		}
		return $object;
	}

	/*
	* START BACKPORT Develop v24 -> PR #37277 - https://github.com/Dolibarr/dolibarr/pull/37277
	*/
	/**
	 * Add a contact type of given ticket
	 *
	 * @param int    $id            Id of ticket to update
	 * @param int    $contactid     Id of contact to add
	 * @param string $type          Type (code in dictionary) of the contact (BILLING, SHIPPING, CUSTOMER + possibly your own)
	 * @param string $source		internal=Contact intern (llx_user), external=Contact extern (llx_socpeople)
	 * @param int    $notrigger		0=Enable all triggers (default), 1=Disable all triggers
	 * @return array
	 * @phan-return array{success:array{code:int,message:string}}
	 * @phpstan-return array{success:array{code:int,message:string}}
	 *
	 * @url	POST {id}/contact/{contactid}/{type}
	 *
	 * @throws RestException 400
	 * @throws RestException 401
	 * @throws RestException 403
	 * @throws RestException 404
	 * @throws RestException 503
	 */
	public function postContact(int $id, int $contactid, string $type, string $source = "external", int $notrigger = 0): array
	{
		// Check permissions
		if (!DolibarrApiAccess::$user->hasRight('ticket', 'write')) {
			throw new RestException(403);
		}

		// test source
		if (empty($source)) {
			throw new RestException(400, 'Source can not be empty');
		}
		// test type
		if (empty($type)) {
			throw new RestException(400, 'type can not be empty');
		}

		// Check type/source contact exists
		$sqlCheckTypeSource = "SELECT tc.rowid";
		$sqlCheckTypeSource .= " FROM ".$this->db->prefix()."c_type_contact as tc";
		$sqlCheckTypeSource .= " WHERE tc.element LIKE 'ticket'";
		$sqlCheckTypeSource .= " AND tc.source = '".$this->db->escape($source)."'";
		$sqlCheckTypeSource .= " AND tc.code = '".$this->db->escape($type)."'";
		$sqlCheckTypeSource .= " AND tc.active = 1";
		$result = $this->db->query($sqlCheckTypeSource);

		if ($result && $this->db->num_rows($result) == 0) {
			throw new RestException(400, 'Contact type not found');
		}

		// Check contact exists
		if ($source == "external") {
			// Check external contact exists
			$sqlCheckExternalContact = "SELECT 1 as exist";
			$sqlCheckExternalContact .= " FROM llx_socpeople";
			$sqlCheckExternalContact .= " WHERE rowid = " . intval($contactid);
			$result = $this->db->query($sqlCheckExternalContact);

			if ($result && $this->db->num_rows($result) == 0) {
				throw new RestException(404, 'External contact not found');
			}
		} else {
			// Check internal contact exists
			$sqlCheckInternalContact = "SELECT 1 as exist";
			$sqlCheckInternalContact .= " FROM llx_user";
			$sqlCheckInternalContact .= " WHERE rowid = " . intval($contactid);
			$result = $this->db->query($sqlCheckInternalContact);

			if ($result && $this->db->num_rows($result) == 0) {
				throw new RestException(404, 'Internal contact not found');
			}
		}

		// tests done, let's get it
		$result = $this->ticket->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Ticket not found');
		}
		if (!DolibarrApi::_checkAccessToResource('ticket', $this->ticket->id)) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$result = $this->ticket->add_contact($contactid, $type, $source, $notrigger);

		if ($result == 0) {
			throw new RestException(400, 'Already exists: Contact='.$contactid.' is already linked to the ticket='.$id.' as source='.$source.' and type='.$type);
		} elseif ($result == -1) {
			throw new RestException(400, 'Wrong contact='.$contactid);
		} elseif ($result == -2) {
			throw new RestException(400, 'Wrong type='.$type);
		} elseif ($result == -3) {
			throw new RestException(400, 'Not allowed contacts');
		} elseif ($result == -4) {
			throw new RestException(400, 'ErrorCommercialNotAllowedForThirdparty');
		} elseif ($result == -5) {
			throw new RestException(400, 'Trigger failed');
		} elseif ($result == -6) {
			throw new RestException(400, 'DB_ERROR_RECORD_ALREADY_EXISTS');
		} elseif ($result == -7) {
			throw new RestException(400, 'Some other error');
		} elseif ($result <= -8) {
			throw new RestException(400, 'Unknown error occurred');
		}

		return array(
			'success' => array(
				'code' => 200,
				'message' => 'Contact='.$contactid.' linked to the ticket='.$id.' as '.$source.' '.$type
			)
		);
	}

	/**
	 * Unlink a contact type of given ticket
	 *
	 * @since	12.0.0	Initial implementation
	 * @param int    $id             Id of ticket to update
	 * @param int    $contactid      Id of contact
	 * @param string $type           Type of the contact (BILLING, SHIPPING, CUSTOMER).
	 * @param string $source		internal=Contact intern (llx_user), external=Contact extern (llx_socpeople)
	 *
	 * @url	DELETE {id}/contact/{contactid}/{type}
	 *
	 * @return array
	 * @phan-return array{success:array{code:int,message:string}}
	 * @phpstan-return array{success:array{code:int,message:string}}
	 *
	 * @throws RestException 401
	 * @throws RestException 404
	 * @throws RestException 500 System error
	 */
	public function deleteContact(int $id, int $contactid, string $type, string $source = "external"): array
	{
		// Check permissions
		if (!DolibarrApiAccess::$user->hasRight('ticket', 'write')) {
			throw new RestException(403);
		}

		// test source
		if (empty($source)) {
			throw new RestException(400, 'Source can not be empty');
		}
		// test type
		if (empty($type)) {
			throw new RestException(400, 'type can not be empty');
		}

		$result = $this->ticket->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Ticket not found');
		}

		if (!DolibarrApi::_checkAccessToResource('ticket', $this->ticket->id)) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$contacts = $this->ticket->liste_contact(-1, $source);
		foreach ($contacts as $contact) {
			if ($contact['id'] == $contactid && $contact['code'] == $type) {
				$result = $this->ticket->delete_contact($contact['rowid']);

				if (!$result) {
					throw new RestException(500, 'Error when deleting the contact '.$contact['rowid']);
				}
			}
		}

		return array(
			'success' => array(
				'code' => 200,
				'message' => 'Contact unlinked from ticket'
			)
		);
	}
	/*
	* END BACKPORT Develop v24 -> PR #37277 - https://github.com/Dolibarr/dolibarr/pull/37277
	*/
}

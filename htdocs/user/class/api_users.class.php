<?php
/* Copyright (C) 2015   Jean-François Ferry     <jfefe@aternatik.fr>
 * Copyright (C) 2020   Thibault FOUCART		<support@ptibogxiv.net>
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France             <frederic.france@free.fr>
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

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';


/**
 * API class for users
 *
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class Users extends DolibarrApi
{
	/**
	 * @var string[]   $FIELDS     Mandatory fields, checked when create and update object
	 */
	public static $FIELDS = array(
		'login',
	);

	/**
	 * @var User $useraccount {@type User}
	 */
	public $useraccount;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $db;

		$this->db = $db;
		$this->useraccount = new User($this->db);
	}


	/**
	 * List Users
	 *
	 * Get a list of Users
	 *
	 * @param string	$sortfield	Sort field
	 * @param string	$sortorder	Sort order
	 * @param int		$limit		Limit for list
	 * @param int		$page		Page number
	 * @param string	$user_ids   User ids filter field. Example: '1' or '1,2,3'          {@pattern /^[0-9,]*$/i}
	 * @param int       $category   Use this param to filter list by category
	 * @param string    $sqlfilters Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101')"
	 * @param string    $properties	Restrict the data returned to these properties. Ignored if empty. Comma separated list of properties names
	 * @return  array               Array of User objects
	 * @phan-return Object[]
	 * @phpstan-return Object[]
	 */
	public function index($sortfield = "t.rowid", $sortorder = 'ASC', $limit = 100, $page = 0, $user_ids = '0', $category = 0, $sqlfilters = '', $properties = '')
	{
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, "You are not allowed to read list of users");
		}

		$obj_ret = array();

		// case of external user, $societe param is ignored and replaced by user's socid
		//$socid = DolibarrApiAccess::$user->socid ? DolibarrApiAccess::$user->socid : $societe;

		$sql = "SELECT t.rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX."user AS t LEFT JOIN ".MAIN_DB_PREFIX."user_extrafields AS ef ON (ef.fk_object = t.rowid)"; // Modification VMR Global Solutions to include extrafields as search parameters in the API GET call, so we will be able to filter on extrafields
		if ($category > 0) {
			$sql .= ", ".$this->db->prefix()."categorie_user as c";
		}
		$sql .= ' WHERE t.entity IN ('.getEntity('user').')';
		if ($user_ids) {
			$sql .= " AND t.rowid IN (".$this->db->sanitize($user_ids).")";
		}

		// Select products of given category
		if ($category > 0) {
			$sql .= " AND c.fk_categorie = ".((int) $category);
			$sql .= " AND c.fk_user = t.rowid";
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

			$sql .= $this->db->plimit($limit + 1, $offset);
		}

		$result = $this->db->query($sql);

		if ($result) {
			$i = 0;
			$num = $this->db->num_rows($result);
			$min = min($num, ($limit <= 0 ? $num : $limit));
			while ($i < $min) {
				$obj = $this->db->fetch_object($result);
				$user_static = new User($this->db);
				if ($user_static->fetch($obj->rowid)) {
					$obj_ret[] = $this->_filterObjectProperties($this->_cleanObjectDatas($user_static), $properties);
				}
				$i++;
			}
		} else {
			throw new RestException(503, 'Error when retrieve User list : '.$this->db->lasterror());
		}

		return $obj_ret;
	}

	/**
	 * Get properties of an user object
	 *
	 * @param	int		$id						ID of user
	 * @param	int		$includepermissions		Set this to 1 to have the array of permissions loaded (not done by default for performance purpose)
	 * @return	array|mixed						data without useless information
	 * @phan-return Object
	 * @phpstan-return Object
	 *
	 * @throws RestException 401 Insufficient rights
	 * @throws RestException 404 User or group not found
	 */
	public function get($id, $includepermissions = 0)
	{
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin) && $id != 0 && DolibarrApiAccess::$user->id != $id) {
			throw new RestException(403, 'Not allowed');
		}

		if ($id == 0) {
			$result = $this->useraccount->initAsSpecimen();
		} else {
			$result = $this->useraccount->fetch($id);
		}
		if (!$result) {
			throw new RestException(404, 'User not found');
		}

		if ($id > 0 && !DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		if ($includepermissions) {
			$this->useraccount->loadRights();
		}

		return $this->_cleanObjectDatas($this->useraccount);
	}

	/**
	 * Get properties of an user object by login
	 *
	 * @param	string	$login					Login of user
	 * @param	int		$includepermissions		Set this to 1 to have the array of permissions loaded (not done by default for performance purpose)
	 * @return	array|mixed						Data without useless information
	 * @phan-return Object
	 * @phpstan-return Object
	 *
	 * @url GET login/{login}
	 *
	 * @throws RestException 400    Bad request
	 * @throws RestException 401	Insufficient rights
	 * @throws RestException 404	User or group not found
	 */
	public function getByLogin($login, $includepermissions = 0)
	{
		if (empty($login)) {
			throw new RestException(400, 'Bad parameters');
		}

		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin) && DolibarrApiAccess::$user->login != $login) {
			throw new RestException(403, 'Not allowed');
		}

		$result = $this->useraccount->fetch(0, $login);
		if (!$result) {
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		if ($includepermissions) {
			$this->useraccount->loadRights();
		}

		return $this->_cleanObjectDatas($this->useraccount);
	}

	/**
	 * Get properties of an user object by Email
	 *
	 * @param	string	$email					Email of user
	 * @param	int		$includepermissions		Set this to 1 to have the array of permissions loaded (not done by default for performance purpose)
	 * @return	array|mixed						Data without useless information
	 * @phan-return Object
	 * @phpstan-return Object[
	 *
	 * @url GET email/{email}
	 *
	 * @throws RestException 400     Bad request
	 * @throws RestException 401     Insufficient rights
	 * @throws RestException 404     User or group not found
	 */
	public function getByEmail($email, $includepermissions = 0)
	{
		if (empty($email)) {
			throw new RestException(400, 'Bad parameters');
		}

		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin) && DolibarrApiAccess::$user->email != $email) {
			throw new RestException(403, 'Not allowed');
		}

		$result = $this->useraccount->fetch(0, '', '', 0, -1, $email);
		if (!$result) {
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		if ($includepermissions) {
			$this->useraccount->loadRights();
		}

		return $this->_cleanObjectDatas($this->useraccount);
	}

	/**
	 * Get more properties of a user
	 *
	 * @url	GET /info
	 *
	 * @param	int			$includepermissions		Set this to 1 to have the array of permissions loaded (not done by default for performance purpose)
	 * @return  array|mixed							Data without useless information
	 *
	 * @throws RestException 401     Insufficient rights
	 * @throws RestException 404     User or group not found
	 */
	public function getInfo($includepermissions = 0)
	{
		if (!DolibarrApiAccess::$user->hasRight('user', 'self', 'creer') && !DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, 'Not allowed');
		}

		$apiUser = DolibarrApiAccess::$user;

		$result = $this->useraccount->fetch($apiUser->id);
		if (!$result) {
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		if ($includepermissions) {
			$this->useraccount->loadRights();
		}

		$usergroup = new UserGroup($this->db);
		$userGroupList = $usergroup->listGroupsForUser($apiUser->id, false);
		if (!is_array($userGroupList)) {
			throw new RestException(404, 'User group not found');
		}

		$this->useraccount->user_group_list = $this->_cleanUserGroupListDatas($userGroupList);

		return $this->_cleanObjectDatas($this->useraccount);
	}

	/**
	 * Create user account
	 *
	 * @param array $request_data New user data
	 * @phan-param ?array<string,mixed> $request_data
	 * @phpstan-param ?array<string,mixed> $request_data
	 * @return int
	 *
	 * @throws RestException 403 Not allowed
	 */
	public function post($request_data = null)
	{
		// Check user authorization
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'creer') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, "User creation not allowed for login ".DolibarrApiAccess::$user->login);
		}

		// check mandatory fields
		/*if (!isset($request_data["login"]))
			throw new RestException(400, "login field missing");
		if (!isset($request_data["password"]))
			throw new RestException(400, "password field missing");
		if (!isset($request_data["lastname"]))
			 throw new RestException(400, "lastname field missing");*/

		//assign field values
		foreach ($request_data as $field => $value) {
			if (in_array($field, array('pass_crypted', 'pass_indatabase', 'pass_indatabase_crypted', 'pass_temp', 'api_key'))) {
				// This properties can't be set/modified with API
				throw new RestException(405, 'The property '.$field." can't be set/modified using the APIs");
			}
			if ($field === 'caller') {
				// Add a mention of caller so on trigger called after action, we can filter to avoid a loop if we try to sync back again with the caller
				$this->useraccount->context['caller'] = sanitizeVal($request_data['caller'], 'aZ09');
				continue;
			}
			/*if ($field == 'pass') {
				if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'password')) {
					throw new RestException(403, 'You are not allowed to modify/set password of other users');
					continue;
				}
			}
			*/

			$this->useraccount->$field = $this->_checkValForAPI($field, $value, $this->useraccount);
		}

		if ($this->useraccount->create(DolibarrApiAccess::$user) < 0) {
			throw new RestException(500, 'Error creating', array_merge(array($this->useraccount->error), $this->useraccount->errors));
		}
		return $this->useraccount->id;
	}


	/**
	 * Update user account
	 *
	 * @param	int			$id					Id of account to update
	 * @param	array		$request_data		Datas
	 * @phan-param ?array<string,mixed> $request_data
	 * @phpstan-param ?array<string,mixed> $request_data
	 * @return 	Object							Updated object
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 Not found
	 * @throws RestException 500 System error
	 */
	public function put($id, $request_data = null)
	{
		// Check user authorization
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'creer') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, "User update not allowed");
		}

		$result = $this->useraccount->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Account not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		foreach ($request_data as $field => $value) {
			if (in_array($field, array('pass_crypted', 'pass_indatabase', 'pass_indatabase_crypted', 'pass_temp', 'api_key'))) {
				// This properties can't be set/modified with API
				throw new RestException(405, 'The property '.$field." can't be set/modified using the APIs");
			}
			if ($field == 'id') {
				continue;
			}
			if ($field == 'pass') {
				if ($this->useraccount->id != DolibarrApiAccess::$user->id && !DolibarrApiAccess::$user->hasRight('user', 'user', 'password')) {
					throw new RestException(403, 'You are not allowed to modify password of other users');
				}
				if ($this->useraccount->id == DolibarrApiAccess::$user->id && !DolibarrApiAccess::$user->hasRight('user', 'self', 'password')) {
					throw new RestException(403, 'You are not allowed to modify your own password');
				}
			}
			if ($field === 'caller') {
				// Add a mention of caller so on trigger called after action, we can filter to avoid a loop if we try to sync back again with the caller
				$this->useraccount->context['caller'] = sanitizeVal($request_data['caller'], 'aZ09');
				continue;
			}
			if ($field == 'array_options' && is_array($value)) {
				foreach ($value as $index => $val) {
					$this->useraccount->array_options[$index] = $this->_checkValForAPI($field, $val, $this->useraccount);
				}
				continue;
			}

			if (DolibarrApiAccess::$user->admin) {	// If user for API is admin
				if ($field == 'admin' && $value != $this->useraccount->admin && empty($value)) {
					throw new RestException(403, 'Reseting the admin status of a user is not possible using the API');
				}
			} else {
				if ($field == 'admin' && $value != $this->useraccount->admin) {
					throw new RestException(403, 'Only an admin user can modify the admin status of another user');
				}
			}
			if ($field == 'entity' && $value != $this->useraccount->entity) {
				throw new RestException(403, 'Changing entity of a user using the APIs is not possible');
			}

			// The status must be updated using setstatus() because it
			// is not handled by the update() method.
			if ($field == 'statut' || $field == 'status') {
				$result = $this->useraccount->setstatus($value);
				if ($result < 0) {
					throw new RestException(500, 'Error when updating status of user: '.$this->useraccount->error);
				}
			} else {
				$this->useraccount->$field = $this->_checkValForAPI($field, $value, $this->useraccount);
			}
		}

		// If there is no error, update() returns the number of affected
		// rows so if the update is a no op, the return value is zezo.
		if ($this->useraccount->update(DolibarrApiAccess::$user) >= 0) {
			return $this->get($id);
		} else {
			throw new RestException(500, $this->useraccount->error);
		}
	}

	/**
	 * Update a user password
	 *
	 * @param   int     $id        			User ID
	 * @param	bool	$send_password		Only if set to true, the new password will send to the user
	 * @return  int                			1 if password changed, 2 if password changed and sent
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 User not found
	 * @throws RestException 500 System error
	 *
	 * @url	GET {id}/setPassword
	 */
	public function setPassword($id, $send_password = false)
	{
		//$conf->global->API_DISABLE_LOGIN_API = 1;
		if (getDolGlobalString('API_DISABLE_LOGIN_API')) {
			throw new RestException(403, "Error: login and password reset APIs are disabled. You can get access token from the backoffice to get access permission but permission and password manipulation from APIs are forbidden.");
		}

		//$conf->global->API_ALLOW_PASSWORD_RESET = 1;
		if (!getDolGlobalString('API_ALLOW_PASSWORD_RESET')) {
			throw new RestException(403, "Error: password reset APIs are disabled by default. To allow this, the option API_ALLOW_PASSWORD_RESET must be set.");
		}

		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'creer') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, "setPassword on user not allowed for login ".DolibarrApiAccess::$user->login);
		}

		$result = $this->useraccount->fetch($id);
		if (!$result) {
			throw new RestException(404, 'User not found, no password changed');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access on this object not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$newpassword = $this->useraccount->setPassword($this->useraccount, '');	// This will generate a new password
		if (is_int($newpassword) && $newpassword < 0) {
			throw new RestException(500, 'ErrorFailedToSetNewPassword'.$this->useraccount->error);
		} else {
			// Success
			if ($send_password) {
				if ($this->useraccount->send_password($this->useraccount, $newpassword) > 0) {
					return 2;
				} else {
					throw new RestException(500, 'ErrorFailedSendingNewPassword - '.$this->useraccount->error);
				}
			} else {
				return 1;
			}
		}
	}

	/**
	 * Get the password policy of this instance
	 *
	 * Returns the password constraints (minimum length and minimum count of
	 * uppercase, digit and special characters) derived from the active password
	 * generator (USER_PASSWORD_GENERATED / USER_PASSWORD_PATTERN), so a portal
	 * front-end can display and pre-validate them before a reset. Read-only.
	 *
	 * Gated by the constant USER_PASSWORD_RESET_PUBLIC_API (off by default) and
	 * meant to be called server-to-server with a system API key.
	 *
	 * @return  array   Password policy {minLength, minUpper, minDigits, minSpecial, generator}
	 * @phan-return array<string,int|string>
	 *
	 * @throws RestException 403 Feature disabled or not allowed
	 *
	 * @url GET passwordpolicy
	 */
	public function passwordPolicy()
	{
		global $conf, $langs;

		if (!getDolGlobalString('USER_PASSWORD_RESET_PUBLIC_API')) {
			throw new RestException(403, 'Public password reset API is disabled (constant USER_PASSWORD_RESET_PUBLIC_API).');
		}
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'password') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, 'Not allowed');
		}

		$generator = getDolGlobalString('USER_PASSWORD_GENERATED');
		$policy = array('minLength' => 0, 'minUpper' => 0, 'minDigits' => 0, 'minSpecial' => 0, 'generator' => $generator);

		if (!empty($generator)) {
			$classname = 'modGeneratePass'.ucfirst($generator);
			$file = DOL_DOCUMENT_ROOT.'/core/modules/security/generate/'.$classname.'.class.php';
			if (file_exists($file)) {
				include_once $file;
				if (class_exists($classname)) {
					$modgen = new $classname($this->db, $conf, $langs, DolibarrApiAccess::$user);
					$policy['minLength'] = (int) (isset($modgen->length2) ? $modgen->length2 : (isset($modgen->length) ? $modgen->length : 0));
					$policy['minUpper'] = (int) (isset($modgen->NbMaj) ? $modgen->NbMaj : 0);
					$policy['minDigits'] = (int) (isset($modgen->NbNum) ? $modgen->NbNum : 0);
					$policy['minSpecial'] = (int) (isset($modgen->NbSpe) ? $modgen->NbSpe : 0);
				}
			}
		}

		return $policy;
	}

	/**
	 * Request a password reset link
	 *
	 * Generates a temporary password for the matching user and emails a reset
	 * LINK pointing to the provided portal returnurl (no password is included in
	 * the email body). The response is identical whether or not an account
	 * matches (anti-enumeration). Gated by USER_PASSWORD_RESET_PUBLIC_API and
	 * meant to be called server-to-server with a system API key holding the
	 * user/password right.
	 *
	 * @param   array   $request_data   Request data {login_or_email, returnurl}
	 * @return  array   Neutral acknowledgement {success}
	 * @phan-return array<string,bool>
	 *
	 * @throws RestException 400 Bad parameters or returnurl host not allowed
	 * @throws RestException 403 Feature disabled or not allowed
	 *
	 * @url POST passwordresetrequest
	 */
	public function passwordResetRequest($request_data = null)
	{
		global $conf;

		if (!getDolGlobalString('USER_PASSWORD_RESET_PUBLIC_API')) {
			throw new RestException(403, 'Public password reset API is disabled (constant USER_PASSWORD_RESET_PUBLIC_API).');
		}
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'password') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, 'Not allowed');
		}

		$loginoremail = (is_array($request_data) && isset($request_data['login_or_email'])) ? trim((string) $request_data['login_or_email']) : '';
		$returnurl = (is_array($request_data) && isset($request_data['returnurl'])) ? trim((string) $request_data['returnurl']) : '';
		if ($loginoremail === '' || $returnurl === '') {
			throw new RestException(400, 'Parameters login_or_email and returnurl are mandatory');
		}

		// Validate the returnurl host against the whitelist (anti open-redirect / phishing)
		$allowedhosts = array_filter(array_map('trim', explode(',', getDolGlobalString('USER_PASSWORD_RESET_ALLOWED_RETURN_HOSTS'))));
		$returnhost = parse_url($returnurl, PHP_URL_HOST);
		if (empty($returnhost) || !in_array($returnhost, $allowedhosts, true)) {
			throw new RestException(400, 'returnurl host is not in the allowed list (constant USER_PASSWORD_RESET_ALLOWED_RETURN_HOSTS)');
		}

		$edituser = new User($this->db);
		if (strpos($loginoremail, '@') !== false) {
			$resfetch = $edituser->fetch(0, '', '', 0, -1, $loginoremail);
		} else {
			$resfetch = $edituser->fetch(0, $loginoremail);
		}

		if ($resfetch > 0 && !empty($edituser->email)) {
			$newpassword = $edituser->setPassword(DolibarrApiAccess::$user, '', 1);	// Generate + store a temporary password (clear) in pass_temp
			if (is_int($newpassword) && $newpassword < 0) {
				dol_syslog("Users::passwordResetRequest failed to set temporary password for user ".((int) $edituser->id)." : ".$edituser->error, LOG_ERR);
			} else {
				$hash = dol_hash($newpassword.'-'.$edituser->id.'-'.$conf->file->instance_unique_id);
				$this->sendPasswordResetLinkEmail($edituser, $returnurl, $hash);
			}
		} else {
			usleep(20000);	// Simulate processing delay so response time does not leak account existence
		}

		// Always neutral: never reveal whether the account exists
		return array('success' => true);
	}

	/**
	 * Confirm a password reset and set the user-chosen new password
	 *
	 * Verifies the possession hash received by email, then sets the new password.
	 * The new password is validated against the instance password policy by
	 * User::setPassword (returns < 0 with an error if it does not comply). Gated
	 * by USER_PASSWORD_RESET_PUBLIC_API and meant to be called server-to-server
	 * with a system API key holding the user/password right.
	 *
	 * @param   array   $request_data   Request data {username, passworduidhash, newpassword}
	 * @return  array   {success}
	 * @phan-return array<string,bool>
	 *
	 * @throws RestException 400 Bad parameters
	 * @throws RestException 403 Feature disabled, not allowed, or invalid/expired link
	 * @throws RestException 422 New password does not meet the password policy
	 *
	 * @url POST passwordresetconfirm
	 */
	public function passwordResetConfirm($request_data = null)
	{
		global $conf;

		if (!getDolGlobalString('USER_PASSWORD_RESET_PUBLIC_API')) {
			throw new RestException(403, 'Public password reset API is disabled (constant USER_PASSWORD_RESET_PUBLIC_API).');
		}
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'password') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, 'Not allowed');
		}

		$username = (is_array($request_data) && isset($request_data['username'])) ? trim((string) $request_data['username']) : '';
		$hash = (is_array($request_data) && isset($request_data['passworduidhash'])) ? trim((string) $request_data['passworduidhash']) : '';
		$newpassword = (is_array($request_data) && isset($request_data['newpassword'])) ? (string) $request_data['newpassword'] : '';
		if ($username === '' || $hash === '' || $newpassword === '') {
			throw new RestException(400, 'Parameters username, passworduidhash and newpassword are mandatory');
		}

		$edituser = new User($this->db);
		$resfetch = $edituser->fetch(0, $username);
		if ($resfetch <= 0 || empty($edituser->pass_temp)
			|| !dol_verifyHash($edituser->pass_temp.'-'.$edituser->id.'-'.$conf->file->instance_unique_id, $hash)) {
			// Generic message: never reveal which part failed (anti-enumeration)
			throw new RestException(403, 'Invalid or expired password reset link');
		}

		$result = $edituser->setPassword(DolibarrApiAccess::$user, $newpassword, 0);
		if (is_int($result) && $result < 0) {
			throw new RestException(422, $edituser->error ? $edituser->error : 'New password does not meet the password policy');
		}

		return array('success' => true);
	}

	/**
	 * Send a password-reset LINK email pointing to a portal returnurl (no password in body)
	 *
	 * @param   User    $edituser   User to email (must hold an email address)
	 * @param   string  $returnurl  Portal base URL of the reset page (already whitelisted)
	 * @param   string  $hash       Possession hash to append to the link
	 * @return  int                 Return integer < 0 if KO, > 0 if OK
	 */
	private function sendPasswordResetLinkEmail($edituser, $returnurl, $hash)
	{
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';

		$outputlangs = $langs;
		if (getDolGlobalString('MAIN_LANG_DEFAULT')) {
			$outputlangs = new Translate('', $conf);
			$outputlangs->setDefaultLang(getDolGlobalString('MAIN_LANG_DEFAULT'));
		}
		$outputlangs->loadLangs(array('main', 'errors', 'users', 'other'));

		$appli = getDolGlobalString('MAIN_APPLICATION_TITLE', constant('DOL_APPLICATION_TITLE'));

		$link = $returnurl.((strpos($returnurl, '?') === false) ? '?' : '&');
		$link .= 'username='.urlencode($edituser->login).'&passworduidhash='.urlencode($hash);

		$subject = '['.$appli.'] '.$outputlangs->transnoentitiesnoconv('SubjectNewPassword', $appli);

		$mesg = $outputlangs->transnoentitiesnoconv('RequestToResetPasswordReceived')."<br>\n<br>\n";
		$mesg .= $outputlangs->transnoentitiesnoconv('YouMustClickToChange')." :<br>\n";
		$mesg .= '<a href="'.dol_escape_htmltag($link).'" rel="noopener">'.$outputlangs->transnoentitiesnoconv('ConfirmPasswordChange').'</a>'."<br>\n<br>\n";
		$mesg .= $outputlangs->transnoentitiesnoconv('ForgetIfNothing')."<br>\n";

		$trackid = 'use'.$edituser->id;
		$sendcontext = 'passwordreset';

		$mailfile = new CMailFile(
			$subject,
			$edituser->email,
			getDolGlobalString('MAIN_MAIL_EMAIL_FROM'),
			$mesg,
			array(),
			array(),
			array(),
			'',
			'',
			0,
			1,
			'',
			'',
			$trackid,
			'',
			$sendcontext
		);

		if (!$mailfile->sendfile()) {
			dol_syslog("Users::sendPasswordResetLinkEmail failed to send to user ".((int) $edituser->id)." : ".$mailfile->error, LOG_ERR);
			return -1;
		}

		return 1;
	}

	/**
	 * List the groups of a user
	 *
	 * @param int $id     Id of user
	 * @return array      Array of group objects
	 * @phan-return Object[]
	 * @phpstan-return Object[]
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 Not found
	 *
	 * @url GET {id}/groups
	 */
	public function getGroups($id)
	{
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403);
		}

		$user = new User($this->db);
		$result = $user->fetch($id);
		if (!$result) {
			throw new RestException(404, 'user not found');
		}

		$usergroup = new UserGroup($this->db);
		$groups = $usergroup->listGroupsForUser($id, false);
		$obj_ret = array();
		foreach ($groups as $group) {
			$obj_ret[] = $this->_cleanObjectDatas($group);
		}
		return $obj_ret;
	}


	/**
	 * Add a user into a group
	 *
	 * @param   int     $id        User ID
	 * @param   int     $group     Group ID
	 * @param   int     $entity    Entity ID (valid only for superadmin in multicompany transverse mode)
	 * @return  int                1 if success
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 User not found
	 * @throws RestException 500 System error
	 *
	 * @url	GET {id}/setGroup/{group}
	 */
	public function setGroup($id, $group, $entity = 1)
	{
		global $conf;

		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'creer') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, 'setGroup on users not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$result = $this->useraccount->fetch($id);
		if (!$result) {
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}

		if (isModEnabled('multicompany') && getDolGlobalString('MULTICOMPANY_TRANSVERSE_MODE') && !empty(DolibarrApiAccess::$user->admin) && empty(DolibarrApiAccess::$user->entity)) {
			$entity = (!empty($entity) ? $entity : $conf->entity);
		} else {
			// When using API, action is done on entity of logged user because a user of entity X with permission to create user should not be able to
			// hack the security by giving himself permissions on another entity.
			$entity = (DolibarrApiAccess::$user->entity > 0 ? DolibarrApiAccess::$user->entity : $conf->entity);
		}

		$result = $this->useraccount->SetInGroup($group, $entity);
		if (!($result > 0)) {
			throw new RestException(500, $this->useraccount->error);
		}

		return 1;
	}

	/**
	 * List Groups
	 *
	 * Return an array with a list of Groups
	 *
	 * @url	GET /groups
	 *
	 * @param string	$sortfield	Sort field
	 * @param string	$sortorder	Sort order
	 * @param int		$limit		Limit for list
	 * @param int		$page		Page number
	 * @param string	$group_ids   Groups ids filter field. Example: '1' or '1,2,3'          {@pattern /^[0-9,]*$/i}
	 * @param string    $sqlfilters Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101')"
	 * @param string    $properties	Restrict the data returned to these properties. Ignored if empty. Comma separated list of properties names
	 * @return  array               Array of User objects
	 * @phan-return Object[]
	 * @phpstan-return Object[]
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 User not found
	 * @throws RestException 503 Error
	 */
	public function listGroups($sortfield = "t.rowid", $sortorder = 'ASC', $limit = 100, $page = 0, $group_ids = '0', $sqlfilters = '', $properties = '')
	{
		$obj_ret = array();

		if ((!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && !DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin)) ||
			getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && !DolibarrApiAccess::$user->hasRight('user', 'group_advance', 'read') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, "You are not allowed to read groups");
		}

		// case of external user, $societe param is ignored and replaced by user's socid
		//$socid = DolibarrApiAccess::$user->socid ? DolibarrApiAccess::$user->socid : $societe;

		$sql = "SELECT t.rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX."usergroup AS t LEFT JOIN ".MAIN_DB_PREFIX."usergroup_extrafields AS ef ON (ef.fk_object = t.rowid)"; // Modification VMR Global Solutions to include extrafields as search parameters in the API GET call, so we will be able to filter on extrafields
		$sql .= ' WHERE t.entity IN ('.getEntity('user').')';
		if ($group_ids) {
			$sql .= " AND t.rowid IN (".$this->db->sanitize($group_ids).")";
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

			$sql .= $this->db->plimit($limit + 1, $offset);
		}

		$result = $this->db->query($sql);

		if ($result) {
			$i = 0;
			$num = $this->db->num_rows($result);
			$min = min($num, ($limit <= 0 ? $num : $limit));
			while ($i < $min) {
				$obj = $this->db->fetch_object($result);
				$group_static = new UserGroup($this->db);
				if ($group_static->fetch($obj->rowid)) {
					$obj_ret[] = $this->_filterObjectProperties($this->_cleanObjectDatas($group_static), $properties);
				}
				$i++;
			}
		} else {
			throw new RestException(503, 'Error when retrieve Group list : '.$this->db->lasterror());
		}

		return $obj_ret;
	}

	/**
	 * Get properties of an group object
	 *
	 * Return an array with group information
	 *
	 * @url	GET /groups/{group}
	 *
	 * @param	int		$group ID of group
	 * @param int       $load_members     Load members list or not {@min 0} {@max 1}
	 * @return  object               object of User objects
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 User not found
	 */
	public function infoGroups($group, $load_members = 0)
	{
		if ((!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && !DolibarrApiAccess::$user->hasRight('user', 'user', 'lire') && empty(DolibarrApiAccess::$user->admin)) ||
			getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && !DolibarrApiAccess::$user->hasRight('user', 'group_advance', 'read') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, "You are not allowed to read groups");
		}

		$group_static = new UserGroup($this->db);
		$result = $group_static->fetch($group, '', $load_members);

		if (!$result) {
			throw new RestException(404, 'Group not found');
		}

		return $this->_cleanObjectDatas($group_static);
	}

	/**
	 * Delete account/user
	 *
	 * @param   int     $id Account ID
	 * @return  array
	 * @phan-return array{success:array{code:int,message:string}}
	 * @phpstan-return array{success:array{code:int,message:string}}
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 User not found
	 */
	public function delete($id)
	{
		if (!DolibarrApiAccess::$user->hasRight('user', 'user', 'supprimer') && empty(DolibarrApiAccess::$user->admin)) {
			throw new RestException(403, 'Not allowed');
		}
		$result = $this->useraccount->fetch($id);
		if (!$result) {
			throw new RestException(404, 'User not found');
		}

		if (!DolibarrApi::_checkAccessToResource('user', $this->useraccount->id, 'user')) {
			throw new RestException(403, 'Access not allowed for login '.DolibarrApiAccess::$user->login);
		}
		$this->useraccount->oldcopy = clone $this->useraccount;

		if (!$this->useraccount->delete(DolibarrApiAccess::$user)) {
			throw new RestException(500);
		}

		return array(
			'success' => array(
				'code' => 200,
				'message' => 'Ticket deleted'
			)
		);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Clean sensible object datas
	 *
	 * @param   Object	$object		Object to clean
	 * @return  Object				Object with cleaned properties
	 */
	protected function _cleanObjectDatas($object)
	{
		// phpcs:enable
		$object = parent::_cleanObjectDatas($object);

		unset($object->default_values);
		unset($object->lastsearch_values);
		unset($object->lastsearch_values_tmp);

		unset($object->total_ht);
		unset($object->total_tva);
		unset($object->total_localtax1);
		unset($object->total_localtax2);
		unset($object->total_ttc);

		unset($object->label_incoterms);
		unset($object->location_incoterms);

		unset($object->fk_delivery_address);
		unset($object->fk_incoterms);
		unset($object->all_permissions_are_loaded);
		unset($object->shipping_method_id);
		unset($object->nb_rights);
		unset($object->search_sid);
		unset($object->ldap_sid);
		unset($object->clicktodial_loaded);

		// List of properties never returned by API, whatever are permissions
		unset($object->pass);
		unset($object->pass_indatabase);
		unset($object->pass_indatabase_crypted);
		unset($object->pass_temp);
		unset($object->api_key);
		unset($object->clicktodial_password);
		unset($object->openid);

		unset($object->lines);
		unset($object->model_pdf);

		$canreadsalary = ((isModEnabled('salaries') && DolibarrApiAccess::$user->hasRight('salaries', 'read')) || !isModEnabled('salaries'));

		if (!$canreadsalary) {
			unset($object->salary);
			unset($object->salaryextra);
			unset($object->thm);
			unset($object->tjm);
		}

		return $object;
	}

	/**
	 * Clean sensible user group list datas
	 *
	 * @param   array<UserGroup>  $objectList   Array of object to clean
	 * @return  array<UserGroup>                Array of cleaned object properties
	 */
	private function _cleanUserGroupListDatas($objectList)
	{
		$cleanObjectList = array();

		foreach ($objectList as $object) {
			$cleanObject = parent::_cleanObjectDatas($object);

			unset($cleanObject->default_values);
			unset($cleanObject->lastsearch_values);
			unset($cleanObject->lastsearch_values_tmp);

			unset($cleanObject->total_ht);
			unset($cleanObject->total_tva);
			unset($cleanObject->total_localtax1);
			unset($cleanObject->total_localtax2);
			unset($cleanObject->total_ttc);

			unset($cleanObject->libelle_incoterms);
			unset($cleanObject->location_incoterms);

			unset($cleanObject->fk_delivery_address);
			unset($cleanObject->fk_incoterms);
			unset($cleanObject->all_permissions_are_loaded);
			unset($cleanObject->shipping_method_id);
			unset($cleanObject->nb_rights);
			unset($cleanObject->search_sid);
			unset($cleanObject->ldap_sid);
			unset($cleanObject->clicktodial_loaded);

			unset($cleanObject->datec);
			unset($cleanObject->tms);
			unset($cleanObject->members);
			unset($cleanObject->note);
			unset($cleanObject->note_private);

			$cleanObjectList[] = $cleanObject;
		}

		return $cleanObjectList;
	}

	/**
	 * Validate fields before create or update object
	 *
	 * @param   ?array<string,mixed>     $data   Data to validate
	 * @return  array<string,mixed>
	 * @throws RestException
	 */
	private function _validate($data) // @phpstan-ignore-line
	{
		$account = array();
		foreach (Users::$FIELDS as $field) {
			if (!isset($data[$field])) {
				throw new RestException(400, "$field field missing");
			}
			$account[$field] = $data[$field];
		}
		return $account;
	}
}

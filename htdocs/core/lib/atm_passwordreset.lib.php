<?php
/* Copyright (C) 2026		ATM Consulting			<support@atm-consulting.fr>
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
 *	\file		htdocs/core/lib/atm_passwordreset.lib.php
 *	\ingroup	core
 *	\brief		SPECIFIQUE ATM password-reset-native - backport of core PR #39370 (Dolibarr v25).
 *
 *	Whole file is ATM specific. On the v25 upgrade, delete it together with every
 *	SPECIFIQUE ATM password-reset-native block found in the core files: v25 ships the
 *	same behavior natively (dolGetPasswordResetHash(), link-only User::send_password()).
 *	Locate every piece with: git grep 'SPECIFIQUE ATM password-reset-native'
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/security.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';


/**
 * Build the possession hash for a password-reset link.
 *
 * The hash binds the secret stored in llx_user.pass_temp to the user id and to the
 * instance unique id, so a link is valid only for one user on one instance. The result
 * must stay deterministic and free of characters that an URL or GETPOST would mangle.
 *
 * @param	string	$secret		Full value stored in pass_temp (see atmVerifyPasswordResetHash)
 * @param	int		$userid		Target user rowid
 * @return	string				Hash to put in the reset link (passworduidhash)
 */
function atmGetPasswordResetHash(string $secret, int $userid): string
{
	global $conf;

	return dol_hash($secret.'-'.$userid.'-'.$conf->file->instance_unique_id, 'hash');
}

/**
 * Verify a password-reset possession hash and its expiry.
 *
 * pass_temp format for links armed by atmRequestPasswordReset():
 *   'r:<YYYYMMDDHHMMSS gmt>:<randomsecret>'.
 * The hash covers the WHOLE value, so neither the secret nor the expiry can be altered.
 * Legacy values without the 'r:' prefix carry no expiry.
 *
 * @param	string|null	$secret			Full value read from pass_temp (null when no reset is pending)
 * @param	int			$userid			Target user rowid
 * @param	string		$hashtotest		Hash received from the reset link
 * @return	int							1 if valid, 0 if bad possession, -1 if expired
 */
function atmVerifyPasswordResetHash(?string $secret, int $userid, string $hashtotest): int
{
	global $conf;

	if ($secret === null || $secret === '' || $hashtotest === '') {
		return 0;
	}
	if (!dol_verifyHash($secret.'-'.$userid.'-'.$conf->file->instance_unique_id, $hashtotest, 'hash')) {
		return 0;
	}

	$reg = array();
	if (preg_match('/^r:(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2}):/', $secret, $reg)) {
		$maxdate = dol_mktime((int) $reg[4], (int) $reg[5], (int) $reg[6], (int) $reg[2], (int) $reg[3], (int) $reg[1], 'gmt');
		if ($maxdate && $maxdate < dol_now()) {
			return -1;
		}
	}

	return 1;
}

/**
 * Arm an expiring password-reset token in pass_temp.
 *
 * No password is set until the user confirms with setPassword($user, $chosen, 0).
 *
 * @param	User	$edituser		User to arm the reset for (id must be set)
 * @param	int		$ttlseconds		Link validity in seconds (0 = USER_PASSWORD_RESET_LINK_VALIDITY, default 3600)
 * @return	string|int				Stored pass_temp value, or < 0 on error
 */
function atmRequestPasswordReset(User $edituser, int $ttlseconds = 0)
{
	if ($edituser->id <= 0) {
		$edituser->error = 'atmRequestPasswordReset called on a user with no id';
		dol_syslog($edituser->error, LOG_ERR);
		return -1;
	}

	if (empty($ttlseconds)) {
		$ttlseconds = getDolGlobalInt('USER_PASSWORD_RESET_LINK_VALIDITY', 3600);
	}

	$secret = getRandomPassword(true);	// generic random, not bound to the user password policy
	$expiry = dol_print_date(dol_now('gmt') + $ttlseconds, '%Y%m%d%H%M%S', 'gmt');
	$value = 'r:'.$expiry.':'.$secret;

	$sql = "UPDATE ".$edituser->db->prefix()."user";
	$sql .= " SET pass_temp = '".$edituser->db->escape($value)."'";
	$sql .= " WHERE rowid = ".((int) $edituser->id);

	dol_syslog("atmRequestPasswordReset user->id=".$edituser->id, LOG_DEBUG);
	if ($edituser->db->query($sql)) {
		$edituser->pass_temp = $value;
		return $value;
	}

	$edituser->error = $edituser->db->lasterror();
	return -1;
}

/**
 * Build the HTML body of a password-reset LINK email (no password in body).
 *
 * @param	Translate	$outputlangs	Output language object (already loaded)
 * @param	string		$link			Absolute reset link
 * @return	string						HTML email body
 */
function atmGetPasswordResetEmailContent(Translate $outputlangs, string $link): string
{
	$mesg  = $outputlangs->transnoentitiesnoconv("RequestToResetPasswordReceived")."<br>\n<br>\n";
	$mesg .= $outputlangs->transnoentitiesnoconv("YouMustClickToChange")." :<br>\n";
	$mesg .= '<a href="'.dol_escape_htmltag($link).'" rel="noopener">'.$outputlangs->transnoentitiesnoconv("ConfirmPasswordChange").'</a>'."<br>\n<br>\n";
	$mesg .= $outputlangs->transnoentitiesnoconv("ForgetIfNothing")."<br>\n";

	return $mesg;
}

/**
 * Email the reset link of an already armed token. Never sends a cleartext password.
 *
 * @param	User	$edituser	User to send the link to (login, id, entity and email must be set)
 * @param	string	$armed		pass_temp value returned by atmRequestPasswordReset(), armed here when empty
 * @return	int					1 if sent, -1 on error (message in $edituser->error)
 */
function atmSendPasswordResetLink(User $edituser, string $armed): int
{
	global $conf, $langs, $dolibarr_main_url_root;

	require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';

	if ($armed === '') {
		// Legacy callers rely on the default $password of User::send_password(): a link built on an
		// empty secret would never validate, so arm the token here rather than emailing a dead link.
		$armed = atmRequestPasswordReset($edituser);
		if (!is_string($armed)) {
			return -1;
		}
	}

	if (getDolGlobalString('MAIN_LANG_DEFAULT')) {
		$outputlangs = new Translate("", $conf);
		$outputlangs->setDefaultLang(getDolGlobalString('MAIN_LANG_DEFAULT'));
	} else {
		$outputlangs = $langs;
	}
	$outputlangs->loadLangs(array("main", "errors", "users", "other"));

	$appli = getDolGlobalString('MAIN_APPLICATION_TITLE', constant('DOL_APPLICATION_TITLE'));
	$subject = '['.$appli.'] '.$outputlangs->transnoentitiesnoconv("SubjectNewPassword", $appli);

	$urlwithouturlroot = preg_replace('/'.preg_quote(DOL_URL_ROOT, '/').'$/i', '', trim($dolibarr_main_url_root));
	$urlwithroot = $urlwithouturlroot.DOL_URL_ROOT;	// This is to use external domain name found into config file

	$url = $urlwithroot.'/user/passwordforgotten.php?setnewpassword=1';
	$url .= '&username='.urlencode($edituser->login).'&passworduidhash='.urlencode(atmGetPasswordResetHash($armed, $edituser->id));
	if (isModEnabled('multicompany')) {
		$url .= '&entity='.(!empty($edituser->entity) ? $edituser->entity : 1);
	}

	dol_syslog("atmSendPasswordResetLink link-only url=".$url);

	$mailfile = new CMailFile(
		$subject,
		$edituser->email,
		getDolGlobalString('MAIN_MAIL_EMAIL_FROM'),
		atmGetPasswordResetEmailContent($outputlangs, $url),
		array(),
		array(),
		array(),
		'',
		'',
		0,
		1,
		'',
		'',
		'use'.$edituser->id,
		'',
		'passwordreset'
	);

	if ($mailfile->sendfile()) {
		return 1;
	}

	$edituser->error = $outputlangs->trans("ErrorFailedToSendPassword").' '.$mailfile->error;
	return -1;
}

/**
 * Validate the submitted security code against the active captcha handler.
 *
 * The handler that rendered the code on the form is the only one able to validate it,
 * so the caller must pass the very same handler name it used to build the page.
 *
 * @param	string	$captcha	Captcha handler name (empty = captcha disabled, nothing to validate)
 * @return	bool				True when the code is accepted, or when no captcha is active
 */
function atmVerifyCaptchaCode(string $captcha): bool
{
	global $conf, $db, $langs, $user;

	if (empty($captcha)) {
		return true;
	}

	$dirModCaptcha = array_merge(array('main' => '/core/modules/security/captcha/'), (isset($conf->modules_parts['captcha']) && is_array($conf->modules_parts['captcha'])) ? $conf->modules_parts['captcha'] : array());
	$fullpathclassfile = '';
	foreach ($dirModCaptcha as $dir) {
		$fullpathclassfile = dol_buildpath($dir."modCaptcha".ucfirst($captcha).'.class.php', 0, 2);
		if ($fullpathclassfile) {
			break;
		}
	}

	if (!$fullpathclassfile) {
		dol_syslog('Error, the captcha handler '.$captcha.' has no class file found modCaptcha'.ucfirst($captcha), LOG_ERR);
		return false;
	}

	include_once $fullpathclassfile;
	$classname = "modCaptcha".ucfirst($captcha);
	if (!class_exists($classname)) {
		dol_syslog('Error, the captcha handler class '.$classname.' was not found after the include', LOG_ERR);
		return false;
	}

	$captchaobj = new $classname($db, $conf, $langs, $user);
	'@phan-var-force ModeleCaptcha $captchaobj';

	if (!method_exists($captchaobj, 'validateCodeAfterLoginSubmit')) {
		dol_syslog('Error, the captcha handler '.get_class($captchaobj).' does not have any method validateCodeAfterLoginSubmit()', LOG_ERR);
		return false;
	}

	return $captchaobj->validateCodeAfterLoginSubmit() > 0;	// @phan-suppress-current-line PhanUndeclaredMethod
}

<?php
/* Copyright (C) 2007-2011	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2008-2012	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2008-2011	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2014       Teddy Andreotti    		<125155@supinfo.com>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
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
 */

/**
 *       \file       htdocs/user/passwordforgotten.php
 *       \brief      Page to ask a new password
 */

define("NOLOGIN", 1); // This means this output page does not require to be logged.

// Load Dolibarr environment
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/usergroups.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
if (isModEnabled('ldap')) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/ldap.class.php';
}

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Load translation files required by page
$langs->loadLangs(array('errors', 'users', 'companies', 'ldap', 'other'));

// Security check
if (getDolGlobalString('MAIN_SECURITY_DISABLEFORGETPASSLINK')) {
	header("Location: ".DOL_URL_ROOT.'/');
	exit;
}

$action = GETPOST('action', 'aZ09');
$mode = $dolibarr_main_authentication;
if (!$mode) {
	$mode = 'http';
}

$username = GETPOST('username', 'alphanohtml');
/**DEBUT SPECIFIQUE ATM password-reset-native**/
// Backport of core PR #39370. Native in v25: delete this block and restore the core line.
// Coeur d'origine : $passworduidhash = GETPOST('passworduidhash', 'alpha');
require_once DOL_DOCUMENT_ROOT.'/core/lib/atm_passwordreset.lib.php';
$passworduidhash = GETPOST('passworduidhash', 'aZ09');	// atmGetPasswordResetHash() returns an hexadecimal hash
$newpass1 = GETPOST('newpass1', 'password');
$newpass2 = GETPOST('newpass2', 'password');
$passwordchanged = 0;
/**FIN SPECIFIQUE ATM**/
$setnewpassword = GETPOST('setnewpassword', 'aZ09');

$conf->entity = (GETPOSTINT('entity') ? GETPOSTINT('entity') : 1);

// Instantiate hooks of thirdparty module only if not already define
$hookmanager->initHooks(array('passwordforgottenpage'));


if (GETPOST('dol_hide_leftmenu', 'alpha') || !empty($_SESSION['dol_hide_leftmenu'])) {
	$conf->dol_hide_leftmenu = 1;
}
if (GETPOST('dol_hide_topmenu', 'alpha') || !empty($_SESSION['dol_hide_topmenu'])) {
	$conf->dol_hide_topmenu = 1;
}
if (GETPOST('dol_optimize_smallscreen', 'alpha') || !empty($_SESSION['dol_optimize_smallscreen'])) {
	$conf->dol_optimize_smallscreen = 1;
}
if (GETPOST('dol_no_mouse_hover', 'alpha') || !empty($_SESSION['dol_no_mouse_hover'])) {
	$conf->dol_no_mouse_hover = 1;
}
if (GETPOST('dol_use_jmobile', 'alpha') || !empty($_SESSION['dol_use_jmobile'])) {
	$conf->dol_use_jmobile = 1;
}


/**DEBUT SPECIFIQUE ATM password-reset-native**/
// Same rule as the "Send password button enabled ?" block of the View section below, needed
// this early because the reset action validates the captcha. Native in v25: delete this block.
$disabled = (preg_match('/dolibarr/i', $mode) || getDolGlobalString('MAIN_SECURITY_ENABLE_SENDPASSWORD')) ? '' : 'disabled';
$captcha = $disabled ? '' : atmGetPasswordResetCaptchaHandler();
/**FIN SPECIFIQUE ATM**/

/*
 * Actions
 */

$parameters = array('username' => $username);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	$message = $hookmanager->error;
} else {
	$message = '';
}

if (empty($reshook)) {
	/**DEBUT SPECIFIQUE ATM password-reset-native**/
	// Backport of core PR #39370: the user chooses the password on the reset page. Security is
	// managed by $passworduidhash (proof of possession of the emailed link), no captcha needed
	// on this step. The core 'validatenewpassword' step below is left in place but no link
	// points to it anymore. Native in v25: delete this block.
	if ($action == 'setnewpassword' && $username && $passworduidhash) {
		if ($newpass1 === '' || $newpass2 === '') {
			$message = '<div class="error">'.$langs->trans("NewPasswordEmpty").'</div>';
		} elseif ($newpass1 !== $newpass2) {
			$message = '<div class="error">'.$langs->trans("NewPasswordMismatch").'</div>';
		} else {
			$edituser = new User($db);
			$result = $edituser->fetch(0, $username, '', 0, $conf->entity);
			if ($result < 0) {
				$message = '<div class="error">'.dol_escape_htmltag($langs->trans("ErrorTechnicalError")).'</div>';
			} else {
				$resverify = atmVerifyPasswordResetHash($edituser->pass_temp, $edituser->id, $passworduidhash);
				if ($resverify < 0) {
					$langs->load("errors");
					$message = '<div class="error">'.$langs->trans("PasswordResetLinkExpired").'</div>';
				} elseif ($resverify == 0) {
					$langs->load("errors");
					$message = '<div class="error">'.$langs->trans("ErrorFailedToValidatePasswordReset").'</div>';
				} else {
					$res = $edituser->setPassword($user, $newpass1, 0);	// validates against the active password policy
					if (is_int($res) && $res < 0) {
						$message = '<div class="error">'.dol_escape_htmltag($edituser->error ? $edituser->error : $langs->trans("ErrorFailedToChangePassword")).'</div>';
					} else {
						unset($_SESSION['dol_login']);
						dol_syslog("passwordforgotten.php new user-chosen password for user->id=".$edituser->id." set in database");

						// Stay on this page and invite the user to sign in. Redirecting to the app with
						// ?username= makes main.inc.php treat it as a login submit with an empty password,
						// so the success message was replaced by a "bad credentials" error.
						$passwordchanged = 1;
						$message = '<div class="ok">'.$langs->trans("NewPasswordValidated").'</div>';

						if (getDolGlobalString('URL_REDIRECTION_AFTER_CHANGEPASSWORD')) {
							$_SESSION['dol_loginmesg'] = '<!-- warning -->'.$langs->transnoentitiesnoconv("NewPasswordValidated");
							header("Location: ".dol_sanitizeUrl(getDolGlobalString('URL_REDIRECTION_AFTER_CHANGEPASSWORD'), 0));
							exit;
						}
					}
				}
			}
		}
	}
	/**FIN SPECIFIQUE ATM**/

	// Validate new password
	if ($action == 'validatenewpassword' && $username && $passworduidhash) {	// Test on permission not required here. Security is managed by $passworduihash
		$edituser = new User($db);
		$result = $edituser->fetch(0, $username, '', 0, $conf->entity);
		if ($result < 0) {
			$message = '<div class="error">'.dol_escape_htmltag($langs->trans("ErrorTechnicalError")).'</div>';
		} else {
			global $conf;

			//print $edituser->pass_temp.'-'.$edituser->id.'-'.$conf->file->instance_unique_id.' '.$passworduidhash;
			if ($edituser->pass_temp && dol_verifyHash($edituser->pass_temp.'-'.$edituser->id.'-'.$conf->file->instance_unique_id, $passworduidhash)) {
				// Clear session
				unset($_SESSION['dol_login']);
				$_SESSION['dol_loginmesg'] = '<!-- warning -->'.$langs->transnoentitiesnoconv('NewPasswordValidated'); // Save message for the session page

				$newpassword = $edituser->setPassword($user, $edituser->pass_temp, 0);
				dol_syslog("passwordforgotten.php new password for user->id=".$edituser->id." validated in database");

				header("Location: ".DOL_URL_ROOT.'/?username='.urlencode($edituser->login));
				exit;
			} else {
				$langs->load("errors");
				$message = '<div class="error">'.$langs->trans("ErrorFailedToValidatePasswordReset").'</div>';
			}
		}
	}

	// Action to set a temporary password and send email for reset
	if ($action == 'buildnewpassword' && $username) {	// Test on permission not required here. This action is done anonymously.
		/**DEBUT SPECIFIQUE ATM password-reset-native**/
		// Backport of core PR #39370: validate with the active captcha handler instead of the raw
		// session value, so a custom handler is honoured. Native in v25: delete this block and
		// restore the two core lines.
		// $disabled belongs to the test: this action stays unreachable while the feature is off.
		// Coeur d'origine :
		// $sessionkey = 'dol_antispam_value';
		// $ok = (array_key_exists($sessionkey, $_SESSION) && (strtolower($_SESSION[$sessionkey]) == strtolower(GETPOST('code'))));
		$ok = ($disabled === '' && atmVerifyCaptchaCode($captcha));
		/**FIN SPECIFIQUE ATM**/

		// Verify code
		if (!$ok) {
			dol_syslog('Bad value for code, password reset refused', LOG_NOTICE);

			$message = '<div class="error">'.$langs->trans("ErrorBadValueForCode").'</div>';
		} else {
			$isanemail = preg_match('/@/', $username);

			$edituser = new User($db);
			$result = $edituser->fetch(0, $username, '', 1, $conf->entity);
			if ($result == 0 && $isanemail) {
				$result = $edituser->fetch(0, '', '', 1, $conf->entity, $username);
			}

			// If the user does not have the right to change his own password
			if ($result > 0) {
				$edituser->loadRights('user');
				if (!$edituser->hasRight('user', 'self', 'password')) {
					$result = 0;
					$edituser->error = 'USERNOTALLOWEDTOCHANGEPASS';
				}
			}

			// Set the message to show (must be the same if login/email exists or not to avoid to guess them.
			$messagewarning = '<div class="warning paddingtopbottom'.(!getDolGlobalString('MAIN_LOGIN_BACKGROUND') ? '' : ' backgroundsemitransparent boxshadow').'">';
			if (!$isanemail) {
				$messagewarning .= $langs->trans("IfLoginExistPasswordRequestSent");
			} else {
				$messagewarning .= $langs->trans("IfEmailExistPasswordRequestSent");
			}
			$messagewarning .= '</div>';

			if ($result <= 0 && ($edituser->error == 'USERNOTFOUND' || $edituser->error == 'USERNOTALLOWEDTOCHANGEPASS')) {
				usleep(20000);	// add delay to simulate setPassword() and send_password() actions delay (0.02s)
				$message .= $messagewarning;
				$username = '';
			} else {
				if (empty($edituser->email)) {
					usleep(20000);	// add delay to simulate setPassword() and send_password() actions delay (0.02s)
					$message .= $messagewarning;
				} else {
					/**DEBUT SPECIFIQUE ATM password-reset-native**/
					// Backport of core PR #39370: arm an expiring token, set no password.
					// Native in v25: delete this block and restore the core line.
					// Coeur d'origine : $newpassword = $edituser->setPassword($user, '', 1);
					$newpassword = atmRequestPasswordReset($edituser);
					/**FIN SPECIFIQUE ATM**/
					if (is_int($newpassword) && $newpassword < 0) {
						// Technical failure
						$message = '<div class="error">'.$langs->trans("ErrorFailedToChangePassword").'</div>';
					} else {
						// Success to set temporary password, send email
						if ($edituser->send_password($user, $newpassword, 1) > 0) {
							$message .= $messagewarning;
							$username = '';
						} else {
							// Technical failure
							$message .= '<div class="error">'.$edituser->error.'</div>';
						}
					}
				}
			}
		}
	}
}


/*
 * View
 */

$dol_url_root = DOL_URL_ROOT;

$appli = constant('DOL_APPLICATION_TITLE');
$applicustom = getDolGlobalString('MAIN_APPLICATION_TITLE');
if ($applicustom) {
	$appli = (preg_match('/^\+/', $applicustom) ? $appli : '').$applicustom;
} else {
	$appli .= " ".DOL_VERSION;
}

// Title
$title = $appli;	// $title is used in .tpl file

// Select templates dir
$template_dir = '';
if (!empty($conf->modules_parts['tpl'])) {	// Using this feature slow down application
	$dirtpls = array_merge($conf->modules_parts['tpl'], array('/core/tpl/'));
	foreach ($dirtpls as $reldir) {
		$tmp = dol_buildpath($reldir.'passwordforgotten.tpl.php');
		if (file_exists($tmp)) {
			$template_dir = preg_replace('/passwordforgotten\.tpl\.php$/', '', $tmp);
			break;
		}
	}
} elseif (file_exists(DOL_DOCUMENT_ROOT."/theme/".$conf->theme."/tpl/passwordforgotten.tpl.php")) {
	$template_dir = DOL_DOCUMENT_ROOT."/theme/".$conf->theme."/tpl/";
} else {
	$template_dir = DOL_DOCUMENT_ROOT."/core/tpl/";
}

if (!$username) {
	$focus_element = 'username';
} else {
	$focus_element = 'password';
}

// Show logo (search in order: small company logo, large company logo, theme logo, common logo)
$width = 0;
$rowspan = 2;
$urllogo = DOL_URL_ROOT.'/theme/common/login_logo.png';
if (!empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small)) {
	$urllogo = DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_small);
} elseif (!empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo)) {
	$urllogo = DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/'.$mysoc->logo);
	$width = 128;
} elseif (is_readable(DOL_DOCUMENT_ROOT.'/theme/'.$conf->theme.'/img/dolibarr_logo.svg')) {
	$urllogo = DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/dolibarr_logo.svg';
} elseif (is_readable(DOL_DOCUMENT_ROOT.'/theme/dolibarr_logo.svg')) {
	$urllogo = DOL_URL_ROOT.'/theme/dolibarr_logo.svg';
}

// Send password button enabled ?
$disabled = 'disabled';
if (preg_match('/dolibarr/i', $mode)) {
	$disabled = '';
}
if (getDolGlobalString('MAIN_SECURITY_ENABLE_SENDPASSWORD')) {
	$disabled = ''; // To force button enabled
}

// Security graphical code
$captcha = '';
if (!$disabled) {
	/**DEBUT SPECIFIQUE ATM password-reset-native**/
	// The form and the action must resolve the same handler, or the code can never validate.
	// Coeur d'origine : $captcha = getDolGlobalString('MAIN_SECURITY_ENABLECAPTCHA_HANDLER', 'standard');
	$captcha = atmGetPasswordResetCaptchaHandler();
	/**FIN SPECIFIQUE ATM**/
}

// Execute hook getPasswordForgottenPageOptions (for table)
$parameters = array('entity' => GETPOSTINT('entity'));
$hookmanager->executeHooks('getPasswordForgottenPageOptions', $parameters); // Note that $action and $object may have been modified by some hooks
if (is_array($hookmanager->resArray) && !empty($hookmanager->resArray)) {
	$morelogincontent = $hookmanager->resArray; // (deprecated) For compatibility
} else {
	$morelogincontent = $hookmanager->resPrint;
}

// Execute hook getPasswordForgottenPageExtraOptions (eg for js)
$parameters = array('entity' => GETPOSTINT('entity'));
$reshook = $hookmanager->executeHooks('getPasswordForgottenPageExtraOptions', $parameters); // Note that $action and $object may have been modified by some hooks.
$moreloginextracontent = $hookmanager->resPrint;

if (empty($setnewpassword)) {
	include $template_dir.'passwordforgotten.tpl.php'; // To use native PHP
} else {
	/**DEBUT SPECIFIQUE ATM password-reset-native**/
	// Backport of core PR #39370. Native in v25: delete this block and restore the core line.
	// Coeur d'origine : include $template_dir.'passwordreset.tpl.php';
	include DOL_DOCUMENT_ROOT.'/core/tpl/atm_passwordreset.tpl.php'; // To use native PHP
	/**FIN SPECIFIQUE ATM**/
}

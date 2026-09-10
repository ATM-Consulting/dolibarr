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
 *	\file		test/phpunit/AtmPasswordResetTest.php
 *	\ingroup	test
 *	\brief		SPECIFIQUE ATM password-reset-native - covers the backport of core PR #39370.
 *				Delete this file on the v25 upgrade, the core ships its own PasswordResetTest.
 */

global $conf, $user, $langs, $db;

if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', '1');
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1');
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}
if (!defined("NOLOGIN")) {
	define("NOLOGIN", '1');
}
if (!defined("NOSESSION")) {
	define("NOSESSION", '1');
}

require_once dirname(__FILE__).'/../../htdocs/main.inc.php';
require_once dirname(__FILE__).'/../../htdocs/core/lib/atm_passwordreset.lib.php';
require_once dirname(__FILE__).'/../../htdocs/user/class/user.class.php';
require_once dirname(__FILE__).'/CommonClassTest.class.php';

if (empty($user->id)) {
	$user->fetch(1);
	$user->loadRights();
}
$conf->global->MAIN_DISABLE_ALL_MAILS = 1;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 */
class AtmPasswordResetTest extends CommonClassTest
{
	/**
	 * Hash round-trip: possession OK, wrong hash and wrong user rejected.
	 *
	 * @return void
	 */
	public function testResetHashRoundtrip()
	{
		global $conf;
		$conf = $this->savconf;

		$secret = 'r:20990101000000:abcDEF123456';	// far-future expiry
		$hash = atmGetPasswordResetHash($secret, 1);

		$this->assertNotEmpty($hash);
		$this->assertSame(1, atmVerifyPasswordResetHash($secret, 1, $hash), 'valid possession');
		$this->assertSame(0, atmVerifyPasswordResetHash($secret, 1, 'garbagehash'), 'bad hash');
		$this->assertSame(0, atmVerifyPasswordResetHash($secret, 2, $hash), 'wrong user id');
		$this->assertSame(0, atmVerifyPasswordResetHash('', 1, $hash), 'empty secret');
	}

	/**
	 * The hash travels in an URL: it must stay deterministic and free of special chars,
	 * even when the instance hashes passwords with password_hash.
	 *
	 * @return void
	 */
	public function testResetHashIsUrlSafe()
	{
		global $conf;
		$conf = $this->savconf;

		$savalgo = getDolGlobalString('MAIN_SECURITY_HASH_ALGO');
		$conf->global->MAIN_SECURITY_HASH_ALGO = 'password_hash';

		$secret = 'r:20990101000000:abcDEF123456';
		$hash = atmGetPasswordResetHash($secret, 1);

		$this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $hash, 'no char that GETPOST(aZ09) would strip');
		$this->assertSame($hash, atmGetPasswordResetHash($secret, 1), 'hash is deterministic');
		$this->assertSame(1, atmVerifyPasswordResetHash($secret, 1, $hash), 'verify still matches');

		$conf->global->MAIN_SECURITY_HASH_ALGO = $savalgo;
	}

	/**
	 * Expired token is rejected with -1.
	 *
	 * @return void
	 */
	public function testResetHashExpired()
	{
		global $conf;
		$conf = $this->savconf;

		$secret = 'r:20000101000000:abcDEF123456';	// past expiry
		$hash = atmGetPasswordResetHash($secret, 1);

		$this->assertSame(-1, atmVerifyPasswordResetHash($secret, 1, $hash));
	}

	/**
	 * Legacy pass_temp (no r: prefix) never expires (backward-compat).
	 *
	 * @return void
	 */
	public function testResetHashLegacyNoTtl()
	{
		global $conf;
		$conf = $this->savconf;

		$secret = 'legacyplaintemp';
		$hash = atmGetPasswordResetHash($secret, 1);

		$this->assertSame(1, atmVerifyPasswordResetHash($secret, 1, $hash));
	}

	/**
	 * Full arm -> verify -> confirm cycle on a real user.
	 *
	 * @return void
	 */
	public function testRequestPasswordResetCycle()
	{
		global $conf, $user, $db;
		$conf = $this->savconf;

		$tmp = new User($db);
		$tmp->login = 'phpunit_pwdreset_'.dol_print_date(dol_now(), 'dayhourlog');
		$tmp->lastname = 'PwdResetTest';
		$tmp->email = 'phpunit_pwdreset@example.com';
		$idcreated = $tmp->create($user);
		$this->assertGreaterThan(0, $idcreated, 'user created');

		$armed = atmRequestPasswordReset($tmp, 3600);
		$this->assertIsString($armed);
		$this->assertStringStartsWith('r:', $armed);

		$reloaded = new User($db);
		$reloaded->fetch($idcreated);
		$this->assertSame($armed, $reloaded->pass_temp, 'pass_temp stored verbatim');

		$hash = atmGetPasswordResetHash($reloaded->pass_temp, $reloaded->id);
		$this->assertSame(1, atmVerifyPasswordResetHash($reloaded->pass_temp, $reloaded->id, $hash));

		$res = $reloaded->setPassword($user, 'Chosen-Passw0rd!2026', 0);
		$this->assertFalse(is_int($res) && $res < 0, 'setPassword accepted the chosen password: '.$reloaded->error);

		$after = new User($db);
		$after->fetch($idcreated);
		$this->assertEmpty($after->pass_temp, 'pass_temp cleared after confirm');
	}

	/**
	 * The reset email body carries the link and never a cleartext password.
	 *
	 * @return void
	 */
	public function testResetEmailBodyIsLinkOnly()
	{
		global $conf, $langs;
		$conf = $this->savconf;

		$link = 'https://portal.example.com/user/passwordforgotten.php?setnewpassword=1&username=admin&passworduidhash=DEADBEEF';
		$body = atmGetPasswordResetEmailContent($langs, $link);

		$this->assertStringContainsString(dol_escape_htmltag($link), $body, 'body contains the reset link (html escaped)');
		$this->assertStringContainsString('passworduidhash=DEADBEEF', $body, 'link hash present');
		$this->assertStringNotContainsString('Password = ', $body, 'no cleartext password label');
	}

	/**
	 * A handler constant stored empty must not disable the captcha.
	 *
	 * @return void
	 */
	public function testCaptchaHandlerNeverEmpty()
	{
		global $conf;
		$conf = $this->savconf;

		$sav = isset($conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER) ? $conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER : null;

		unset($conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER);
		$this->assertSame('standard', atmGetPasswordResetCaptchaHandler(), 'absent constant falls back to standard');

		$conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER = '';
		$this->assertSame('standard', atmGetPasswordResetCaptchaHandler(), 'empty constant falls back to standard');

		$conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER = 'mycustom';
		$this->assertSame('mycustom', atmGetPasswordResetCaptchaHandler(), 'configured handler is honoured');

		if (is_null($sav)) {
			unset($conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER);
		} else {
			$conf->global->MAIN_SECURITY_ENABLECAPTCHA_HANDLER = $sav;
		}
	}

	/**
	 * Without a handler there is nothing to validate against, so the code is refused.
	 *
	 * @return void
	 */
	public function testCaptchaVerificationFailsClosed()
	{
		global $conf;
		$conf = $this->savconf;

		$this->assertFalse(atmVerifyCaptchaCode(''), 'no handler refuses the code');
		$this->assertFalse(atmVerifyCaptchaCode('nosuchhandler'), 'unknown handler refuses the code');
	}
}

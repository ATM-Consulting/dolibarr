<?php

/**
 * If user select border on left & right in Dolibarr screen settings
 */

if (!empty($conf->global->THEME_ELDY_USEBORDERONTABLE)) {
	if ($conf->global->THEME_ELDY_USEBORDERONTABLE) {
		// if (!empty(getDolGlobalInt('THEME_ELDY_USEBORDERONTABLE'))){
		require __DIR__ . '/custom-scrollbar.css';
	}
}

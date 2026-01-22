<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_EXTENDEDCONTRACT')) {
		require __DIR__ . '/extendedcontract.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_EXTENDEDCONTRACT)) {
		if ($conf->global->MAIN_MODULE_EXTENDEDCONTRACT) {
			require __DIR__ . '/extendedcontract.css';
		}
	}
}
// echo file_get_contents( __DIR__ . '/breadcrumb.css');

<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_GESTIONPARC')) {
		require __DIR__ . '/devices_listing.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_GESTIONPARC)) {
		if ($conf->global->MAIN_MODULE_GESTIONPARC) {
			require __DIR__ . '/devices_listing.css';
		}
	}
}
// echo file_get_contents( __DIR__ . '/breadcrumb.css');

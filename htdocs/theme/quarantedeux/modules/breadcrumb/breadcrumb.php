<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_BREADCRUMB')) {
		require __DIR__ . '/breadcrumb.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_BREADCRUMB)) {
		if ($conf->global->MAIN_MODULE_BREADCRUMB) {
			require __DIR__ . '/breadcrumb.css';
		}
	}
}
// echo file_get_contents( __DIR__ . '/breadcrumb.css');

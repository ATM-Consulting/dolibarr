<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_LISTINCSV')) {
		require __DIR__ . '/listincsv.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_LISTINCSV)) {
		if ($conf->global->MAIN_MODULE_LISTINCSV) {
			require __DIR__ . '/listincsv.css';
		}
	}
}

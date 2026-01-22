<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_LEAD')) {
		require __DIR__ . '/lead.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_LEAD)) {
		if ($conf->global->MAIN_MODULE_LEAD) {
			require __DIR__ . '/lead.css';
		}
	}
}

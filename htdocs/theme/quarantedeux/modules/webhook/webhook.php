<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_WEBHOOK')) {
		require __DIR__ . '/webhook.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_WEBHOOK)) {
		if ($conf->global->MAIN_MODULE_WEBHOOK) {
			require __DIR__ . '/webhook.css';
		}
	}
}

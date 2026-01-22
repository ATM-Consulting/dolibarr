<?php
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_SUBTOTAL')) {
		require __DIR__ . '/subtotal.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_SUBTOTAL)) {
		if ($conf->global->MAIN_MODULE_SUBTOTAL) {
			require __DIR__ . '/subtotal.css';
		}
	}
}

<?php

$page = $_SERVER['HTTP_REFERER'];
if (function_exists('getDolGlobalInt')) {
	if (getDolGlobalInt('MAIN_MODULE_UPTOSIGN') && preg_match('/uptosign/i', $page)) {
		require __DIR__ . '/uptosign.css';
	}
} else {
	if (!empty($conf->global->MAIN_MODULE_UPTOSIGN)) {
		if ($conf->global->MAIN_MODULE_UPTOSIGN && preg_match('/uptosign/i', $page)) {
			require __DIR__ . '/uptosignscss';
		}
	}
}
// echo file_get_contents( __DIR__ . '/breadcrumb.css');

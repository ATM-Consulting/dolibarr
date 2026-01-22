<?php
// phpcs:ignoreFile
function dark_mode_only()
{
	   require __DIR__ . '/palettes/palette-dark.css';
}

function dark_mode_detect_device()
{
	echo '@media (prefers-color-scheme: dark) {';
	require __DIR__ . '/palettes/palette-dark.css';
	echo '}';
}

function dark_mode_disabled()
{
	require __DIR__ . '/palettes/default-palette.css';
}

if (function_exists('getDolGlobalInt')) {
	if (in_array(getDolGlobalInt('THEME_DARKMODEENABLED'), array(0))) {
		dark_mode_disabled();
	}
	if (in_array(getDolGlobalInt('THEME_DARKMODEENABLED'), array(1))) {
		dark_mode_detect_device();
	}
	if (in_array(getDolGlobalInt('THEME_DARKMODEENABLED'), array(2))) {
		dark_mode_only();
	}
} else {
	dark_mode_disabled();
}

if (!empty($conf->global->THEME_DARKMODEENABLED)) {
	if ($conf->global->THEME_DARKMODEENABLED) {
		if ($conf->global->THEME_DARKMODEENABLED == 0) {
			dark_mode_disabled();
		}
		if ($conf->global->THEME_DARKMODEENABLED == 1) {
			dark_mode_detect_device();
		}
		if ($conf->global->THEME_DARKMODEENABLED == 2) {
			dark_mode_only();
		}
	} else {
		dark_mode_disabled();
	}
}

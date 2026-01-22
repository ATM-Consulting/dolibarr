<?php
/* Base depuis le thème Eldy (toutes les versions dl) */
require __DIR__.'/../../theme/eldy/style.css.php';


/* Surcharge Code 42 */
require __DIR__ . '/scss.css.php';
// #390
if (version_compare(DOL_VERSION, "20.0.0") >= 0) {
    require __DIR__ . '/style_v20.css.php';
}

if (!defined('ISLOADEDBYSTEELSHEET')) {
	die('Must be call by steelsheet');
} ?>
/* <style type="text/css" > */

<?php

/**
 * GLOBAL THEME SETTINGS
 * WIP For array of variables ...
 * Order by layout, style ...
 * Idea : create a specific function for regroup all in :root{...}
 */
$width_primary_menu = 'clamp(100px, 5vw, 150px)';
$usercolor = '';


/**
 * Call all menu variation
 * Swtich css for each value of THEME_TOPMENU_DISABLE_IMAGE
 */
require __DIR__ . '/theme-objects/option-css/option-css.php';
require __DIR__ . '/theme-objects/colors-palette/colors-palette.php';

require __DIR__ . '/theme-objects/primary-menu/menu-item-variation.php';
require __DIR__ . '/theme-objects/primary-menu/menu-position-variation.php';
require __DIR__ . '/theme-objects/custom-scrollbar/custom-scrollbar.php';
// /* Tests for new mobile layout */
require __DIR__ . '/theme-objects/mobile-optim/mobile-optim.css';
require __DIR__ . '/modules/breadcrumb/breadcrumb.php';
// echo file_get_contents( __DIR__ . '/../../theme/quarantedeux/modules/breadcrumb/breadcrumb.css');

require __DIR__ . '/modules/H2G2/H2G2.php'; // #283
require __DIR__ . '/modules/gestionparc/gestionparc.php'; // #300
require __DIR__ . '/modules/lead/lead.php'; // #289
require __DIR__ . '/modules/gestionparc/gestionparc.php'; // #300
require __DIR__ . '/modules/extendedcontract/extendedcontract.php'; // #311
require __DIR__ . '/modules/listincsv/listincsv.php'; // #313
require __DIR__ . '/modules/uptosign/uptosign.php'; // #317
require __DIR__ . '/modules/subtotal/subtotal.php'; // #341
require __DIR__ . '/modules/webhook/webhook.php'; // #415

/**
 * WIP All css--var regrouped at the end of theme
 * Todo - function for echo array of $42_theme_vars
 */

echo ':root{';
if ($width_primary_menu) { echo '--width-primary-menu:' . $width_primary_menu . ';'; }
echo '}';



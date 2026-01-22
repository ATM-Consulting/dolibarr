<?php
// phpcs:ignoreFile
global $user, $conf;

/**
 * Array of value for TC42_POSITION_MENU (1,2)
 * 1 => Classic
 * 2 => Moderne
 */

//user doesn't have custom settings
if (!isset($user->conf->TC42_POSITION_MENU)) {
    switch ($conf->global->TC42_POSITION_MENU) {
        case 2:
            leftMenu();
            break;
        default:
            topMenu();
            break;
    }
} else { //user have custom settings
    switch ($user->conf->TC42_POSITION_MENU) {
        case 2:
            leftMenu();
            break;
        default:
            topMenu();
            break;
    }
}

function leftMenu()
{
    require __DIR__ . '/menu-position-variation/left-menu.css';
}

function topMenu()
{
    require __DIR__ . '/menu-position-variation/top-menu.css';
}
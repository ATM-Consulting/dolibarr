<?php


/* TOP MENU VARIATIONS -> THEME_TOPMENU_DISABLE_IMAGE */
/**
 *

 * Array of value for THEME_TOPMENU_DISABLE_IMAGE (0,1,2,3,4)
 * 0 => Icône et texte
 * 1 => Texte seulement
 * 2 => Icône uniquement - Tous les textes apparaissent sous l'icône sur la barre de menu du survol de la souris
 * 3 => Icône uniquement - Le texte de l'icône apparaît sous l'icône à la souris survolez l'icône
 * 4 => Icône uniquement - Texte sur l'info-bulle uniquement
 */


/* DEFAULT ICON */
require __DIR__ . '/menu-item-variation/default-menu-item.css';

if (function_exists('getDolGlobalInt')) {
	switch (getDolGlobalInt('THEME_TOPMENU_DISABLE_IMAGE')) {
		case 1:
			$width_primary_menu = 'clamp(150px, 8vw, 200px)';
			require __DIR__ . '/menu-item-variation/1-text-only.css';
			break;
		case 2:
			if (getDolGlobalInt('TC42_POSITION_MENU') != 1) {
				if (!isset($user->conf->TC42_POSITION_MENU) || $user->conf->TC42_POSITION_MENU != "1") {
					$width_primary_menu = 'clamp(60px, 4vw, 100px)';
					require __DIR__ . '/menu-item-variation/2-all-label-hover.css';
					break;
				}
			}
		case 3:
			$width_primary_menu = 'clamp(60px, 4vw, 100px)';
			require __DIR__ . '/menu-item-variation/3-icon-hover-label.css';
			break;
		case 4:
			$width_primary_menu = 'clamp(60px, 4vw, 100px)';
			require __DIR__ . '/menu-item-variation/4-icon-only.css';
			break;
		default:
			break;
	}
}

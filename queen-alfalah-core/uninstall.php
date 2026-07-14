<?php
/**
 * Queen Al-Falah Core uninstall routine.
 *
 * School content, terms, menus, media references, and settings are intentionally
 * retained. Removing a presentation/helper plugin must never erase institutional
 * records. Administrators who need permanent deletion should first export a
 * backup, then remove the data explicitly with WordPress administration tools.
 *
 * @package Queen_Alfalah_Core
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Deliberately no destructive operation. All plugin data is preserved.


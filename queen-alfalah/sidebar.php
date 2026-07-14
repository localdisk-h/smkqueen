<?php
/**
 * Optional article sidebar.
 *
 * @package Queen_AlFalah
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Informasi tambahan', 'queen-alfalah' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>

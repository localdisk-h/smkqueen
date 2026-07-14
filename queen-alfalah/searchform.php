<?php
/**
 * Accessible search form.
 *
 * @package Queen_AlFalah
 */
?>
<?php $queen_search_id = wp_unique_id( 'site-search-' ); ?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $queen_search_id ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Cari:', 'queen-alfalah' ); ?></span>
		<input id="<?php echo esc_attr( $queen_search_id ); ?>" type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Cari berita, program, atau informasi…', 'placeholder', 'queen-alfalah' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit" class="search-submit"><?php echo queen_alfalah_icon( 'search' ); ?><span><?php echo esc_html_x( 'Cari', 'submit button', 'queen-alfalah' ); ?></span></button>
</form>

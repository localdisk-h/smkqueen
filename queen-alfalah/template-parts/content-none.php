<?php
/**
 * Empty-content state.
 *
 * @package Queen_AlFalah
 */

$queen_empty_title = is_search() ? __( 'Belum ada hasil yang cocok', 'queen-alfalah' ) : __( 'Belum ada informasi', 'queen-alfalah' );
$queen_empty_text  = is_search()
	? __( 'Periksa ejaan, gunakan kata kunci yang lebih singkat, atau cari topik lain.', 'queen-alfalah' )
	: __( 'Konten untuk bagian ini belum diterbitkan. Silakan kembali lagi nanti atau jelajahi halaman lain.', 'queen-alfalah' );
?>

<section class="nothing-found" aria-labelledby="nothing-found-title">
	<p class="eyebrow"><?php esc_html_e( 'Informasi', 'queen-alfalah' ); ?></p>
	<h2 id="nothing-found-title"><?php echo esc_html( $queen_empty_title ); ?></h2>
	<p><?php echo esc_html( $queen_empty_text ); ?></p>
	<?php get_search_form(); ?>
	<p><a class="button button--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Kembali ke Beranda', 'queen-alfalah' ); ?></a></p>
</section>

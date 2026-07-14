<?php
/**
 * Not-found template.
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();
?>

<main id="main-content" class="site-main">
	<div class="container content-area">
		<section class="error-404 not-found empty-state" aria-labelledby="error-title">
			<p class="eyebrow"><?php esc_html_e( 'Galat 404', 'queen-alfalah' ); ?></p>
			<h1 id="error-title"><?php esc_html_e( 'Halaman tidak ditemukan', 'queen-alfalah' ); ?></h1>
			<p><?php esc_html_e( 'Alamat mungkin berubah, halaman sudah dipindahkan, atau tautan yang dibuka tidak lengkap. Coba pencarian atau pilih tujuan berikut.', 'queen-alfalah' ); ?></p>

			<?php get_search_form(); ?>

			<nav aria-label="<?php esc_attr_e( 'Tautan pemulihan', 'queen-alfalah' ); ?>">
				<ul class="quick-links">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo queen_alfalah_icon( 'building' ); ?><span><?php esc_html_e( 'Kembali ke Beranda', 'queen-alfalah' ); ?></span></a></li>
					<li><a href="<?php echo esc_url( queen_alfalah_archive_url( 'qaf_program', 'program-keahlian' ) ); ?>"><?php echo queen_alfalah_icon( 'book' ); ?><span><?php esc_html_e( 'Program Keahlian', 'queen-alfalah' ); ?></span></a></li>
					<li><a href="<?php echo esc_url( queen_alfalah_page_url( 'ppdb' ) ); ?>"><?php echo queen_alfalah_icon( 'users' ); ?><span><?php esc_html_e( 'Informasi PPDB', 'queen-alfalah' ); ?></span></a></li>
					<li><a href="<?php echo esc_url( queen_alfalah_page_url( 'kontak' ) ); ?>"><?php echo queen_alfalah_icon( 'phone' ); ?><span><?php esc_html_e( 'Hubungi Sekolah', 'queen-alfalah' ); ?></span></a></li>
				</ul>
			</nav>
		</section>
	</div>
</main>

<?php
get_footer();

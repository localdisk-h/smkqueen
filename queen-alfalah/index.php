<?php
/**
 * Fallback template.
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();
?>

<main id="main-content" class="site-main">
	<header class="archive-header">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Informasi Sekolah', 'queen-alfalah' ); ?></p>
			<h1><?php esc_html_e( 'Informasi Terbaru', 'queen-alfalah' ); ?></h1>
			<p><?php esc_html_e( 'Berita, kegiatan, dan pembaruan dari SMK Queen Al-Falah.', 'queen-alfalah' ); ?></p>
		</div>
	</header>

	<div class="<?php echo esc_attr( 'container content-area' . ( is_active_sidebar( 'sidebar-1' ) ? ' content-area--with-sidebar' : '' ) ); ?>">
		<div>
			<?php if ( have_posts() ) : ?>
				<div class="archive-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'mid_size'           => 1,
						'prev_text'          => __( 'Sebelumnya', 'queen-alfalah' ),
						'next_text'          => __( 'Berikutnya', 'queen-alfalah' ),
						'screen_reader_text' => __( 'Navigasi halaman informasi', 'queen-alfalah' ),
					)
				);
				?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</main>

<?php
get_footer();

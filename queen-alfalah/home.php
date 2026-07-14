<?php
/**
 * Posts page.
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();

$posts_page_id = (int) get_option( 'page_for_posts' );
$page_title    = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Berita & Informasi', 'queen-alfalah' );
$page_intro    = $posts_page_id ? get_post_field( 'post_excerpt', $posts_page_id ) : '';
$page_intro    = $page_intro ? $page_intro : __( 'Ikuti kabar, kegiatan, prestasi, dan informasi terbaru sekolah.', 'queen-alfalah' );
?>

<main id="main-content" class="site-main">
	<header class="archive-header">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Kabar Queen', 'queen-alfalah' ); ?></p>
			<h1><?php echo esc_html( $page_title ); ?></h1>
			<p><?php echo esc_html( wp_strip_all_tags( $page_intro ) ); ?></p>
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
						'screen_reader_text' => __( 'Navigasi halaman berita', 'queen-alfalah' ),
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

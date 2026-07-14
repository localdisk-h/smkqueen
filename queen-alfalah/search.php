<?php
/**
 * Search results template.
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();

global $wp_query;
$search_query = get_search_query();
$result_count = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
?>

<main id="main-content" class="site-main">
	<header class="archive-header">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Pencarian', 'queen-alfalah' ); ?></p>
			<h1>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: search query. */
						__( 'Hasil untuk “%s”', 'queen-alfalah' ),
						$search_query
					)
				);
				?>
			</h1>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: number of search results. */
						_n( '%s hasil ditemukan.', '%s hasil ditemukan.', $result_count, 'queen-alfalah' ),
						number_format_i18n( $result_count )
					)
				);
				?>
			</p>
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
						'screen_reader_text' => __( 'Navigasi hasil pencarian', 'queen-alfalah' ),
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

<?php
/**
 * Single post and school-content template.
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();
?>

<main id="main-content" class="site-main">
	<div class="<?php echo esc_attr( 'container content-area' . ( is_active_sidebar( 'sidebar-1' ) ? ' content-area--with-sidebar' : '' ) ); ?>">
		<div>
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'single' );

				the_post_navigation(
					array(
						'screen_reader_text' => __( 'Navigasi konten', 'queen-alfalah' ),
						'prev_text'          => '<span class="nav-subtitle">' . esc_html__( 'Sebelumnya', 'queen-alfalah' ) . '</span><span class="nav-title">%title</span>',
						'next_text'          => '<span class="nav-subtitle">' . esc_html__( 'Berikutnya', 'queen-alfalah' ) . '</span><span class="nav-title">%title</span>',
					)
				);

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</main>

<?php
get_footer();

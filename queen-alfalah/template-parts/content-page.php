<?php
/**
 * Page content.
 *
 * @package Queen_AlFalah
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-entry' ); ?>>
	<header class="entry-header">
		<p class="eyebrow"><?php esc_html_e( 'SMK Queen Al-Falah', 'queen-alfalah' ); ?></p>
		<h1 class="entry-title"><?php echo esc_html( get_the_title() ); ?></h1>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="post-thumbnail">
			<?php the_post_thumbnail( 'full', array( 'decoding' => 'async' ) ); ?>
		</figure>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Halaman konten', 'queen-alfalah' ) . '"><span>' . esc_html__( 'Halaman:', 'queen-alfalah' ) . '</span>',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				esc_html__( 'Sunting halaman', 'queen-alfalah' ),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer>
	<?php endif; ?>
</article>

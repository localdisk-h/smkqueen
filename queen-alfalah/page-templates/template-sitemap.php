<?php
/**
 * Template Name: Peta Situs
 * Template Post Type: page
 *
 * Human-readable site directory.
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();

$queen_pages = wp_list_pages(
	array(
		'title_li' => '',
		'depth'    => 3,
		'echo'     => false,
		'sort_column' => 'menu_order,post_title',
	)
);

$queen_content_archives = array();
$queen_posts_page       = (int) get_option( 'page_for_posts' );
$queen_content_archives[] = array(
	'label' => $queen_posts_page ? get_the_title( $queen_posts_page ) : __( 'Berita', 'queen-alfalah' ),
	'url'   => $queen_posts_page ? get_permalink( $queen_posts_page ) : home_url( '/berita/' ),
);

foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $queen_post_type ) {
	if ( in_array( $queen_post_type->name, array( 'post', 'page', 'attachment' ), true ) || ! $queen_post_type->has_archive ) {
		continue;
	}
	$queen_archive_url = get_post_type_archive_link( $queen_post_type->name );
	if ( $queen_archive_url ) {
		$queen_content_archives[] = array( 'label' => $queen_post_type->labels->name, 'url' => $queen_archive_url );
	}
}

$queen_term_groups = array();
foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $queen_taxonomy ) {
	if ( 'post_format' === $queen_taxonomy->name ) {
		continue;
	}
	$queen_terms = get_terms(
		array(
			'taxonomy'   => $queen_taxonomy->name,
			'hide_empty' => true,
		)
	);
	if ( $queen_terms && ! is_wp_error( $queen_terms ) ) {
		$queen_term_groups[] = array( 'label' => $queen_taxonomy->labels->name, 'terms' => $queen_terms );
	}
}
?>

<main id="main-content" class="site-main">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="page-header">
			<div class="container">
				<p class="eyebrow"><?php esc_html_e( 'Direktori', 'queen-alfalah' ); ?></p>
				<h1><?php echo esc_html( get_the_title() ); ?></h1>
				<p><?php esc_html_e( 'Jelajahi halaman, informasi, dan kelompok konten yang tersedia di situs sekolah.', 'queen-alfalah' ); ?></p>
			</div>
		</header>

		<section class="section" aria-label="<?php esc_attr_e( 'Daftar isi situs', 'queen-alfalah' ); ?>">
			<div class="container">
				<?php if ( trim( get_the_content() ) ) : ?><div class="entry-content"><?php the_content(); ?></div><?php endif; ?>

				<div class="card-grid card-grid--3">
					<section class="widget">
						<h2 class="widget-title"><?php esc_html_e( 'Halaman', 'queen-alfalah' ); ?></h2>
						<?php if ( $queen_pages ) : ?><ul><?php echo wp_kses_post( $queen_pages ); ?></ul><?php else : ?><p><?php esc_html_e( 'Belum ada halaman publik.', 'queen-alfalah' ); ?></p><?php endif; ?>
					</section>

					<section class="widget">
						<h2 class="widget-title"><?php esc_html_e( 'Informasi Sekolah', 'queen-alfalah' ); ?></h2>
						<ul>
							<?php foreach ( $queen_content_archives as $queen_archive ) : ?>
								<li><a href="<?php echo esc_url( $queen_archive['url'] ); ?>"><?php echo esc_html( $queen_archive['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</section>

					<section class="widget">
						<h2 class="widget-title"><?php esc_html_e( 'Arsip Berita', 'queen-alfalah' ); ?></h2>
						<ul><?php wp_get_archives( array( 'type' => 'monthly', 'limit' => 18 ) ); ?></ul>
					</section>
				</div>

				<?php if ( $queen_term_groups ) : ?>
					<section class="section section--compact" aria-labelledby="topic-directory-title">
						<h2 id="topic-directory-title"><?php esc_html_e( 'Topik dan Kategori', 'queen-alfalah' ); ?></h2>
						<div class="card-grid card-grid--3">
							<?php foreach ( $queen_term_groups as $queen_group ) : ?>
								<section class="widget">
									<h3 class="widget-title"><?php echo esc_html( $queen_group['label'] ); ?></h3>
									<ul>
										<?php foreach ( $queen_group['terms'] as $queen_term ) : ?>
											<?php $queen_term_url = get_term_link( $queen_term ); ?>
											<?php if ( ! is_wp_error( $queen_term_url ) ) : ?><li><a href="<?php echo esc_url( $queen_term_url ); ?>"><?php echo esc_html( $queen_term->name ); ?></a></li><?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</section>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>
			</div>
		</section>
	<?php endwhile; ?>
</main>

<?php
get_footer();

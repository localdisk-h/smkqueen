<?php
/**
 * Site footer.
 *
 * @package Queen_AlFalah
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$socials       = queen_alfalah_social_links();
$custom_logo_id = absint( get_theme_mod( 'custom_logo', 0 ) );
?>
	<footer id="colophon" class="site-footer">
		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
			<div class="footer-widgets">
				<div class="container footer-grid footer-grid--widgets">
					<?php dynamic_sidebar( 'footer-1' ); ?>
					<?php dynamic_sidebar( 'footer-2' ); ?>
					<?php dynamic_sidebar( 'footer-3' ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="footer-main site-footer__main">
			<div class="container footer-grid">
				<div class="footer-about">
					<a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php if ( $custom_logo_id ) : ?>
							<?php echo wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'footer-logo', 'alt' => queen_alfalah_school_info( 'school_name' ) ) ); ?>
						<?php else : ?>
							<img class="footer-logo" src="<?php echo esc_url( QUEEN_ALFALAH_URI . '/assets/images/brand-mark.svg' ); ?>" width="64" height="64" alt="">
						<?php endif; ?>
						<span><strong><?php echo esc_html( queen_alfalah_school_info( 'school_name' ) ); ?></strong><small><?php echo esc_html( queen_alfalah_school_info( 'motto' ) ); ?></small></span>
					</a>
					<p><?php echo esc_html( get_theme_mod( 'queen_footer_note', __( 'Pendidikan vokasi berbasis pesantren untuk generasi yang kompeten dan berkarakter.', 'queen-alfalah' ) ) ); ?></p>
					<?php if ( $socials ) : ?>
						<div class="social-links" aria-label="<?php esc_attr_e( 'Media sosial sekolah', 'queen-alfalah' ); ?>">
							<?php foreach ( $socials as $network => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>"><?php echo queen_alfalah_icon( $network ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="footer-column">
					<h2><?php esc_html_e( 'Jelajahi', 'queen-alfalah' ); ?></h2>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => 'queen_alfalah_menu_fallback',
						)
					);
					?>
				</div>

				<div class="footer-column">
					<h2><?php esc_html_e( 'Layanan', 'queen-alfalah' ); ?></h2>
					<?php if ( has_nav_menu( 'services' ) ) : ?>
						<?php wp_nav_menu( array( 'theme_location' => 'services', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); ?>
					<?php else : ?>
						<ul>
							<li><a href="<?php echo esc_url( queen_alfalah_school_info( 'ppdb_url' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Pendaftaran/PPDB', 'queen-alfalah' ); ?></a></li>
							<li><a href="<?php echo esc_url( queen_alfalah_archive_url( 'qaf_service', 'aplikasi' ) ); ?>"><?php esc_html_e( 'Pusat Aplikasi', 'queen-alfalah' ); ?></a></li>
							<li><a href="<?php echo esc_url( queen_alfalah_archive_url( 'qaf_vacancy', 'lowongan' ) ); ?>"><?php esc_html_e( 'Bursa Kerja', 'queen-alfalah' ); ?></a></li>
							<li><a href="<?php echo esc_url( queen_alfalah_page_url( 'kontak' ) ); ?>"><?php esc_html_e( 'Kontak & Pengaduan', 'queen-alfalah' ); ?></a></li>
						</ul>
					<?php endif; ?>
				</div>

				<div class="footer-column footer-contact">
					<h2><?php esc_html_e( 'Hubungi Sekolah', 'queen-alfalah' ); ?></h2>
					<address>
						<p><?php echo queen_alfalah_icon( 'map-pin' ); ?><a href="<?php echo esc_url( queen_alfalah_school_info( 'maps_url' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( queen_alfalah_school_info( 'address' ) ); ?></a></p>
						<p><?php echo queen_alfalah_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', queen_alfalah_school_info( 'phone' ) ) ); ?>"><?php echo esc_html( queen_alfalah_school_info( 'phone' ) ); ?></a></p>
						<p><?php echo queen_alfalah_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( antispambot( queen_alfalah_school_info( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( queen_alfalah_school_info( 'email' ) ) ); ?></a></p>
						<p><?php echo queen_alfalah_icon( 'clock' ); ?><span><?php echo esc_html( queen_alfalah_school_info( 'opening_hours' ) ); ?></span></p>
					</address>
				</div>
			</div>
		</div>

		<div class="footer-bottom site-footer__bottom">
			<div class="container footer-bottom__inner site-footer__bottom-inner">
				<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html__( 'Beneficia Tech. Hak cipta dilindungi.', 'queen-alfalah' ); ?></p>
				<p><?php echo esc_html( sprintf( __( 'NPSN %1$s · Akreditasi %2$s', 'queen-alfalah' ), queen_alfalah_school_info( 'npsn' ), queen_alfalah_school_info( 'accreditation' ) ) ); ?></p>
			</div>
		</div>
	</footer>

	<?php if ( get_theme_mod( 'queen_show_whatsapp', true ) && queen_alfalah_school_info( 'whatsapp' ) ) : ?>
		<a class="floating-whatsapp" href="<?php echo esc_url( queen_alfalah_whatsapp_url( 'Assalamu’alaikum, saya ingin memperoleh informasi tentang SMK Queen Al-Falah.' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Hubungi SMK Queen Al-Falah melalui WhatsApp', 'queen-alfalah' ); ?>">
			<?php echo queen_alfalah_icon( 'whatsapp' ); ?><span><?php esc_html_e( 'Tanya Queen', 'queen-alfalah' ); ?></span>
		</a>
	<?php endif; ?>

	<button class="back-to-top" type="button" aria-label="<?php esc_attr_e( 'Kembali ke atas', 'queen-alfalah' ); ?>" hidden><?php echo queen_alfalah_icon( 'arrow-up' ); ?></button>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>

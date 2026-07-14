<?php
/**
 * Theme onboarding screen and companion-plugin reminder.
 *
 * @package Queen_AlFalah
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a small, capability-scoped setup page.
 */
function queen_alfalah_admin_menu() {
	add_theme_page(
		__( 'Queen Al-Falah — Mulai', 'queen-alfalah' ),
		__( 'Queen Al-Falah', 'queen-alfalah' ),
		'edit_theme_options',
		'queen-alfalah',
		'queen_alfalah_welcome_page'
	);
}
add_action( 'admin_menu', 'queen_alfalah_admin_menu' );

/**
 * Render onboarding page.
 */
function queen_alfalah_welcome_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$core_active = function_exists( 'qaf_core_get_setting' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Selamat datang di Queen Al-Falah', 'queen-alfalah' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Tema portal sekolah vokasi berbasis pesantren untuk SMK QUEEN AL-FALAH.', 'queen-alfalah' ); ?></p>
		<div class="notice <?php echo $core_active ? 'notice-success' : 'notice-warning'; ?> inline">
			<p>
				<strong><?php echo $core_active ? esc_html__( 'Queen Al-Falah Core aktif.', 'queen-alfalah' ) : esc_html__( 'Queen Al-Falah Core belum aktif.', 'queen-alfalah' ); ?></strong>
				<?php echo $core_active ? esc_html__( 'Post type sekolah dan pengaturan data portabel tersedia.', 'queen-alfalah' ) : esc_html__( 'Pasang plugin pendamping dari paket unduhan untuk program keahlian, agenda, guru, layanan, dan setup demo.', 'queen-alfalah' ); ?>
			</p>
		</div>
		<h2><?php esc_html_e( 'Langkah yang disarankan', 'queen-alfalah' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Pasang dan aktifkan plugin queen-alfalah-core.zip.', 'queen-alfalah' ); ?></li>
			<li><?php esc_html_e( 'Buka menu Sekolah → Penyiapan Demo untuk membuat halaman, menu, dan data awal.', 'queen-alfalah' ); ?></li>
			<li><?php esc_html_e( 'Unggah logo dan foto sekolah di Tampilan → Sesuaikan.', 'queen-alfalah' ); ?></li>
			<li><?php esc_html_e( 'Verifikasi kontak, pimpinan, tautan PPDB, statistik, serta izin publikasi foto.', 'queen-alfalah' ); ?></li>
		</ol>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Buka Customizer', 'queen-alfalah' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Kelola Menu', 'queen-alfalah' ); ?></a>
			<?php if ( $core_active ) : ?>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=qaf-school' ) ); ?>"><?php esc_html_e( 'Pengaturan Sekolah', 'queen-alfalah' ); ?></a>
			<?php endif; ?>
		</p>
	</div>
	<?php
}

/**
 * One-time non-invasive plugin reminder after theme activation.
 */
function queen_alfalah_core_notice() {
	if ( ! current_user_can( 'install_plugins' ) || function_exists( 'qaf_core_get_setting' ) || get_user_meta( get_current_user_id(), '_queen_hide_core_notice', true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'themes', 'appearance_page_queen-alfalah' ), true ) ) {
		return;
	}
	?>
	<div class="notice notice-info is-dismissible">
		<p><strong><?php esc_html_e( 'Lengkapi tema Queen Al-Falah:', 'queen-alfalah' ); ?></strong> <?php esc_html_e( 'aktifkan plugin Queen Al-Falah Core yang disertakan dalam paket untuk fitur portal sekolah dan setup demo.', 'queen-alfalah' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'queen_alfalah_core_notice' );

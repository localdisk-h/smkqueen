<?php
/**
 * Template Name: Informasi PPDB
 * Template Post Type: page
 *
 * @package Queen_AlFalah
 */

get_header();
queen_alfalah_breadcrumbs();

$queen_ppdb_label = (string) queen_alfalah_school_info( 'ppdb_label', __( 'Pendaftaran', 'queen-alfalah' ) );
$queen_ppdb_url   = (string) queen_alfalah_school_info( 'ppdb_url' );
$queen_programs   = queen_alfalah_archive_url( 'qaf_program', 'program-keahlian' );
$queen_contact    = queen_alfalah_page_url( 'kontak' );
?>

<main id="main-content" class="site-main">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="page-header">
			<div class="container">
				<p class="eyebrow"><?php esc_html_e( 'Penerimaan Murid Baru', 'queen-alfalah' ); ?></p>
				<h1><?php echo esc_html( get_the_title() ); ?></h1>
				<p><?php esc_html_e( 'Temukan informasi program, persyaratan, jadwal, dan kanal pendaftaran resmi SMK Queen Al-Falah.', 'queen-alfalah' ); ?></p>
				<?php if ( $queen_ppdb_url ) : ?>
					<p><a class="button button--gold" href="<?php echo esc_url( $queen_ppdb_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $queen_ppdb_label ); ?><?php echo queen_alfalah_icon( 'external' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(tab baru)', 'queen-alfalah' ); ?></span></a></p>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( trim( get_the_content() ) ) : ?>
			<section class="section" aria-labelledby="ppdb-information-title">
				<div class="container">
					<h2 id="ppdb-information-title"><?php esc_html_e( 'Informasi Pendaftaran', 'queen-alfalah' ); ?></h2>
					<div class="entry-content"><?php the_content(); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<section class="section section--cream" aria-labelledby="ppdb-steps-title">
			<div class="container">
				<?php queen_alfalah_section_heading( __( 'Alur Ringkas', 'queen-alfalah' ), __( 'Persiapkan pendaftaran dengan tenang', 'queen-alfalah' ), __( 'Ikuti petunjuk terbaru dari panitia; tahapan dapat berubah sesuai kebijakan sekolah.', 'queen-alfalah' ), 'left', 'ppdb-steps-title' ); ?>
				<ol class="card-grid card-grid--4">
					<li class="widget"><h3><?php esc_html_e( 'Kenali program', 'queen-alfalah' ); ?></h3><p><?php esc_html_e( 'Pelajari kompetensi dan karakter setiap program keahlian.', 'queen-alfalah' ); ?></p></li>
					<li class="widget"><h3><?php esc_html_e( 'Periksa informasi', 'queen-alfalah' ); ?></h3><p><?php esc_html_e( 'Cocokkan jadwal, persyaratan, biaya, dan kuota pada pengumuman resmi.', 'queen-alfalah' ); ?></p></li>
					<li class="widget"><h3><?php esc_html_e( 'Daftar di kanal resmi', 'queen-alfalah' ); ?></h3><p><?php esc_html_e( 'Isi data hanya pada sistem pendaftaran yang ditautkan sekolah.', 'queen-alfalah' ); ?></p></li>
					<li class="widget"><h3><?php esc_html_e( 'Simpan bukti', 'queen-alfalah' ); ?></h3><p><?php esc_html_e( 'Catat nomor pendaftaran dan ikuti instruksi verifikasi dari panitia.', 'queen-alfalah' ); ?></p></li>
				</ol>
				<div class="notice notice--warning">
					<p><strong><?php esc_html_e( 'Mohon verifikasi:', 'queen-alfalah' ); ?></strong> <?php esc_html_e( 'Jadwal, kuota, biaya, seleksi, dan dokumen yang diperlukan harus mengikuti pengumuman panitia pada tahun ajaran berjalan. Tema ini tidak memproses pendaftaran atau pembayaran.', 'queen-alfalah' ); ?></p>
				</div>
			</div>
		</section>

		<section class="section section--compact" aria-labelledby="ppdb-help-title">
			<div class="container cta-banner">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Butuh Bantuan?', 'queen-alfalah' ); ?></p>
					<h2 id="ppdb-help-title"><?php esc_html_e( 'Pilih program dan tanyakan kepada panitia', 'queen-alfalah' ); ?></h2>
					<p><?php esc_html_e( 'Kami membantu calon murid dan orang tua menemukan informasi yang tepat tanpa meminta data sensitif melalui halaman publik.', 'queen-alfalah' ); ?></p>
				</div>
				<div class="cta-banner__actions">
					<a class="button" href="<?php echo esc_url( $queen_programs ); ?>"><?php esc_html_e( 'Lihat Program Keahlian', 'queen-alfalah' ); ?></a>
					<?php if ( queen_alfalah_school_info( 'whatsapp' ) ) : ?><a class="button button--outline" href="<?php echo esc_url( queen_alfalah_whatsapp_url( 'Assalamu’alaikum, saya ingin berkonsultasi tentang pendaftaran SMK Queen Al-Falah.' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Konsultasi WhatsApp', 'queen-alfalah' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(tab baru)', 'queen-alfalah' ); ?></span></a><?php endif; ?>
					<a class="button button--outline" href="<?php echo esc_url( $queen_contact ); ?>"><?php esc_html_e( 'Kontak Sekolah', 'queen-alfalah' ); ?></a>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
</main>

<?php
get_footer();

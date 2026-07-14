<?php
/**
 * Single post and custom school-content entry.
 *
 * @package Queen_AlFalah
 */

$queen_post_type   = get_post_type();
$queen_type_object = get_post_type_object( $queen_post_type );
$queen_type_label  = $queen_type_object ? $queen_type_object->labels->singular_name : __( 'Informasi Sekolah', 'queen-alfalah' );
$queen_placeholder = 'default';

if ( in_array( $queen_post_type, array( 'qaf_teacher', 'qaf_alumni' ), true ) ) {
	$queen_placeholder = 'person';
} elseif ( 'qaf_program' === $queen_post_type ) {
	$queen_placeholder = 'program';
} elseif ( 'qaf_gallery' === $queen_post_type ) {
	$queen_placeholder = 'gallery';
}

/*
 * Field definitions stay in the presentation layer so the theme remains
 * useful when the companion plugin is deactivated after content was created.
 */
$queen_detail_fields = array(
	'qaf_program'     => array(
		array( 'program_code', __( 'Kode/Singkatan', 'queen-alfalah' ), 'text' ),
		array( 'program_head', __( 'Kepala Program', 'queen-alfalah' ), 'text' ),
		array( 'program_gender', __( 'Ketentuan Peserta', 'queen-alfalah' ), 'text' ),
		array( 'competencies', __( 'Kompetensi Utama', 'queen-alfalah' ), 'list' ),
		array( 'careers', __( 'Prospek Karier dan Studi', 'queen-alfalah' ), 'list' ),
	),
	'qaf_teacher'     => array(
		array( 'role', __( 'Jabatan/Peran', 'queen-alfalah' ), 'text' ),
		array( 'subject', __( 'Mata Pelajaran/Unit', 'queen-alfalah' ), 'text' ),
	),
	'qaf_notice'      => array(
		array( 'priority', __( 'Prioritas', 'queen-alfalah' ), 'priority' ),
		array( 'expiry', __( 'Berlaku Sampai', 'queen-alfalah' ), 'date' ),
		array( 'file_url', __( 'Lampiran Resmi', 'queen-alfalah' ), 'url', __( 'Buka lampiran', 'queen-alfalah' ) ),
	),
	'qaf_agenda'      => array(
		array( 'start_date', __( 'Mulai', 'queen-alfalah' ), 'datetime' ),
		array( 'end_date', __( 'Selesai', 'queen-alfalah' ), 'datetime' ),
		array( 'location', __( 'Lokasi', 'queen-alfalah' ), 'text' ),
	),
	'qaf_achievement' => array(
		array( 'level', __( 'Tingkat', 'queen-alfalah' ), 'text' ),
		array( 'achievement_date', __( 'Tanggal Prestasi', 'queen-alfalah' ), 'date' ),
		array( 'recipient', __( 'Penerima', 'queen-alfalah' ), 'multiline' ),
	),
	'qaf_extra'       => array(
		array( 'schedule', __( 'Jadwal Latihan', 'queen-alfalah' ), 'text' ),
		array( 'coach', __( 'Pembina/Pelatih', 'queen-alfalah' ), 'text' ),
	),
	'qaf_service'     => array(
		array( 'external_url', __( 'Layanan Digital', 'queen-alfalah' ), 'url', __( 'Buka layanan', 'queen-alfalah' ) ),
	),
	'qaf_gallery'     => array(
		array( 'album_date', __( 'Tanggal Dokumentasi', 'queen-alfalah' ), 'date' ),
		array( 'video_url', __( 'Video Resmi', 'queen-alfalah' ), 'url', __( 'Tonton video', 'queen-alfalah' ) ),
	),
	'qaf_partner'     => array(
		array( 'partner_sector', __( 'Sektor Kerja Sama', 'queen-alfalah' ), 'text' ),
		array( 'partner_url', __( 'Website Mitra', 'queen-alfalah' ), 'url', __( 'Kunjungi website', 'queen-alfalah' ) ),
	),
	'qaf_vacancy'     => array(
		array( 'company', __( 'Perusahaan/Instansi', 'queen-alfalah' ), 'text' ),
		array( 'deadline', __( 'Batas Lamaran', 'queen-alfalah' ), 'date' ),
		array( 'apply_url', __( 'Kanal Lamaran Resmi', 'queen-alfalah' ), 'url', __( 'Buka informasi lamaran', 'queen-alfalah' ) ),
	),
	'qaf_alumni'      => array(
		array( 'graduation_year', __( 'Tahun Lulus', 'queen-alfalah' ), 'number' ),
		array( 'current_role', __( 'Aktivitas/Peran Saat Ini', 'queen-alfalah' ), 'text' ),
	),
	'qaf_facility'    => array(
		array( 'capacity', __( 'Kapasitas/Jumlah', 'queen-alfalah' ), 'number' ),
		array( 'facility_status', __( 'Status Sarana', 'queen-alfalah' ), 'facility_status' ),
	),
);

$queen_details = array();
if ( isset( $queen_detail_fields[ $queen_post_type ] ) ) {
	foreach ( $queen_detail_fields[ $queen_post_type ] as $queen_field ) {
		$queen_value = queen_alfalah_meta( get_the_ID(), $queen_field[0] );
		if ( '' !== $queen_value && null !== $queen_value && false !== $queen_value && 0 !== $queen_value && '0' !== $queen_value ) {
			$queen_field['value'] = $queen_value;
			$queen_details[]      = $queen_field;
		}
	}
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-entry' ); ?>>
	<header class="entry-header">
		<p class="eyebrow"><?php echo esc_html( $queen_type_label ); ?></p>
		<h1 class="entry-title"><?php echo esc_html( get_the_title() ); ?></h1>
		<?php queen_alfalah_post_meta(); ?>
	</header>

	<figure class="post-thumbnail">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'full', array( 'decoding' => 'async' ) ); ?>
		<?php else : ?>
			<img src="<?php echo esc_url( queen_alfalah_placeholder( $queen_placeholder ) ); ?>" alt="" width="1200" height="800" decoding="async">
		<?php endif; ?>
	</figure>

	<?php if ( $queen_details ) : ?>
		<section class="entry-details card widget flow" aria-labelledby="entry-details-title">
			<h2 id="entry-details-title"><?php esc_html_e( 'Informasi Utama', 'queen-alfalah' ); ?></h2>
			<dl>
				<?php foreach ( $queen_details as $queen_detail ) : ?>
					<?php
					$queen_key        = $queen_detail[0];
					$queen_label      = $queen_detail[1];
					$queen_format     = $queen_detail[2];
					$queen_value      = $queen_detail['value'];
					$queen_link_label = isset( $queen_detail[3] ) ? $queen_detail[3] : $queen_label;
					?>
					<div class="entry-details__item">
						<dt><?php echo esc_html( $queen_label ); ?></dt>
						<dd>
							<?php if ( 'url' === $queen_format ) : ?>
								<a href="<?php echo esc_url( $queen_value ); ?>" rel="external"><?php echo esc_html( $queen_link_label ); ?><?php echo queen_alfalah_icon( 'external' ); ?></a>
							<?php elseif ( 'date' === $queen_format || 'datetime' === $queen_format ) : ?>
								<?php
								$queen_input_format = 'datetime' === $queen_format ? 'Y-m-d\TH:i' : 'Y-m-d';
								$queen_date         = DateTimeImmutable::createFromFormat( $queen_input_format, $queen_value, wp_timezone() );
								if ( $queen_date ) {
									$queen_output_format = 'datetime' === $queen_format ? get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) : get_option( 'date_format' );
									echo '<time datetime="' . esc_attr( $queen_value ) . '">' . esc_html( wp_date( $queen_output_format, $queen_date->getTimestamp(), wp_timezone() ) ) . '</time>';
								} else {
									echo esc_html( $queen_value );
								}
								?>
							<?php elseif ( 'list' === $queen_format ) : ?>
								<ul>
									<?php foreach ( preg_split( '/\r\n|\r|\n/', (string) $queen_value ) as $queen_line ) : ?>
										<?php if ( trim( $queen_line ) ) : ?>
											<li><?php echo esc_html( trim( $queen_line ) ); ?></li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							<?php elseif ( 'multiline' === $queen_format ) : ?>
								<?php echo nl2br( esc_html( $queen_value ) ); ?>
							<?php elseif ( 'priority' === $queen_format ) : ?>
								<?php
								$queen_priority_labels = array( 'normal' => __( 'Normal', 'queen-alfalah' ), 'penting' => __( 'Penting', 'queen-alfalah' ), 'mendesak' => __( 'Mendesak', 'queen-alfalah' ) );
								echo esc_html( isset( $queen_priority_labels[ $queen_value ] ) ? $queen_priority_labels[ $queen_value ] : $queen_value );
								?>
							<?php elseif ( 'facility_status' === $queen_format ) : ?>
								<?php
								$queen_status_labels = array( 'baik' => __( 'Baik/Operasional', 'queen-alfalah' ), 'perlu-perawatan' => __( 'Perlu Perawatan', 'queen-alfalah' ), 'tidak-operasional' => __( 'Tidak Operasional', 'queen-alfalah' ) );
								echo esc_html( isset( $queen_status_labels[ $queen_value ] ) ? $queen_status_labels[ $queen_value ] : $queen_value );
								?>
							<?php elseif ( 'number' === $queen_format ) : ?>
								<?php echo esc_html( number_format_i18n( absint( $queen_value ) ) ); ?>
							<?php else : ?>
								<?php echo esc_html( $queen_value ); ?>
							<?php endif; ?>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</section>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Halaman artikel', 'queen-alfalah' ) . '"><span>' . esc_html__( 'Halaman:', 'queen-alfalah' ) . '</span>',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<footer class="entry-footer">
		<?php if ( 'post' === $queen_post_type ) : ?>
			<?php if ( get_the_category_list() ) : ?><span><?php esc_html_e( 'Kategori:', 'queen-alfalah' ); ?> <?php echo wp_kses_post( get_the_category_list( ', ' ) ); ?></span><?php endif; ?>
			<?php if ( get_the_tag_list() ) : ?><span><?php esc_html_e( 'Tag:', 'queen-alfalah' ); ?> <?php echo wp_kses_post( get_the_tag_list( '', ', ' ) ); ?></span><?php endif; ?>
		<?php else : ?>
			<?php foreach ( get_object_taxonomies( $queen_post_type, 'objects' ) as $queen_taxonomy ) : ?>
				<?php $queen_term_list = get_the_term_list( get_the_ID(), $queen_taxonomy->name, '', ', ' ); ?>
				<?php if ( $queen_term_list && ! is_wp_error( $queen_term_list ) ) : ?>
					<span><?php echo esc_html( $queen_taxonomy->labels->singular_name ); ?>: <?php echo wp_kses_post( $queen_term_list ); ?></span>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php
		edit_post_link(
			esc_html__( 'Sunting konten', 'queen-alfalah' ),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer>

	<?php queen_alfalah_share_links(); ?>
</article>

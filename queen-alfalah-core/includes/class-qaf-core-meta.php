<?php
/**
 * Registered REST meta and secure generic meta boxes.
 *
 * @package Queen_Alfalah_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register structured fields shared by REST, the editor, and the theme.
 */
final class QAF_Core_Meta {
	/**
	 * Attach meta registration and editor hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ), 20 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ), 20 );
		add_action( 'save_post', array( __CLASS__, 'save_meta_box' ), 10, 2 );
	}

	/**
	 * Meta field contract used by both plugin and theme.
	 *
	 * @return array<string,array<string,array<string,mixed>>>
	 */
	public static function get_fields() {
		return array(
			'qaf_program'     => array(
				'_qaf_program_code'   => array(
					'label'       => 'Kode/Singkatan Program',
					'type'        => 'text',
					'description' => 'Contoh: TJKT, MPLB, atau DKV.',
				),
				'_qaf_program_head'   => array(
					'label'       => 'Kepala Program',
					'type'        => 'text',
					'description' => 'Nama kepala program; kosongkan jika belum ditetapkan untuk publik.',
				),
				'_qaf_program_gender' => array(
					'label'       => 'Ketentuan Peserta',
					'type'        => 'text',
					'description' => 'Contoh: Putra, Putri, atau Putra/Putri.',
				),
				'_qaf_competencies'   => array(
					'label'       => 'Kompetensi Utama',
					'type'        => 'textarea',
					'description' => 'Ringkasan kompetensi; satu butir per baris bila diperlukan.',
				),
				'_qaf_careers'        => array(
					'label'       => 'Prospek Karier',
					'type'        => 'textarea',
					'description' => 'Prospek karier atau studi lanjut; hindari janji penempatan kerja.',
				),
			),
			'qaf_teacher'     => array(
				'_qaf_role'    => array(
					'label'       => 'Jabatan/Peran',
					'type'        => 'text',
					'description' => 'Contoh: Kepala Sekolah, Guru Produktif, atau Tenaga Administrasi.',
				),
				'_qaf_subject' => array(
					'label'       => 'Mata Pelajaran/Unit',
					'type'        => 'text',
					'description' => 'Bidang ajar atau unit kerja.',
				),
				'_qaf_order'   => array(
					'label'       => 'Urutan Tampil',
					'type'        => 'integer',
					'description' => 'Angka lebih kecil ditampilkan lebih awal.',
					'default'     => 0,
				),
			),
			'qaf_notice'      => array(
				'_qaf_priority' => array(
					'label'       => 'Prioritas',
					'type'        => 'select',
					'description' => 'Gunakan Mendesak hanya untuk informasi yang benar-benar harus segera terlihat.',
					'default'     => 'normal',
					'options'     => array(
						'normal'   => 'Normal',
						'penting'  => 'Penting',
						'mendesak' => 'Mendesak',
					),
				),
				'_qaf_expiry'   => array(
					'label'       => 'Berlaku Sampai',
					'type'        => 'date',
					'description' => 'Setelah tanggal ini tema dapat menyembunyikan pengumuman dari daftar aktif.',
				),
				'_qaf_file_url' => array(
					'label'       => 'Tautan Lampiran',
					'type'        => 'url',
					'description' => 'Tautan HTTPS ke dokumen resmi; unggah melalui Media lalu salin URL-nya.',
				),
			),
			'qaf_agenda'      => array(
				'_qaf_start_date' => array(
					'label'       => 'Mulai',
					'type'        => 'datetime',
					'description' => 'Waktu lokal situs.',
				),
				'_qaf_end_date'   => array(
					'label'       => 'Selesai',
					'type'        => 'datetime',
					'description' => 'Waktu lokal situs; boleh dikosongkan.',
				),
				'_qaf_location'   => array(
					'label'       => 'Lokasi',
					'type'        => 'text',
					'description' => 'Lokasi fisik atau nama ruang pertemuan daring.',
				),
			),
			'qaf_achievement' => array(
				'_qaf_level'            => array(
					'label'       => 'Tingkat',
					'type'        => 'text',
					'description' => 'Contoh: Sekolah, Kabupaten, Provinsi, Nasional, atau Internasional.',
				),
				'_qaf_achievement_date' => array(
					'label'       => 'Tanggal Prestasi',
					'type'        => 'date',
					'description' => 'Tanggal perolehan atau pengumuman prestasi.',
				),
				'_qaf_recipient'        => array(
					'label'       => 'Penerima',
					'type'        => 'textarea',
					'description' => 'Nama peserta atau tim yang telah disetujui untuk dipublikasikan.',
				),
			),
			'qaf_extra'       => array(
				'_qaf_schedule' => array(
					'label'       => 'Jadwal Latihan',
					'type'        => 'text',
					'description' => 'Kosongkan jika jadwal belum final.',
				),
				'_qaf_coach'    => array(
					'label'       => 'Pembina/Pelatih',
					'type'        => 'text',
					'description' => 'Nama pembina yang telah disetujui untuk tampil publik.',
				),
			),
			'qaf_service'     => array(
				'_qaf_external_url' => array(
					'label'       => 'Alamat Layanan',
					'type'        => 'url',
					'description' => 'Hanya URL HTTP/HTTPS yang diizinkan.',
				),
				'_qaf_icon_name'    => array(
					'label'       => 'Nama Ikon',
					'type'        => 'text',
					'description' => 'Nama ikon yang didukung tema, misalnya globe, book, atau user-plus.',
				),
				'_qaf_open_new'     => array(
					'label'       => 'Buka Tab Baru',
					'type'        => 'boolean',
					'description' => 'Tautan eksternal yang membuka tab baru harus memakai rel=noopener.',
					'default'     => true,
				),
				'_qaf_service_status' => array(
					'label'       => 'Status Aplikasi',
					'type'        => 'select',
					'description' => 'Status terlihat oleh pengunjung pada kartu aplikasi.',
					'default'     => 'active',
					'options'     => array(
						'active'      => 'Aktif',
						'maintenance' => 'Pemeliharaan',
						'inactive'    => 'Nonaktif',
					),
				),
			),
			'qaf_gallery'     => array(
				'_qaf_video_url' => array(
					'label'       => 'URL Video',
					'type'        => 'url',
					'description' => 'URL halaman video resmi, bukan kode iframe.',
				),
				'_qaf_album_date' => array(
					'label'       => 'Tanggal Album',
					'type'        => 'date',
					'description' => 'Tanggal kegiatan atau dokumentasi.',
				),
			),
			'qaf_partner'     => array(
				'_qaf_partner_url'    => array(
					'label'       => 'Website Mitra',
					'type'        => 'url',
					'description' => 'Website resmi mitra.',
				),
				'_qaf_partner_sector' => array(
					'label'       => 'Sektor Kerja Sama',
					'type'        => 'text',
					'description' => 'Contoh: Teknologi, Kesehatan, atau Bisnis.',
				),
			),
			'qaf_vacancy'     => array(
				'_qaf_deadline'  => array(
					'label'       => 'Batas Lamaran',
					'type'        => 'date',
					'description' => 'Lowongan kedaluwarsa dapat disembunyikan oleh tema.',
				),
				'_qaf_company'   => array(
					'label'       => 'Perusahaan/Instansi',
					'type'        => 'text',
					'description' => 'Pastikan sumber lowongan telah diverifikasi.',
				),
				'_qaf_apply_url' => array(
					'label'       => 'Tautan Lamaran',
					'type'        => 'url',
					'description' => 'Tautan resmi perusahaan atau penyedia lowongan.',
				),
			),
			'qaf_alumni'      => array(
				'_qaf_graduation_year' => array(
					'label'       => 'Tahun Lulus',
					'type'        => 'integer',
					'description' => 'Tahun empat digit.',
				),
				'_qaf_current_role'    => array(
					'label'       => 'Aktivitas/Peran Saat Ini',
					'type'        => 'text',
					'description' => 'Tampilkan hanya dengan persetujuan alumni.',
				),
			),
			'qaf_facility'    => array(
				'_qaf_capacity'        => array(
					'label'       => 'Kapasitas/Jumlah',
					'type'        => 'integer',
					'description' => 'Gunakan data yang sudah diverifikasi; nol berarti tidak ditampilkan.',
				),
				'_qaf_facility_status' => array(
					'label'       => 'Status Sarana',
					'type'        => 'select',
					'description' => 'Status operasional internal untuk membantu penyajian informasi.',
					'default'     => 'baik',
					'options'     => array(
						'baik'              => 'Baik/Operasional',
						'perlu-perawatan'   => 'Perlu Perawatan',
						'tidak-operasional' => 'Tidak Operasional',
					),
				),
			),
		);
	}

	/**
	 * Register every field with the WordPress metadata and REST APIs.
	 *
	 * @return void
	 */
	public static function register_meta() {
		foreach ( self::get_fields() as $post_type => $fields ) {
			foreach ( $fields as $meta_key => $field ) {
				$rest_type = self::get_rest_type( $field['type'] );

				register_post_meta(
					$post_type,
					$meta_key,
					array(
						'type'              => $rest_type,
						'description'       => $field['description'],
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => static function ( $value ) use ( $field ) {
							return QAF_Core_Meta::sanitize_value( $value, $field );
						},
						'auth_callback'     => static function ( $allowed, $key, $post_id ) {
							unset( $allowed, $key );
							return $post_id > 0 && current_user_can( 'edit_post', (int) $post_id );
						},
					)
				);
			}
		}
	}

	/**
	 * Add one generated meta box per post type and hide the raw custom-fields box.
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		foreach ( self::get_fields() as $post_type => $fields ) {
			unset( $fields );
			remove_meta_box( 'postcustom', $post_type, 'normal' );
			add_meta_box(
				'qaf-core-details',
				'Detail Terstruktur',
				array( __CLASS__, 'render_meta_box' ),
				$post_type,
				'normal',
				'default'
			);
		}
	}

	/**
	 * Render a generated table of fields for the current post type.
	 *
	 * @param WP_Post $post Current post.
	 * @return void
	 */
	public static function render_meta_box( $post ) {
		$fields = self::get_fields();
		if ( ! isset( $fields[ $post->post_type ] ) ) {
			return;
		}

		wp_nonce_field( 'qaf_core_save_meta_' . $post->post_type, 'qaf_core_meta_nonce' );
		?>
		<table class="form-table" role="presentation">
			<tbody>
			<?php foreach ( $fields[ $post->post_type ] as $meta_key => $field ) : ?>
				<?php
				$value = metadata_exists( 'post', $post->ID, $meta_key )
					? get_post_meta( $post->ID, $meta_key, true )
					: ( isset( $field['default'] ) ? $field['default'] : '' );
				?>
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( 'qaf-meta-' . $meta_key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
					<td>
						<?php self::render_input( $meta_key, $field, $value ); ?>
						<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Verify and persist a generated meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public static function save_meta_box( $post_id, $post ) {
		$all_fields = self::get_fields();
		if ( ! isset( $all_fields[ $post->post_type ] ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['qaf_core_meta_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['qaf_core_meta_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'qaf_core_save_meta_' . $post->post_type ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$posted = isset( $_POST['qaf_core_meta'] ) && is_array( $_POST['qaf_core_meta'] )
			? wp_unslash( $_POST['qaf_core_meta'] )
			: array();

		foreach ( $all_fields[ $post->post_type ] as $meta_key => $field ) {
			if ( ! array_key_exists( $meta_key, $posted ) && 'boolean' !== $field['type'] ) {
				continue;
			}

			$raw_value = array_key_exists( $meta_key, $posted ) ? $posted[ $meta_key ] : 0;
			$value     = self::sanitize_value( $raw_value, $field );

			if ( '' === $value && ! in_array( $field['type'], array( 'integer', 'boolean' ), true ) ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}
	}

	/**
	 * Sanitize one registered meta value.
	 *
	 * @param mixed               $value Raw value.
	 * @param array<string,mixed> $field Field definition.
	 * @return mixed
	 */
	public static function sanitize_value( $value, $field ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		switch ( $type ) {
			case 'boolean':
				return ! empty( $value ) && 'false' !== $value;
			case 'integer':
				return absint( $value );
			case 'textarea':
				return sanitize_textarea_field( is_scalar( $value ) ? (string) $value : '' );
			case 'url':
				return esc_url_raw( is_scalar( $value ) ? (string) $value : '', array( 'http', 'https' ) );
			case 'date':
				return self::sanitize_date( $value );
			case 'datetime':
				return self::sanitize_datetime( $value );
			case 'select':
				$value = sanitize_key( is_scalar( $value ) ? (string) $value : '' );
				return isset( $field['options'][ $value ] ) ? $value : '';
			default:
				return sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
		}
	}

	/**
	 * Map editor input types to REST schema scalar types.
	 *
	 * @param string $field_type Editor type.
	 * @return string
	 */
	private static function get_rest_type( $field_type ) {
		if ( 'integer' === $field_type ) {
			return 'integer';
		}
		if ( 'boolean' === $field_type ) {
			return 'boolean';
		}
		return 'string';
	}

	/**
	 * Render one input control.
	 *
	 * @param string              $meta_key Meta key.
	 * @param array<string,mixed> $field    Field definition.
	 * @param mixed               $value    Current value.
	 * @return void
	 */
	private static function render_input( $meta_key, $field, $value ) {
		$id   = 'qaf-meta-' . $meta_key;
		$name = 'qaf_core_meta[' . $meta_key . ']';

		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea class="large-text" rows="4" id="%1$s" name="%2$s">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $value )
			);
			return;
		}

		if ( 'select' === $field['type'] ) {
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field['options'] as $option_value => $option_label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $option_value ),
					selected( $value, $option_value, false ),
					esc_html( $option_label )
				);
			}
			echo '</select>';
			return;
		}

		if ( 'boolean' === $field['type'] ) {
			printf( '<input type="hidden" name="%1$s" value="0">', esc_attr( $name ) );
			printf(
				'<label><input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s> Ya</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (bool) $value, true, false )
			);
			return;
		}

		$type = 'text';
		if ( 'url' === $field['type'] ) {
			$type = 'url';
		} elseif ( 'date' === $field['type'] ) {
			$type = 'date';
		} elseif ( 'datetime' === $field['type'] ) {
			$type = 'datetime-local';
		} elseif ( 'integer' === $field['type'] ) {
			$type = 'number';
		}

		printf(
			'<input class="regular-text" type="%1$s" id="%2$s" name="%3$s" value="%4$s"%5$s>',
			esc_attr( $type ),
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $value ),
			'number' === $type ? ' min="0" step="1"' : ''
		);
	}

	/**
	 * Validate an ISO date.
	 *
	 * @param mixed $value Raw date.
	 * @return string
	 */
	private static function sanitize_date( $value ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';
		if ( '' === $value ) {
			return '';
		}

		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches ) ) {
			return '';
		}

		return checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1] ) ? $value : '';
	}

	/**
	 * Validate a local ISO date and time.
	 *
	 * @param mixed $value Raw date/time.
	 * @return string
	 */
	private static function sanitize_datetime( $value ) {
		$value = is_scalar( $value ) ? trim( (string) $value ) : '';
		if ( '' === $value ) {
			return '';
		}

		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})T([01]\d|2[0-3]):([0-5]\d)$/', $value, $matches ) ) {
			return '';
		}

		return checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1] ) ? $value : '';
	}
}

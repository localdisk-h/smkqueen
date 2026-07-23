<?php
/**
 * Private school media center backed by per-user Google Drive folders.
 *
 * @package Queen_Alfalah_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register school roles, provision the portal, and enforce per-user access.
 */
final class QAF_Core_Media_Center {
	const CAPABILITY          = 'qaf_access_media_center';
	const FOLDER_META         = '_qaf_drive_folder_id';
	const UNIT_META           = '_qaf_school_unit';
	const VERSION_OPTION      = 'qaf_media_center_schema_version';
	const SCHEMA_VERSION      = '1.0.0';
	const PAGE_OPTION         = 'qaf_media_center_page_id';
	const PROFILE_NONCE       = 'qaf_media_profile_nonce';
	const PROFILE_NONCE_ACTION = 'qaf_save_media_profile';

	/** Register runtime hooks. */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_upgrade' ) );
		add_action( 'admin_init', array( __CLASS__, 'restrict_portal_users' ), 20 );
		add_shortcode( 'qaf_media_center', array( __CLASS__, 'render_shortcode' ) );
		add_action( 'show_user_profile', array( __CLASS__, 'render_user_fields' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'render_user_fields' ) );
		add_action( 'user_new_form', array( __CLASS__, 'render_new_user_fields' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'save_user_fields' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'save_user_fields' ) );
		add_action( 'user_register', array( __CLASS__, 'save_new_user_fields' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_post_qaf_media_download', array( __CLASS__, 'download_file' ) );
		add_filter( 'login_redirect', array( __CLASS__, 'login_redirect' ), 10, 3 );
		add_filter( 'show_admin_bar', array( __CLASS__, 'show_admin_bar' ) );
		add_filter( 'wp_robots', array( __CLASS__, 'robots' ) );
	}

	/** Install roles and portal content during plugin activation. */
	public static function activate() {
		self::install_roles();
		self::ensure_page();
		update_option( self::VERSION_OPTION, self::SCHEMA_VERSION, false );
	}

	/** Apply non-destructive upgrades to existing installations. */
	public static function maybe_upgrade() {
		if ( self::SCHEMA_VERSION === get_option( self::VERSION_OPTION ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		self::activate();
	}

	/** Add dedicated roles without replacing existing role configuration. */
	private static function install_roles() {
		$roles = array(
			'qaf_waka'        => 'Waka Sekolah',
			'qaf_media_team'  => 'Tim Media',
			'qaf_school_unit' => 'Bidang Sekolah',
		);

		foreach ( $roles as $role_key => $role_label ) {
			$role = get_role( $role_key );
			if ( ! $role ) {
				add_role(
					$role_key,
					$role_label,
					array(
						'read'                    => true,
						self::CAPABILITY          => true,
					)
				);
				$role = get_role( $role_key );
			}

			if ( $role && ! $role->has_cap( self::CAPABILITY ) ) {
				$role->add_cap( self::CAPABILITY );
			}
		}

		$administrator = get_role( 'administrator' );
		if ( $administrator && ! $administrator->has_cap( self::CAPABILITY ) ) {
			$administrator->add_cap( self::CAPABILITY );
		}
	}

	/** Create the shortcode page once and reuse an editor-created matching page. */
	private static function ensure_page() {
		$page_id = absint( get_option( self::PAGE_OPTION ) );
		if ( $page_id && 'trash' !== get_post_status( $page_id ) ) {
			return $page_id;
		}

		$existing = get_page_by_path( 'pusat-media', OBJECT, 'page' );
		if ( $existing instanceof WP_Post && 'trash' !== $existing->post_status ) {
			update_option( self::PAGE_OPTION, $existing->ID, false );
			return (int) $existing->ID;
		}

		$page_id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_title'     => 'Pusat Media',
				'post_name'      => 'pusat-media',
				'post_content'   => '<!-- wp:shortcode -->[qaf_media_center]<!-- /wp:shortcode -->',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'meta_input'     => array(
					'_wp_page_template' => 'page-templates/template-full-width.php',
				),
			),
			true
		);

		if ( ! is_wp_error( $page_id ) ) {
			update_option( self::PAGE_OPTION, (int) $page_id, false );
		}

		return $page_id;
	}

	/** Return the stable portal URL even before the page upgrade has run. */
	public static function portal_url() {
		$page_id = absint( get_option( self::PAGE_OPTION ) );
		$url     = $page_id ? get_permalink( $page_id ) : '';
		return $url ? $url : home_url( '/pusat-media/' );
	}

	/** Load isolated portal presentation styles. */
	public static function enqueue_assets() {
		$page_id = absint( get_option( self::PAGE_OPTION ) );
		if ( ! $page_id || ! is_page( $page_id ) ) {
			return;
		}

		wp_enqueue_style(
			'qaf-media-center',
			QAF_CORE_URL . 'assets/css/media-center.css',
			array(),
			QAF_CORE_VERSION
		);
	}

	/** Keep the private portal and login form out of search indexes. */
	public static function robots( $robots ) {
		$page_id = absint( get_option( self::PAGE_OPTION ) );
		if ( $page_id && is_page( $page_id ) ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}
		return $robots;
	}

	/** Redirect portal-only users to their media center after login. */
	public static function login_redirect( $redirect_to, $requested_redirect_to, $user ) {
		if ( $user instanceof WP_User && user_can( $user, self::CAPABILITY ) && ! user_can( $user, 'manage_options' ) ) {
			return self::portal_url();
		}

		return $redirect_to;
	}

	/** Keep portal-only accounts out of the WordPress administration screens. */
	public static function restrict_portal_users() {
		global $pagenow;

		if ( ! current_user_can( self::CAPABILITY ) || current_user_can( 'manage_options' ) || wp_doing_ajax() || 'admin-post.php' === $pagenow ) {
			return;
		}

		wp_safe_redirect( self::portal_url() );
		exit;
	}

	/** Hide the WordPress toolbar from accounts intended only for Pusat Media. */
	public static function show_admin_bar( $show ) {
		if ( current_user_can( self::CAPABILITY ) && ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return $show;
	}

	/** Add Drive assignment fields to an existing user profile. */
	public static function render_user_fields( $user ) {
		if ( ! $user instanceof WP_User || ! current_user_can( 'edit_users' ) || ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		self::render_access_fields( $user );
	}

	/** Add Drive assignment fields while an administrator creates a user. */
	public static function render_new_user_fields( $operation ) {
		if ( 'add-new-user' !== $operation || ! current_user_can( 'create_users' ) ) {
			return;
		}

		self::render_access_fields( null );
	}

	/** Render shared field markup for new and existing accounts. */
	private static function render_access_fields( $user ) {
		$user_id   = $user instanceof WP_User ? $user->ID : 0;
		$unit      = $user_id ? get_user_meta( $user_id, self::UNIT_META, true ) : '';
		$folder_id = $user_id ? get_user_meta( $user_id, self::FOLDER_META, true ) : '';
		wp_nonce_field( self::PROFILE_NONCE_ACTION, self::PROFILE_NONCE );
		?>
		<h2><?php esc_html_e( 'Akses Pusat Media', 'queen-alfalah-core' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="qaf-school-unit"><?php esc_html_e( 'Waka / Tim / Bidang', 'queen-alfalah-core' ); ?></label></th>
				<td>
					<input class="regular-text" type="text" id="qaf-school-unit" name="qaf_school_unit" value="<?php echo esc_attr( $unit ); ?>">
					<p class="description"><?php esc_html_e( 'Contoh: Waka Kurikulum, Tim Media, Tata Usaha, BK, Hubin, atau bidang lain pada struktur sekolah.', 'queen-alfalah-core' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="qaf-drive-folder-id"><?php esc_html_e( 'ID Folder Google Drive', 'queen-alfalah-core' ); ?></label></th>
				<td>
					<input class="regular-text code" type="text" id="qaf-drive-folder-id" name="qaf_drive_folder_id" value="<?php echo esc_attr( $folder_id ); ?>" autocomplete="off" pattern="[A-Za-z0-9_-]{10,}">
					<p class="description"><?php esc_html_e( 'Salin bagian setelah /folders/ pada URL Google Drive. Akun ini hanya dapat menelusuri folder tersebut dan seluruh subfoldernya melalui portal.', 'queen-alfalah-core' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/** Save Drive assignment fields on an existing account. */
	public static function save_user_fields( $user_id ) {
		if ( ! current_user_can( 'edit_users' ) || ! current_user_can( 'edit_user', $user_id ) || ! self::valid_profile_nonce() ) {
			return;
		}

		self::persist_access_fields( $user_id );
	}

	/** Save Drive assignment fields when a new user is created. */
	public static function save_new_user_fields( $user_id ) {
		if ( ! current_user_can( 'create_users' ) || ! self::valid_profile_nonce() ) {
			return;
		}

		self::persist_access_fields( $user_id );
	}

	/** Verify the profile form nonce without accepting missing values. */
	private static function valid_profile_nonce() {
		return isset( $_POST[ self::PROFILE_NONCE ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::PROFILE_NONCE ] ) ), self::PROFILE_NONCE_ACTION );
	}

	/** Store only normalized labels and Drive identifiers. */
	private static function persist_access_fields( $user_id ) {
		$unit = isset( $_POST['qaf_school_unit'] ) ? sanitize_text_field( wp_unslash( $_POST['qaf_school_unit'] ) ) : '';
		$raw  = isset( $_POST['qaf_drive_folder_id'] ) ? wp_unslash( $_POST['qaf_drive_folder_id'] ) : '';
		$id   = QAF_Core_Google_Drive::sanitize_id( $raw );

		$unit ? update_user_meta( $user_id, self::UNIT_META, $unit ) : delete_user_meta( $user_id, self::UNIT_META );
		$id ? update_user_meta( $user_id, self::FOLDER_META, $id ) : delete_user_meta( $user_id, self::FOLDER_META );
	}

	/** Render login, authorization state, or the assigned Drive folder. */
	public static function render_shortcode() {
		ob_start();
		?>
		<section class="qaf-media-center" aria-labelledby="qaf-media-title">
			<header class="qaf-media-hero">
				<p class="qaf-media-eyebrow"><?php esc_html_e( 'Ruang Kerja Privat', 'queen-alfalah-core' ); ?></p>
				<h2 id="qaf-media-title"><?php esc_html_e( 'Pusat Media Sekolah', 'queen-alfalah-core' ); ?></h2>
				<p><?php esc_html_e( 'Akses dokumen resmi sesuai Waka, tim, atau bidang yang ditetapkan pada akun Anda.', 'queen-alfalah-core' ); ?></p>
			</header>

			<?php if ( ! is_user_logged_in() ) : ?>
				<div class="qaf-media-login">
					<h3><?php esc_html_e( 'Masuk ke akun sekolah', 'queen-alfalah-core' ); ?></h3>
					<p><?php esc_html_e( 'Gunakan username dan password yang diberikan administrator.', 'queen-alfalah-core' ); ?></p>
					<?php
					echo wp_login_form(
						array(
							'echo'           => false,
							'redirect'       => self::portal_url(),
							'label_username' => __( 'Username', 'queen-alfalah-core' ),
							'label_password' => __( 'Password', 'queen-alfalah-core' ),
							'label_log_in'   => __( 'Masuk', 'queen-alfalah-core' ),
							'remember'       => true,
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<p class="qaf-media-login__help"><a href="<?php echo esc_url( wp_lostpassword_url( self::portal_url() ) ); ?>"><?php esc_html_e( 'Lupa password?', 'queen-alfalah-core' ); ?></a></p>
				</div>
			<?php elseif ( ! current_user_can( self::CAPABILITY ) ) : ?>
				<div class="qaf-media-notice qaf-media-notice--error"><strong><?php esc_html_e( 'Akses belum diberikan.', 'queen-alfalah-core' ); ?></strong> <?php esc_html_e( 'Hubungi administrator untuk menetapkan peran Pusat Media pada akun Anda.', 'queen-alfalah-core' ); ?></div>
			<?php else : ?>
				<?php self::render_authenticated_portal(); ?>
			<?php endif; ?>
		</section>
		<?php
		return ob_get_clean();
	}

	/** Render the authorized user's folder listing. */
	private static function render_authenticated_portal() {
		$user      = wp_get_current_user();
		$unit      = get_user_meta( $user->ID, self::UNIT_META, true );
		$root_id   = QAF_Core_Google_Drive::sanitize_id( get_user_meta( $user->ID, self::FOLDER_META, true ) );
		$folder_id = isset( $_GET['qaf_folder'] ) ? QAF_Core_Google_Drive::sanitize_id( wp_unslash( $_GET['qaf_folder'] ) ) : $root_id;
		?>
		<div class="qaf-media-account">
			<div><span><?php esc_html_e( 'Masuk sebagai', 'queen-alfalah-core' ); ?></span><strong><?php echo esc_html( $user->display_name ); ?></strong><?php if ( $unit ) : ?><small><?php echo esc_html( $unit ); ?></small><?php endif; ?></div>
			<a href="<?php echo esc_url( wp_logout_url( self::portal_url() ) ); ?>"><?php esc_html_e( 'Keluar', 'queen-alfalah-core' ); ?></a>
		</div>
		<?php

		if ( ! $root_id ) {
			self::render_notice( __( 'Folder belum ditetapkan. Administrator perlu mengisi ID Folder Google Drive pada profil akun ini.', 'queen-alfalah-core' ), true );
			return;
		}

		if ( ! QAF_Core_Google_Drive::is_configured() ) {
			$message = current_user_can( 'manage_options' )
				? __( 'Google Drive belum terhubung. Tambahkan konfigurasi service account sesuai petunjuk pada README plugin.', 'queen-alfalah-core' )
				: __( 'Pusat Media sedang disiapkan oleh administrator. Silakan coba kembali nanti.', 'queen-alfalah-core' );
			self::render_notice( $message, true );
			return;
		}

		if ( ! $folder_id || ! QAF_Core_Google_Drive::is_folder_allowed( $folder_id, $root_id ) ) {
			self::render_notice( __( 'Folder yang diminta berada di luar akses akun Anda.', 'queen-alfalah-core' ), true );
			return;
		}

		$items = QAF_Core_Google_Drive::list_children( $folder_id );
		if ( is_wp_error( $items ) ) {
			$message = current_user_can( 'manage_options' ) ? $items->get_error_message() : __( 'Folder belum dapat dimuat. Hubungi administrator Pusat Media.', 'queen-alfalah-core' );
			self::render_notice( $message, true );
			return;
		}
		?>
		<nav class="qaf-media-breadcrumb" aria-label="<?php esc_attr_e( 'Lokasi folder', 'queen-alfalah-core' ); ?>">
			<a href="<?php echo esc_url( self::portal_url() ); ?>"><?php esc_html_e( 'Folder Saya', 'queen-alfalah-core' ); ?></a>
			<?php if ( $folder_id !== $root_id ) : ?><span aria-hidden="true">/</span><span><?php esc_html_e( 'Subfolder', 'queen-alfalah-core' ); ?></span><?php endif; ?>
		</nav>
		<div class="qaf-media-grid">
			<?php if ( ! $items ) : ?>
				<div class="qaf-media-empty"><strong><?php esc_html_e( 'Folder masih kosong', 'queen-alfalah-core' ); ?></strong><span><?php esc_html_e( 'File yang disinkronkan ke Google Drive akan tampil di sini.', 'queen-alfalah-core' ); ?></span></div>
			<?php else : ?>
				<?php foreach ( $items as $item ) : ?>
					<?php self::render_item( $item ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/** Render one folder or downloadable file card. */
	private static function render_item( $item ) {
		$is_folder = QAF_Core_Google_Drive::FOLDER_MIME === $item['mimeType'];
		$url       = $is_folder
			? add_query_arg( 'qaf_folder', rawurlencode( $item['id'] ), self::portal_url() )
			: wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'qaf_media_download',
						'file'   => $item['id'],
					),
					admin_url( 'admin-post.php' )
				),
				'qaf_media_download_' . $item['id']
			);
		$type      = $is_folder ? __( 'Folder', 'queen-alfalah-core' ) : self::file_type_label( $item['mimeType'] );
		$meta      = array_filter(
			array(
				$type,
				! empty( $item['size'] ) ? size_format( (int) $item['size'] ) : '',
				! empty( $item['modifiedTime'] ) ? wp_date( get_option( 'date_format' ), strtotime( $item['modifiedTime'] ) ) : '',
			)
		);
		?>
		<article class="qaf-media-item qaf-media-item--<?php echo $is_folder ? 'folder' : 'file'; ?>">
			<span class="qaf-media-item__icon" aria-hidden="true"><?php echo $is_folder ? '&#128193;' : '&#128196;'; ?></span>
			<div class="qaf-media-item__body"><h3><?php echo esc_html( $item['name'] ); ?></h3><p><?php echo esc_html( implode( ' · ', $meta ) ); ?></p></div>
			<a class="qaf-media-item__action" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $is_folder ? __( 'Buka', 'queen-alfalah-core' ) : __( 'Unduh', 'queen-alfalah-core' ) ); ?><span class="screen-reader-text"> <?php echo esc_html( $item['name'] ); ?></span></a>
		</article>
		<?php
	}

	/** Present a compact file category without leaking raw MIME values. */
	private static function file_type_label( $mime ) {
		if ( 0 === strpos( $mime, 'image/' ) ) {
			return __( 'Gambar', 'queen-alfalah-core' );
		}
		if ( 0 === strpos( $mime, 'video/' ) ) {
			return __( 'Video', 'queen-alfalah-core' );
		}
		if ( 0 === strpos( $mime, 'audio/' ) ) {
			return __( 'Audio', 'queen-alfalah-core' );
		}
		if ( false !== strpos( $mime, 'pdf' ) ) {
			return 'PDF';
		}
		if ( false !== strpos( $mime, 'spreadsheet' ) || false !== strpos( $mime, 'excel' ) ) {
			return __( 'Spreadsheet', 'queen-alfalah-core' );
		}
		if ( false !== strpos( $mime, 'presentation' ) || false !== strpos( $mime, 'powerpoint' ) ) {
			return __( 'Presentasi', 'queen-alfalah-core' );
		}
		return __( 'Dokumen', 'queen-alfalah-core' );
	}

	/** Print a consistent portal warning. */
	private static function render_notice( $message, $error = false ) {
		printf( '<div class="qaf-media-notice%1$s">%2$s</div>', $error ? ' qaf-media-notice--error' : '', esc_html( $message ) );
	}

	/** Authorize and proxy a Drive download without exposing credentials. */
	public static function download_file() {
		if ( ! is_user_logged_in() || ! current_user_can( self::CAPABILITY ) ) {
			auth_redirect();
		}

		$file_id = isset( $_GET['file'] ) ? QAF_Core_Google_Drive::sanitize_id( wp_unslash( $_GET['file'] ) ) : '';
		if ( ! $file_id || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'qaf_media_download_' . $file_id ) ) {
			wp_die( esc_html__( 'Tautan unduhan tidak valid.', 'queen-alfalah-core' ), '', array( 'response' => 403 ) );
		}

		$root_id = QAF_Core_Google_Drive::sanitize_id( get_user_meta( get_current_user_id(), self::FOLDER_META, true ) );
		$file    = QAF_Core_Google_Drive::get_metadata( $file_id );
		if ( ! $root_id || is_wp_error( $file ) || ! QAF_Core_Google_Drive::is_item_allowed( $file, $root_id ) || QAF_Core_Google_Drive::FOLDER_MIME === $file['mimeType'] ) {
			wp_die( esc_html__( 'File berada di luar akses akun Anda atau sudah tidak tersedia.', 'queen-alfalah-core' ), '', array( 'response' => 403 ) );
		}

		$temp_file = wp_tempnam( 'qaf-media-' . $file_id );
		if ( ! $temp_file ) {
			wp_die( esc_html__( 'Server tidak dapat menyiapkan unduhan.', 'queen-alfalah-core' ), '', array( 'response' => 500 ) );
		}

		$download = QAF_Core_Google_Drive::download_to( $file, $temp_file );
		if ( is_wp_error( $download ) ) {
			wp_delete_file( $temp_file );
			wp_die( esc_html( $download->get_error_message() ), '', array( 'response' => 502 ) );
		}

		$filename = sanitize_file_name( QAF_Core_Google_Drive::download_name( $file ) );
		$mime     = QAF_Core_Google_Drive::download_mime( $file );
		nocache_headers();
		header( 'Content-Type: ' . $mime );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode( $filename ) );
		header( 'Content-Length: ' . (string) filesize( $temp_file ) );
		header( 'X-Content-Type-Options: nosniff' );

		$handle = fopen( $temp_file, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ( $handle ) {
			while ( ! feof( $handle ) ) {
				echo fread( $handle, 1048576 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.AlternativeFunctions.file_system_operations_fread
				flush();
			}
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		}
		wp_delete_file( $temp_file );
		exit;
	}
}

/**
 * Minimal read-only Google Drive v3 client using a service account.
 */
final class QAF_Core_Google_Drive {
	const FOLDER_MIME = 'application/vnd.google-apps.folder';
	const API_BASE    = 'https://www.googleapis.com/drive/v3/';
	const TOKEN_SCOPE = 'https://www.googleapis.com/auth/drive.readonly';

	/** Normalize Drive file and folder IDs. */
	public static function sanitize_id( $value ) {
		$value = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value );
		return is_string( $value ) && strlen( $value ) >= 10 ? $value : '';
	}

	/** Check whether service-account configuration has been provided. */
	public static function is_configured() {
		return defined( 'QAF_GOOGLE_DRIVE_CREDENTIALS_JSON' ) || defined( 'QAF_GOOGLE_DRIVE_CREDENTIALS_PATH' );
	}

	/** List direct children of an authorized folder. */
	public static function list_children( $folder_id ) {
		$folder_id = self::sanitize_id( $folder_id );
		if ( ! $folder_id ) {
			return new WP_Error( 'qaf_drive_folder', __( 'ID folder Google Drive tidak valid.', 'queen-alfalah-core' ) );
		}

		$files      = array();
		$page_token = '';
		$pages      = 0;
		do {
			$query = array(
				'q'                         => "'" . $folder_id . "' in parents and trashed = false",
				'fields'                    => 'nextPageToken,files(id,name,mimeType,size,modifiedTime,parents)',
				'pageSize'                  => 1000,
				'orderBy'                   => 'folder,name_natural',
				'includeItemsFromAllDrives' => 'true',
				'supportsAllDrives'         => 'true',
			);
			if ( $page_token ) {
				$query['pageToken'] = $page_token;
			}

			$response = self::api_get( 'files', $query );
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$files      = array_merge( $files, isset( $response['files'] ) && is_array( $response['files'] ) ? $response['files'] : array() );
			$page_token = isset( $response['nextPageToken'] ) ? sanitize_text_field( $response['nextPageToken'] ) : '';
			++$pages;
		} while ( $page_token && $pages < 10 );

		return $files;
	}

	/** Retrieve security-relevant metadata for one item. */
	public static function get_metadata( $file_id ) {
		$file_id = self::sanitize_id( $file_id );
		if ( ! $file_id ) {
			return new WP_Error( 'qaf_drive_file', __( 'ID file Google Drive tidak valid.', 'queen-alfalah-core' ) );
		}

		return self::api_get(
			'files/' . rawurlencode( $file_id ),
			array(
				'fields'            => 'id,name,mimeType,size,modifiedTime,parents',
				'supportsAllDrives' => 'true',
			)
		);
	}

	/** Permit the root itself or any descendant folder. */
	public static function is_folder_allowed( $folder_id, $root_id ) {
		$folder_id = self::sanitize_id( $folder_id );
		$root_id   = self::sanitize_id( $root_id );
		if ( ! $folder_id || ! $root_id ) {
			return false;
		}
		if ( hash_equals( $root_id, $folder_id ) ) {
			return true;
		}

		$folder = self::get_metadata( $folder_id );
		return ! is_wp_error( $folder ) && self::FOLDER_MIME === $folder['mimeType'] && self::is_item_allowed( $folder, $root_id );
	}

	/** Walk parents with a strict depth limit to enforce the assigned root. */
	public static function is_item_allowed( $item, $root_id ) {
		$root_id = self::sanitize_id( $root_id );
		$parents = isset( $item['parents'] ) && is_array( $item['parents'] ) ? $item['parents'] : array();
		$seen    = array();

		for ( $depth = 0; $depth < 20 && $parents; ++$depth ) {
			$next_parents = array();
			foreach ( $parents as $parent_id ) {
				$parent_id = self::sanitize_id( $parent_id );
				if ( ! $parent_id || isset( $seen[ $parent_id ] ) ) {
					continue;
				}
				if ( hash_equals( $root_id, $parent_id ) ) {
					return true;
				}
				$seen[ $parent_id ] = true;
				$parent             = self::get_metadata( $parent_id );
				if ( ! is_wp_error( $parent ) && ! empty( $parent['parents'] ) ) {
					$next_parents = array_merge( $next_parents, $parent['parents'] );
				}
			}
			$parents = $next_parents;
		}

		return false;
	}

	/** Download a binary file or export a native Google Workspace document. */
	public static function download_to( $file, $destination ) {
		$file_id = self::sanitize_id( $file['id'] );
		if ( ! $file_id ) {
			return new WP_Error( 'qaf_drive_download', __( 'File Google Drive tidak valid.', 'queen-alfalah-core' ) );
		}

		$export = self::export_definition( $file['mimeType'] );
		if ( 0 === strpos( $file['mimeType'], 'application/vnd.google-apps.' ) ) {
			if ( ! $export ) {
				return new WP_Error( 'qaf_drive_export', __( 'Jenis file Google ini belum mendukung unduhan.', 'queen-alfalah-core' ) );
			}
			$path  = 'files/' . rawurlencode( $file_id ) . '/export';
			$query = array( 'mimeType' => $export['mime'] );
		} else {
			$path  = 'files/' . rawurlencode( $file_id );
			$query = array(
				'alt'               => 'media',
				'supportsAllDrives' => 'true',
			);
		}

		return self::api_get( $path, $query, $destination );
	}

	/** Resolve the final download filename, including native export extensions. */
	public static function download_name( $file ) {
		$name   = isset( $file['name'] ) ? $file['name'] : 'dokumen';
		$export = self::export_definition( $file['mimeType'] );
		if ( $export && substr( strtolower( $name ), -strlen( $export['extension'] ) ) !== $export['extension'] ) {
			$name .= $export['extension'];
		}
		return $name;
	}

	/** Resolve the response MIME type for a Drive download. */
	public static function download_mime( $file ) {
		$export = self::export_definition( $file['mimeType'] );
		$mime   = $export ? $export['mime'] : ( isset( $file['mimeType'] ) ? sanitize_mime_type( $file['mimeType'] ) : '' );
		return $mime ? $mime : 'application/octet-stream';
	}

	/** Map Google Workspace formats to portable download formats. */
	private static function export_definition( $mime ) {
		$exports = array(
			'application/vnd.google-apps.document'     => array( 'mime' => 'application/pdf', 'extension' => '.pdf' ),
			'application/vnd.google-apps.spreadsheet'  => array( 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'extension' => '.xlsx' ),
			'application/vnd.google-apps.presentation' => array( 'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'extension' => '.pptx' ),
			'application/vnd.google-apps.drawing'      => array( 'mime' => 'application/pdf', 'extension' => '.pdf' ),
		);
		return isset( $exports[ $mime ] ) ? $exports[ $mime ] : null;
	}

	/** Perform one authenticated Drive API GET request. */
	private static function api_get( $path, $query = array(), $destination = '' ) {
		$token = self::access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$url  = add_query_arg( $query, self::API_BASE . ltrim( $path, '/' ) );
		$args = array(
			'timeout'     => 60,
			'redirection' => 2,
			'headers'     => array( 'Authorization' => 'Bearer ' . $token ),
		);
		if ( $destination ) {
			$args['stream']   = true;
			$args['filename'] = $destination;
		}

		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status = wp_remote_retrieve_response_code( $response );
		if ( $status < 200 || $status >= 300 ) {
			if ( $destination && file_exists( $destination ) ) {
				wp_delete_file( $destination );
			}
			return new WP_Error( 'qaf_drive_api', sprintf( __( 'Google Drive menolak permintaan (HTTP %d). Periksa folder dan izin service account.', 'queen-alfalah-core' ), $status ) );
		}

		if ( $destination ) {
			return true;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return is_array( $data ) ? $data : new WP_Error( 'qaf_drive_json', __( 'Respons Google Drive tidak dapat dibaca.', 'queen-alfalah-core' ) );
	}

	/** Create and cache a short-lived OAuth access token. */
	private static function access_token() {
		$credentials = self::credentials();
		if ( is_wp_error( $credentials ) ) {
			return $credentials;
		}

		$cache_key = 'qaf_drive_token_' . md5( $credentials['client_email'] . $credentials['private_key_id'] );
		$cached    = get_transient( $cache_key );
		if ( is_string( $cached ) && $cached ) {
			return $cached;
		}

		if ( ! function_exists( 'openssl_sign' ) ) {
			return new WP_Error( 'qaf_drive_openssl', __( 'Ekstensi OpenSSL PHP diperlukan untuk menghubungkan Google Drive.', 'queen-alfalah-core' ) );
		}

		$now    = time();
		$header = self::base64url( wp_json_encode( array( 'alg' => 'RS256', 'typ' => 'JWT' ) ) );
		$claims = self::base64url(
			wp_json_encode(
				array(
					'iss'   => $credentials['client_email'],
					'scope' => self::TOKEN_SCOPE,
					'aud'   => $credentials['token_uri'],
					'iat'   => $now - 30,
					'exp'   => $now + 3600,
				)
			)
		);
		$unsigned = $header . '.' . $claims;
		$signature = '';
		if ( ! openssl_sign( $unsigned, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256 ) ) {
			return new WP_Error( 'qaf_drive_sign', __( 'Private key Google Drive tidak dapat digunakan.', 'queen-alfalah-core' ) );
		}

		$jwt      = $unsigned . '.' . self::base64url( $signature );
		$response = wp_remote_post(
			$credentials['token_uri'],
			array(
				'timeout' => 30,
				'body'    => array(
					'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
					'assertion'  => $jwt,
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status = wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( 200 !== $status || empty( $data['access_token'] ) ) {
			return new WP_Error( 'qaf_drive_token', __( 'Google tidak menerbitkan token akses. Periksa service account dan Google Drive API.', 'queen-alfalah-core' ) );
		}

		$ttl = isset( $data['expires_in'] ) ? max( 60, (int) $data['expires_in'] - 300 ) : 3300;
		set_transient( $cache_key, sanitize_text_field( $data['access_token'] ), $ttl );
		return sanitize_text_field( $data['access_token'] );
	}

	/** Load service-account JSON from wp-config or a path outside the web root. */
	private static function credentials() {
		$json = '';
		if ( defined( 'QAF_GOOGLE_DRIVE_CREDENTIALS_JSON' ) ) {
			$json = (string) QAF_GOOGLE_DRIVE_CREDENTIALS_JSON;
		} elseif ( defined( 'QAF_GOOGLE_DRIVE_CREDENTIALS_PATH' ) ) {
			$path = wp_normalize_path( (string) QAF_GOOGLE_DRIVE_CREDENTIALS_PATH );
			if ( ! $path || ! is_readable( $path ) ) {
				return new WP_Error( 'qaf_drive_credentials_path', __( 'Berkas kredensial Google Drive tidak ditemukan atau tidak dapat dibaca.', 'queen-alfalah-core' ) );
			}
			$json = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		}

		$data = json_decode( $json, true );
		if ( ! is_array( $data ) || empty( $data['client_email'] ) || empty( $data['private_key'] ) ) {
			return new WP_Error( 'qaf_drive_credentials', __( 'Kredensial service account Google Drive tidak lengkap.', 'queen-alfalah-core' ) );
		}

		return array(
			'client_email'  => sanitize_email( $data['client_email'] ),
			'private_key'   => $data['private_key'],
			'private_key_id'=> isset( $data['private_key_id'] ) ? sanitize_text_field( $data['private_key_id'] ) : '',
			'token_uri'     => 'https://oauth2.googleapis.com/token',
		);
	}

	/** Encode JWT segments without padding. */
	private static function base64url( $value ) {
		return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
}

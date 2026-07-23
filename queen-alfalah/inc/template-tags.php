<?php
/**
 * Reusable presentation helpers.
 *
 * @package Queen_AlFalah
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verified defaults plus conservative display copy.
 *
 * @return array<string, mixed>
 */
function queen_alfalah_default_school_settings() {
	return array(
		'school_name'       => 'SMK QUEEN AL-FALAH',
		'legal_name'        => 'SMK Queen Al-Falah Mojo',
		'motto'             => 'Pelopor Teknologi yang Islami',
		'npsn'              => '20574699',
		'accreditation'     => 'B',
		'founded'           => '21 Februari 2011',
		'foundation'        => 'Yayasan YPI Al-Muttaqien',
		'address'           => 'Jl. Raya Kebanan–Ploso, Ds. Ploso, Kec. Mojo, Kab. Kediri, Jawa Timur',
		'phone'             => '0354 4520550',
		'whatsapp'          => '6281222245445',
		'email'             => 'smkqueenalfalah@yahoo.com',
		'opening_hours'     => 'Senin–Sabtu, 08.00–14.00 WIB',
		'principal_name'    => 'Kepala SMK Queen Al-Falah',
		'principal_title'   => 'Kepala Sekolah',
		'principal_message' => 'Pendidikan vokasi yang kuat menyatukan kompetensi, karakter, dan keberanian untuk terus belajar.',
		'vision'            => 'Mencetak siswa yang cerdas, profesional, berdaya saing nasional maupun internasional, dan berakhlaqul karimah.',
		'ppdb_label'        => 'Pendaftaran Santri Baru',
		'ppdb_url'          => 'https://psb.queenalfalah.id/',
		'maps_url'          => 'https://www.google.com/maps/search/?api=1&query=SMK+Queen+Al+Falah+Mojo+Kediri',
		'instagram'         => '',
		'facebook'          => '',
		'youtube'           => '',
		'tiktok'            => '',
	);
}

/**
 * Retrieve portable plugin settings first, then theme fallback settings.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Optional default.
 * @return mixed
 */
function queen_alfalah_school_info( $key, $default = '' ) {
	$defaults = queen_alfalah_default_school_settings();
	$fallback = array_key_exists( $key, $defaults ) ? $defaults[ $key ] : $default;

	if ( function_exists( 'qaf_core_get_setting' ) ) {
		$plugin_keys = array(
			'school_name' => 'short_name',
			'legal_name'  => 'school_name',
			'founded'     => 'founded_date',
			'ppdb_url'    => 'registration_url',
			'maps_url'    => 'map_url',
			'facebook'    => 'facebook_url',
			'instagram'   => 'instagram_url',
			'youtube'     => 'youtube_url',
			'tiktok'      => 'tiktok_url',
		);
		$plugin_key = isset( $plugin_keys[ $key ] ) ? $plugin_keys[ $key ] : $key;
		$value      = qaf_core_get_setting( $plugin_key, $fallback );

		if ( 'founded' === $key && is_string( $value ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, wp_timezone() );
			if ( $date ) {
				$value = wp_date( 'j F Y', $date->getTimestamp(), wp_timezone() );
			}
		}
		if ( '' !== $value && null !== $value ) {
			return $value;
		}
		if ( in_array( $key, array( 'whatsapp', 'facebook', 'instagram', 'youtube', 'tiktok' ), true ) ) {
			return '';
		}
	}

	return get_theme_mod( 'queen_' . $key, $fallback );
}

/**
 * Get a page URL while remaining useful before demo setup.
 *
 * @param string $slug Page slug.
 * @return string
 */
function queen_alfalah_page_url( $slug ) {
	$page = get_page_by_path( sanitize_title( $slug ) );
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Resolve a CPT archive, with a stable fallback URL.
 *
 * @param string $post_type Post type.
 * @param string $fallback  Fallback slug.
 * @return string
 */
function queen_alfalah_archive_url( $post_type, $fallback = '' ) {
	$link = get_post_type_archive_link( $post_type );
	if ( $link ) {
		return $link;
	}
	$fallback = $fallback ? $fallback : str_replace( 'qaf_', '', $post_type );
	return home_url( '/' . trim( $fallback, '/' ) . '/' );
}

/**
 * Small inline SVG icon set. All icons are decorative by default.
 *
 * @param string $name  Icon name.
 * @param string $class Optional additional class.
 * @return string
 */
function queen_alfalah_icon( $name, $class = '' ) {
	$paths = array(
		'arrow-right'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-up'     => '<path d="m18 15-6-6-6 6"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'menu'         => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'        => '<path d="m6 6 12 12M18 6 6 18"/>',
		'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
		'phone'        => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.65 2.63a2 2 0 0 1-.45 2.11L8.04 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.31 1.73.53 2.63.65A2 2 0 0 1 22 16.92z"/>',
		'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'map-pin'      => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="2.5"/>',
		'calendar'     => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
		'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'users'        => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
		'book'         => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 5.5v14A2.5 2.5 0 0 0 6.5 22H20"/>',
		'monitor'      => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
		'briefcase'    => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V4h8v3M3 12h18M10 12v2h4v-2"/>',
		'heart'        => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>',
		'palette'      => '<path d="M12 3a9 9 0 0 0 0 18h1.5a1.5 1.5 0 0 0 0-3H12a2 2 0 0 1 0-4h4a5 5 0 0 0 0-10z"/><circle cx="7.5" cy="10" r=".7" fill="currentColor"/><circle cx="9.5" cy="6.8" r=".7" fill="currentColor"/><circle cx="14" cy="6.5" r=".7" fill="currentColor"/>',
		'award'        => '<circle cx="12" cy="8" r="6"/><path d="m8.5 13-1.5 9 5-3 5 3-1.5-9"/>',
		'building'     => '<path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6M8 10h.01M12 10h.01M16 10h.01"/>',
		'folder'       => '<path d="M3 6a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
		'external'     => '<path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/>',
		'check'        => '<path d="m5 12 4 4L19 6"/>',
		'play'         => '<circle cx="12" cy="12" r="9"/><path d="m10 8 6 4-6 4z"/>',
		'quote'        => '<path d="M10 11H5a4 4 0 0 1 4-4M20 11h-5a4 4 0 0 1 4-4M5 11v6h5v-6M15 11v6h5v-6"/>',
		'whatsapp'     => '<path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.5L3 20.5l1.3-4.7a8.5 8.5 0 1 1 16.2-4.1z"/><path d="M8.2 7.7c.2-.4.4-.4.7-.4h.4c.2 0 .4.1.5.4l.7 1.7c.1.3 0 .5-.1.7l-.5.6c-.2.2-.2.4 0 .7.7 1.2 1.7 2.2 3 2.8.3.2.6.1.8-.1l.7-.9c.2-.2.4-.3.7-.2l1.7.8c.3.1.4.3.4.5 0 .3-.2 1.5-1.1 2.1-.6.5-1.4.7-2.3.5-1.1-.2-2.6-.7-4.4-2.3-1.5-1.3-2.6-3-2.9-4.1-.3-1.1 0-2.1.4-2.6z"/>',
		'instagram'    => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>',
		'facebook'     => '<path d="M14 22v-8h3l.5-4H14V8c0-1.2.4-2 2-2h2V2.5c-.5-.1-1.7-.2-3-.2-3 0-5 1.8-5 5.2V10H7v4h3v8z"/>',
		'youtube'      => '<path d="M21.5 7.2a2.5 2.5 0 0 0-1.8-1.8C18.1 5 12 5 12 5s-6.1 0-7.7.4a2.5 2.5 0 0 0-1.8 1.8A26 26 0 0 0 2 12a26 26 0 0 0 .5 4.8 2.5 2.5 0 0 0 1.8 1.8c1.6.4 7.7.4 7.7.4s6.1 0 7.7-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.5-4.8z"/><path d="m10 15 5-3-5-3z"/>',
		'tiktok'       => '<path d="M15 3c.4 2.2 1.7 3.6 4 4v3a8 8 0 0 1-4-1.1v6.4a5.7 5.7 0 1 1-5-5.7v3.1a2.7 2.7 0 1 0 2 2.6V3z"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		$name = 'arrow-right';
	}

	$class = trim( 'icon icon--' . sanitize_html_class( $name ) . ' ' . $class );
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths[ $name ] . '</svg>';
}

/**
 * Print a consistent section heading.
 *
 * @param string $eyebrow Small label.
 * @param string $title   Section heading.
 * @param string $text    Supporting copy.
 * @param string $align   left or center.
 * @param string $id      Optional heading ID for aria-labelledby.
 */
function queen_alfalah_section_heading( $eyebrow, $title, $text = '', $align = 'left', $id = '' ) {
	$align = in_array( $align, array( 'left', 'center' ), true ) ? $align : 'left';
	?>
	<header class="section-heading section-heading--<?php echo esc_attr( $align ); ?>">
		<?php if ( $eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<h2<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>><?php echo esc_html( $title ); ?></h2>
		<?php if ( $text ) : ?>
			<p class="section-heading__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Retrieve a post meta value with a fallback.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key without or with leading underscore.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function queen_alfalah_meta( $post_id, $key, $default = '' ) {
	$key   = 0 === strpos( $key, '_qaf_' ) ? $key : '_qaf_' . ltrim( $key, '_' );
	$value = get_post_meta( $post_id, $key, true );
	return '' !== $value && null !== $value ? $value : $default;
}

/**
 * Render post date and category metadata.
 */
function queen_alfalah_post_meta() {
	?>
	<div class="entry-meta">
		<span><?php echo queen_alfalah_icon( 'calendar' ); ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
		<?php if ( 'post' === get_post_type() && get_the_category_list() ) : ?>
			<span class="entry-meta__category"><?php echo wp_kses_post( get_the_category_list( ', ' ) ); ?></span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Print breadcrumb navigation.
 */
function queen_alfalah_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items   = array();
	$items[] = array( 'label' => __( 'Beranda', 'queen-alfalah' ), 'url' => home_url( '/' ) );

	if ( is_home() ) {
		$items[] = array( 'label' => __( 'Berita', 'queen-alfalah' ), 'url' => '' );
	} elseif ( is_singular() ) {
		$post_type = get_post_type();
		if ( 'post' === $post_type ) {
			$posts_page = (int) get_option( 'page_for_posts' );
			$items[]    = array( 'label' => __( 'Berita', 'queen-alfalah' ), 'url' => $posts_page ? get_permalink( $posts_page ) : home_url( '/berita/' ) );
		} elseif ( 'page' !== $post_type ) {
			$object = get_post_type_object( $post_type );
			if ( $object ) {
				$items[] = array( 'label' => $object->labels->name, 'url' => get_post_type_archive_link( $post_type ) );
			}
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive() ) {
		$items[] = array( 'label' => post_type_archive_title( '', false ), 'url' => '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
	} elseif ( is_search() ) {
		$items[] = array( 'label' => sprintf( __( 'Hasil pencarian: %s', 'queen-alfalah' ), get_search_query() ), 'url' => '' );
	} elseif ( is_404() ) {
		$items[] = array( 'label' => __( 'Halaman tidak ditemukan', 'queen-alfalah' ), 'url' => '' );
	}

	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs container" aria-label="<?php esc_attr_e( 'Breadcrumb', 'queen-alfalah' ); ?>">
		<ol>
			<?php foreach ( $items as $index => $item ) : ?>
				<li>
					<?php if ( $item['url'] && $index < count( $items ) - 1 ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( wp_strip_all_tags( $item['label'] ) ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * Use a local illustration when a post has no thumbnail.
 *
 * @param string $variant Optional placeholder type.
 * @return string
 */
function queen_alfalah_placeholder( $variant = 'default' ) {
	$allowed = array( 'default', 'program', 'person', 'gallery' );
	$variant = in_array( $variant, $allowed, true ) ? $variant : 'default';
	return QUEEN_ALFALAH_URI . '/assets/images/placeholder-' . $variant . '.svg';
}

/**
 * Build a safe WhatsApp deep link.
 *
 * @param string $message Prefilled message.
 * @return string
 */
function queen_alfalah_whatsapp_url( $message = '' ) {
	$number = preg_replace( '/\D+/', '', (string) queen_alfalah_school_info( 'whatsapp' ) );
	if ( ! $number ) {
		return '';
	}
	if ( 0 === strpos( $number, '0' ) ) {
		$number = '62' . substr( $number, 1 );
	}
	$url = 'https://wa.me/' . $number;
	if ( $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}
	return $url;
}

/**
 * Public social links configured for the school.
 *
 * @return array<string,string>
 */
function queen_alfalah_social_links() {
	$links = array();
	foreach ( array( 'instagram', 'facebook', 'youtube', 'tiktok' ) as $network ) {
		$url = queen_alfalah_school_info( $network );
		if ( $url ) {
			$links[ $network ] = $url;
		}
	}
	return $links;
}

/**
 * Render links to share the current public URL without loading trackers.
 */
function queen_alfalah_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	$links = array(
		'WhatsApp' => 'https://wa.me/?text=' . $title . '%20' . $url,
		'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		'Telegram' => 'https://t.me/share/url?url=' . $url . '&text=' . $title,
	);
	?>
	<div class="share-links" aria-label="<?php esc_attr_e( 'Bagikan artikel', 'queen-alfalah' ); ?>">
		<span><?php esc_html_e( 'Bagikan:', 'queen-alfalah' ); ?></span>
		<?php foreach ( $links as $label => $link ) : ?>
			<a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?><span class="screen-reader-text"> <?php esc_html_e( '(tab baru)', 'queen-alfalah' ); ?></span></a>
		<?php endforeach; ?>
	</div>
	<?php
}

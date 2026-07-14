<?php
/**
 * Lightweight structured data that mirrors visible school content.
 *
 * @package Queen_AlFalah
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Avoid duplicate entity/article schema when a known SEO plugin owns it.
 *
 * @return bool
 */
function queen_alfalah_has_schema_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Output JSON-LD for the school and selected editorial content.
 */
function queen_alfalah_schema() {
	if ( queen_alfalah_has_schema_plugin() || ! apply_filters( 'queen_alfalah_enable_schema', true ) ) {
		return;
	}

	$schemas = array();
	if ( is_front_page() ) {
		$school = array(
			'@context'    => 'https://schema.org',
			'@type'       => array( 'EducationalOrganization', 'HighSchool' ),
			'@id'         => home_url( '/#school' ),
			'name'        => queen_alfalah_school_info( 'school_name' ),
			'alternateName' => queen_alfalah_school_info( 'legal_name' ),
			'url'         => home_url( '/' ),
			'email'       => queen_alfalah_school_info( 'email' ),
			'telephone'   => queen_alfalah_school_info( 'phone' ),
			'slogan'      => queen_alfalah_school_info( 'motto' ),
			'foundingDate'=> '2011-02-21',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => queen_alfalah_school_info( 'address' ),
				'addressLocality' => 'Kabupaten Kediri',
				'addressRegion'   => 'Jawa Timur',
				'addressCountry'  => 'ID',
			),
		);

		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$logo = wp_get_attachment_image_url( $logo_id, 'full' );
			if ( $logo ) {
				$school['logo'] = $logo;
			}
		}
		$same_as = array_values( queen_alfalah_social_links() );
		if ( $same_as ) {
			$school['sameAs'] = $same_as;
		}
		$schemas[] = $school;
	}

	if ( is_singular( 'post' ) ) {
		$article = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'NewsArticle',
			'headline'      => wp_strip_all_tags( get_the_title() ),
			'datePublished' => get_the_date( DATE_W3C ),
			'dateModified'  => get_the_modified_date( DATE_W3C ),
			'mainEntityOfPage' => get_permalink(),
			'publisher'      => array( '@id' => home_url( '/#school' ) ),
		);
		$image = get_the_post_thumbnail_url( null, 'full' );
		if ( $image ) {
			$article['image'] = $image;
		}
		$schemas[] = $article;
	}

	if ( is_singular( 'qaf_agenda' ) ) {
		$start = queen_alfalah_meta( get_the_ID(), 'start_date' );
		$end   = queen_alfalah_meta( get_the_ID(), 'end_date' );
		$event = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'Event',
			'name'      => wp_strip_all_tags( get_the_title() ),
			'url'       => get_permalink(),
			'eventStatus' => 'https://schema.org/EventScheduled',
			'location'  => array(
				'@type' => 'Place',
				'name'  => queen_alfalah_meta( get_the_ID(), 'location', queen_alfalah_school_info( 'school_name' ) ),
				'address' => queen_alfalah_school_info( 'address' ),
			),
		);
		if ( $start ) {
			$event['startDate'] = $start;
		}
		if ( $end ) {
			$event['endDate'] = $end;
		}
		$schemas[] = $event;
	}

	foreach ( $schemas as $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'queen_alfalah_schema', 30 );


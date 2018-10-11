<?php
/**
 * WP Rig Assets Management
 *
 * @package wp_rig
 */

/**
 * Enqueue styles.
 */
function wp_rig_styles() {

	// Add custom fonts, used in the main stylesheet.
	$fonts_url = wp_rig_fonts_url();
	if ( ! empty( $fonts_url ) ) {
		wp_enqueue_style( 'wp-rig-fonts', $fonts_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	// Enqueue main stylesheet.
	wp_enqueue_style( 'wp-rig-base-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );

	// Register all styles in the css dir.
	$wp_rig_theme_css_dir = get_theme_file_path( '/css/' );

	foreach ( glob( $wp_rig_theme_css_dir . '*.css' ) as $file_path ) {
		$file_modified_time = filemtime( $file_path );
		$file_name = str_replace( $wp_rig_theme_css_dir, '', $file_path );
		$file_slug = str_replace( '.css', '', $file_name );
		wp_register_style( "wp-rig-$file_slug", get_theme_file_uri( "/css/$file_name" ), array(), $file_modified_time );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_rig_styles' );

/**
 * Enqueue scripts.
 */
function wp_rig_scripts() {

	// If the AMP plugin is active, return early.
	if ( wp_rig_is_amp() ) {
		return;
	}

	// Enqueue the navigation script.
	wp_enqueue_script( 'wp-rig-navigation', get_theme_file_uri( '/js/navigation.js' ), array(), filemtime( get_stylesheet_directory() . '/js/navigation.js' ), false );
	wp_script_add_data( 'wp-rig-navigation', 'async', true );
	wp_localize_script(
		'wp-rig-navigation',
		'wpRigScreenReaderText',
		array(
			'expand'   => __( 'Expand child menu', 'wp-rig' ),
			'collapse' => __( 'Collapse child menu', 'wp-rig' ),
		)
	);

	// Enqueue skip-link-focus script.
	wp_enqueue_script( 'wp-rig-skip-link-focus-fix', get_theme_file_uri( '/js/skip-link-focus-fix.js' ), array(), filemtime( get_stylesheet_directory() . '/js/skip-link-focus-fix.js' ), false );
	wp_script_add_data( 'wp-rig-skip-link-focus-fix', 'defer', true );

	// Enqueue comment script on singular post/page views only.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_rig_scripts' );

/**
 * Enqueue WordPress theme styles within Gutenberg.
 */
function wp_rig_gutenberg_styles() {

	// Add custom fonts, used in the main stylesheet.
	$fonts_url = wp_rig_fonts_url();
	if ( ! empty( $fonts_url ) ) {
		wp_enqueue_style( 'wp-rig-fonts', $fonts_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	// Enqueue main stylesheet.
	wp_enqueue_style( 'wp-rig-editor-styles', get_theme_file_uri( '/css/editor/editor-styles.css' ), array(), filemtime( get_stylesheet_directory() . '/css/editor/editor-styles.css' ) );
}
add_action( 'enqueue_block_editor_assets', 'wp_rig_gutenberg_styles' );

/**
 * Returns Google Fonts used in theme.
 *
 * Has filter "wp_rig_google_fonts".
 *
 * @return array
 */
function wp_rig_get_google_fonts() {

	$fonts_default = array(
		'Roboto Condensed' => array( '400', '400i', '700', '700i' ),
		'Crimson Text'     => array( '400', '400i', '600', '600i' ),
	);

	/*
	 * Filters default Google fonts.
	 *
	 * @param array $fonts_default array of fonts to use
	 */
	return apply_filters( 'wp_rig_google_fonts', $fonts_default );
}

/**
 * Register Google Fonts
 */
function wp_rig_fonts_url() {

	$fonts_register = wp_rig_get_google_fonts();

	if ( empty( $fonts_register ) ) {
		return '';
	}

	$font_families = array();

	foreach ( $fonts_register as $font_name => $font_variants ) {
		if ( ! empty( $font_variants ) ) {

			// Make sure its an array.
			if ( ! is_array( $font_variants ) ) {
				$font_variants = explode( ',', str_replace( ' ', '', $font_variants ) );
			}

			$font_families[] = $font_name . ':' . implode( ',', $font_variants );

		} else {
			$font_families[] = $font_name;
		}
	}

	$query_args = array(
		'family' => implode( '|', $font_families ),
		'subset' => 'latin-ext',
	);

	return add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
}

/**
 * Add preconnect for Google Fonts.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function wp_rig_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'wp-rig-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'wp_rig_resource_hints', 10, 2 );

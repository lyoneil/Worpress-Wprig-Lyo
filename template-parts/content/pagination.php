<?php
/**
 * Template part for displaying a pagination
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( is_search() ) {
	the_posts_pagination(
		array(
			'mid_size'           => 2,
			'prev_text'          => _x( 'Previous', 'previous set of search results', 'wp-rig' ),
			'next_text'          => _x( 'Next', 'next set of search results', 'wp-rig' ),
			'screen_reader_text' => __( 'Search results navigation', 'wp-rig' ),
		)
	);
} else {
	the_posts_navigation();
}

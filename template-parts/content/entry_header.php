<?php
/**
 * Template part for displaying a post's header
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<header class="article-header entry-header">
	<?php get_template_part( 'template-parts/content/entry_title', get_post_type() ); ?>

	<?php get_template_part( 'template-parts/content/entry_meta', get_post_type() ); ?>

	<?php get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() ); ?>
</header><!-- .article-header -->

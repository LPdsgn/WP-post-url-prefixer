<?php
/**
 * WordPress Post URL prefixer
 *
 * @package     WP_PUP
 *
 * @wordpress-plugin
 * Plugin Name:     Post URL prefixer
 * Plugin URI:      https://jaimemartinez.nl/plugins/wp-post-url-prefixer/
 * Description:     Prefix only your post URL and not your tag and category permalinks & have a automatic blog posts archive
 * Author:          Jaime Martinez
 * Author URI:      https://jaimemartinez.nl
 * Text Domain:     wp_pup
 * Domain Path:     /languages
 * Version:         0.2.0
 */

if (!defined('ABSPATH')) {
    die; // Exit if accessed directly
}

// Include il file plugin.php per utilizzare is_plugin_active()
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Include le funzioni di gestione delle impostazioni
require_once plugin_dir_path(__FILE__) . '/admin/admin.php';

/**
 * Get the singular prefix of the post Post type.
 */
function wp_pup_get_singular_url_prefix() {
    $options = get_option( 'wp_pup_options' );
    return \apply_filters( 'wp_pup_singular_prefix', $options['singular_prefix'] ?? 'blog' );
}

/**
 * Get the archive slug of the post Post type.
 */
function wp_pup_get_archive_url_prefix() {
    $options = get_option( 'wp_pup_options' );
    if ( isset( $options['enable_archive'] ) && $options['enable_archive'] == 1 ) {
        return \apply_filters( 'wp_pup_archive_slug', $options['archive_slug'] ?? 'blog' );
    } else {
        return \apply_filters( 'wp_pup_archive_slug', false );
    }
}

/**
 * Set prefix value as slug of post and it's archive.
 *
 * @props https://stackoverflow.com/a/24635076/557225
 */
function wp_pup_register_post_type_args( $args, $post_type ) {

	if ( $post_type !== 'post' ) {
		return $args;
	}

	$prefix = \wp_pup_get_singular_url_prefix();

	if ( empty( $prefix ) ) {
		return $args;
	}

	$args['rewrite'] = array(
		'slug' => $prefix,
	);

	$page_for_posts = (int) \get_option( 'page_for_posts' );

	if ( $page_for_posts === 0 ) {
		$args['has_archive'] = \wp_pup_get_archive_url_prefix();
	}

	return $args;
}
add_filter( 'register_post_type_args', 'wp_pup_register_post_type_args', 15, 2 );

/**
 * Change permalink of post to contain our new prefix when calling `get_the_permalink()`
 */
function wp_pup_pre_post_link( $permalink, $post ) {
	$prefix = \wp_pup_get_singular_url_prefix();

	if ( empty( $prefix ) ) {
		return $permalink;
	}

	if ( $post instanceof WP_Post && $post->post_type === 'post' ) {
		return '/' . $prefix . $permalink;
	}

	return $permalink;
}
add_filter( 'pre_post_link', 'wp_pup_pre_post_link', 15, 2 );

/**
 * Add rewrite rule
 *
 * @props https://stackoverflow.com/a/63312315/557225
 */
function wp_pup_posts_add_rewrite_rules( $wp_rewrite ) {
	$archive           = wp_pup_get_archive_url_prefix();
	$singular          = \wp_pup_get_singular_url_prefix();
	$new_rules         = array(
		$archive . '/page/([0-9]{1,})/?$' => 'index.php?post_type=post&paged=' . $wp_rewrite->preg_index( 1 ),
		$singular . '/(.+?)/?$'           => 'index.php?post_type=post&name=' . $wp_rewrite->preg_index( 1 ),
	);
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	return $wp_rewrite->rules;
}
add_action( 'generate_rewrite_rules', 'wp_pup_posts_add_rewrite_rules' );

/**
 * Change post type archive link when calling `get_post_type_archive_link`.
 */
function wp_pup_post_type_archive_link( $link, $post_type ) {
	if ( $post_type === 'post' ) {
		return \trailingslashit( \trailingslashit( \get_home_url() ) . \wp_pup_get_archive_url_prefix() );
	}

	return $link;
}
add_filter( 'post_type_archive_link', 'wp_pup_post_type_archive_link', 10, 2 );

/**
 * Add our custom post type archive to the Yoast Breadcrumb when there is no page selected at the "Reading settings > Posts page".
 */
function wp_pup_wpseo_breadcrumb_links( $crumbs ) {

	// If there is a page selected on the "Reading settings > Posts page", then let Yoast do it's magic.
	if ( (int) \get_option( 'page_for_posts' ) !== 0 ) {
		return $crumbs;
	}

	$post_type_object = \get_post_type_object( 'post' );

	// If you the Posts don't have an Archive - e.g. FSE custom template with custom Query block - then do not.
	if ( ! $post_type_object->has_archive ) {
		return $crumbs;
	}

	$_crumbs[] = $crumbs[0]; // Save our home crumb in a tmp array.
	unset( $crumbs[0] ); // delete it.

	// Add our custom post type archive link.
	$_crumbs[] = array(
		'url'  => \get_post_type_archive_link( 'post' ),
		'text' => ( $post_type_object->labels->name ),
	);

	// Loop the remaining crumbs and re-add them to the tmp array to have a correct index numbering.
	foreach ( $crumbs as $crumbs ) {
		$_crumbs[] = $crumbs;
	}

	return $_crumbs;
}
add_filter( 'wpseo_breadcrumb_links', 'wp_pup_wpseo_breadcrumb_links', 20 );

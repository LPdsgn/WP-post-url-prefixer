<?php

if (!defined('ABSPATH')) {
    die; // Exit if accessed directly
}

// Aggiungi le impostazioni del plugin alla pagina delle impostazioni dei permalink
add_action( 'admin_init', 'wp_pup_add_permalinks_settings' );
function wp_pup_add_permalinks_settings() {
    add_settings_section(
        'wp_pup_section', // ID della sezione
        'Post URL Prefix Settings', // Titolo della sezione
        'wp_pup_section_callback', // Callback della funzione
        'permalink' // Pagina delle impostazioni dei permalink
    );

    add_settings_field(
        'wp_pup_singular_prefix', // ID del campo
        'Singular Post Prefix', // Titolo del campo
        'wp_pup_singular_prefix_render', // Callback della funzione
        'permalink', // Pagina delle impostazioni dei permalink
        'wp_pup_section' // ID della sezione
    );

    add_settings_field(
        'wp_pup_archive_slug', // ID del campo
        'Archive Slug', // Titolo del campo
        'wp_pup_archive_slug_render', // Callback della funzione
        'permalink', // Pagina delle impostazioni dei permalink
        'wp_pup_section' // ID della sezione
    );

    register_setting( 'permalink', 'wp_pup_options' );
}

function wp_pup_singular_prefix_render() {
    $options = get_option( 'wp_pup_options' );
    ?>
    <input type="text" name="wp_pup_options[singular_prefix]" value="<?php echo esc_attr( $options['singular_prefix'] ?? 'blog' ); ?>" />
    <?php
}

function wp_pup_archive_slug_render() {
    $options = get_option( 'wp_pup_options' );
    ?>
    <input type="text" name="wp_pup_options[archive_slug]" value="<?php echo esc_attr( $options['archive_slug'] ?? 'blog' ); ?>" />
    <?php
}

function wp_pup_section_callback() {
    echo 'Set your desired URL prefix for posts and post archive slug.';
}

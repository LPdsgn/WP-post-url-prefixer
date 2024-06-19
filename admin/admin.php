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
        'wp_pup_enable_archive', // ID del campo
        'Enable Archive', // Titolo del campo
        'wp_pup_enable_archive_render', // Callback della funzione
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

function wp_pup_enable_archive_render() {
    $options = get_option( 'wp_pup_options' );
    ?>
    <input type="checkbox" name="wp_pup_options[enable_archive]" value="1" <?php checked( 1, $options['enable_archive'] ?? 0 ); ?> />
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

// Aggiungi il JavaScript per gestire l'abilitazione/disabilitazione del campo "Archive Slug"
add_action( 'admin_enqueue_scripts', 'wp_pup_enqueue_scripts' );
function wp_pup_enqueue_scripts( $hook_suffix ) {
    if ( 'options-permalink.php' !== $hook_suffix ) {
        return;
    }
    wp_enqueue_script( 'wp_pup_admin_script', plugin_dir_url( __FILE__ ) . 'admin/wp-pup-admin.js', array( 'jquery' ), null, true );
}
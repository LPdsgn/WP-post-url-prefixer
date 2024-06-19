jQuery(document).ready(function($) {
    function toggleArchiveSlug() {
        if ($('#wp_pup_enable_archive').is(':checked')) {
            $('#wp_pup_archive_slug').prop('disabled', false);
        } else {
            $('#wp_pup_archive_slug').prop('disabled', true);
        }
    }

    $('#wp_pup_enable_archive').change(function() {
        toggleArchiveSlug();
    });

    // Chiamata iniziale per impostare lo stato corretto al caricamento della pagina
    toggleArchiveSlug();
});

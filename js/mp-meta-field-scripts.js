/**
 * mp-meta-fields-scripts.js
 *
 * General site scripts
 */

jQuery( document ).ready( function( $ ) {

    // Meta Box Template Selection
    // If a page template selection is changed, reveal the corresponding meta box
    allBoxes = $('#history_meta, #locations_meta');

    allBoxes.hide();
    function showMetaBoxes() {
        template = $('#page_template').val();

        if( template == 'page-templates/history.php' ) {
            // Show the meta fields
            $('#history_meta').slideDown();
        } else if(template == 'page-templates/locations.php') {
            $('#locations_meta').slideDown();
        } else {
            allBoxes.slideUp();
        }
    }
    // on page load
    showMetaBoxes();
    // on selection of the page template
    $('#page_template').change(showMetaBoxes);

});

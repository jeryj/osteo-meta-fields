/**
 * mp-meta-fields-scripts.js
 *
 * General site scripts
 */

jQuery( document ).ready( function( $ ) {

    // Meta Box Template Selection
    // If a page template selection is changed, reveal the corresponding meta box
    // the meta box has to be one word and end in _meta for it to work,
    // like homepage_meta with the page-template called homepage.php

    // get the possible meta boxes
    page_templates = new Array();
    // loop through each possible meta box
    $('#page_template option').each(function() {
        //grab the IDs
        file_name = $(this).val();
        file_name = file_name.replace('page-templates/', '');
        file_name = file_name.replace('.php', '');
        // push them to the meta box array
        page_templates.push(file_name);
    });

    // see if there are meta boxes that match the page_templates
    meta_boxes = new Array();
    $('.postbox').each( function() {
        meta_box = $(this).attr('id');
        meta_box = meta_box.replace('_meta', '');
        // check if the meta box is in the page_template array
        if(page_templates.indexOf(meta_box) !== -1) {
            // if it is, push it
            meta_boxes.push(meta_box);
        }
    });

    function showMetaBoxes() {
        if(meta_boxes.length === 0) {
            // there are no meta boxes, so don't worry about it
            // otherwise, it breaks the javascript on all other admin pages
            return;
        }
        template = $('#page_template').val();
        template = template.replace('page-templates/', '');
        template = template.replace('.php', '');

        meta_box_index = meta_boxes.indexOf(template);
        console.log(meta_box_index);
        // see if the template is in the page_templates array
        if( meta_box_index !== -1) {
            // Show the meta fields
            $('#'+meta_boxes[meta_box_index]+'_meta').slideDown();
        } else {
            // get all the boxes in a jquery handle
            var meta_boxes_length = meta_boxes.length;
            for (var i = 0; i < meta_boxes_length; i++) {
                $('#'+meta_boxes[i]+'_meta').slideUp();
            }
        }
    }
    // on page load
    showMetaBoxes();
    // on selection of the page template
    $('#page_template').change( showMetaBoxes );

});

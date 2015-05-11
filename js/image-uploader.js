jQuery( document ).ready( function( $ ) {

    // Uploading files / Image Selector

    // If you just want ONE image, add "single-image" to the image uploader div

    var file_frame;

    $(document).on('click', '.upload_attachments_button', function( event ){

        event.preventDefault();

        // Get the section title (ie - web, design, etc) so we know where we're at
        the_section = $(this).parent().attr('id');
        console.log(the_section);
        // Get the one in the featured input so we can remove it if necessary
        the_chosen_one = $('#'+the_section + ' .featured_id').val();

        // If the media frame already exists, reopen it.

        /* REMOVED because if there were multiple on one page it was opening the previous modal and not a new one
        if ( file_frame ) {
          file_frame.open();
          return;
        }*/

        //If the button was for a single upload, then set the multiple to false
        if( $(this).parent().hasClass('single-image') ) {
            multipleOption = false;
            selectorTitle = 'Select the Image';
        } else {
            multipleOption = true;
            selectorTitle = 'Select the Images';
        }

        console.log('multipleOption is '+multipleOption);
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          id: 'the-image-selector',
          title: selectorTitle,
          button: {
                    text: 'Submit'
                  },
          library : { type : 'image' },
          multiple: multipleOption  // Set to true to allow multiple files to be selected
        });

        // When frame is open, select existing image attachments from custom field
        file_frame.on( 'open', function() {
            var selection = file_frame.state().get('selection');

            var attachment_ids = $('#'+the_section+' #attachment_ids').val().split(',');


            attachment_ids.forEach(function(id) {
              attachment = wp.media.attachment(id);
              attachment.fetch();
              selection.add( attachment ? [ attachment ] : [] );
            });
        });

        // When images are selected, place IDs in hidden custom field and show thumbnails.
        file_frame.on( 'select', function() {
            console.log('on select ' +the_section);
            var selection = file_frame.state().get('selection');
            console.log(selection);
            // Place IDs in custom field
            var attachment_ids = selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                return attachment.id;
            }).join();

            // Remove Featured Image ID if that image isn't in the array
            /* attachment_ids_arr = split(',', attachment_ids);
            console.log(attachment_ids_arr);
            console.log(the_chosen_one);
            chosen_in_array = $.inArray( the_chosen_one, attachment_ids_arr);

            console.log(chosen_in_array); */

            if( attachment_ids.charAt(0) === ',' ) {
                attachment_ids = attachment_ids.substring(1);
            }
            $('#'+the_section+' #attachment_ids').val( attachment_ids );

            // Show Thumbs

            // Set the chosen one to false as a default
            the_chosen_one_is_here = 0;
            removeIcon = '<i class="icon-times-circle remove-img"></i>';
            var attachment_thumbs = selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                if( attachment.id != '' ) {
                    if(attachment.id) {
                        if(attachment.id == the_chosen_one && multipleOption !== false) {
                            // The chosen one is here! Mark it as such.
                            the_chosen_one_is_here = 1;
                            return '<div class="thumb"><img class="chosen" src="' + attachment.sizes.thumbnail.url + '" id="id-' + attachment.id +'"/>'+removeIcon+'</div>';
                        } else {
                            if(attachment.sizes.thumbnail !== undefined) {
                                // There is a thumbnail size
                                return '<div class="thumb"><img src="' + attachment.sizes.thumbnail.url + '" id="id-' + attachment.id + '" />'+removeIcon+'</div>';
                            } else {
                                // The image was too small to generate a thumbnail
                                return '<div class="thumb"><img src="' + attachment.sizes.full.url + '" id="id-' + attachment.id + '" />'+removeIcon+'</div>';

                            }

                        }
                    }
                }
            }).join(' ');
            $('#'+the_section+' #thumbs').html(attachment_thumbs);

            if(the_chosen_one_is_here === 1 && multipleOption !== false) {
                // the chose one is here!
            } else {
                // the chosen one is not here :(  Remove the value from the featured input
                $('#'+the_section+' .featured_id').val('');
            }

        });

        // Finally, open the modal
        file_frame.open();

    });

    // Place selected thumbnail ID into custom field to save as featured image
    $(document).on('click', '#thumbs img', function() {
        if($(this).parent().parent().parent().hasClass('single-image')) {
            // don't worry about it. There's only one.
        } else {
            var the_section = $(this).parent().parent().parent().attr('id');
            console.log(the_section);
            if($(this).hasClass('chosen')) {
                // we're clicking on the one that was already chosen, so let's clear it out
                $(this).removeClass('chosen');
                // clear the value
                $('#' + the_section + ' .featured_id').val('');
            } else {
                // remove chosen class from any other chosen one
                $('#' + the_section + ' #thumbs img').removeClass('chosen');
                // get the ID to enter it on the featured id field
                var thumb_ID = $(this).attr('id').substring(3);
                // set the value
                $('#' + the_section + ' .featured_id').val(thumb_ID);
                // add the chosen class
                $(this).addClass('chosen');

            }

        }
    });

    $(document).on('click', '.remove-img', function() {

            var the_section = $(this).parent().parent().parent().attr('id');
            console.log(the_section);
            var thumb_ID = $(this).siblings().attr('id').substring(3);
            console.log(thumb_ID);
            var FeaturedID = $('#' + the_section + ' .featured_id').val();
            if(thumb_ID == FeaturedID ) {
                // unset featured ID val if the one deleted is the featured img
                $('#' + the_section + ' .featured_id').val('');
            }

            // get all values
            var attachment_ids = $('#'+the_section+' #attachment_ids').val().split(',');
            // search for a match
            console.log(attachment_ids);
            attachmentIndex = $.inArray(thumb_ID, attachment_ids);
            // delete the match
            attachment_ids.splice(attachmentIndex,1);
            console.log(attachment_ids);
            // turn it back into comma separated string
            if(attachment_ids.length === 0) {
                 $('#'+the_section+' #attachment_ids').val('');
            } else {
                var thumbIDs = attachment_ids.join(',');
                // put it into the array
                $('#'+the_section+' #attachment_ids').val(thumbIDs);
            }

            // destroy the thumb
            $(this).parent().remove();

    });


});

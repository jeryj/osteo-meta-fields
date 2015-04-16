jQuery( document ).ready( function( $ ) {

    $('#addtag #submit').on('click', function(e) {
        e.preventDefault();

        // this isn't the best check, but it'll work for now
        // ideally, we'd get the ajax response and check it for errors and then
        // remove them if it's a positive response, but I can't find a way to
        // hook into their response and get it back :(

        input_errors = $('.form-invalid').length;
        if(input_errors > 0) {
            // there are no errors being output, so it should be OK to clear stuff
            // console.log('form_errors = '+input_errors);
        } else {
            // there are no inline form errors. it might remove them if
            // they are creating a term that already matches another term though...
            $('#thumbs img').remove();
            // uncheck everything
            $('#addtag input:checkbox').prop('checked', false);
            $('#addtag input:radio').prop('checked', false);
            // select first option in dropdown
            $('#addtag select option:first-child').prop("selected", true);
        }
    });

});

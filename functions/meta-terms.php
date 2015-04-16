<?php

// USAGE
/*
add_action('service_type_add_form_fields','extra_service_type_fields', 10, 2 ); // adds them to the creation form
add_action( 'service_type_edit_form_fields', 'extra_service_type_fields', 10, 2); // adds them to the edit form
function extra_service_type_fields($term) {

    $metaFields = array(
                        array(
                                'title'=>'Tagline',
                                'dataname'=>'tagline'
                            ),
                        array(
                                'title'=>'Header Background Image',
                                'type'=>'images',
                                'dataname'=>'bg_img',
                                'single-image'=>'true'
                            ),
                        array(
                                'title'=>'Service Category Logo',
                                'dataname'=>'logo',
                                'type' => 'images',
                                'single-image' => 'true'
                            ),

                        );
    // just pass it the $term
    metaTerm($term, $metaFields);

}
*/

function metaTerm($term, $fields = array()) {
    // getting passed the $term object and we're analyzing it to see what to do with it

    // since no term_id ever repeats, we're storing everything in the
    // wp_options table as "term_$term_id" as an array of all fields, like meta-items.php
    if(isset($term->term_id)) {
        // this means that we're editing a term, not creating a new one
        $term_id = $term->term_id;
        $term_value = get_option('term_'.$term_id);
    } else {
        // there's no value
        $term_value = null;
        $term_id = null;
    }

    $term_field = 'term_'.$term_id;

    foreach($fields as $field) {
        // use plain ol 'term_meta' for the array name. We'll change it to 'term_'.$term_id when we save it
        $fieldName = 'term_meta['.$field["dataname"].']';

        // pass on the value, if any
        if(isset($term_value[$field["dataname"]])) {
            $fieldValue = $term_value[$field["dataname"]];
        } else {
            $fieldValue = null;
        }

        // build the title / description for passing on to meta-fields.php
        (isset($field['title']) ? $termTitle = $field['title']: $termTitle = NULL);
        (isset($field['description']) ? $termDescription = $field['description'] : $termDescription = NULL);
        (isset($field['type']) ? $fieldType = $field['type'] : $fieldType = 'input');

        // Options for checkboxes, dropdowns, radio buttons
        (isset($field['options']) ? $termOpts = $field['options'] : $termOpts = NULL);

        // for passing on featured image value if we're on images
        if($fieldType == 'images' && isset($term_value[$field["dataname"].'FeaturedIMG'])) {
            $featuredImgVal = $term_value[$field["dataname"].'FeaturedIMG'];
        } else {
            $featuredImgVal = null;
        }
        // for passing on if we're on a single image
        if($fieldType == 'images' && isset($field['single-image'])) {
            $singleImage = $field['single-image'];
        } else {
            $singleImage = null;
        }

        // clear the array
        $termOptions = '';
        // set-up new options to pass on to meta-fields.php
        $termOptions = array(
                                'title' => $termTitle,
                                'description' => $termDescription,
                                'type' => $fieldType,
                                // For building out the field name (dataname = field[i][dataname]) & others thangs as needed
                                'dataname' => $field["dataname"],
                                // passing the actual value to meta-fields.php
                                'value' => $fieldValue,
                                // For chcekboxes, dropdowns, radio buttons
                                'options' => $termOpts,
                                'is_term' => true,
                                'featured_value' => $featuredImgVal,
                                'single-image' => $singleImage,
                            );

        // create and edit have different formatting, so we're giving them
        // the right html so it formats correctly
        $request_link = "$_SERVER[REQUEST_URI]";
        if(strpos($request_link,'action=edit') !== false) {
            $div_or_tr = 'tr';
        } else {
            $div_or_tr = 'div';
        }
                            ?>

        <<?php echo $div_or_tr;?> class="form-field">
            <th scope="row" valign="top">
                <?php echo (!empty($termTitle) ? '<label for="'.$field["dataname"].'">'.$termTitle.'</label>' : '');?>
            </th>
            <td>
                <?php
                    whichField($fieldName, $termOptions);
                    echo (!empty($termDescription) ? '<span class="description">'.$termDescription.'</span>' : '');
                ?>
            </td>
        </<?php echo $div_or_tr;?>>
        <?php
    }
}

function saveTerm($term_id, $options = array()) {
    if ( isset( $_POST['term_meta'] ) ) {
        $term_values = array_keys($_POST['term_meta']);
            foreach ($term_values as $key){
            if (isset($_POST['term_meta'][$key])){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( 'term_'.$term_id, $term_meta );
    }
}

// generic created term so it tries to save it all the time
// no need to put it in the template now
add_action( 'edited_term', 'update_term_fields', 10, 2);
add_action('created_term', 'update_term_fields', 10, 2);
function update_term_fields($term_id) {
    saveTerm($term_id);
}
?>

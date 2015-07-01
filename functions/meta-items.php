<?php

function allItems($field, $options = array()) {

    if($options['data'] == 'option') {
        $items = get_option($field);
    } else {
        $items = get_post_meta(get_the_ID(), $field);
    }

    $i = 0;
    if($items !== false && !empty($items)) {
        if($options['data'] == 'option') {
            foreach ($items as $detail) {
                // set the count
                $options['i'] = $i;
                // set the value
                $options['value'] = $detail;
                getItem($field, $options);
                // getItem($field, $options['fields'], $options['i'], $options['value']);
                $i++;
            }
        } else {
            foreach($items as $the_item => $details) {
                foreach ($details as $detail) {
                    // set the count
                    $options['i'] = $i;
                    // set the value
                    $options['value'] = $detail;
                    getItem($field, $options);
                    // getItem($field, $options['fields'], $options['i'], $options['value']);
                    $i++;
                }
            }
        }

    } else {
        // there aren't any items, so let's add one blank one to start out
        // give value a value so it doesn't give us a notice
        $options['value'] = NULL;
        $options['i'] = $i;
        getItem($field, $options);
        // getItem($field, $options['fields'], $options['i']);
    }
}

// function getItem($field, $itemFields, $item_id,  $detail = false ) {
function getItem($field, $options) {
    $item_id = $options['i'];
    $value = $options['value'];
    ?>
    <div class="well item item-<?php echo $item_id;?>">
        <div class="add-item"><i class="icon-plus-circle"></i></div>
        <div class="remove-item"><i class="icon-times-circle"></i></div>

        <?php
        // loop through each of the fields
        foreach($options['fields'] as $itemField) {


            $fieldName = $field.'['.$item_id.']['.$itemField["dataname"].']';

            // get the value
            (isset( $value[$itemField["dataname"]] ) ? $itemValue = $value[$itemField["dataname"]] : $itemValue = NULL );
            // build the title / description for passing on to meta-fields.php
            (isset($itemField['type']) ? $itemType = $itemField['type']: $itemType = 'input');
            (isset($itemField['title']) ? $itemTitle = $itemField['title']: $itemTitle = NULL);
            (isset($itemField['required']) ? $itemRequired = $itemField['required'] : $itemRequired = false);
            (isset($itemField['description']) ? $itemDescription = $itemField['description'] : $itemDescription = NULL);
            // Options for checkboxes, dropdowns, radio buttons
            (isset($itemField['options']) ? $itemOpts = $itemField['options'] : $itemOpts = NULL);
            // Args for loop
            (isset($itemField['args']) ? $itemArgs = $itemField['args'] : $itemArgs = NULL);
            // When Args are set, we might have a secondary type (like radio, or dropdown)
            (isset($itemField['secondary_type']) ? $itemSecondaryType= $itemField['secondary_type'] : $itemSecondaryType= NULL);
            (isset($itemField['taxonomy']) ? $itemTaxonomy = $itemField['taxonomy'] : $itemTaxonomy = NULL);

            // clear the array
            $itemOptions = '';
            // set-up new options to pass on to meta-fields.php
            $itemOptions = array(
                                    'title' => $itemTitle,
                                    'required' => $itemRequired,
                                    'description' => $itemDescription,
                                    'type' => $itemType,
                                    // For building out the field name (dataname = field[i][dataname]) & others thangs as needed
                                    'dataname' => $itemField["dataname"],
                                    // passing the actual value to meta-fields.php
                                    'value' => $itemValue,
                                    // For chcekboxes, dropdowns, radio buttons
                                    'options' => $itemOpts,
                                    // args for Loop
                                    'args' => $itemArgs,
                                    'secondary_type' => $itemSecondaryType,
                                    'taxonomy' => $itemTaxonomy,
                                );
            ?>
            <?php

            if($itemType == 'images' || $itemType == 'image') :
                $itemOptions['og_field'] = $field;
                $itemOptions['i'] = $item_id;
                (isset($value[$itemField["dataname"].'FeaturedIMG']) ? $itemOptions['featured_value'] = $value[$itemField["dataname"].'FeaturedIMG'] : $itemOptions['featured_value'] = '');
                $itemOptions['meta-items'] = true;
                metaField($fieldName, $itemOptions);
            else :
                metaField($fieldName, $itemOptions);
            endif;
        }?>

    </div>
<?php
}


function metaItems($field, $options = array()) {
    $defaultOptions = array(
                            'fields' => array(), // an array of all the fields we need
                            'data' => 'post_meta' // or 'option'. Are we saving a post meta or option field?
                        );
    $options = array_merge($defaultOptions, $options);
    // Noncename needed to verify where the data originated
    wp_nonce_field( basename( __FILE__ ), $field.'_nonce' );
    wp_enqueue_script( 'osteo-meta-items-scripts', plugins_url('osteo-meta-fields/js/meta-items.js'), array(), '20120206', true );
    ?>
    <div class="<?php echo $field;?> items osteo-meta-styles">
        <?php allItems($field, $options);?>
        <div class="add-item last"><i class="icon-plus-circle"></i></div>
    </div>
<?php
}


?>

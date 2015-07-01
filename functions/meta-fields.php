<?php

function metaField($field, $options = array() ) {
    wp_enqueue_style( 'osteo-meta-styles', plugins_url('osteo-meta-fields/css/osteo-meta-styles.css') );
    wp_enqueue_style( 'osteo-meta-icons', plugins_url('osteo-meta-fields/icons/style.css') );
    wp_enqueue_script( 'osteo-general-field-scripts', plugins_url('osteo-meta-fields/js/osteo-meta-field-scripts.js'), array(), '20120206', true );

    $defaultOptions = array(
                            'title' => null, // title for the field(s)
                            'description' => null, // enter a description
                            'type' => 'input', // checkbox, dropdown, select, images, image, editor, loop, terms
                            'class' => null, // pass a class if you want to
                            'options' => null, //an array of arrays
                                                // array(
                                                //      array('value'=>'yes', 'description'=>'Of Course!'),
                                                //      array('value'=>'no', 'description'=>'No Way!'),
                                                //    ),
                                                // for use with checkboxes, dropdowns, selects
                            'required' => false, // for displaying the red *. still need to define it as required when saving in metaSave()
                            'data' => 'post_meta', // option, term
                            'args' => false, // pass WP_Query args if needed
                            'secondary_type' => null, // dropdown, radio, checkbox. For use with Loop or Terms
                            'style' => false, // an array of any style attribute that might go with that field.
                                                // like, cols or size or width or height. Only used some places
                            'taxonomy' => false, // a string or array of taxonomies for use with terms type
                            'value' => null, // set the value if necessary. we'll set it later anyways.
                            'meta-items' => null // for some instances (we're looking at you images) when we need to know if it's from the meta-items functions
                            );
    $options = array_merge($defaultOptions, $options);
    // get the value of the field and set it

    if(isset($options['value'])) {
        // weird... probably shouldn't be set yet
    } else {
        // check if we should get_meta or get_option
        if($options['data'] == 'post_meta') {
            /* Removed because we don't need it to be an array of arrays, we just need a single array of choices
                if($options['type'] == 'checkbox') {
                    $array_or_string = false; // give us that array
                } else {
                    $array_or_string = true; // let's get a string instead of an array
                }
                $options['value'] = get_post_meta(get_the_ID(), $field, $array_or_string);
            */
            $options['value'] = get_post_meta(get_the_ID(), $field, true);

        } elseif($options['data'] == 'option') {
            // use get_option
            $options['value'] = get_option($field);
        }
    }

    if(isset($options['title'])){
        echo '<p class="meta-title"><strong>'.$options["title"].'</strong>'.( $options["required"] == true ?  '<span style="color: red;">*</span>' : '').(!empty($options["description"]) ? ' '.$options["description"] : '').'</p>';
    }

    whichField($field, $options);
}

function whichField($field, $options) {
    if($options['type'] == 'input') :
        metaInput($field, $options);
    elseif($options['type'] == 'textarea') :
        metaTextarea($field, $options);
    elseif($options['type'] == 'editor') :
        metaEditor($field, $options);
    elseif($options['type'] == 'dropdown') :
        metaDropdown($field, $options);
    elseif($options['type'] == 'checkbox') :
        metaCheckbox($field, $options);
    elseif($options['type'] == 'radio') :
        metaRadio($field, $options);
    elseif($options['type'] == 'loop') :
        metaLoop($field, $options);
    elseif($options['type'] == 'terms') :
        metaTerms($field, $options);
    elseif($options['type'] == 'images') :
        metaImages($field, $options);
    elseif($options['type'] == 'image') :
        $single = true;
        metaImages($field, $options, $single);
    elseif($options['type'] == 'icon') :
        metaIcon($field, $options);
    endif;
}

function metaInput($field, $options) {
    if(!empty($options['style'])) {
        $size = ' size="'.$options['style']['size'].'"';
    }
    if(!empty($options['class'])) {
        $class = ' class="'.$options['class'].'"';
    } else {
        $class = ' class="widefat"';
    }
    // add the [i] in for item loops from meta-items
    echo '<input type="text" name="'.$field.'" value="'.(!empty($options['value']) ? $options['value'] : '').'"'.$class.(!empty($size) ? $size : '' ).'/>';
}

function metaTextarea($field, $options) {
    if(!empty($options['class'])) {
        $class = ' class="'.$options['class'].'"';
    } else {
        $class = ' class="widefat"';
    }
    echo '<textarea name="'.$field.'"'.$class.'>'.(!empty($options['value']) ? $options['value'] : '').'</textarea>';
}

function metaEditor($field, $options) {
    if(!empty($options['style'])) {
        $rows = $options['style']['textarea_rows'];
    } else {
        $rows = 3;
    }
    // editor ID can't have [] in it
    $editorID = str_replace(array('[',']'), '-', $field);

    wp_editor($options['value'], $editorID, array( 'textarea_name'=>$field, 'textarea_rows' => $rows));
}

function metaDropdown($field, $options) {
    $dropdownOptions = $options['options'];
    (isset($options['value']) ? $is_selected = $options['value'] : $is_selected ='');
    ?>
    <label>
        <select name="<?php echo $field;?>">
            <?php

            foreach($dropdownOptions as $dropdownOption) {?>
                <option value="<?php echo $dropdownOption["value"];?>" <?php if( isset($is_selected) ) selected($is_selected, $dropdownOption["value"]);?>><?php echo $dropdownOption["description"];?>
            <?php }?>
        </select>
    </label>
<?php
}

function metaRadio($field, $options) {
    $radioOptions = $options['options'];

    (isset($options['value']) ? $is_checked = $options['value'] : $is_checked ='');

    foreach($radioOptions as $radioOption) {?>
        <input type="radio" name="<?php echo $field;?>" value="<?php echo $radioOption["value"];?>" <?php checked( $is_checked, $radioOption["value"]);?>><?php echo $radioOption["description"];?><br/>
    <?php }
}

function metaCheckbox($field, $options) {
    $checkboxOptions = $options['options'];
    (isset($options['value']) && !empty($options['value']) ? $checkedItems = $options['value'] : $checkedItems = array());
    foreach($checkboxOptions as $checkboxOption) {?>
        <input type="checkbox" name="<?php echo $field;?>[]" value="<?php echo $checkboxOption["value"];?>"<?php echo (in_array($checkboxOption["value"], $checkedItems) ? ' checked="checked"' : '');?>><?php echo $checkboxOption["description"];?><br/>
    <?php }
}

function metaLoop($field, $options) {
    /* $loop_args = ;
        $loop_args = array('type' => 'loop',
                            'args' => array(
                                        'post_type' => 'services',
                                        'posts_per_page' => '-1'
                                      )
                            'secondary_type' => 'checkbox' // dropdown, radio
                            )

        metaField('buttz', ); */
    // we have to use get_posts because WP_Query can't reset in admin
    // https://core.trac.wordpress.org/ticket/18408
    if(!isset($options['secondary_type'])){$options['secondary_type'] = 'checkbox';}
    $the_posts = get_posts( $options['args'] );
    $options['options'] = array();
    foreach ($the_posts as $the_post) : setup_postdata( $the_post );
        $postOptions = array('value' => $the_post->ID, 'description' => $the_post->post_title);
        array_push($options['options'], $postOptions);
    endforeach;
    wp_reset_postdata();
    // pass it on to whatever we need to do
    if($options['secondary_type'] == 'checkbox') :
        metaCheckbox($field, $options);
    elseif($options['secondary_type'] == 'dropdown') :
        metaDropdown($field, $options);
    elseif($options['secondary_type'] == 'radio') :
        metaRadio($field, $options);
    endif;
}

function metaTerms($field, $options) {
    /*
        $term_args = 'type' => 'terms',
                    'title' => 'Select Related Terms',
                    'taxonomy' => 'test_type', //taxonomy name
                    'args' => // any args for get_terms https://codex.wordpress.org/Function_Reference/get_terms
                    'secondary_type' => 'checkbox',
                    );


        metaField('buttz', $term_args); */

    if(!isset($options['secondary_type'])){$options['secondary_type'] = 'checkbox';}
    $the_terms = get_terms( $options['taxonomy'], $options['args'] );
    $options['options'] = array();

    foreach ($the_terms as $the_term) :
        $termOptions = array('value' => $the_term->term_id, 'description' => $the_term->name);
        array_push($options['options'], $termOptions);
    endforeach;

    // pass it on to whatever we need to do
    if($options['secondary_type'] == 'checkbox') :
        metaCheckbox($field, $options);
    elseif($options['secondary_type'] == 'dropdown') :
        metaDropdown($field, $options);
    elseif($options['secondary_type'] == 'radio') :
        metaRadio($field, $options);
    endif;
}

function metaImages($field, $options, $single = false) {
    wp_enqueue_style( 'osteo-meta-icons', plugins_url('osteo-meta-fields/icons/style.css') );
    wp_enqueue_style( 'osteo-meta-styles', plugins_url('osteo-meta-fields/css/osteo-meta-styles.css') );
    wp_enqueue_script( 'osteo-image-uploader', plugins_url('osteo-meta-fields/js/image-uploader.js'), array(), '20120206', true );

    // see if we're dealing with a single one or not
    if($single == false) {
        // check to see if it's set by the $options array
        if(isset($options['single-image']) && $options['single-image'] == true) {
            $single = true;

        }
    }

    if($single !== false) {
        $single = ' single-image';
        $btn_val = 'Select Image';
    } else {
        $single = '';
        $btn_val = 'Select Images';
    }

    if(isset($options['meta-items']) && $options['meta-items'] === true) {
        $identifier = $options['og_field'].'-'.$options['dataname'].'-image-'.$options['i'];
    } else {
        $identifier = $field;
    }


    // Create our variables
    $Images = $options['value'];

    $featuredIMG = '';
    // OK. This is super lame, I know, but we need to know if it's from the meta-items loop or not so we can set the name value right
    if(isset($options['meta-items']) && $options['meta-items'] == true){
        // put it in brackets
        $identifier = $options['og_field'].'-'.$options['dataname'].'-image-'.$options['i'];
        $featuredName = $options['og_field'].'['.$options['i'].']['.$options['dataname'].'FeaturedIMG]';
        $imagesName = $options['og_field'].'['.$options['i'].']['.$options['dataname'].']';
        (!empty($options['featured_value']) ? $featuredIMG = $options['featured_value'] : $featuredIMG = '');
    } elseif(isset($options['is_term']) && $options['is_term'] == true) {
        // only include the meta-fields thumbs clear on terms create page
        (!isset($options['value']) ? wp_enqueue_script( 'osteo-meta-fields-ajax', plugins_url('osteo-meta-fields/js/ajax-response.js'), array(), '20120206', false ) : '');
        $identifier = $options['dataname'].'-image';
        $featuredName = 'term_meta['.$options['dataname'].'FeaturedIMG]';
        $imagesName = 'term_meta['.$options['dataname'].']';
        (!empty($options['featured_value']) ? $featuredIMG = $options['featured_value'] : $featuredIMG = '');
    } else {
        // proceed without it
        $identifier = $field.'-image';
        $featuredName = $field.'FeaturedIMG';
        $imagesName = $field;
        $featuredIMG = get_post_meta(get_the_ID(), $field.'FeaturedIMG', true);
    }
    ?>

    <div class="image-uploader<?php echo $single;?>" id="<?php echo $identifier;?>">
        <div id="thumbs">
            <?php if( !empty( $Images ) ){
                $the_attachment_ids = explode(",", $Images);
                foreach($the_attachment_ids as $the_id) {
                    $the_thumb = wp_get_attachment_image_src( $the_id, 'thumbnail' );
                    echo '<div class="thumb">';
                        if( $the_id == $featuredIMG ) {
                            echo '<img src="'.$the_thumb[0] .'" class="chosen" id="id-'.$the_id.'"/>';
                        } else {
                            echo '<img src="'.$the_thumb[0] .'" id="id-'.$the_id.'"/>';
                        }
                    echo '<i class="icon-times-circle remove-img"></i></div>';
                }
            }?>
        </div>
        <?php if($single !== false){?>
            <input type="hidden" class="featured_id" name="<?php echo $featuredName;?>" id="<?php echo $identifier;?>FeaturedIMG" value="<?php echo $featuredIMG;?>"/>
        <?php } ?>
        <input type="hidden" class="attachment_ids" name="<?php echo $imagesName;?>" id="attachment_ids" value="<?php echo $Images;?>"/>
        <input type="button" class="button upload_attachments_button" value="<?php echo $btn_val;?>" />
    </div>
<?php
}

function metaIcon($field, $options) {
    wp_enqueue_script( 'osteo-icon-selector', plugins_url('osteo-meta-fields/js/meta-icons.js'), array(), '20120206', true );
    wp_enqueue_style( 'current-theme-icons', get_bloginfo('template_directory' ).'/icons/style.css' );

    (isset($options['value']) ? $is_selected = $options['value'] : $is_selected ='');

    $pattern = '/\.(icon-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
    $subject = file_get_contents( get_bloginfo('template_directory' ).'/icons/style.css' );
    preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
    if(empty($matches)) {
        // try with font-awesome fa- prefix
        $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
    }
    echo '<div class="icon-selector">';
        echo '<ul class="icon-grid">';
        foreach($matches as $match){
            // if( (strpos($match[1],'break') !== false) || (strpos($match[1],'history') !== false) || (strpos($match[1],'demo') !== false) || (strpos($match[1],'eco') !== false) || (strpos($match[1],'tool') !== false) || (strpos($match[1],'worker') !== false) ) {
                     echo '<li><i class="'.$match[1].'"></i></li>';
            // }
        }
        echo '</ul>';

        echo '<select name="'.$field.'" class="icon-dropdown">';
        echo '<option value="none">none</option>';
        foreach($matches as $match){
            // use this if we need to only get certain icons
            // if( (strpos($match[1],'break') !== false) || (strpos($match[1],'history') !== false) || (strpos($match[1],'demo') !== false) || (strpos($match[1],'eco') !== false) || (strpos($match[1],'tool') !== false) || (strpos($match[1],'worker') !== false) ) { ?>

                <option value="<?php echo $match[1];?>" <?php if ( isset($is_selected) ) selected($is_selected, $match[1] );?>><?php echo $match[1];?></option>

            <?php // }
        }
        echo '</select>';
    echo '</div>';
}

// Don't worry about setting metaField data=>'option'
function metaOption($field, $options = array()) {
    // set it to be data option
    $defaultOptions = array('data' => 'option');
    //merge the array
    $options = array_merge($defaultOptions, $options);
    // pass it on to the metaField for processing
    metaField($field, $options);

}

function metaSave($field, $options = array() ) {
    $defaultOptions = array(
                            'required' => false, // array of required field data titles. ex - array('_metaKey', '_anotherRequiredKey');
                            'data' => 'post_meta', // option
                            );

    $options = array_merge($defaultOptions, $options);

    if($options['data'] == 'post_meta') {
        ( isset($_POST[$field]) ? $fieldData = $_POST[$field] : $fieldData = NULL);
    } elseif($options['data'] == 'option') {
        $fieldData = $field;
    }

    if(is_array($fieldData)) {
        $saveItem = array();
        foreach($fieldData as $the_Item) {
            if(is_array($the_Item)){
                $the_Item = array_filter($the_Item);
            }

            // Check for required fields
            if($options['required'] !== false) {
                // loop through each field and see if it has a missing field
                foreach($options['required'] as $requiredField) {
                    if(!empty($the_Item[$requiredField])) {
                        $saveIt = true;
                    } else {
                        // There's a missing required field! Break the fuck outa here
                        $saveIt = false;
                        break;
                    }
                }

            } else {
                $saveIt = true;
            }

            if($saveIt === true) {
                array_push($saveItem, $the_Item);
            }

        }
        // Now save the whole array
        $saveItem = array_filter($saveItem);

    } else {
        // not an array, just a string
        $saveItem = $fieldData;
    }

    if(isset($saveItem) && !empty($saveItem)) {
        if($options['data'] == 'post_meta') {
            // check if it's empty
            update_post_meta(get_the_ID(), $field, $saveItem);
        } elseif($options['data'] == 'option') {
            return $saveItem;
        }
    } else {
        // it's empty, so delete it
        if($options['data'] == 'post_meta') {
            // check if it's empty
            delete_post_meta(get_the_ID(), $field);
        }
            // don't need to deal with $options['data'] == 'option',
            // because register_setting( '....', 'field...' ); handles it for us
    }
}

function metaSaveSetup($post_id) {
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return false;
    } else {
        return true;
    }
}

function metaOptionArraySaveSetup($value) {
    if(!empty($value)) {
        // have to return it instead of save it right there
        return metaSave($value, array('data'=>'option'));
    } else {
        return;
    }
}


/* Could be useful later for saving something FROM a meta field into the options table
    if( isset($_POST['_serviceLogin']) ) {
        // save it
        metaSave('_serviceLogin');

        // update the wp_options table with all of our logins
        $currentServiceLogins = get_option( 'serviceLogins' );
        // loop through
        if(!empty($currentServiceLogins)) {
            $i = 0;
            foreach($currentServiceLogins as $login) {
                // check to see if this post ID is already there
                if($login["id"] == $post_id) {
                    // remove it from the array
                     unset($currentServiceLogins[$i]);
                }
                $i++;
            }
            // now add it back in to the array
            $currentServiceLogins[] = array("id" => $post_id, "title" => $_POST['post_title'], "url" => $_POST['_serviceLogin']);
            update_option( 'serviceLogins', $currentServiceLogins );
        } else {
            // create the option
            $currentServiceLogins[] = array("id" => $post_id, "title" => $_POST['post_title'], "url" => $_POST['_serviceLogin']);
            update_option( 'serviceLogins', $currentServiceLogins );
        }

    } else {
        // delete it from the array
        $currentServiceLogins = get_option( 'serviceLogins' );
        // loop through
        if(!empty($currentServiceLogins)) {
            $i = 0;
            foreach($currentServiceLogins as $login) {
                // check to see if this post ID is already there
                if($login["id"] == $post_id) {
                    // remove it from the array
                    unset($currentServiceLogins[$i]);
                }
                $i++;
            }
            // now DON'T add it back in to the array
            update_option( 'serviceLogins', $currentServiceLogins );
        }
    }
*/


?>

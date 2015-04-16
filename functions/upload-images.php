<?php
/* function for adding uploading specific images or image types to a custom post type

// add your sectionImages('artists');
// then make sure to save the info:
//
// $artists_meta['_artistsFeaturedIMG'] = $_POST['_artistsFeaturedIMG'];
// $artists_meta['_artistsImages'] = $_POST['_artistsImages'];

*/

function sectionImages($the_section) {
    // Enqueue styles
    wp_enqueue_style( 'mp-meta-styles', plugins_url('mp-meta-fields/css/mp-meta-styles.css') );
    wp_enqueue_script( 'mp-image-uploader', plugins_url('mp-meta-fields/js/image-uploader.js'), array(), '20120206', true );

    // Create our variables
    $FeaturedIMG = get_post_meta(get_the_ID(), '_'.$the_section.'FeaturedIMG', true);
    $Images = get_post_meta(get_the_ID(), '_'.$the_section.'Images', true);?>

    <div class="image-uploader" id="<?php echo $the_section;?>">
        <input type="hidden" class="featured_id" name="_<?php echo $the_section;?>FeaturedIMG" id="_<?php echo $the_section;?>FeaturedIMG" value="<?php echo $FeaturedIMG;?>"/>
        <input type="hidden" class="attachment_ids" name="_<?php echo $the_section;?>Images" id="attachment_ids" value="<?php echo $Images;?>"/>
        <input type="button" class="button upload_attachments_button" value="Select Images" />
        <p>You have to hold down Shift or Apple / CMD to select multiples. Then click a thumbnail to set it as featured.</p>

        <div id="thumbs">

            <?php if( !empty( $Images ) ){
                $the_attachment_ids = explode(",", $Images);
                foreach($the_attachment_ids as $the_id) {
                    $the_thumb = wp_get_attachment_image_src( $the_id, 'thumbnail' );
                    if( $the_id == $FeaturedIMG ) {
                        echo '<img src="'.$the_thumb[0] .'" class="chosen" id="id-'.$the_id.'"/>';
                    } else {
                        echo '<img src="'.$the_thumb[0] .'" id="id-'.$the_id.'"/>';
                    }
                }
            }?>
        </div>
    </div>
<?php
}

function singleImageURL($the_section) {
    // Enqueue styles
    wp_enqueue_style( 'mp-meta-styles', plugins_url('mp-meta-fields/css/mp-meta-styles.css') );
    wp_enqueue_script( 'mp-image-uploader', plugins_url('mp-meta-fields/js/image-uploader.js'), array(), '20120206', true );

    // Create our variables
    $Images = get_option($the_section); ?>

    <div class="image-uploader single-image" id="<?php echo $the_section;?>">
        <div id="thumbs">
            <?php if( !empty( $Images ) ){
                $the_attachment_ids = explode(",", $Images);
                foreach($the_attachment_ids as $the_id) {
                    $the_thumb = wp_get_attachment_image_src( $the_id, 'thumbnail' );
                        echo '<div class="thumb"><img src="'.$the_thumb[0] .'" id="id-'.$the_id.'"/><i class="icon-times-circle remove-img"></i></div>';
                    }
                }?>
        </div>
        <input type="hidden" class="attachment_ids" name="<?php echo $the_section;?>" id="attachment_ids" value="<?php echo $Images;?>"/>
        <input type="button" class="button upload_attachments_button" value="Select Image" />
    </div>
<?php
}

  __  __ ___   __  __ ___ _____ _     ___ ___ ___ _    ___  ___
 |  \/  | _ \ |  \/  | __|_   _/_\   | __|_ _| __| |  |   \/ __|
 | |\/| |  _/ | |\/| | _|  | |/ _ \  | _| | || _|| |__| |) \__ \
 |_|  |_|_|   |_|  |_|___| |_/_/ \_\ |_| |___|___|____|___/|___/


USAGE
=====

Creating a meta field
=====================
Pass the options you want to the metaField($field, $options) function to creat the field you want. Here's a list of all the possible options, what they do, and what their defaults are.


    $field = 'your_field_identifier';
    // Pass any options you need to change from defaults
    $defaultOptions = array(
                    'title' => null, // title for the field(s)
                    'description' => null, // enter a description
                    'type' => 'input', // checkbox, dropdown, select, images, image, textarea, loop
                    'options' => null, // An array of fields for checkboxes, dropdowns, selects, etc:
                                        // array('value'=>'yes', 'description'=>'Of Course!')
                    'required' => false, // for displaying the red *. still need to define it as required when
                                         // saving in metaSave()
                    'data' => 'post_meta', // set as 'option' when using it for wp_options table, like in
                                           //site-setup
                    'args' => false, // pass WP_Query args if needed for when 'type' => 'loop'.
                    'secondary_type' => null // dropdown, radio, checkbox. For use with Loop
                    // These ones won't really need to get set ever
                    'value' => null, // set the value if necessary. we'll set it later anyways.
                    'meta-items' => null // for some instances (we're looking at you images) when we
                                         // need to know if it's from the meta-items functions. This is used
                                         // automatically by metaItems() in meta-items.php
                );
    // output it
    metaField($field, $defaultOptions);


Saving a meta field
===================
Saving meta in a custom post type / page / post creation. Anything that'll get saved in the wp_postmeta table. Everything gets passed to metaSave($field, $options).

    // A field example with all the defaults
    $field = 'your_field_identifier';
    // Default options for metaSave();
    $defaultOptions = array(
                            'required' => false, // array of required field data titles.
                                                 //ex - array('_metaKey', '_anotherRequiredKey');
                            'data' => 'post_meta', // 'option' if saving to wp_option table
                        );
    metaSave($field, $defaultOptions);

When you're saving a meta field, you'll need to place the code in the right spot. Generally this is in:

    function save_meta($post_id, $post) {
            // A handy little script from meta-fields.php to check if we're allowed to save here
            // Exits script if autosave, if nonces don't match, or if revision
            if(metaSaveSetup($post_id) !== true){
                return;
            }
            ... PUT YOUR metaSave() HERE ...
    }

if it's a custom post, it'd look like this (replace "services" with your post type name:

    function save_services_meta($post_id, $post) {
        // A handy little script from meta-fields.php to check if we're allowed to save here
        // Exits script if autosave, if nonces don't match, or if revision
        if(metaSaveSetup($post_id) !== true){
            return;
        }
        // ... PUT YOUR metaSave() HERE ...
        metaSave('your-field-name');
    }

Most of the time all you'll need to do is this:

    metaSave('your-field-name');

If you have to save something with required fields, you'd need to do this:

    $defaultOptions = array(
                            'required' => array(
                                                '_metaKey',
                                                '_anotherRequiredKey',
                                            ),
                        );
    metaSave('your-field-name', $defaultOptions);


LOOP EXAMPLE
------------
Output a list of checkboxes containing all post titles from the custom post type "services" and saving it to the wp options table with the name 'featured_services'

    // Setup options
    $featured_services = array(
                                'type' => 'loop',
                                'title' => 'Select Featured Services', // outputs the title on the page
                                'data' => 'option', // this is necessary for saving it wp_options table
                                'secondary_type' => 'checkbox', // dropdown, radio // defaults to checkbox for loops
                                                               // but you can also use 'dropdown' and 'radio'
                                'args' => array(    // the arguments passed to get_posts
                                                'post_type' => 'services',
                                                'posts_per_page' => '-1'
                                              ),
                                );
    // Output it
    metaField('featured_services', $featured_services);


Save the 'loop' example above to the wp options table. When saving to wp options from a file like in mp-site-setup.php in the mp custom posts plugin, you need to do a little extra work when saving arrays like a checkbox.

    // How to save an array for the loop of checkboxes
    // Add filters for any fields that need extra work before saving
    // You'll need to change all instances of 'featured_services' parts to whatever field name you're saving
    // and then change the metaSave array to whatever you want
    add_filter( 'pre_update_option_featured_services', 'validate_featured_services_array', 10, 2 );
    function validate_featured_services_array($value) {
        if(!empty($value)) {
            return metaSave($value, array('data'=>'option'));
        } else {
            return;
        }
    }


META ITEMS
==========
For outputting an array of items that will repeat the meta fields you've setup, you can use the function metaItems($field, $options) This outputs a 'well' with the option to click a + button to add another well and each also gets an X when you hover over it to remove the well. Each well is basically a collection of meta fields that you pass to it. This is really handy for creating sliders, employee lists, timeline entries, etc.

    $field = 'your_field_name';
    $defaultOptions = array(
                            'fields' => array(), // an array of all the fields we need. Each item in this
                                                 // array can take any of the options from metaField()
                                                 // since that's what each field gets passed to eventually
                            'data' => 'post_meta' // or 'option'. Are we saving a post meta or option field?
                        );
    metaItems($field, $defaultOptions);

Meta Items Example
------------------
A simple collection of saving titles and descriptions. This could be used to output a list of items on a page.

    $options['fields'] = array(
                            array(
                                'type' => 'input',
                                'title' => 'Title',
                                'dataname' => 'title', // dataname is basically the "field" for this item
                                                       // when you access it it will look like
                                                       // your_field_name[0][dataname]
                                'required' => true, // This doesn't "really" require it. We have to set
                                                    // that when we save it. this just outputs a red *
                            ),
                            array(
                                'type' => 'textarea',
                                'title' => 'Description',
                                'dataname' => 'description', // your_field_name[5]['description'] would get
                                                             // you this field in the 5th array
                                                             // of your_field_name
                                'required' => false, // false is the default
                            ),
                        );
    metaItems('your_field_name', $options);

Now, let's save it with the title field as required.

    // Put this where you save it
    // required field
    $options['required'] = array('title');
    // save it
    metaSave('your_field_name', $options);



ROADMAP
=============

- DONE: instead of single-image => true, just make a new field type called image or single image.
------ Now it's type => 'image' for single and type => 'images' for multiples


- remove all the single-image i, og-field, etc stuff and just explode the fieldname = $field[i][dataname] into fieldname-i-dataname

- Prefix all fields with ANOTHER array like, 'mp-meta-field[$fieldName]' then on save, we'll just check for any $_POST[mp-meta-field] and then, if there is one, we'll save it. That way we don't have to even add save fields and we might even be able to check for required fields there too. (this would also allow us to just do ONE get request on the post instead of all of them. Meta data might be loaded by default anyways. not sure.

- Adding functions for saveOption and optionField like the terms are. A lot better than checking for 'data'=>'option', etc. Annoying syntax.

- meta-items.js for terms?

- Look into this whole Classes thing.


BUGS
==============
- Having multiple image fields on meta items doesn't work so well
- Loop won't work with radio or dropdown

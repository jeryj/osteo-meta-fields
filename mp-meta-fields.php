<?php
   /*
   Plugin Name: MP Meta Fields
   Plugin URI: http://www.messengerpigeons.com
   Description: A plugin for easily creating custom meta fields for Wordpress on posts, custom posts, and the wp_options table for site setup
   Version: 1.0
   Author: Messenger Pigeons
   Author URI: http://www.messengerpigeons.com
   License: GPL2
   */

    // Auto Add all files from Functions folder
    $directory = plugin_dir_path( __FILE__ ) . 'functions/';
    foreach (glob($directory."*.php") as $filename){
        include $filename;
    }
?>

<?php
   /*
   Plugin Name: Osteo Meta Fields
   Description: A plugin for easily creating custom meta fields for Wordpress on posts, custom posts, and the wp_options table for site setup
   Version: 1.0
   Author: Jeremy Jones
   Author URI: http://jeremyjon.es
   License: GPL2
   */

    // Auto Add all files from Functions folder
    $directory = plugin_dir_path( __FILE__ ) . 'functions/';
    foreach (glob($directory."*.php") as $filename){
        include $filename;
    }
?>

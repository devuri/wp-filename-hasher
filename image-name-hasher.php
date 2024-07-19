<?php

/**
 * Plugin Name:       Image NameHas her
 * Plugin URI:        https://github.com/devuri/wp-basic-plugin
 * Description:       Automatically renames uploaded images in WordPress to a unique hashed filename.
 * Version:           0.1.0
 * Requires at least: 5.3.0
 * Requires PHP:      7.3.5
 * Author:            uriel
 * Author URI:        https://github.com/devuri
 * Text Domain:       image-name-hasher
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Network: true.
 */

if ( ! \defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'src/HashImages.php';

new ImageNameHasher\HashImages();

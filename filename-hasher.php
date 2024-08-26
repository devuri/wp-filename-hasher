<?php

/**
 * Plugin Name:       File Name Hasher
 * Plugin URI:        https://github.com/devuri/filename-hasher
 * Description:       Automatically renames uploaded files/images in WordPress to a unique hashed filename.
 * Version:           0.1.0
 * Requires at least: 5.3.0
 * Requires PHP:      7.3.5
 * Author:            uriel
 * Author URI:        https://github.com/devuri
 * Text Domain:       filename-hasher
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Network: true.
 */

if ( ! \defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'src/HashFiles.php';

// maybe later allow the user to change allowed files.
$file_hasher = new FileNameHasher\HashFiles(
    [ 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf' ]
);

$file_hasher->add_prefilter();

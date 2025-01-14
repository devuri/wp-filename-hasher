<?php

/**
 * Plugin Name:       File Name Hasher
 * Plugin URI:        https://github.com/devuri/filename-hasher
 * Description:       Automatically renames uploaded files/images in WordPress to a unique hashed filename.
 * Version:           0.1.6
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

// plugin dir path
\define( 'WPFNH_DIR_PATH', plugin_dir_path( __FILE__ ) );

// setup option IDs.
\define( 'REGISTERED_WPFNH_SETTINGS_ID', 'wpfnh_filename_hasher_settings' );
\define( 'ALLOWED_EXTENSIONS_OPTION_ID', 'wpfnh_allowed_extensions' );
\define( 'KEEP_ORIGINAL_PREFIX_OPTION_ID', 'wpfnh_keep_original_prefix' );
\define( 'CUSTOM_PREFIX_OPTION_ID', 'wpfnh_custom_prefix' );
\define( 'ADD_UNIQID_OPTION_ID', 'wpfnh_add_uniqid' );

require_once WPFNH_DIR_PATH . 'src/HashFiles.php';
require_once WPFNH_DIR_PATH . 'src/Options.php';

$wpfnAllowedExtensions = explode( ',', get_option( ALLOWED_EXTENSIONS_OPTION_ID, 'jpg,jpeg,png,gif,bmp,pdf' ) );

// maybe later allow the user to change allowed files.
$wpfnHasher = new FileNameHasher\HashFiles(
    new FileNameHasher\Options(
        $wpfnAllowedExtensions,
        (int) get_option( KEEP_ORIGINAL_PREFIX_OPTION_ID, '0' ),
        get_option( CUSTOM_PREFIX_OPTION_ID, '' ),
        (int) get_option( ADD_UNIQID_OPTION_ID, '1' )
    )
);

// upload_prefilter
$wpfnHasher->addPrefilter();

// remove options.
register_deactivation_hook(
    __FILE__,
    function (): void {
        delete_option( ALLOWED_EXTENSIONS_OPTION_ID );
        delete_option( KEEP_ORIGINAL_PREFIX_OPTION_ID );
        delete_option( CUSTOM_PREFIX_OPTION_ID );
        delete_option( ADD_UNIQID_OPTION_ID );
    }
);

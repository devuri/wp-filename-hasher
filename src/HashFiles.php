<?php
/**
 * This file is part of the File Name Hasher WordPress PLugin.
 *
 * (c) Uriel Wilson <hello@urielwilson.com>
 *
 * Please see the LICENSE file that was distributed with this source code
 * for full copyright and license information.
 */

namespace FileNameHasher;

require_once WPFNH_DIR_PATH . 'src/Options.php';

class HashFiles
{
    private $settings;
    private $hashed_files = [];

    public function __construct( Options $option_settings )
    {
        $this->settings = $option_settings;
        $this->settings->registerOptions();
        $this->settings->addAdminPage();
    }

    public function addPrefilter(): void
    {
        add_filter( 'wp_handle_upload_prefilter', [ $this, 'uploadHashFilter' ] );
    }

    public function uploadHashFilter( $file )
    {
        if ( ! isset( $file['name'] ) ) {
            return $file;
        }

        $file_info     = pathinfo( $file['name'] );
        $new_file_name = null;

        if ( ! isset( $file_info['extension'] ) ) {
            return $file;
        }

        // get extension.
        $extension = strtolower( $file_info['extension'] );

        if ( 'zip' === $extension ) {
            return $file;
        }

        $allowed_extensions   = $this->settings->getAllowedExtensions();
        $add_uniqid           = $this->settings->getAddUniqid();
        $keep_original_prefix = $this->settings->getKeepOriginalPrefix();
        $custom_prefix        = $this->settings->getCustomPrefix();

        // Check if the extension is in the allowed list
        if ( \in_array( $extension, $allowed_extensions, true ) ) {
            $unique_bytes = bin2hex( random_bytes( 32 ) );
            $hashed_name  = hash( 'sha256', $unique_bytes );

            // Start with the hashed name
            if ( $add_uniqid ) {
                $new_file_name = $hashed_name . uniqid( '-' );
            } else {
                $new_file_name = $hashed_name;
            }

            // Add the original filename prefix if the option is enabled
            if ( $keep_original_prefix ) {
                $new_file_name = $file_info['filename'] . '-' . $new_file_name;
            }

            // Add the custom prefix if it's set
            if ( ! empty( $custom_prefix ) ) {
                $new_file_name .= '-' . $custom_prefix;
            }

            // clean filename.
            $new_file_name = self::sanitize( $new_file_name );

            // Combine with the original extension
            $new_file_name .= '.' . $extension;
            $file['name']   = $new_file_name;

            // Check if the filename is already hashed.
            if ( ! $this->isFileHashed( $new_file_name ) ) {
                $this->hashed_files[] = $new_file_name;
            }
        }// end if

        return $file;
    }

    protected function isFileHashed( $filename )
    {
        return \in_array( $filename, $this->hashed_files, true );
    }

    protected static function sanitize( $filename )
    {
        return sanitize_file_name( strtolower( $filename ) );
    }
}

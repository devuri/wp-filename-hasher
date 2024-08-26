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

class HashFiles
{
    private $hashed_files = [];
    private $allowed_extensions;

    public function __construct( array $allowed_extensions )
    {
        $this->allowed_extensions = $allowed_extensions;
    }

    public function add_prefilter(): void
    {
        add_filter( 'wp_handle_upload_prefilter', [ $this, 'upload_to_hash' ] );
    }

    public function upload_to_hash( $file )
    {
        if ( ! isset( $file['name'] ) ) {
            return $file;
        }

        $file_info = pathinfo( $file['name'] );

        if ( ! isset( $file_info['extension'] ) ) {
            return $file;
        }

        $extension = strtolower( $file_info['extension'] );

        // Check if the extension is in the allowed list
        if ( \in_array( $extension, $this->allowed_extensions, true ) ) {
            $unique_id     = bin2hex( random_bytes( 32 ) );
            $hashed_name   = hash( 'sha256', $unique_id );
            $new_file_name = $hashed_name . '.' . $extension;
            $file['name']  = $new_file_name;

            // Check if the filename is already hashed.
            if ( ! $this->is_file_hashed( $new_file_name ) ) {
                $this->hashed_files[] = $new_file_name;
            }
        }

        return $file;
    }

    public function get_allowed_extensions()
    {
        return $this->allowed_extensions;
    }

    protected function is_file_hashed( $filename )
    {
        return \in_array( $filename, $this->hashed_files, true );
    }
}

<?php
/**
 * This file is part of the Image Name Hasher WordPress PLugin.
 *
 * (c) Uriel Wilson <hello@urielwilson.com>
 *
 * Please see the LICENSE file that was distributed with this source code
 * for full copyright and license information.
 */

namespace ImageNameHasher;

class HashImages
{
    public function __construct()
    {
        add_filter( 'wp_handle_upload_prefilter', [ $this, 'change_image_name_to_hash' ] );
    }

    public function change_image_name_to_hash( $file )
    {
        $file_info     = pathinfo( $file['name'] );
        $extension     = $file_info['extension'];
        $unique_id     = bin2hex( random_bytes( 32 ) );
        $hashed_name   = md5( $unique_id );
        $new_file_name = $hashed_name . '.' . $extension;
        $file['name']  = $new_file_name;

        return $file;
    }
}

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
    private $keep_original_prefix;
    private $custom_prefix;
    private $add_uniqid;

    public function __construct( array $allowed_extensions, int $keep_original_prefix, string $custom_prefix, int $add_uniqid )
    {
        $defaults                   = [ 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf' ];
        $this->allowed_extensions   = ! empty( $allowed_extensions ) ? $allowed_extensions : $defaults;
        $this->keep_original_prefix = (bool) $keep_original_prefix;
        $this->custom_prefix        = ! empty( $custom_prefix ) ? $custom_prefix : null;
		$this->add_uniqid = (bool) $add_uniqid;

        // register settings.
        add_action(
            'admin_init',
            function(): void {
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, ALLOWED_EXTENSIONS_OPTION_ID );
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, KEEP_ORIGINAL_PREFIX_OPTION_ID );
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, CUSTOM_PREFIX_OPTION_ID );
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, ADD_UNIQID_OPTION_ID );
            }
        );

        // admin menu.
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
    }

    public function add_prefilter(): void
    {
        add_filter( 'wp_handle_upload_prefilter', [ $this, 'upload_to_hash' ] );
    }

    public function add_admin_page(): void
    {
        add_submenu_page(
            'tools.php',
            'File Name Hasher Settings',
            'File Name Hasher',
            'manage_options',
            'file-name-hasher',
            [ $this, 'settings_page' ]
        );
    }

    public function settings_page(): void
    {
        $allowed_extensions = implode( ',', $this->allowed_extensions );
        ?>
         <div class="wrap">
             <h1>Hasher Settings</h1>
             <form method="post" action="options.php">
                <?php settings_fields( REGISTERED_WPFNH_SETTINGS_ID ); ?>
                <?php do_settings_sections( REGISTERED_WPFNH_SETTINGS_ID ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Allowed File Extensions</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( ALLOWED_EXTENSIONS_OPTION_ID ); ?>"
                                    value="<?php echo esc_attr( $allowed_extensions ); ?>" />
                            <p class="description">Enter the file extensions (comma-separated) that should be hashed, e.g., jpg,jpeg,png,gif,bmp,pdf.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Keep Original Filename</th>
                        <td>
                            <input type="checkbox" name="<?php echo esc_attr( KEEP_ORIGINAL_PREFIX_OPTION_ID ); ?>" value="1"
                                <?php checked( $this->keep_original_prefix, 1 ); ?> />
                            <p class="description">If checked, the original filename will be included as a prefix in the new hashed filename.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Custom Prefix</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( CUSTOM_PREFIX_OPTION_ID ); ?>"
                                    value="<?php echo esc_attr( $this->custom_prefix ); ?>" />
                            <p class="description">Enter a custom prefix to add to the hashed filename, e.g., website-name. This will appear after the original filename if both options are enabled.</p>
                        </td>
                    </tr>

					<tr valign="top">
 						<th scope="row">Add a unique ID</th>
 						<td>
 							<input type="checkbox" name="<?php echo esc_attr( ADD_UNIQID_OPTION_ID ); ?>" value="1"
 								<?php checked( $this->add_uniqid, 1 ); ?> />
 							<p class="description">If checked, generates a time-based identifier that will be included as part of the new filename hash.</p>
 						</td>
 					</tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function upload_to_hash( $file )
    {
        if ( ! isset( $file['name'] ) ) {
            return $file;
        }

        $file_info     = pathinfo( $file['name'] );
        $new_file_name = null;

        if ( ! isset( $file_info['extension'] ) ) {
            return $file;
        }

		if ( 'zip' === strtolower($file_info['extension']) ) {
			return $file;
		}

        $extension = strtolower( $file_info['extension'] );

        // Check if the extension is in the allowed list
        if ( \in_array( $extension, $this->allowed_extensions, true ) ) {
            $unique_bytes = bin2hex( random_bytes( 32 ) );
            $hashed_name = hash( 'sha256', $unique_bytes );

            // Start with the hashed name
			if ( $this->add_uniqid ) {
				$new_file_name = $hashed_name . uniqid( '-' );
			} else {
				$new_file_name = $hashed_name;
			}

            // Add the original filename prefix if the option is enabled
            if ( $this->keep_original_prefix ) {
                $new_file_name = $file_info['filename'] . '-' . $new_file_name;
            }

            // Add the custom prefix if it's set
            if ( ! empty( $this->custom_prefix ) ) {
                $new_file_name .= '-' . $this->custom_prefix;
            }

            // clean filename.
            $new_file_name = self::sanitize( $new_file_name );

            // Combine with the original extension
            $new_file_name .= '.' . $extension;
            $file['name']   = $new_file_name;

            // Check if the filename is already hashed.
            if ( ! $this->is_file_hashed( $new_file_name ) ) {
                $this->hashed_files[] = $new_file_name;
            }
        }// end if

        return $file;
    }

    protected function is_file_hashed( $filename )
    {
        return \in_array( $filename, $this->hashed_files, true );
    }

    protected static function sanitize( $filename )
    {
        return sanitize_file_name( strtolower( $filename ) );
    }
}

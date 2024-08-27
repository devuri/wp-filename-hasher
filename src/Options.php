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

class Options
{
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
        $this->add_uniqid           = (bool) $add_uniqid;
    }

    public function addAdminPage(): void
    {
        add_action(
            'admin_menu',
            function (): void {
                add_submenu_page(
                    'tools.php',
                    __( 'File Name Hasher Settings', 'filename-hasher' ),
                    __( 'File Name Hasher', 'filename-hasher' ),
                    'manage_options',
                    'file-name-hasher',
                    [ $this, 'settingsPage' ]
                );
            }
        );
    }

    public function registerOptions(): void
    {
        add_action(
            'admin_init',
            function(): void {
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, ALLOWED_EXTENSIONS_OPTION_ID );
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, KEEP_ORIGINAL_PREFIX_OPTION_ID );
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, CUSTOM_PREFIX_OPTION_ID );
                register_setting( REGISTERED_WPFNH_SETTINGS_ID, ADD_UNIQID_OPTION_ID );
            }
        );
    }

    public function settingsPage(): void
    {
        $allowed_extensions = implode( ',', $this->allowed_extensions );

        $this->renderFormPageHeader();
        $this->renderFormFieldSections();
        $this->renderSettingsTable( $allowed_extensions );
        $this->renderFormPageFooter();
    }

    public function getAllowedExtensions(): array
    {
        return $this->allowed_extensions;
    }

    public function getAddUniqid(): bool
    {
        return $this->add_uniqid;
    }

    public function getKeepOriginalPrefix(): bool
    {
        return $this->keep_original_prefix;
    }

    public function getCustomPrefix(): ?string
    {
        return $this->custom_prefix;
    }

    protected function renderFormPageHeader(): void
    {
        ?>
        <div class="wrap">
            <h1>Hasher Settings</h1>
        <form method="post" action="options.php">
        <?php
    }

    protected function renderFormFieldSections(): void
    {
        settings_fields( REGISTERED_WPFNH_SETTINGS_ID );
        do_settings_sections( REGISTERED_WPFNH_SETTINGS_ID );
    }

    protected function renderFormPageFooter(): void
    {
        submit_button();
        ?>
        </form>
        </div>
        <?php
    }

    protected function renderSettingsTable( string $allowed_extensions ): void
    {
        ?>
        <table class="form-table">
            <?php
            $this->renderAllowedExtensionsRow( $allowed_extensions );
			$this->renderKeepOriginalFilename();
			$this->renderCustomPrefix();
			$this->renderAddUniqueId();
			?>
        </table>
        <?php
    }

    protected function renderAllowedExtensionsRow( string $allowed_extensions ): void
    {
        $this->inputRow(
            'Allowed File Extensions',
            ALLOWED_EXTENSIONS_OPTION_ID,
            $allowed_extensions,
            'Enter the file extensions (comma-separated) that should be hashed, e.g., jpg,jpeg,png,gif,bmp,pdf.',
            'text'
        );
    }

    protected function renderKeepOriginalFilename(): void
    {
        $this->inputRow(
            'Keep Original Filename',
            KEEP_ORIGINAL_PREFIX_OPTION_ID,
            $this->keep_original_prefix,
            'If checked, the original filename will be included as a prefix in the new hashed filename.',
            'checkbox',
            '1'
        );
    }

    protected function renderCustomPrefix(): void
    {
        $this->inputRow(
            'Custom Prefix',
            CUSTOM_PREFIX_OPTION_ID,
            $this->custom_prefix,
            'Enter a custom prefix to add to the hashed filename, e.g., website-name. This will appear after the original filename if both options are enabled.',
            'text'
        );
    }

    protected function renderAddUniqueId(): void
    {
        $this->inputRow(
            'Add a unique ID',
            ADD_UNIQID_OPTION_ID,
            $this->add_uniqid,
            'If checked, generates a time-based identifier that will be included as part of the new filename hash.',
            'checkbox',
            '1'
        );
    }

    /**
     * Renders a table row containing an input field within an HTML form.
     *
     * This method generates a table row (`<tr>`) with a title (`<th>`) and a field (`<td>`).
     * The field can be a text input or a checkbox depending on the `$type` parameter.
     * An optional description can be displayed below the input field.
     *
     * @param string $title         The label displayed in the `<th>` element.
     * @param string $field_name    The name attribute for the input field.
     * @param string $value         Optional. The current value of the input field. Default empty string.
     * @param string $description   Optional. A description displayed below the input field. Default empty string.
     * @param string $type          Optional. The type of the input field (e.g., 'text', 'checkbox'). Default 'text'.
     * @param string $checked_value Optional. The value used to determine if the checkbox should be checked.
     *                              Only used if `$type` is 'checkbox'. Default '1'.
     */
    private function inputRow( string $title, string $field_name, ?string $value = '', string $description = '', string $type = 'text', string $checked_value = '1' ): void
    {
        ?>
        <tr valign="top">
            <th scope="row">
                <?php echo esc_html( $title ); ?>
            </th>
            <td>
                <?php if ( 'checkbox' === $type ) { ?>
                    <input type="checkbox" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $checked_value ); ?>"
                        <?php checked( $value, $checked_value ); ?> />
                <?php } else { ?>
                    <input type="<?php echo esc_attr( $type ); ?>"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           value="<?php echo esc_attr( $value ); ?>" />
                <?php } ?>
                <?php if ( ! empty( $description ) ) { ?>
                    <p class="description">
                        <?php echo esc_html( $description ); ?>
                    </p>
                <?php } ?>
            </td>
        </tr>
        <?php
    }
}

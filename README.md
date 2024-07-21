# Image Name Hasher Plugin

## Description

The `Image Name Hasher` plugin automatically renames uploaded images in WordPress to a unique hashed filename. This helps to avoid conflicts with existing filenames and enhances security by generating unpredictable names for uploaded images.

## Example

An image uploaded with the original name `example.jpg` might be renamed to something like `5d41402abc4b2a76b9719d911017c592.jpg`.

## Features

- Automatically renames uploaded images to a hashed filename.
- Ensures unique filenames for each uploaded image.
- Enhances security by making filenames unpredictable.

## Installation

1. Download the `Image Name Hasher` plugin.
2. Extract the plugin files to the `wp-content/plugins/` directory of your WordPress installation.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

Once activated, the plugin will automatically rename any image uploaded through the WordPress media uploader. No further configuration is required.

## How It Works

1. **Initialization**: The plugin hooks into the `wp_handle_upload_prefilter` filter during its construction.
2. **Renaming Process**: When an image is uploaded:
   - The plugin extracts the file extension from the original filename.
   - It generates a unique ID using `random_bytes`.
   - The unique ID is then hashed using the MD5 algorithm.
   - The hashed ID is combined with the original file extension to create the new filename.
   - The original filename is replaced with the new hashed filename.
3. **File Upload**: The image is uploaded with the new hashed filename.

## License

This plugin is open-source and licensed under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license.

## Support

For any issues or feature requests, please open an issue on the plugin's [GitHub repository](https://github.com/your-repo/image-name-hasher).

# File Name Hasher Plugin

## Description

The `File Name Hasher` plugin automatically renames uploaded files (images and PDFs) in WordPress to unique, hashed filenames. This helps to avoid conflicts with existing filenames and enhances security by generating unpredictable names for uploaded files.

## Features

- Automatically renames uploaded images and PDFs to hashed filenames.
- Ensures unique filenames for each uploaded file.
- Enhances security by making filenames unpredictable.
- Only hashes specific file types (images and PDFs), leaving other file types with their original names.

## Installation

1. Download the `File Name Hasher` plugin.
2. Extract the plugin files to the `wp-content/plugins/` directory of your WordPress installation.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

Once activated, the plugin will automatically rename any image or PDF uploaded through the WordPress media uploader. No further configuration is required.

## How It Works

1. **Initialization**: The plugin hooks into the `wp_handle_upload_prefilter` filter during its construction.
2. **File Type Check**: When a file is uploaded, the plugin checks the file's extension to see if it matches one of the allowed file types (images and PDFs).
3. **Renaming Process**: If the file type is allowed:
   - The plugin generates a unique ID using `random_bytes`.
   - The unique ID is hashed using the SHA-256 algorithm.
   - The hashed ID is combined with the original file extension to create the new filename.
   - The original filename is replaced with the new hashed filename.
4. **File Upload**: The file is uploaded with the new hashed filename.
5. **Tracking Hashed Files**: The plugin keeps track of all hashed filenames during the current session, allowing you to check if a file has already been renamed.

## Example

An image uploaded with the original name `example.jpg` might be renamed to something like `3d4b2f4c8d9e1a6b2f3a123456789abc123456789abc123456789abc12345678.jpg`.

## License

This plugin is open-source and licensed under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license.

## Support

For any issues or feature requests, please open an issue on the plugin's [GitHub repository](https://github.com/your-repo/filename-hasher).

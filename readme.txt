=== SheetBridge: REST API & Sync Hub for Google Sheets ===
Contributors: konstantin1agafonov
Donate link: https://boosty.to/konstantin1agafonov
Tags: google sheets, rest api, webhook, sync, spreadsheet, headless
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transform Google Sheets into a headless backend. Provides a robust REST API and webhooks for bi-directional data sync between WP and Sheets.

== Description ==

SheetBridge turns Google Sheets into a headless backend for WordPress. Create connections to Google Sheets via a custom post type, configure authentication, and use the SheetBridge PHP class to push and pull data programmatically.

= Features =

* Custom post type (sheet_bridge) for managing Google Sheets connections
* Service account authentication (OAuth 2.0 JWT) for secure server-to-server access
* Google API Key support for public sheet access
* Push data to the first empty row of any sheet tab
* Pull data as structured arrays with header mapping
* REST API endpoints for remote data operations
* Webhook support for bi-directional sync
* No frontend URLs — admin-only management

== Installation ==

1. Upload the `wp_sheetbridge` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **SheetBridge → Add New Sheet Bridge** to create your first connection

== Usage ==

Create a new Sheet Bridge post, fill in the Spreadsheet ID and service account credentials, then use the SheetBridge class in your code:

    $bridge = new SheetBridge(123);
    $bridge->push('{"name": "John", "email": "john@example.com"}');
    $data = $bridge->pull();

== Frequently Asked Questions ==

= How do I get a Spreadsheet ID? =

The Spreadsheet ID is the long string in your Google Sheets URL between `/d/` and `/edit`. For example, in `https://docs.google.com/spreadsheets/d/abc123/edit`, the ID is `abc123`.

= How do I set up a service account? =

1. Go to the Google Cloud Console
2. Create a project and enable the Google Sheets API
3. Create a service account and download the JSON key
4. Share your Google Sheet with the service account's client email
5. Paste the entire JSON key into the Service Account JSON field in the Sheet Bridge edit page

= Can I use an API Key instead? =

Yes, for read-only access to public sheets. For write access, you must use service account authentication.

== Upgrade Notice ==

= 1.0.0 =
Initial version.

== Changelog ==

= 1.0.0 =
Initial version.

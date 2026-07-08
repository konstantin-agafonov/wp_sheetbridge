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

1. Go to the [Google Cloud Console](https://console.cloud.google.com/) and sign in
2. Create a new project or select an existing one from the top toolbar
3. Navigate to **APIs & Services → Library**, search for "Google Sheets API", click on it, then click **Enable**
4. Go to **APIs & Services → Credentials**, click **+ Create Credentials**, and choose **Service Account**
5. Give the service account a name (e.g., "sheetbridge"), optionally add a description, then click **Create and Continue**
6. Assign the role **Editor** (or a custom role with sheets permissions) to the service account, then click **Done**
7. Back on the Credentials page, click on the newly created service account email to open its details
8. Go to the **Keys** tab, click **Add Key → Create New Key**, choose **JSON**, and click **Create** — the JSON key file will download to your computer
9. Open your Google Sheet, click the **Share** button in the top-right, paste the `client_email` value from the downloaded JSON (it looks like `name@project.iam.gserviceaccount.com`), give it **Editor** permission, and click **Send**
10. In WordPress, edit your Sheet Bridge CPT, paste the entire contents of the downloaded JSON file into the **Service Account JSON** field, then save — the individual fields (Client Email, Private Key, etc.) will populate automatically

= Can I use an API Key instead? =

Yes, for read-only access to public sheets. For write access, you must use service account authentication.

== Upgrade Notice ==

= 1.0.0 =
Initial version.

== Changelog ==

= 1.0.0 =
Initial version.

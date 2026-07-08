<?php
/**
 * @var int    $post_id
 * @var string $spreadsheet_id
 * @var string $spreadsheet_url
 * @var string $sheet_name
 * @var string $api_key
 * @var string $client_email
 * @var string $private_key
 * @var string $private_key_id
 * @var string $project_id
 * @var string $service_account_json
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template_dir = SHEETBRIDGE_PLUGIN_DIR . 'templates';
?>
<table class="form-table">
	<tbody>
		<?php
		$id          = 'sb_spreadsheet_id';
		$name        = 'sb_spreadsheet_id';
		$value       = $spreadsheet_id;
		$label       = __( 'Spreadsheet ID', 'sheetbridge' );
		$description = __( 'The ID from your Google Sheets URL (the long string between /d/ and /edit).', 'sheetbridge' );
		include $template_dir . '/field-text.php';

		$id          = 'sb_spreadsheet_url';
		$name        = 'sb_spreadsheet_url';
		$value       = $spreadsheet_url;
		$label       = __( 'Spreadsheet URL', 'sheetbridge' );
		$description = __( 'Full URL to the Google Sheet (optional, can be used instead of Spreadsheet ID).', 'sheetbridge' );
		include $template_dir . '/field-text.php';

		$id          = 'sb_sheet_name';
		$name        = 'sb_sheet_name';
		$value       = $sheet_name;
		$label       = __( 'Sheet Name', 'sheetbridge' );
		$description = __( 'The name of the sheet tab (e.g. Sheet1, Sheet2). Default: Sheet1', 'sheetbridge' );
		include $template_dir . '/field-text.php';
		?>
	</tbody>
</table>

<hr style="margin: 20px 0;" />

<h3><?php esc_html_e( 'Authentication', 'sheetbridge' ); ?></h3>

<table class="form-table">
	<tbody>
		<?php
		$id          = 'sb_api_key';
		$name        = 'sb_api_key';
		$value       = $api_key;
		$label       = __( 'Google API Key', 'sheetbridge' );
		$description = __( 'Google Cloud API key (for public sheets / read-only access).', 'sheetbridge' );
		include $template_dir . '/field-text.php';
		?>
	</tbody>
</table>

<h4><?php esc_html_e( 'Service Account Authentication (recommended for write access)', 'sheetbridge' ); ?></h4>

<table class="form-table">
	<tbody>
		<?php
		$id          = 'sb_service_account_json';
		$name        = 'sb_service_account_json';
		$value       = $service_account_json;
		$label       = __( 'Service Account JSON', 'sheetbridge' );
		$rows        = 8;
		$description = __( 'Paste the entire service account JSON key file contents here. Fills in the fields below automatically on save.', 'sheetbridge' );
		include $template_dir . '/field-textarea.php';

		$id    = 'sb_client_email';
		$name  = 'sb_client_email';
		$value = $client_email;
		$label = __( 'Client Email', 'sheetbridge' );
		$description = '';
		include $template_dir . '/field-text.php';

		$id          = 'sb_private_key';
		$name        = 'sb_private_key';
		$value       = $private_key;
		$label       = __( 'Private Key', 'sheetbridge' );
		$description = '';
		include $template_dir . '/field-textarea.php';

		$id    = 'sb_private_key_id';
		$name  = 'sb_private_key_id';
		$value = $private_key_id;
		$label = __( 'Private Key ID', 'sheetbridge' );
		$description = '';
		include $template_dir . '/field-text.php';

		$id    = 'sb_project_id';
		$name  = 'sb_project_id';
		$value = $project_id;
		$label = __( 'Project ID', 'sheetbridge' );
		$description = '';
		include $template_dir . '/field-text.php';
		?>
	</tbody>
</table>

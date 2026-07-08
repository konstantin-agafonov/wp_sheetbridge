<?php
/**
 * Variables passed via $args to load_template().
 *
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

// WP 7.0 load_template() no longer extracts $args into the template scope.
// Read all variables explicitly from the $args parameter.
if ( isset( $args ) && is_array( $args ) ) {
    $spreadsheet_id       = $args['spreadsheet_id'] ?? '';
    $spreadsheet_url      = $args['spreadsheet_url'] ?? '';
    $sheet_name           = $args['sheet_name'] ?? '';
    $api_key              = $args['api_key'] ?? '';
    $client_email         = $args['client_email'] ?? '';
    $private_key          = $args['private_key'] ?? '';
    $private_key_id       = $args['private_key_id'] ?? '';
    $project_id           = $args['project_id'] ?? '';
    $service_account_json = $args['service_account_json'] ?? '';
}
?>
<table class="form-table">
	<tbody>
		<?php
		load_template( $template_dir . '/field-text.php', false, array(
			'id'          => 'sb_spreadsheet_id',
			'name'        => 'sb_spreadsheet_id',
			'value'       => $spreadsheet_id,
			'label'       => __( 'Spreadsheet ID', 'sheetbridge' ),
			'description' => __( 'The ID from your Google Sheets URL (the long string between /d/ and /edit).', 'sheetbridge' ),
		) );

		load_template( $template_dir . '/field-text.php', false, array(
			'id'          => 'sb_spreadsheet_url',
			'name'        => 'sb_spreadsheet_url',
			'value'       => $spreadsheet_url,
			'label'       => __( 'Spreadsheet URL', 'sheetbridge' ),
			'description' => __( 'Full URL to the Google Sheet (optional, can be used instead of Spreadsheet ID).', 'sheetbridge' ),
		) );

		load_template( $template_dir . '/field-text.php', false, array(
			'id'          => 'sb_sheet_name',
			'name'        => 'sb_sheet_name',
			'value'       => $sheet_name,
			'label'       => __( 'Sheet Name', 'sheetbridge' ),
			'description' => __( 'The name of the sheet tab (e.g. Sheet1, Sheet2). Default: Sheet1', 'sheetbridge' ),
		) );
		?>
	</tbody>
</table>

<hr style="margin: 20px 0;" />

<h3><?php esc_html_e( 'Authentication', 'sheetbridge' ); ?></h3>

<table class="form-table">
	<tbody>
		<?php
		load_template( $template_dir . '/field-text.php', false, array(
			'id'          => 'sb_api_key',
			'name'        => 'sb_api_key',
			'value'       => $api_key,
			'label'       => __( 'Google API Key', 'sheetbridge' ),
			'description' => __( 'Google Cloud API key (for public sheets / read-only access).', 'sheetbridge' ),
		) );
		?>
	</tbody>
</table>

<h4><?php esc_html_e( 'Service Account Authentication (recommended for write access)', 'sheetbridge' ); ?></h4>

<table class="form-table">
	<tbody>
		<?php
		load_template( $template_dir . '/field-textarea.php', false, array(
			'id'          => 'sb_service_account_json',
			'name'        => 'sb_service_account_json',
			'value'       => $service_account_json,
			'label'       => __( 'Service Account JSON', 'sheetbridge' ),
			'rows'        => 8,
			'description' => __( 'Paste the entire service account JSON key file contents here. Fills in the fields below automatically on save.', 'sheetbridge' ),
		) );

		load_template( $template_dir . '/field-text.php', false, array(
			'id'    => 'sb_client_email',
			'name'  => 'sb_client_email',
			'value' => $client_email,
			'label' => __( 'Client Email', 'sheetbridge' ),
			'description' => '',
		) );

		load_template( $template_dir . '/field-textarea.php', false, array(
			'id'          => 'sb_private_key',
			'name'        => 'sb_private_key',
			'value'       => $private_key,
			'label'       => __( 'Private Key', 'sheetbridge' ),
			'description' => '',
		) );

		load_template( $template_dir . '/field-text.php', false, array(
			'id'    => 'sb_private_key_id',
			'name'  => 'sb_private_key_id',
			'value' => $private_key_id,
			'label' => __( 'Private Key ID', 'sheetbridge' ),
			'description' => '',
		) );

		load_template( $template_dir . '/field-text.php', false, array(
			'id'    => 'sb_project_id',
			'name'  => 'sb_project_id',
			'value' => $project_id,
			'label' => __( 'Project ID', 'sheetbridge' ),
			'description' => '',
		) );
		?>
	</tbody>
</table>

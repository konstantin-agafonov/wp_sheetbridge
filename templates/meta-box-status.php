<?php
/**
 * Variables passed via $args to load_template().
 *
 * @var string $last_sync_time
 * @var string $last_sync_status
 * @var string $spreadsheet_id
 * @var string $sheet_name
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WP 7.0 load_template() no longer extracts $args into the template scope.
if ( isset( $args ) && is_array( $args ) ) {
	$last_sync_time   = $args['last_sync_time'] ?? '';
	$last_sync_status = $args['last_sync_status'] ?? '';
	$spreadsheet_id   = $args['spreadsheet_id'] ?? '';
	$sheet_name       = $args['sheet_name'] ?? '';
}
?>
<p>
	<strong><?php esc_html_e( 'Spreadsheet ID:', 'sheetbridge' ); ?></strong><br />
	<code><?php echo esc_html( $spreadsheet_id ?: '—' ); ?></code>
</p>
<p>
	<strong><?php esc_html_e( 'Sheet:', 'sheetbridge' ); ?></strong><br />
	<?php echo esc_html( $sheet_name ); ?>
</p>
<p>
	<strong><?php esc_html_e( 'Last Sync:', 'sheetbridge' ); ?></strong><br />
	<?php echo esc_html( $last_sync_time ?: __( 'Never', 'sheetbridge' ) ); ?>
</p>
<p>
	<strong><?php esc_html_e( 'Status:', 'sheetbridge' ); ?></strong><br />
	<?php echo esc_html( $last_sync_status ?: __( 'Not synced yet', 'sheetbridge' ) ); ?>
</p>

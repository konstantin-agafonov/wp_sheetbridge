<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SheetBridge_Admin {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_filter( 'manage_' . SHEETBRIDGE_CPT_SLUG . '_posts_columns', array( $this, 'custom_columns' ) );
		add_action( 'manage_' . SHEETBRIDGE_CPT_SLUG . '_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
		add_filter( 'plugin_action_links_' . SHEETBRIDGE_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	public function add_meta_boxes(): void {
		add_meta_box(
			'sheetbridge_connection_box',
			__( 'Google Sheets Connection', 'sheetbridge' ),
			array( $this, 'connection_box_callback' ),
			SHEETBRIDGE_CPT_SLUG,
			'normal',
			'high'
		);

		add_meta_box(
			'sheetbridge_status_box',
			__( 'Sync Status', 'sheetbridge' ),
			array( $this, 'status_box_callback' ),
			SHEETBRIDGE_CPT_SLUG,
			'side',
			'high'
		);
	}

	public function connection_box_callback( $post ): void {
		wp_nonce_field( 'sheetbridge_connection_box', 'sheetbridge_connection_box_nonce' );

		$spreadsheet_id       = get_post_meta( $post->ID, '_sb_spreadsheet_id', true );
		$spreadsheet_url      = get_post_meta( $post->ID, '_sb_spreadsheet_url', true );
		$sheet_name           = get_post_meta( $post->ID, '_sb_sheet_name', true ) ?: 'Sheet1';
		$api_key              = get_post_meta( $post->ID, '_sb_api_key', true );
		$client_email         = get_post_meta( $post->ID, '_sb_client_email', true );
		$private_key          = get_post_meta( $post->ID, '_sb_private_key', true );
		$private_key_id       = get_post_meta( $post->ID, '_sb_private_key_id', true );
		$project_id           = get_post_meta( $post->ID, '_sb_project_id', true );
		$service_account_json = get_post_meta( $post->ID, '_sb_service_account_json', true );

		load_template(
			SHEETBRIDGE_PLUGIN_DIR . 'templates/meta-box-connection.php',
			false,
			array(
				'spreadsheet_id'       => $spreadsheet_id,
				'spreadsheet_url'      => $spreadsheet_url,
				'sheet_name'           => $sheet_name,
				'api_key'              => $api_key,
				'client_email'         => $client_email,
				'private_key'          => $private_key,
				'private_key_id'       => $private_key_id,
				'project_id'           => $project_id,
				'service_account_json' => $service_account_json,
			)
		);
	}

	public function status_box_callback( $post ): void {
		$last_sync_time   = get_post_meta( $post->ID, '_sb_last_sync_time', true );
		$last_sync_status = get_post_meta( $post->ID, '_sb_last_sync_status', true );
		$spreadsheet_id   = get_post_meta( $post->ID, '_sb_spreadsheet_id', true );
		$sheet_name       = get_post_meta( $post->ID, '_sb_sheet_name', true ) ?: 'Sheet1';

		load_template(
			SHEETBRIDGE_PLUGIN_DIR . 'templates/meta-box-status.php',
			false,
			array(
				'last_sync_time'   => $last_sync_time,
				'last_sync_status' => $last_sync_status,
				'spreadsheet_id'   => $spreadsheet_id,
				'sheet_name'       => $sheet_name,
			)
		);
	}

	public function save_meta_boxes( int $post_id ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['sheetbridge_connection_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_POST['sheetbridge_connection_box_nonce'] ), 'sheetbridge_connection_box' ) ) {
			return;
		}

		if ( SHEETBRIDGE_CPT_SLUG !== get_post_type( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'sb_spreadsheet_id',
			'sb_spreadsheet_url',
			'sb_sheet_name',
			'sb_api_key',
		);

		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}

		if ( isset( $_POST['sb_service_account_json'] ) ) {
			$json_raw = wp_unslash( $_POST['sb_service_account_json'] );
			update_post_meta( $post_id, '_sb_service_account_json', $json_raw );

			$parsed = json_decode( $json_raw, true );
			if ( is_array( $parsed ) ) {
				if ( isset( $parsed['client_email'] ) ) {
					update_post_meta( $post_id, '_sb_client_email', sanitize_text_field( $parsed['client_email'] ) );
				}
				if ( isset( $parsed['private_key'] ) ) {
					update_post_meta( $post_id, '_sb_private_key', $parsed['private_key'] );
				}
				if ( isset( $parsed['private_key_id'] ) ) {
					update_post_meta( $post_id, '_sb_private_key_id', sanitize_text_field( $parsed['private_key_id'] ) );
				}
				if ( isset( $parsed['project_id'] ) ) {
					update_post_meta( $post_id, '_sb_project_id', sanitize_text_field( $parsed['project_id'] ) );
				}
			}
		} else {
			// Save individual service-account fields only when the value is not empty
			// to avoid overwriting JSON-derived values with empty form submissions.
			if ( isset( $_POST['sb_client_email'] ) && '' !== $_POST['sb_client_email'] ) {
				update_post_meta( $post_id, '_sb_client_email', sanitize_text_field( wp_unslash( $_POST['sb_client_email'] ) ) );
			}
			if ( isset( $_POST['sb_private_key_id'] ) && '' !== $_POST['sb_private_key_id'] ) {
				update_post_meta( $post_id, '_sb_private_key_id', sanitize_text_field( wp_unslash( $_POST['sb_private_key_id'] ) ) );
			}
			if ( isset( $_POST['sb_project_id'] ) && '' !== $_POST['sb_project_id'] ) {
				update_post_meta( $post_id, '_sb_project_id', sanitize_text_field( wp_unslash( $_POST['sb_project_id'] ) ) );
			}
		}

		if ( isset( $_POST['sb_private_key'] ) && '' !== $_POST['sb_private_key'] ) {
			update_post_meta( $post_id, '_sb_private_key', wp_unslash( $_POST['sb_private_key'] ) );
		}

		if ( isset( $_POST['sb_spreadsheet_url'] ) ) {
			$url = sanitize_text_field( wp_unslash( $_POST['sb_spreadsheet_url'] ) );
			if ( ! empty( $url ) && empty( $_POST['sb_spreadsheet_id'] ) ) {
				$parsed_id = $this->extract_spreadsheet_id_from_url( $url );
				if ( $parsed_id ) {
					update_post_meta( $post_id, '_sb_spreadsheet_id', $parsed_id );
				}
			}
		}
	}

	private function extract_spreadsheet_id_from_url( string $url ): ?string {
		if ( preg_match( '/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
			return $matches[1];
		}
		return null;
	}

	public function custom_columns( array $columns ): array {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			if ( 'title' === $key ) {
				$new_columns['id'] = __( 'ID', 'sheetbridge' );
			}
			$new_columns[ $key ] = $value;
			if ( 'title' === $key ) {
				$new_columns['spreadsheet_id'] = __( 'Spreadsheet ID', 'sheetbridge' );
				$new_columns['sheet_name']     = __( 'Sheet', 'sheetbridge' );
				$new_columns['sync_status']    = __( 'Sync Status', 'sheetbridge' );
			}
		}

		return $new_columns;
	}

	public function custom_column_content( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'id':
				echo '<a href="#" class="sb-copy-id" data-id="' . esc_attr( $post_id ) . '" title="Click copies to clipboard" style="text-decoration:none;color:inherit;">'
					. esc_html( $post_id ) . '</a>';
				break;

			case 'spreadsheet_id':
				$id = get_post_meta( $post_id, '_sb_spreadsheet_id', true );
				echo '<code>' . esc_html( $id ?: '—' ) . '</code>';
				break;

			case 'sheet_name':
				$name = get_post_meta( $post_id, '_sb_sheet_name', true );
				echo esc_html( $name ?: 'Sheet1' );
				break;

			case 'sync_status':
				$status = get_post_meta( $post_id, '_sb_last_sync_status', true );
				$time   = get_post_meta( $post_id, '_sb_last_sync_time', true );
				if ( $status ) {
					echo '<span style="color:' . ( 'success' === $status ? 'green' : 'red' ) . ';">'
						. esc_html( $status ) . '</span>';
					if ( $time ) {
						echo '<br /><small>' . esc_html( $time ) . '</small>';
					}
				} else {
					echo '—';
				}
				break;
		}
	}

	public function add_plugin_action_links( array $links ): array {
		$settings_link = '<a href="' . admin_url( 'edit.php?post_type=' . SHEETBRIDGE_CPT_SLUG ) . '">'
			. __( 'Manage Bridges', 'sheetbridge' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}

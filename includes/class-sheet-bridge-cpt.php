<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SheetBridge_CPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_filter( 'manage_' . SHEETBRIDGE_CPT_SLUG . '_posts_columns', array( $this, 'custom_columns' ) );
		add_action( 'manage_' . SHEETBRIDGE_CPT_SLUG . '_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'flush_rewrite_rules' ) );
		add_action( 'after_switch_theme', array( $this, 'force_flush_rewrite_rules' ) );
	}

	public function register_post_type(): void {
		$labels = array(
			'name'                  => _x( 'Sheet Bridges', 'Post Type General Name', 'sheetbridge' ),
			'singular_name'         => _x( 'Sheet Bridge', 'Post Type Singular Name', 'sheetbridge' ),
			'menu_name'             => __( 'SheetBridge', 'sheetbridge' ),
			'name_admin_bar'        => __( 'Sheet Bridge', 'sheetbridge' ),
			'archives'              => __( 'Sheet Bridge Archives', 'sheetbridge' ),
			'attributes'            => __( 'Sheet Bridge Attributes', 'sheetbridge' ),
			'parent_item_colon'     => __( 'Parent Sheet Bridge:', 'sheetbridge' ),
			'all_items'             => __( 'All Sheet Bridges', 'sheetbridge' ),
			'add_new_item'          => __( 'Add New Sheet Bridge', 'sheetbridge' ),
			'add_new'               => __( 'Add New', 'sheetbridge' ),
			'new_item'              => __( 'New Sheet Bridge', 'sheetbridge' ),
			'edit_item'             => __( 'Edit Sheet Bridge', 'sheetbridge' ),
			'update_item'           => __( 'Update Sheet Bridge', 'sheetbridge' ),
			'view_item'             => __( 'View Sheet Bridge', 'sheetbridge' ),
			'view_items'            => __( 'View Sheet Bridges', 'sheetbridge' ),
			'search_items'          => __( 'Search Sheet Bridges', 'sheetbridge' ),
			'not_found'             => __( 'Not found', 'sheetbridge' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'sheetbridge' ),
			'featured_image'        => __( 'Featured Image', 'sheetbridge' ),
			'set_featured_image'    => __( 'Set featured image', 'sheetbridge' ),
			'remove_featured_image' => __( 'Remove featured image', 'sheetbridge' ),
			'use_featured_image'    => __( 'Use as featured image', 'sheetbridge' ),
			'insert_into_item'      => __( 'Insert into sheet bridge', 'sheetbridge' ),
			'uploaded_to_this_item' => __( 'Uploaded to this sheet bridge', 'sheetbridge' ),
			'items_list'            => __( 'Sheet Bridges list', 'sheetbridge' ),
			'items_list_navigation' => __( 'Sheet Bridges list navigation', 'sheetbridge' ),
			'filter_items_list'     => __( 'Filter sheet bridges list', 'sheetbridge' ),
		);

		$args = array(
			'label'               => __( 'Sheet Bridge', 'sheetbridge' ),
			'description'         => __( 'Google Sheets integration connection', 'sheetbridge' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-grid-view',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
		);

		register_post_type( SHEETBRIDGE_CPT_SLUG, $args );
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

		$spreadsheet_id  = get_post_meta( $post->ID, '_sb_spreadsheet_id', true );
		$spreadsheet_url = get_post_meta( $post->ID, '_sb_spreadsheet_url', true );
		$sheet_name      = get_post_meta( $post->ID, '_sb_sheet_name', true ) ?: 'Sheet1';
		$api_key         = get_post_meta( $post->ID, '_sb_api_key', true );
		$client_email    = get_post_meta( $post->ID, '_sb_client_email', true );
		$private_key     = get_post_meta( $post->ID, '_sb_private_key', true );
		$private_key_id  = get_post_meta( $post->ID, '_sb_private_key_id', true );
		$project_id      = get_post_meta( $post->ID, '_sb_project_id', true );
		$service_account_json = get_post_meta( $post->ID, '_sb_service_account_json', true );
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="sb_spreadsheet_id"><?php esc_html_e( 'Spreadsheet ID', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="text" id="sb_spreadsheet_id" name="sb_spreadsheet_id"
							   value="<?php echo esc_attr( $spreadsheet_id ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'The ID from your Google Sheets URL (the long string between /d/ and /edit).', 'sheetbridge' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sb_spreadsheet_url"><?php esc_html_e( 'Spreadsheet URL', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="url" id="sb_spreadsheet_url" name="sb_spreadsheet_url"
							   value="<?php echo esc_attr( $spreadsheet_url ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Full URL to the Google Sheet (optional, can be used instead of Spreadsheet ID).', 'sheetbridge' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sb_sheet_name"><?php esc_html_e( 'Sheet Name', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="text" id="sb_sheet_name" name="sb_sheet_name"
							   value="<?php echo esc_attr( $sheet_name ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'The name of the sheet tab (e.g. Sheet1, Sheet2). Default: Sheet1', 'sheetbridge' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<hr style="margin: 20px 0;" />

		<h3><?php esc_html_e( 'Authentication', 'sheetbridge' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="sb_api_key"><?php esc_html_e( 'Google API Key', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="text" id="sb_api_key" name="sb_api_key"
							   value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Google Cloud API key (for public sheets / read-only access).', 'sheetbridge' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Service Account Authentication (recommended for write access)', 'sheetbridge' ); ?></h4>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="sb_service_account_json"><?php esc_html_e( 'Service Account JSON', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<textarea id="sb_service_account_json" name="sb_service_account_json"
								  rows="8" class="large-text code"><?php echo esc_textarea( $service_account_json ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Paste the entire service account JSON key file contents here. Fills in the fields below automatically on save.', 'sheetbridge' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sb_client_email"><?php esc_html_e( 'Client Email', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="text" id="sb_client_email" name="sb_client_email"
							   value="<?php echo esc_attr( $client_email ); ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sb_private_key"><?php esc_html_e( 'Private Key', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<textarea id="sb_private_key" name="sb_private_key"
								  rows="6" class="large-text code"><?php echo esc_textarea( $private_key ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sb_private_key_id"><?php esc_html_e( 'Private Key ID', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="text" id="sb_private_key_id" name="sb_private_key_id"
							   value="<?php echo esc_attr( $private_key_id ); ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sb_project_id"><?php esc_html_e( 'Project ID', 'sheetbridge' ); ?></label>
					</th>
					<td>
						<input type="text" id="sb_project_id" name="sb_project_id"
							   value="<?php echo esc_attr( $project_id ); ?>" class="regular-text" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	public function status_box_callback( $post ): void {
		$last_sync_time   = get_post_meta( $post->ID, '_sb_last_sync_time', true );
		$last_sync_status = get_post_meta( $post->ID, '_sb_last_sync_status', true );
		$spreadsheet_id   = get_post_meta( $post->ID, '_sb_spreadsheet_id', true );
		$sheet_name       = get_post_meta( $post->ID, '_sb_sheet_name', true ) ?: 'Sheet1';
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
		<?php
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
			'sb_client_email',
			'sb_private_key_id',
			'sb_project_id',
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
		}

		if ( isset( $_POST['sb_private_key'] ) ) {
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

	public function enqueue_admin_assets( string $hook_suffix ): void {
		$screen = get_current_screen();
		if ( ! $screen || SHEETBRIDGE_CPT_SLUG !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'sheetbridge-admin',
			SHEETBRIDGE_PLUGIN_URL . 'assets/admin/css/admin.css',
			array(),
			SHEETBRIDGE_VERSION
		);

		wp_enqueue_script(
			'sheetbridge-admin',
			SHEETBRIDGE_PLUGIN_URL . 'assets/admin/js/admin.js',
			array(),
			SHEETBRIDGE_VERSION,
			true
		);
	}

	public function flush_rewrite_rules(): void {
		$flushed = get_option( 'sheetbridge_rules_flushed' );
		if ( ! $flushed ) {
			$this->force_flush_rewrite_rules();
			update_option( 'sheetbridge_rules_flushed', true );
		}
	}

	public function force_flush_rewrite_rules(): void {
		$this->register_post_type();
		flush_rewrite_rules();
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SheetBridge_Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
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
}

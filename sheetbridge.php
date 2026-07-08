<?php
/**
 * Plugin Name: SheetBridge: REST API & Sync Hub for Google Sheets
 * Plugin URI: https://github.com/konstantin-agafonov/sheetbridge
 * Description: Transform Google Sheets into a headless backend. Provides a robust REST API and webhooks for bi-directional data sync between WP and Sheets.
 * Version: 1.0.0
 * Author: kagafonov2222@yandex.ru
 * Author URI: https://x.com/K0HCTAHTIH
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sheetbridge
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * Copyright (C) 2026 SheetBridge
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SHEETBRIDGE_VERSION', '1.0.0' );
define( 'SHEETBRIDGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHEETBRIDGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SHEETBRIDGE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SHEETBRIDGE_CPT_SLUG', 'sheet_bridge' );

final class SheetBridge_Plugin {

	private static $instance = null;

	public static function get_instance(): SheetBridge_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->init();
	}

	private function init(): void {
		require_once SHEETBRIDGE_PLUGIN_DIR . 'includes/class-sheet-bridge.php';

		add_action( 'init', array( $this, 'load_admin_includes' ), -100 );
	}

	public function load_admin_includes(): void {
		require_once SHEETBRIDGE_PLUGIN_DIR . 'includes/class-sheet-bridge-cpt.php';
		require_once SHEETBRIDGE_PLUGIN_DIR . 'includes/class-sheet-bridge-admin.php';
		require_once SHEETBRIDGE_PLUGIN_DIR . 'includes/class-sheet-bridge-assets.php';

		new SheetBridge_CPT();
		new SheetBridge_Admin();
		new SheetBridge_Assets();
	}
}

SheetBridge_Plugin::get_instance();

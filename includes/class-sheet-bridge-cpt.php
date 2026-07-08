<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SheetBridge_CPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ), 0 );
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

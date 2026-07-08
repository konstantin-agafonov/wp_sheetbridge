<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! get_option( 'sheetbridge_remove_data_on_uninstall', false ) ) {
	return;
}

define( 'SHEETBRIDGE_CPT_SLUG', 'sheet_bridge' );

$posts = get_posts(
	array(
		'post_type'      => SHEETBRIDGE_CPT_SLUG,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $posts as $post_id ) {
	wp_delete_post( $post_id, true );
}

delete_option( 'sheetbridge_rules_flushed' );
delete_option( 'sheetbridge_remove_data_on_uninstall' );

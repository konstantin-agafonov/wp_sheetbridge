<?php
/**
 * Variables passed via $args to load_template().
 *
 * @var string $id
 * @var string $name
 * @var string $value
 * @var string $label
 * @var int    $rows
 * @var string $description
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// load_template() uses extract( $args, EXTR_SKIP ) which skips $id (global).
// Access field vars directly from $args which is in the same scope.
if ( isset( $args ) && is_array( $args ) ) {
	$id          = $args['id'] ?? ( $id ?? '' );
	$name        = $args['name'] ?? ( $name ?? '' );
	$value       = $args['value'] ?? ( $value ?? '' );
	$label       = $args['label'] ?? ( $label ?? '' );
	$rows        = isset( $args['rows'] ) ? (int) $args['rows'] : ( isset( $rows ) ? (int) $rows : 6 );
	$description = $args['description'] ?? ( $description ?? '' );
} else {
	$rows = isset( $rows ) ? (int) $rows : 6;
}
?>
<tr>
	<th scope="row">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"
				  rows="<?php echo esc_attr( $rows ); ?>" class="large-text code"><?php echo esc_textarea( $value ); ?></textarea>
		<?php if ( ! empty( $description ) ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	</td>
</tr>

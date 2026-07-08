<?php
/**
 * Variables passed via $args to load_template().
 *
 * @var string $id
 * @var string $name
 * @var string $value
 * @var string $label
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
	$description = $args['description'] ?? ( $description ?? '' );
}
?>
<tr>
	<th scope="row">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"
			   value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
		<?php if ( ! empty( $description ) ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	</td>
</tr>

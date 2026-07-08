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

$rows = isset( $rows ) ? (int) $rows : 6;
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

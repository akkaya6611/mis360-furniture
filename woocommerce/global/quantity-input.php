<?php
/**
 * Product quantity inputs with +/- stepper buttons
 *
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

/* translators: %s: Quantity. */
$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'woocommerce' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'woocommerce' );

// CRITICAL: WooCommerce passes $type = 'hidden' when min_value === max_value (e.g. stock is 1).
// We ALWAYS make sure the input is visible so customer sees the number (1) and +/- buttons.
$input_type = ( 'hidden' === $type ) ? 'number' : $type;
$val = ( isset( $input_value ) && '' !== $input_value && 0 < (int) $input_value ) ? (int) $input_value : 1;
?>
<div class="quantity emdief-qty-stepper">
	<?php
	/**
	 * Hook to output something before the quantity input field.
	 *
	 * @since 7.2.0
	 */
	do_action( 'woocommerce_before_quantity_input_field' );
	?>
	<button type="button" class="emdief-qty-btn qty-minus" aria-label="<?php esc_attr_e( 'Azalt', 'mis360-mobilya' ); ?>" tabindex="-1">−</button>
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $label ); ?></label>
	<input
		type="<?php echo esc_attr( $input_type ); ?>"
		id="<?php echo esc_attr( $input_id ); ?>"
		class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
		name="<?php echo esc_attr( $input_name ); ?>"
		value="<?php echo esc_attr( $val ); ?>"
		aria-label="<?php esc_attr_e( 'Product quantity', 'woocommerce' ); ?>"
		<?php if ( in_array( $input_type, array( 'text', 'search', 'tel', 'url', 'email', 'password' ), true ) ) : ?>
			size="4"
		<?php endif; ?>
		min="<?php echo esc_attr( $min_value ); ?>"
		<?php if ( 0 < $max_value ) : ?>
			max="<?php echo esc_attr( $max_value ); ?>"
		<?php endif; ?>
		step="<?php echo esc_attr( $step ); ?>"
		placeholder="<?php echo esc_attr( $placeholder ); ?>"
		inputmode="numeric"
		autocomplete="off"
	/>
	<button type="button" class="emdief-qty-btn qty-plus" aria-label="<?php esc_attr_e( 'Arttır', 'mis360-mobilya' ); ?>" tabindex="-1">+</button>
	<?php
	/**
	 * Hook to output something after quantity input field
	 *
	 * @since 3.6.0
	 */
	do_action( 'woocommerce_after_quantity_input_field' );
	?>
</div>

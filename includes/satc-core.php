<?php

/**
 * Agrega un campo numerico para seleccionar la cantidad de productos a agregar al carrito.
 */
add_action( 'woocommerce_after_shop_loop_item', 'satc_show_quantity', 10 );
function satc_show_quantity() {
	if ( is_product_category() || is_shop()) { 
	?>
	<label for="product-quantity">Cantidad</label>
	<input type="number" value="1" name="product-quantity" class="product-quantity" />
	<?php
	}
}

/**
 * Muestra una seccion con las variedades disponibles de un producto.
 */
add_action( 'woocommerce_after_shop_loop_item', 'list_product_variations', 15 );
function list_product_variations() {
	global $product;
	if ( is_product_category() || is_shop() ) {
		if ( $product->is_type( 'variable' ) ) {
			$variation_group 	= array();
			$json_array			= array();
			$variations 		= $product->get_available_variations();
			// Ciclo de cada variacion.
			for ( $i = 0; $i <= count($variations) - 1; $i++ ) {
				$variation 		= $variations[$i];
				$attributes 	= $variation['attributes'];
				$variation_data	= array();
				$att_group		= array();
				$j = 0;
				foreach ( $attributes as $attribute_key => $value ) {
					if ( !in_array( $value, $variation_group[$attribute_key] ) ) {
						$variation_group[$attribute_key][] = $value;
					}
					$att_group[$j]['att_name'] 	= $attribute_key;
					$att_group[$j]['att_value']	= $value;
					$j++;
				}
				$variation_data['variation_id'] = $variation['variation_id'];
				$variation_data['price'] 		= strip_tags( $variation['display_price'] );
				$variation_data['attributes']	= $att_group;
				$json_array[] 					= $variation_data;
			}			
		}

		foreach ( $variation_group as $key => $value ) {
			$options = $value;
		?>
		<label for="<?php echo $key; ?>"><?php echo wc_attribute_label( str_replace('attribute_', '', $key), $product ); ?></label>
		<select name="<?php echo $key; ?>" class="<?php echo $key; ?> cbo-variation">
			<option value="-1"><?php echo _e('-Seleccione-', 'simple-add-to-cart'); ?></option>
			<?php 
			for ( $i = 0; $i <= count( $options ) - 1; $i++ ) {
				echo "<option value=\"$options[$i]\">$options[$i]</option>";
			}
			?>
		</select>
		<input type="hidden" name="variation-data" class="variation-data" value="<?php echo esc_html(json_encode( $json_array )); ?>" />
		<input type="hidden" name="product-id" class="product-id" value="<?php echo $product->get_ID(); ?>" />
		<input type="hidden" name="variation-id" class="variation-id" value="-1" />
		<?php
		}
	}
}

/**
 * Quitar el botón por defecto para agregar al carrito en el listado de productos.
 */
add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );
function remove_add_to_cart_buttons() {
	if ( is_product_category() || is_shop() ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
	}
}

/**
 * Agrega un botón personalizado en el listado de productos por cada producto, en conjunto con las variedades y la cantidad, agrega mediante ajax el producto al carrito.
 */
add_action( 'woocommerce_after_shop_loop_item', 'append_custom_add_to_cart_button', 20 );
function append_custom_add_to_cart_button() {
	if( is_product_category() || is_shop() ) { 
	?>
	<input type="button" class="satc-add-to-cart" value="Agregar al carrito" />
	<div class="status-cart"></div>
	<?php
	}
}

/**
 * Agrega la acción de sumar un producto al carrito de manera asincrona
 */
add_action( 'wp_ajax_nopriv_satc_add_to_cart', 'satc_add_to_cart');
function satc_add_to_cart() {
	header('Content-type: text/json');
	$product_id 		= $_POST['product_id'];
	$variation_id		= $_POST['variation_id'];
	$quantity			= $_POST['quantity'];
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    = get_post_status( $product_id );

	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		wc_add_to_cart_message( $product_id );
		wp_send_json(
			array(
				'status' => 'OK',
			)
		);
	} else {
		// If there was an error adding to the cart, redirect to the product page to show any errors
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
		);

		wp_send_json( $data );
	}

	wp_die();
}
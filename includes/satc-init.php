<?php
/**
 * Carga de scripts necesarios para agregar los productos al carrito mediante ajax.
 */

function satc_enqueue_scripts() {

	if ( ! wp_script_is( 'jquery' ) ) {
		wp_enqueue_script( 'jquery' );
	}
    
    wp_enqueue_script (
        'satc-core-script',
        plugin_dir_url( __FILE__ ) . '../public/js/satc-core.js',
        array( 'jquery' )
    );

	wp_localize_script (
		'satc-core-script',
		'site_config',
		array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'base_url' => get_site_url(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'satc_enqueue_scripts');
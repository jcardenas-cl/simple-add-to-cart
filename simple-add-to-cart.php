<?php
/**
 * Archivo inicial del plugin
 *
 * @category Install
 * @package  Install
 * @author   Julio Cárdenas <julio@arknite.dev>
 * @license  GPLv2 or later
 * @link     https://arknite.dev/plugins/simple-add-to-cart
 */

/*
Plugin Name: Simple Add To Cart
Plugin URI: https://arknite.dev/plugins/simple-add-to-cart
Description: Reemplaza el botón estadar de WooCommerce por uno con mayor funcionalidad
Version: 0.8
Author: Julio Cárdenas
Author URI: https://arknite.dev
Text Domain: simple-add-to-cart
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Simple_add_to_cart' ) ) {

	class Simple_add_to_cart {

		/**
		 * Definir la versión del plugin actual.
		 *
		 * @var String $plugin_version Versión del plugin.
		 */
		private $plugin_version = '1.0.0';

		/**
		 * Constructor del plugin, de momento se inicia vacio
		 */
		public function __construct() {

		}

		/**
		 * Realiza la instalación del plugin
		 *
		 * @return void
		 */
		public static function install() {
			// Do nothing
		}

		/**
		 * Metodo que se ejecuta al cargar el plugin, cuando ya esta activado
		 *
		 * @return void
		 */
		public function init_setup() {
			include_once plugin_dir_path( __FILE__ ) . 'includes/satc-init.php';
			include_once plugin_dir_path( __FILE__ ) . 'includes/satc-core.php';
		}
	}
	
}

$simple_add_to_cart = new Simple_add_to_cart();
$simple_add_to_cart->init_setup();

register_activation_hook( __FILE__, [ 'Simple_add_to_cart', 'install' ] );
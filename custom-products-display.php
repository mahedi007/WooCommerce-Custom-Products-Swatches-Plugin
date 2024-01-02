<?php
/*
Plugin Name: Custom Products for WooCommerce
Plugin URI: 
Description: WordPress plugin that adds the various functionality to the WooCommerce 
Author: Mahedi Hasan
Version: 1.0.0
Author URI: 
*/

class CPD_Variations {

	protected $cpd_variation;

	public function __construct() {
		define( 'CPD_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'CPD_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		define( 'CPD_VERSION', '1.0.0' );

		load_plugin_textdomain( 'cpd-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


		include_once( CPD_PATH . '/inc/core-functions.php' );

		if ( is_admin() ) {
			include_once( CPD_PATH . '/admin/class-cpd-admin.php' );
		}

		if ( ! is_admin() ) {
			include_once( CPD_PATH . '/inc/class-cpd-render.php' );
		}

		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$this->cpd_variation = apply_filters( 'cpd_variation', $this->cpd_variation );
	}

	public function get_cpd_variation() {
		return $this->cpd_variation;
	}
}

add_action( 'plugins_loaded', 'cpd_init' );
function cpd_init() {
	if ( function_exists( 'WC' ) ) {
		$GLOBALS['CPD_Instance'] = new CPD_Variations();
	}
}

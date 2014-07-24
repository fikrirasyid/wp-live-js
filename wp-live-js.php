<?php
/**
 * Plugin Name: WP Live.js
 * Plugin URI: http://fikrirasyid.com/wordpress-plugins/wp-live-js
 * Description: Easily switch on/off from admin bar to enqueue live.js to the <head>. This plugin is made for development.
 * Version: 0.1
 * Author: Fikri Rasyid
 * Author URI: http://fikrirasyid.com/
 * License: GPLv2 or later
 * 
 * @package WP_Live_JS
 * @author Fikri Rasyid
*/

// Constants
if (!defined('WP_LIVE_JS_URL'))
    define('WP_LIVE_JS_URL', plugin_dir_url( __FILE__ ));

class WP_Live_JS{

	var $status;

	/**
	 * Constructing the class
	 */
	function __construct(){
		$this->status = get_option( 'wp_live_js_status', 1 );

		// Toggle live.js status
		add_action( 'wp_ajax_toggle_live_js', array( $this, 'toggle_status' ) );
		add_action( 'wp_ajax_nopriv_toggle_live_js', array( $this, 'toggle_status' ) );

		// Adding admin bar link
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ) ); 

		// Adding enqueue scripts
		if( $this->status == 1 ){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}		
	}

	/**
	 * Enqueue live.js to the WordPress <head>
	 * 
	 * @return void
	 */
	function enqueue_scripts(){
		wp_enqueue_script( 'live', WP_LIVE_JS_URL . '/js/live.js', array(), 4, false );
	}

	/**
	 * Change live.js insertion status
	 * 
	 * @return void
	 */
	function toggle_status(){
		// Set new status
		if( $this->status == 1 ){
			$new_status = 0;
		} else {
			$new_status = 1;
		}

		// Update the status
		update_option( 'wp_live_js_status', $new_status );

		// Set the status
		$this->status = $new_status;

		// If there's any referrer
		if( isset( $_REQUEST['referrer'] ) )
			wp_redirect( $_REQUEST['referrer'] );

		die();
	}

	/**
	 * Adding toggle link on admin bar
	 * 
	 * @return void
	 */
	function admin_bar_menu(){

		if( is_admin_bar_showing() ){
			global $wp_admin_bar, $wp;

			// Link of toggle functions
			$current_url = trailingslashit( home_url( $wp->request ) );
			$href = admin_url() . 'admin-ajax.php?action=toggle_live_js&referrer=' . urlencode( $current_url );

			// Determine the title of admin bar
			if( $this->status == 1 ){
				$title = __( 'Deactivate Live.js', 'wp-live-js' );
			} else {
				$title = __( 'Activate Live.js', 'wp-live-js' );				
			}

			$wp_admin_bar->add_menu( array(
				'id'     => 'wp-live-js',
				'parent' => 'top-secondary',
				'title'	 => $title,
				'href'	 => $href
			) );
		}

	}
}
new WP_Live_JS;
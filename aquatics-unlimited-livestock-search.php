<?php
/*
  Plugin Name: Aquatics Unlimited Livestock Search
  Plugin URI:  http://getsim.com
  Description: A custom livestock search.
  Version:     1.0
  Author:      GetSIM
  Author URI:  http://getsim.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$pluginSlug = 'aquatics-unlimited-livestock-search';

/*
** Set up wp_ajax requests for frontend UI.
** NOTE: _nopriv_ makes ajaxurl work for logged out users.
*/
add_action( 'wp_ajax_au_fetch_livestock', 'au_fetch_livestock' );
add_action( 'wp_ajax_nopriv_au_fetch_livestock', 'au_fetch_livestock' );
function au_fetch_livestock() {
  include( plugin_dir_path( __FILE__ ) . 'inc/search.php' );
}

/*
** Assets
*/
function aquatics_ulimited_livestock_search_scripts_styles() {
  global $pluginSlug;
  wp_register_style( $pluginSlug . '-css', plugins_url('/css/styles.css',  __FILE__ ));
  wp_register_script( $pluginSlug . '-js', plugins_url('/js/app.js',  __FILE__ ), '', false, true );
}
add_action('wp_enqueue_scripts', 'aquatics_ulimited_livestock_search_scripts_styles');

/*
** Shortcode
*/
include 'inc/shortcode.php';

?>

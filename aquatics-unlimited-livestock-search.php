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
** Setup
*/
function aquatics_ulimited_livestock_search_scripts_styles() {
  global $pluginSlug;
  wp_register_style( $pluginSlug . '-css', plugins_url('/css/' . $pluginSlug . '.css',  __FILE__ ));
  wp_register_script( $pluginSlug . '-js', plugins_url('/js/' . $pluginSlug . '.js',  __FILE__ ), array('jquery'), false, true );
}
add_action('wp_enqueue_scripts', 'aquatics_ulimited_livestock_search_scripts_styles');

/*
** Set up wp_ajax requests for frontend UI.
** NOTE: _nopriv_ makes ajaxurl work for logged out users.
*/
// add_action( 'wp_ajax_thinkpawsitive_fetch_bookings', 'thinkpawsitive_fetch_bookings' );
// add_action( 'wp_ajax_nopriv_thinkpawsitive_fetch_bookings', 'thinkpawsitive_fetch_bookings' );
// function thinkpawsitive_fetch_bookings() {
//   include( plugin_dir_path( __FILE__ ) . 'inc/thinkpawsitive-fetch-bookings.php' );
// }

/*
** Shortcode
*/
function aquatics_unlimited_livestock_search_func( $atts ) {
  global $pluginSlug;
  wp_enqueue_style($pluginSlug . '-css');
  wp_localize_script( $pluginSlug . '-js', 'wp_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
  wp_enqueue_script($pluginSlug . '-js');

  $html = '<div id="' . $pluginSlug . '">';
    // loading
    $html .= '<div id="loading" class="pixel-spinner">';
      $html .= '<div class="pixel-spinner-inner"></div>';
    $html .= '</div>';
    // calendar
    $html .= '<div>'.$pluginSlug.'</div>';
  $html .= '</div>';

  return $html;
}
add_shortcode( $pluginSlug, 'aquatics_unlimited_livestock_search_func' );

?>

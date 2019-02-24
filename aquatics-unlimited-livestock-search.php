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
  include( plugin_dir_path( __FILE__ ) . 'inc/db-search.php' );
}

/*
** Assets
*/
function aquatics_ulimited_livestock_search_scripts_styles() {
  global $pluginSlug;
  wp_register_style( $pluginSlug . '-css', plugins_url('/css/' . $pluginSlug . '.css',  __FILE__ ));
  wp_register_script( $pluginSlug . '-js', plugins_url('/js/' . $pluginSlug . '.js',  __FILE__ ), array('jquery'), false, true );
}
add_action('wp_enqueue_scripts', 'aquatics_ulimited_livestock_search_scripts_styles');

/*
** Shortcode
*/
function aquatics_unlimited_livestock_search_func( $atts ) {
  global $pluginSlug;
  wp_enqueue_style($pluginSlug . '-css');
  wp_localize_script( $pluginSlug . '-js', 'wp_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
  wp_enqueue_script($pluginSlug . '-js');

  //show all top level livestock_categories terms
  $catsArray = array(18,15,19,88,87,16,17);
  $args = [
    'taxonomy' => 'livestock_categories',
    'exclude' => array( 104 ),
    'parent' => 0,
    'number' => 10,
    'include' => $catsArray,
    'hide_empty' => false,
    'orderby' => 'include',
  ];
  $terms = get_terms( $args );

  $html = '<div id="' . $pluginSlug . '">';
    // loading
    $html .= '<div id="loading" class="pixel-spinner">';
      $html .= '<div class="pixel-spinner-inner"></div>';
    $html .= '</div>';

    // Search UI
    $html .= '<form id="au-search">
      <button type="submit">Search</button>
    </form>';
    $html .= '<button id="reset-au-search-results" type="button">Clear</button>';

    // Results
    $html .= '<ul id="au-search-results" class="livestock-grid">';
    foreach ( $terms as $term ):
      $theID = $term->term_id;
      $html .= '<a href="'. site_url() . '/livestock-category/?cat='. $theID . '">';
        // $html .= '<img src="do_shortcode(sprintf("[wp_custom_image_category term_id="%s" size="parent-category" onlysrc="true"]", $theID));" alt="' . $term->name '" . />';
        $html .= '<span>' . $term->name . '</span>';
      $html .= '</a>';
    endforeach;
    $html .= '</ul>';

  $html .= '</div>';
  return $html;
}
add_shortcode( $pluginSlug, 'aquatics_unlimited_livestock_search_func' );

?>

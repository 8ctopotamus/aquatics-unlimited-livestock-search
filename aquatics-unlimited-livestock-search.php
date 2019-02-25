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
  wp_localize_script( $pluginSlug . '-js', 'wp_data', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'plugin_slug' => $pluginSlug
  ));
  wp_enqueue_script($pluginSlug . '-js');

  //show all top level livestock_categories terms
  $catsArray = array(18, 15, 19, 88, 87, 16, 17);
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
    $html .= '<form id="au-search-form">
      <div id="au-search-fields"></div>
      <button type="submit">Search</button>
      <button id="reset-au-search-results" type="button">Reset</button>
    </form>';

    // Results grid
    $html .= '<ul id="au-search-results" class="livestock-grid">';

    // All categories
    $html .= '<li>';
      $html .= '<a href="#" class="catSelector">';
        $html .= '<img src="' . plugins_url('/img/all-category.jpg',  __FILE__ ) . '" class="livestock-thumbnail" alt="All Categories" />';
        $html .= '<span class="livestock-title">All Categories</span>';
      $html .= '</a>';
    $html .= '</li>';

    // Initial Cats
    foreach ( $terms as $term ):
      $theID = $term->term_id;
      $html .= '<li>';
        $html .= '<a href="#" class="catSelector" data-catid="' . $theID . '">';
          $html .= '<img src="' . do_shortcode(sprintf("[wp_custom_image_category term_id='%s' size='medium' onlysrc='true']", $theID)) . '" class="livestock-thumbnail" alt="' . $term->name . '" />';
          $html .= '<span class="livestock-title">' . $term->name . '</span>';
        $html .= '</a>';
      $html .= '</li>';
    endforeach;
    $html .= '</ul>';

  $html .= '</div>';
  return $html;
}
add_shortcode( $pluginSlug, 'aquatics_unlimited_livestock_search_func' );

?>

<?php

function aquatics_unlimited_livestock_search_func( $atts ) {
  global $pluginSlug;
  global $fieldsWeCareAbout;

  wp_enqueue_style($pluginSlug . '-css');
  wp_localize_script( $pluginSlug . '-js', 'wp_data', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'plugin_slug' => $pluginSlug
  ));
  wp_enqueue_script($pluginSlug . '-js');

  // show all top level livestock_categories terms
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
    // Search UI
    $html .= '<form id="au-search-form">';
      $html .= '<div class="au-search-form-fields-flex">';
        // Render fields from ACF Fields Group (Livestock Single Post, ID: 53)
        $fields = acf_get_fields(53);
        foreach ($fields as $field):
          $name = $field['name'];
          $label = $field['label'];
          $choices = $field['choices'];
          if ( !in_array( $name, $fieldsWeCareAbout ) ):
            continue;
          endif;
          $html .= '<div class="form-control">';
          $html .= '<label for="' . $name . '">' . $label . '</label>';
          $html .= '<select name="' . $name . '">';
            $html .= '<option value="" selected>---</option>';
            foreach ($choices as $choice):
              $html .= '<option value="' . $choice . '">' . $choice . '</option>';
            endforeach;
          $html .= '</select>';
          $html .= '</div>';
        endforeach;
        $html .= '<button type="submit">Search</button>';
      $html .= '</div>';
    $html .= '</form>';

    // loading
    $html .= '<div id="loading" class="progress-line"></div>';

    // Results stats
    $html .= '<div id="results-stats-container">';
      $html .= '<div id="results-stats"></div>';
      $html .= '<button id="reset-au-search-results" type="button">🔄 Reset</button>';
    $html .= '</div>';

    // Results grid
    $html .= '<ul id="au-search-results-grid" class="livestock-grid">';

    // All categories
    $html .= '<li>';
      $html .= '<a href="#" data-catname="All Livestock" class="catSelector fade-in">';
        $html .= '<img src="' . plugins_url('/img/all-category.jpg',  __DIR__ ) . '" class="livestock-thumbnail" alt="All Categories" />';
        $html .= '<span class="livestock-title">All Livestock</span>';
      $html .= '</a>';
    $html .= '</li>';

    // Initial Cats
    foreach ( $terms as $term ):
      $theID = $term->term_id;
      $html .= '<li>';
        $html .= '<a href="#" class="catSelector fade-in" data-catid="' . $theID . '" data-catname="' . $term->name . '">';
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

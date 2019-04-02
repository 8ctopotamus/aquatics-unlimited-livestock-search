<?php

$fieldsWeCareAbout = [
  'minimum_tank_size',
  'care_level',
  'temperament',
  'diet',
  'max_size',
  'placement',
  'reef_compatible',
  'plant_safe'
];

$catsArray = [
  // marine fish
  18 => ['exclude' => [
    'placement',
    'reef_compatible',
    'plant_safe',
    'max_size'
  ] ],
  // freshwater fish
  15 => ['exclude' => [
    'placement',
    'reef_compatible',
    'plant_safe'
  ] ],
  // marine invertebrates
  19  => ['exclude' => [
    'placement',
    'plant_safe'
  ] ],
  // freshwater invertebrates
  88  => ['exclude' => [
    'temperament',
    'placement',
    'reef_compatible'
  ] ],
  // corals
  87 => ['exclude' => [
    'temperament',
    'diet',
    'max_size',
    'reef_compatible',
    'plant_safe'
  ] ],
  // freshwater plants
  16 => ['exclude' => [
    'temperament',
    'diet',
    'max_size',
    'reef_compatible',
    'plant_safe'
  ] ],
  // pond fish
  167 => ['exclude' => [
    'placement',
    'reef_compatible',
    'plant_safe',
  ] ],
  // pond plants
  17 => ['exclude' => [
    'temperament',
    'diet',
    'max_size',
    'reef_compatible',
    'plant_safe'
  ] ]
];

function aquatics_unlimited_livestock_search_func( $atts ) {
  global $pluginSlug;
  global $fieldsWeCareAbout;
  global $catsArray;

  wp_enqueue_style($pluginSlug . '-css');
  wp_localize_script( $pluginSlug . '-js', 'wp_data', array(
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'plugin_slug' => $pluginSlug,
    'cats_array' => $catsArray
  ));
  wp_enqueue_script($pluginSlug . '-js');

  $args = [
    'taxonomy' => 'livestock_categories',
    'exclude' => array( 104 ),
    'parent' => 0,
    'number' => 10,
    'include' => array_keys($catsArray),
    'hide_empty' => false,
    'orderby' => 'include',
  ];

  $terms = get_terms( $args );

  $html = '<div id="' . $pluginSlug . '">';
    $html .= '<noscript>The Livestock Search requires JavaScript to be enabled.</noscript>';

    // Search UI
    $html .= '<form id="' . $pluginSlug . '-form">';
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
          $suffix = $name === 'minimum_tank_size' ? ' gallons' : '';
          $html .= '<div class="form-control">';
          $html .= '<label for="' . $name . '">' . $label . '</label>';
          $html .= '<select id="' . $name . '" name="' . $name . '">';
            $html .= '<option value="" selected>---</option>';
            foreach ($choices as $choice):
              $html .= '<option value="' . $choice . '">' . $choice . ' ' . $suffix . '</option>';
            endforeach;
          $html .= '</select>';
          $html .= '</div>';
        endforeach;
        $html .= '<button id="'. $pluginSlug .'-form-submit" type="submit">Search</button>';
      $html .= '</div>';
    $html .= '</form>';

    // loading
    $html .= '<div id="loading" class="progress-line"></div>';

    // Results stats
    $html .= '<div id="results-stats-container">';
      $html .= '<div id="results-stats"></div>';
      $html .= '<div class="au-results-actions">';
        $html .= '<button class="au-pagination-button" data-dir="-1">&#9668;</button>';
        $html .= '<div id="page-count"></div>';
        $html .= '<button class="au-pagination-button" data-dir="1">&#9658;</button>';
        $html .= '<button id="reset-au-search-results" type="button">🔄 Reset</button>';
      $html .= '</div>';
    $html .= '</div>';

    // Results grid
    $html .= '<ul id="au-search-results-grid" class="livestock-grid">';

    // All categories
    $html .= '<li>';
      $html .= '<a href="#" data-catname="All Livestock" class="catSelector">';
        $html .= '<img src="' . plugins_url('/img/all-category.jpg',  __DIR__ ) . '" class="livestock-thumbnail" alt="All Categories" />';
        $html .= '<span class="livestock-title">All Livestock</span>';
      $html .= '</a>';
    $html .= '</li>';

    // Initial Cats
    foreach ( $terms as $term ):
      $theID = $term->term_id;
      $html .= '<li>';
        $html .= '<a href="#" class="catSelector" data-catid="' . $theID . '" data-catname="' . $term->name . '">';
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

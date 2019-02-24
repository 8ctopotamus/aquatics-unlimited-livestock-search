<?php

global $fieldsWeCareAbout;
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

$cat = !empty($_POST['cat']) ? $_POST['cat'] : NULL;
$placeholderImgUrl = site_url() . '/wp-content/uploads/2017/11/featured-default-150x150.jpg';

function filterACFFields($key) {
  global $fieldsWeCareAbout;
  return in_array( $key, $fieldsWeCareAbout );
}

$args = array(
  'post_type' => 'livestock',
  // 'posts_per_page' => '-1',
  'orderby' => 'title',
  'order' => 'ASC',
  'tax_query' => array(
    array (
      'taxonomy' => 'livestock_categories',
      'terms' => $cat,
    )
  )
);

$query = new WP_Query( $args );
$results = [];

if ( $query->have_posts() ):
  while ( $query->have_posts() ) : $query->the_post();
    $attachment_id = get_field('main_photo');
    $size = "medium"; // livestock-thumb
    $mainImageACF = wp_get_attachment_image_src( $attachment_id, $size )[0];
    $thumbnail = !is_null($mainImageACF) ?
                 $mainImageACF :
                 $placeholderImgUrl;
    $acfFields = array_filter( get_fields(get_the_id()), 'filterACFFields', ARRAY_FILTER_USE_KEY );
    $results[] = [
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
      'thumbnail' => $thumbnail,
      'acf' => $acfFields
    ];
    wp_reset_postdata();
  endwhile;
endif;

echo json_encode($results);
wp_die();

?>

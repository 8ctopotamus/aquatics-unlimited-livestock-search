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

$placeholderImgUrl = plugins_url('/img/placeholder.jpg',  __DIR__ );

$cat = !empty($_POST['cat']) ? $_POST['cat'] : false;
$paged = !empty($_POST['paged']) ? $_POST['paged'] : 0;

function filterACFFields($key) {
  global $fieldsWeCareAbout;
  return in_array( $key, $fieldsWeCareAbout );
}

$args = array(
  'post_type' => 'livestock',
  'posts_per_page' => '12',
  'paged' => $paged,
  'orderby' => 'title',
  'order' => 'ASC',
);

if ($cat):
  $args['tax_query'] = array(
    array (
      'taxonomy' => 'livestock_categories',
      'terms' => $cat,
    )
  );
endif;

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
    $results['data'][] = [
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
      'thumbnail' => $thumbnail,
      'acf' => $acfFields
    ];
    wp_reset_postdata();
  endwhile;
endif;

$results['total'] = $query->found_posts;

echo json_encode($results);
wp_die();

?>

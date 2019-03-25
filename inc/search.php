<?php

global $fieldsWeCareAbout;

$cat = !empty($_POST['cat']) ? $_POST['cat'] : false;
$postsPerPage = $_POST['postsPerPage'] ? intval($_POST['postsPerPage']) : 12;
$paged = $_POST['paged'] ? intval($_POST['paged']) : 0;
$includeMeta = $_POST['includeMeta'] === 'true' ? boolval($_POST['includeMeta']) : false;
$debug = $_POST['debug'] === 'true' ? boolval($_POST['debug']) : false;

$placeholderImgUrl = plugins_url('/img/placeholder.jpg',  __DIR__ );

$results = [
  'data' => [],
  'total' => 0
];

function filterACFFields($key) {
  global $fieldsWeCareAbout;
  return in_array( $key, $fieldsWeCareAbout );
}

$args = array(
  'post_type' => 'livestock',
  'posts_per_page' => $postsPerPage,
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

if ($includeMeta):
  $args['meta_query']	= array(
    'relation'		=> 'AND',
  );
  foreach ($fieldsWeCareAbout as $field):
    if ($_POST[$field]):
      $compare = $field === 'minimum_tank_size' ? '>=' : 'LIKE';
      $type = $field === 'minimum_tank_size' ? 'NUMERIC' : 'CHAR';
      $args['meta_query'][] = [
        'key' => $field,
        'value' => $_POST[$field],
        'compare' => $compare,
        'type' => $type
      ];
    endif;
  endforeach;
endif;

$query = new WP_Query( $args );

if ( $query->have_posts() ):
  $results['total'] = $query->found_posts;
  while ( $query->have_posts() ) : $query->the_post();
    $acfFields = array_filter( get_fields( get_the_id() ), 'filterACFFields', ARRAY_FILTER_USE_KEY );
    $attachment_id = get_field('main_photo');
    $size = "medium";
    $mainImageACF = wp_get_attachment_image_src( $attachment_id, $size )[0];
    $thumbnail = !is_null($mainImageACF) ?
                 $mainImageACF :
                 $placeholderImgUrl;
    $results['data'][] = [
      'title' => get_the_title(),
      'permalink' => get_the_permalink(),
      'thumbnail' => $thumbnail,
      'acf' => $acfFields
    ];
    wp_reset_postdata();
  endwhile;
endif;

if ($debug):
  $results['debug'] = [
    'WP_Query' => [
      '$query' => $query,
      '$args' => $args,
    ]
  ];
endif;

echo json_encode($results);
wp_die();

?>

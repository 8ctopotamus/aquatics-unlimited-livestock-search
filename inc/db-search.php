<?php

$cat = $_POST['cat'];
$placeholderImgUrl = '/wp-content/uploads/2017/11/featured-default-150x150.jpg';

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
  ),
);

$query = new WP_Query( $args );

$results = [];
while ( $query->have_posts() ) : $query->the_post();
  $attachment_id = get_field('main_photo');
  $size = "livestock-thumb";
  $mainImageACF = wp_get_attachment_image_src( $attachment_id, $size )[0];
  $thumbnail = !is_null($mainImageACF) ?
               $mainImageACF :
               $placeholderImgUrl;
  $results[] = [
    'title' => get_the_title(),
    'permalink' => get_the_permalink(),
    'thumbnail' => $thumbnail
  ];
  wp_reset_postdata();
endwhile;

echo json_encode($results);
wp_die();

?>

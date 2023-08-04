<?php 

function uniRegisterSearch() {
  register_rest_route('uni/v1','search', array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'uniSearchResults'
  ));
}

function uniSearchResults($data) {
  $mainQuery = new WP_Query(array(
    'post_type' => array('post','page','professor','program','event','campuses'),
    's' => sanitize_text_field($data['term'])
  ));

  $results = array(
    'generalInfo' => array(),
    'professors' => array(),
    'programs' => array(),
    'events' => array(),
    'campuses' => array()
  );

  while($mainQuery->have_posts()) {
    $mainQuery->the_post();

    if (get_post_type() == 'post' || get_post_type() == 'page') {
      array_push($results['generalInfo'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'professors') {
      array_push($results['professors'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'events') {
      array_push($results['events'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));

    if (get_post_type() == 'programs') {
      array_push($results['programs'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'campuses') {
      array_push($results['campuses'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }
  }

  }

  return $results;
}

add_action('rest_api_init', 'uniRegisterSearch');
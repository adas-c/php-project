<?php 

function uniRegisterSearch() {
  register_rest_route('uni/v1','search', array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'uniSearchResults'
  ));
}

function uniSearchResults() {
  return 'con';
}

add_action('rest_api_init', 'uniRegisterSearch');
<?php

function university_files() {
  wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
  add_theme_support('title-tag');
  register_nav_menu('headerMenu', 'Header Manu Location');
  register_nav_menu('footerMenuOne', 'Footer Manu Location One');
  register_nav_menu('footerMenuTwo', 'Footer Manu Location Two');
  add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'university_features');


function uni_adjust_queries($query) {
  if (!is_admin() && is_post_type_archive('program') && is_main_query()) {
    $query->set('orderby','title'); 
    $query->set('order', 'ASC');
    $query->set('post_per_page', -1);
  }

  if (!is_admin() && is_post_type_archive('event') && is_main_query()) {
    $today = date('Ymd');
    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value_val');
    $query->set('order', 'ASC');
    $query->set('meta_query', array(
              array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
              )
            ));
  }
}

add_action('pre_get_posts', 'uni_adjust_queries');



// function moved to new file called 'must use plugins'

// function uni_post_types() {
//   // Events post type
//   register_post_type('event', array(
//     'supports' => array('title','editor','excerpt'),
//     'rewrite' => array(
//       'slug' => 'events'
//     ),
//     'has_archive' => true,
//     'public' => true,
//     'show_in_rest' => true,
//     'labels' => array(
//       'name' => 'Events',
//       'add_new_item' => 'Add New Event',
//       'edit_item' => 'Edit Event',
//       'all_items' => 'All Events',
//       'singular_name' => 'Event'
//     ),
//     'menu_icon' => 'dashicons-calendar'
//   ));

//   // Program post type
//   register_post_type('program', array(
//     'supports' => array('title','editor'),
//     'rewrite' => array(
//       'slug' => 'programs'
//     ),
//     'has_archive' => true,
//     'public' => true,
//     'show_in_rest' => true,
//     'labels' => array(
//       'name' => 'Programs',
//       'add_new_item' => 'Add New Program',
//       'edit_item' => 'Edit Program',
//       'all_items' => 'All Programs',
//       'singular_name' => 'Program'
//     ),
//     'menu_icon' => 'dashicons-awards'
//   ));
//   // Professor post type
//   register_post_type('professor', array(
//     'supports' => array('title','editor','thumbnail'),
//     'public' => true,
//     'show_in_rest' => true,
//     'labels' => array(
//       'name' => 'Professors',
//       'add_new_item' => 'Add New Professor',
//       'edit_item' => 'Edit Professor',
//       'all_items' => 'All Professors',
//       'singular_name' => 'Professor'
//     ),
//     'menu_icon' => 'dashicons-welcome-learn-more'
//   ));
// };

// add_action('init', 'uni_post_types');
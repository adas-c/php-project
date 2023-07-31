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
}

add_action('after_setup_theme', 'university_features');

// function moved to new file called 'must use plugins'

// function uni_post_types() {
//   register_post_type('event', array(
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
// };

// add_action('init', 'uni_post_types');
<?php

require get_theme_file_path('/inc/search-route.php');
require get_theme_file_path('/inc/like-rout.php');

function uni_custom_rest(){
  register_rest_field('post', 'authorName', array(
    'get_callback' => function() { return get_the_author(); }
  ));

  register_rest_field('notes', 'userNoteCout', array(
    'get_callback' => function() { return count_user_posts(get_current_user_id(), 'note'); }
  ));
}

add_action('rest_api_init','uni_custom_rest');

function pageBanner($args = NULL) {

  if (!isset($args['title'])) {
    $args['title'] = get_the_title();
  }

  if (!isset($args['subtitle'])) {
    $args['subtitle'] = get_field('page_banner_title');
  }

  if (!isset($args['photo'])) {
    if (get_field('page_banner_background_image') && !is_archive() && !is_home()) {
      $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
    } else {
      $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }
  }

  ?>
  <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
        <div class="page-banner__intro">
          <p><?php echo $args['subtitle']; ?></p>
        </div>
      </div>  
    </div>  
    
<?php }

function university_files() {
  wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

  wp_localize_script('main-university-js', 'uniData', array(
    'root_url' => get_site_url(),
    'nonce' => wp_create_nonce('wp_rest')
  ));

}

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
  add_theme_support('title-tag');
  register_nav_menu('headerMenu', 'Header Manu Location');
  register_nav_menu('footerMenuOne', 'Footer Manu Location One');
  register_nav_menu('footerMenuTwo', 'Footer Manu Location Two');
  add_theme_support('post-thumbnails');
  add_image_size('professorLandscape', 400, 260, true);
  add_image_size('professorPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
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


// redirect sub accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend() {
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 && $ourCurrentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    exit;
  }
}

add_action('wp_loaded', 'noSubAdminBar');

function noSubAdminBar() {
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 && $ourCurrentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
}

//  Customize login Screen

add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
  return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}


add_filter('login_headertitle', 'ourHeaderTitle');

function ourHeaderTitle(){
  return get_bloginfo('name');
}


// force note posts to be private

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate($data, $postarr) {

  if ($data['post_type'] == 'notes') {
    if (count_user_posts(get_current_user_id(), 'notes') > 4 && !$postarr['ID']) {
      die('You have reach your note limit');
    } 

    $data['post_content'] = sanitize_textarea_field($data['post_content']);
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }

  if ($data['post_type'] == 'notes' && $data['post_status'] != 'trash') {
    $data['post_status'] = 'private'; 
  }

  return $data;
}


// function moved to new file called 'must use plugins'

// function uni_post_types() {
//   // Events post type
//   register_post_type('event', array(
//     'capability_type' => 'event',
//     'map_meta_cap' => true,
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

//     Like post type
    // register_post_type('like', array(
    //   'supports' => array('title'),
    //   'show_ui' => true,
    //   'public' => false,
    //   'labels' => array(
    //     'name' => 'Likes',
    //     'add_new_item' => 'Add New Like',
    //     'edit_item' => 'Edit Like',
    //     'all_items' => 'All Likes',
    //     'singular_name' => 'Like'
    //   ),
    //   'menu_icon' => 'dashicons-heart'
    // ));

//   // Program post type
//   register_post_type('program', array(
//     'supports' => array('title'),
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
      // Notes post type
      // register_post_type('notes', array(
      //   'supports' => array('title','editor'),
      //   'public' => false,
      //   'show_ui' => true,
      //   'show_in_rest' => true,
      //   'labels' => array(
      //     'name' => 'Notes',
      //     'add_new_item' => 'Add New Note',
      //     'edit_item' => 'Edit Note',
      //     'all_items' => 'All Notes',
      //     'singular_name' => 'Note'
      //   ),
      //   'menu_icon' => 'dashicons-welcome-write-blog'
      // ));
// };

// add_action('init', 'uni_post_types');
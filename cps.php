<?php

/**
 * @link              http://ivcor.com
 * @since             1.0.0
 * @package           cps
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Property Search
 * Description:       Custom Property Search plugin.
 * Version:           1.0.0
 * Author:            IVCOR Company
 * Author URI:        http://ivcor.com
 * Text Domain:       CPS
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cps-activator.php
 */
function activate_cps() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cps-activator.php';
	cps_Activator::activate();
}


add_action( 'template_redirect', 'wpa5413_init' );
function wpa5413_init()
{
  $slug = basename(get_permalink());
	$taxonomyNames = cps_Admin::CPS_PROPERTIES_NAME;
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$parts = parse_url($url);
	$true_url = get_site_url().'/search';
	if (strpos($url, 'cps_operation') !== false) {
    if ($_GET['cps_type'] == '') { $_GET['cps_type'] = 'property'; }
    if ($_GET['cps_minroom'] == '') { $_GET['cps_minroom'] = 'with-any-badrooms'; }
		foreach (cps_Admin::get_permalink_order() as $key =>  $taxonomy) {
      if ($taxonomy == 'cps_location') { $taxonomy = 'cps_locations'; }
			if (isset($_GET[$taxonomy])) {
        if ($_GET['cps_locations'] == '') { $_GET['cps_locations'] = 'uae'; }
					$name = cps_Admin::get_name($taxonomy, $_GET[$taxonomy]);
					$true_url = $true_url.'/'.$name;
				}
    }
    $link = '';
    $id = 0;
		foreach($_GET as $key => $value)
		{
        if($key !== 'get_search_fields_nonce' && $key !== 'cps_type' && $key !== 'cps_operation' 
        && $key !== 'cps_minroom' && $key !== 'cps_locations') {
					if ($value !== '') {
            if ($id == 0) {$link = '?'; $link = $link.''.$key.'='.$value; }
            else {
              $link = $link.'&'.$key.'='.$value;
            }
            $id += 1;
					}
				}
		}

		$true_url = stripslashes($true_url).''.$link;
		echo "<script>location.href = '$true_url';</script>";
	
	}

}

add_filter('init', 'add_page_rewrite_rules');
function add_page_rewrite_rules()
{
    global $wp_rewrite, $wp, $wpdb;


    $args = [
      'post_type'=>'property'
    ];
    
    // we get an array of posts objects
    $posts = get_posts($args);
    foreach ($posts as $post) {
      // /print_r($post);
      $link = $post->guid;
      $parts = explode("/",$link);
      array_shift($parts);array_shift($parts);array_shift($parts);
      $newurl = implode("/",$parts);
      $extracted = 'index.php'.$newurl;
      $name = $post->post_name;
      //add_rewrite_rule('^property/'.$name.'/?$' ,$extracted,'top');
      //echo $name;
    }
    // /print_r($posts);
        //this will hold the text field data
        $wp->add_query_var('cps_operation');
        $wp->add_query_var('cps_type2');
        $wp->add_query_var('cps_minroom');
        $wp->add_query_var('cps_furnishing');
        $wp->add_query_var('cps_price_min');
        $wp->add_query_var('cps_price_max');
        $wp->add_query_var('cps_amenitie');
        $wp->add_query_var('cps_minroom');
        $wp->add_query_var('cps_maxroom');
        $wp->add_query_var('cps_area_min');
        $wp->add_query_var('cps_area_max');
        $wp->add_query_var('cps_minbath_room');
        $wp->add_query_var('cps_maxbath_room');
        $wp->add_query_var('cps_keywords');
        $wp->add_query_var('cps_locations');

        add_rewrite_tag('%cps_operation%','([^/]*)');
        add_rewrite_tag('%cps_type2%','([^/]*)');
        add_rewrite_tag('%cps_minroom%','([^/]*)');
        add_rewrite_tag('%cps_locations%','([^/]*)');

      $link = 'index.php?post_type=property&';
      $regex = '^';

    foreach (cps_Admin::get_permalink_order() as $key =>  $taxonomy) {
      if ($taxonomy == 'cps_type') { $taxonomy = 'cps_type2'; }
      if ($taxonomy == 'cps_location') { $taxonomy = 'cps_locations'; }
      $id = $key + 1;
      $link = $link.$taxonomy.'=$matches['.$id.']&';
      $truelink = substr($link, 0, -1);
      $regex = $regex.'([^/]*)/';
      $trueregex = $regex.'?$';

      add_rewrite_rule($trueregex ,$truelink,'bottom');
    }

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cps_custom_title", OBJECT );
    $current_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $relink = 'index.php?post_type=property';
    foreach ($results as $res) {
      $site_url = get_site_url().$res->cps_url;
      $oper = '';
      if ($res->cps_operation == 0) {$oper = 'rent';}
      if ($res->cps_operation == 2) {$oper = 'rent';}
      if ($res->cps_operation == 3) {$oper = 'buy';}
      if ($res->cps_operation == 4) {$oper= 'rent-commercial';}
      if ($res->cps_operation == 5) {$oper = 'buy-commercial';}
        $link = $relink.del_empty('cps_operation', $oper).del_empty('cps_type2', check($res->cps_type)).del_empty('cps_locations', check($res->cps_location)).del_empty('cps_furnishing', check($res->cps_furnishings)).del_empty('cps_price_min', check($res->cps_minprice)).del_empty('cps_price_max', check($res->cps_maxprice)).del_empty('cps_amenitie', check($res->cps_amenities)).del_empty('cps_minroom', check($res->cps_minroom)).del_empty('cps_maxroom', check($res->cps_maxroom)).del_empty('cps_minbath_room', check($res->cps_minbath_room)).del_empty('cps_maxbath_room', check($res->cps_maxbath_room)).del_empty('cps_keywords', check($res->cps_keywords));
       
        add_rewrite_rule('^'.$res->cps_url.'/?$' ,$link,'top');

       
      }



    flush_rewrite_rules();
  }

function del_empty($operation, $data) {

  $need = '';

  if ($data !== '') {
    $need = '&'.$operation.'='.$data;
    return $need;
  }

}

add_action( 'wp_ajax_myaction', 'so_wp_ajax_function' );
add_action( 'wp_ajax_nopriv_myaction', 'so_wp_ajax_function' );
function so_wp_ajax_function(){

  global $wp_rewrite;
  $data = print_r(array_search($_POST['link'], $wp_rewrite->rules));
  

  //DO whatever you want with data posted
  //To send back a response you have to echo the result!
  echo $data;
  wp_die(); // ajax call must die to avoid trailing 0 in your response
}

// function wpse12535_redirect_sample() {

//     global $wpdb;
//   $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cps_custom_title", OBJECT );
//   $current_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
// 	foreach ($results as $res) {
// 		$site_url = get_site_url().$res->cps_url;

// 		if($current_url == $site_url)  {
// 			$link = get_site_url().'/?cps_operation='.check($res->cps_operation).'&get_search_fields_nonce=9bd4f16249&_wp_http_referer=%2F&cps_search=&cps_type='.check($res->cps_type).'&cps_contract=&cps_furnishings='.check($res->cps_furnishings).'&cps_price_min='.check($res->cps_minprice).'&cps_price_max='.check($res->cps_maxprice).'&cps_amenities%5B%5D='.check($res->cps_amenities).'&cps_minroom='.check($res->cps_minroom).'&cps_maxroom='.check($res->cps_maxroom).'&cps_area_min=0&cps_area_max=0&cps_minbath_room='.check($res->cps_minbath_room).'&cps_maxbath_room='.check($res->cps_maxbath_room).'&cps_keywords='.check($res->cps_keywords);
// 			echo "<script>location.href = '$link';</script>";
// 			//wp_redirect( );
// 		  exit;  
// 		}
// 	}
	

// }
// add_action( 'template_redirect', 'wpse12535_redirect_sample');

function check($data) {

	if ($data == 0) {
		$text = '';
	} else {
		$text = $data;
	}

	return $text;
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cps-deactivator.php
 */
function deactivate_cps() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cps-deactivator.php';
	cps_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cps' );
register_deactivation_hook( __FILE__, 'deactivate_cps' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cps.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cps() {

	$plugin = new cps();
	$plugin->run();

}



run_cps();


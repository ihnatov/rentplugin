<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    cps
 * @subpackage cps/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    cps
 * @subpackage cps/public
 * @author     Your Name <email@example.com>
 */
class cps_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cps    The ID of this plugin.
	 */
	private $cps;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $cps       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $cps, $version ) {

		$this->cps = $cps;
		$this->version = $version;
        $this->activate_search_box();

        add_filter( 'template_include',         array( $this, 'cps_template_include' ), 10 );
        //add_action( 'init',                     array( $this, 'get_cps_page_template' ), 10 );

	}

    /**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in cps_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The cps_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_style( 'select-css', plugin_dir_url(__FILE__) . 'css/select2.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->cps, plugin_dir_url( __FILE__ ) . 'css/cps-public.css', array(), $this->version, 'all' );


    }

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in cps_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The cps_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->cps, plugin_dir_url( __FILE__ ) . 'js/cps-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'select-js', plugin_dir_url(__FILE__) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

	}

	public function get_cps_page_template () {

	    if( isset( $_GET['cps_operation'] ) && $operation = $_GET['cps_operation'] ){

            if ($operation == 'rent' || $operation == 'buy' ) {
                wp_safe_redirect( site_url('?post_type=property') );
            }

            if ($operation == 'rent-commercial' || $operation == 'buy-commercial' ) {
                wp_safe_redirect( site_url('/property-commercial') );
            }

            exit;
        }

    }
    public function cps_template_include( $template ) {

        $operation  = isset($_GET['cps_operation']) ? $_GET['cps_operation'] : false;

        if ( is_post_type_archive('property') || $operation == 'rent' || $operation == 'buy' ) {
            return plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/archive-property.php';
        }

        if ( is_post_type_archive('property-commercial') || $operation == 'rent-commercial' || $operation == 'buy-commercial' ) {
            return plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/archive-property.php';
        }

        global $post;
        if ($post) {
            if ( $post->post_type == 'property' ){
                return plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/single-property.php';
            }

            if ( $post->post_type == 'property-commercial' ){
                return plugin_dir_path( dirname( __FILE__ ) ) . 'public/templates/single-property.php';
            }
        }

        return $template;

    }
	public function activate_search_box () {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-search-box.php';
        new SearchBox;
    }
}

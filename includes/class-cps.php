<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    cps
 * @subpackage cps/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    cps
 * @subpackage cps/includes
 * @author     Your Name <email@example.com>
 */

class cps {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      cps_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $cps    The string used to uniquely identify this plugin.
	 */
	protected $cps;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->cps = 'cps';

        add_action( 'init', array( $this, 'create_taxonomy' ) );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

    }

    public function enqueue_styles() {

        wp_enqueue_style( $this->cps, plugin_dir_url( __FILE__ ) . 'css/cps-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'fontAwesome', plugin_dir_url( __FILE__ ) . 'css/fontawesome/css/all.min.css', array(), '5.11.2', 'all' );

    }
    /**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - cps_Loader. Orchestrates the hooks of the plugin.
	 * - cps_i18n. Defines internationalization functionality.
	 * - cps_Admin. Defines all hooks for the admin area.
	 * - cps_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cps-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cps-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cps-admin.php';

		/**
		 * The class add meta box to Property post type.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-property-meta-box.php';

		/**
		 * The add gallery meta box to Property post type.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/gallery-meta-box.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cps-public.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cps-property.php';

		$this->loader = new cps_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the cps_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new cps_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new cps_Admin( $this->get_cps(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new cps_Public( $this->get_cps(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_cps() {
		return $this->cps;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    cps_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    public function create_taxonomy(){

        register_post_type('property', array(
            'labels'             => array(
                'name'               => 'Properties',
                'singular_name'      => 'Property',
                'add_new'            => 'Add New',
                'add_new_item'       => 'Add New Property',
                'edit_item'          => 'Edit Property',
                'new_item'           => 'New Property',
                'view_item'          => 'View Property',
                'search_items'       => 'Search Property',
                'not_found'          => 'Property not found',
                'not_found_in_trash' => 'Property not found in trash',
                'parent_item_colon'  => '',
                'menu_name'          => 'Property'
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title','editor','thumbnail')
        ) );
        register_post_type('property-commercial', array(
            'labels'             => array(
                'name'               => 'Commercial property',
                'singular_name'      => 'Commercial property',
                'add_new'            => 'Add New',
                'add_new_item'       => 'Add New Commercial property',
                'edit_item'          => 'Edit Commercial property',
                'new_item'           => 'New Commercial property',
                'view_item'          => 'View Commercial property',
                'search_items'       => 'Search Commercial property',
                'not_found'          => 'Commercial property not found',
                'not_found_in_trash' => 'Commercial property not found in trash',
                'parent_item_colon'  => '',
                'menu_name'          => 'Commercial property'
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title','editor','thumbnail')
        ) );

        register_taxonomy( 'cps_operation', [ 'property', 'property-commercial' ], [
            'labels'                => [
                'name'              => 'Property Operations',
                'singular_name'     => 'Operation',
                'search_items'      => 'Search Operations',
                'all_items'         => 'All Operations',
                'view_item '        => 'View Operation',
                'parent_item'       => 'Parent Operation',
                'parent_item_colon' => 'Parent Operation:',
                'edit_item'         => 'Edit Operation',
                'update_item'       => 'Update Operation',
                'add_new_item'      => 'Add New Operation',
                'new_item_name'     => 'New Operation Name',
                'menu_name'         => 'Operation',
            ],
            'description'           => '',
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
            'hierarchical'          => true,
        ] );
        register_taxonomy( 'cps_type', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Types',
                'singular_name'     => 'Type',
                'search_items'      => 'Search Types',
                'all_items'         => 'All Types',
                'view_item '        => 'View Type',
                'parent_item'       => 'Parent Type',
                'parent_item_colon' => 'Parent Type:',
                'edit_item'         => 'Edit Type',
                'update_item'       => 'Update Type',
                'add_new_item'      => 'Add New Type',
                'new_item_name'     => 'New Type Name',
                'menu_name'         => 'Type',
            ],
            'public'                => true,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
            'hierarchical'          => true,
        ] );
        register_taxonomy( 'cps_type_commercial', [ 'property-commercial' ], [
            'labels'                => [
                'name'              => 'Commercial Property Types',
                'singular_name'     => 'Commercial Property',
                'search_items'      => 'Search Commercial Property',
                'all_items'         => 'All Commercial Property',
                'view_item '        => 'View Commercial Property',
                'parent_item'       => 'Parent Commercial Property',
                'parent_item_colon' => 'Parent Commercial Property:',
                'edit_item'         => 'Edit Commercial Property',
                'update_item'       => 'Update Commercial Property',
                'add_new_item'      => 'Add New Commercial Property',
                'new_item_name'     => 'New Commercial Property Name',
                'menu_name'         => 'Commercial Property',
            ],
            'public'                => true,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
            'hierarchical'          => true,
        ] );
        register_taxonomy( 'cps_location', [ 'property', 'property-commercial' ], [
            'labels'                => [
                'name'              => 'Property Locations',
                'singular_name'     => 'Location',
                'search_items'      => 'Search Locations',
                'all_items'         => 'All Locations',
                'view_item '        => 'View Location',
                'parent_item'       => 'Parent Location',
                'parent_item_colon' => 'Parent Location:',
                'edit_item'         => 'Edit Location',
                'update_item'       => 'Update Location',
                'add_new_item'      => 'Add New Location',
                'new_item_name'     => 'New Location Name',
                'menu_name'         => 'Location',
            ],
            'public'                => true,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
            'hierarchical'          => true,
        ] );
        register_taxonomy( 'cps_amenities', [ 'property', 'property-commercial' ], [
            'labels'                => [
                'name'              => 'Property Amenities',
                'singular_name'     => 'Amenities',
                'search_items'      => 'Search Amenities',
                'all_items'         => 'All Amenities',
                'view_item '        => 'View Amenities',
                'parent_item'       => 'Parent Amenities',
                'parent_item_colon' => 'Parent Amenities:',
                'edit_item'         => 'Edit Amenities',
                'update_item'       => 'Update Amenities',
                'add_new_item'      => 'Add New Amenities',
                'new_item_name'     => 'New Amenities Name',
                'menu_name'         => 'Amenities',
            ],
            'public'                => true,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_furnishings', [ 'property', 'property-commercial' ], [
            'labels'                => [
                'name'              => 'Property Furnishings',
                'singular_name'     => 'Furnishings',
                'search_items'      => 'Search Furnishings',
                'all_items'         => 'All Furnishings',
                'view_item '        => 'View Furnishings',
                'parent_item'       => 'Parent Furnishings',
                'parent_item_colon' => 'Parent Furnishings:',
                'edit_item'         => 'Edit Furnishings',
                'update_item'       => 'Update Furnishings',
                'add_new_item'      => 'Add New Furnishings',
                'new_item_name'     => 'New Furnishings Name',
                'menu_name'         => 'Furnishings',
            ],
            'public'                => true,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_location2', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Location',
                'singular_name'     => 'Locations',
                'search_items'      => 'Search Locations',
                'all_items'         => 'All Locations',
                'view_item '        => 'View Locations',
                'parent_item'       => 'Parent Locations',
                'parent_item_colon' => 'Parent Locations:',
                'edit_item'         => 'Edit Locations',
                'update_item'       => 'Update Locations',
                'add_new_item'      => 'Add New Locations',
                'new_item_name'     => 'New Locations Name',
                'menu_name'         => 'Locations',
            ],
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );

        register_taxonomy( 'cps_location3', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Location',
                'singular_name'     => 'Locations',
                'search_items'      => 'Search Locations',
                'all_items'         => 'All Locations',
                'view_item '        => 'View Locations',
                'parent_item'       => 'Parent Locations',
                'parent_item_colon' => 'Parent Locations:',
                'edit_item'         => 'Edit Locations',
                'update_item'       => 'Update Locations',
                'add_new_item'      => 'Add New Locations',
                'new_item_name'     => 'New Locations Name',
                'menu_name'         => 'Locations',
            ],
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_minroom', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Rooms',
                'singular_name'     => 'Room',
                'search_items'      => 'Search Rooms',
                'all_items'         => 'All Rooms',
                'view_item '        => 'View Room',
                'parent_item'       => 'Parent Room',
                'parent_item_colon' => 'Parent Room:',
                'edit_item'         => 'Edit Room',
                'update_item'       => 'Update Room',
                'add_new_item'      => 'Add New Room',
                'new_item_name'     => 'New Room Name',
                'menu_name'         => 'Room',
            ],
            'description'           => '',
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_maxroom', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Rooms',
                'singular_name'     => 'Room',
                'search_items'      => 'Search Rooms',
                'all_items'         => 'All Rooms',
                'view_item '        => 'View Room',
                'parent_item'       => 'Parent Room',
                'parent_item_colon' => 'Parent Room:',
                'edit_item'         => 'Edit Room',
                'update_item'       => 'Update Room',
                'add_new_item'      => 'Add New Room',
                'new_item_name'     => 'New Room Name',
                'menu_name'         => 'Room',
            ],
            'description'           => '',
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_minbath_room', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Bath rooms',
                'singular_name'     => 'Bath room',
                'search_items'      => 'Search Bath rooms',
                'all_items'         => 'All Bath rooms',
                'view_item '        => 'View Bath room',
                'parent_item'       => 'Parent Bath room',
                'parent_item_colon' => 'Parent Bath room:',
                'edit_item'         => 'Edit Bath room',
                'update_item'       => 'Update Bath room',
                'add_new_item'      => 'Add New Bath room',
                'new_item_name'     => 'New Bath room Name',
                'menu_name'         => 'Bath room',
            ],
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_maxbath_room', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Bath rooms',
                'singular_name'     => 'Bath room',
                'search_items'      => 'Search Bath rooms',
                'all_items'         => 'All Bath rooms',
                'view_item '        => 'View Bath room',
                'parent_item'       => 'Parent Bath room',
                'parent_item_colon' => 'Parent Bath room:',
                'edit_item'         => 'Edit Bath room',
                'update_item'       => 'Update Bath room',
                'add_new_item'      => 'Add New Bath room',
                'new_item_name'     => 'New Bath room Name',
                'menu_name'         => 'Bath room',
            ],
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_minprice', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Price',
                'singular_name'     => 'Price',
                'search_items'      => 'Search Price',
                'all_items'         => 'All Price',
                'view_item '        => 'View Price',
                'parent_item'       => 'Parent Price',
                'parent_item_colon' => 'Parent Price:',
                'edit_item'         => 'Edit Price',
                'update_item'       => 'Update Price',
                'add_new_item'      => 'Add New Price',
                'new_item_name'     => 'New Price',
                'menu_name'         => 'Price',
            ],
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_maxprice', [ 'property' ], [
            'labels'                => [
                'name'              => 'Property Price',
                'singular_name'     => 'Price',
                'search_items'      => 'Search Price',
                'all_items'         => 'All Price',
                'view_item '        => 'View Price',
                'parent_item'       => 'Parent Price',
                'parent_item_colon' => 'Parent Price:',
                'edit_item'         => 'Edit Price',
                'update_item'       => 'Update Price',
                'add_new_item'      => 'Add New Price',
                'new_item_name'     => 'New Price',
                'menu_name'         => 'Price',
            ],
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
    }

}


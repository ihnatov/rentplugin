<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    cps
 * @subpackage cps/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    cps
 * @subpackage cps/includes
 * @author     Your Name <email@example.com>
 */
class cps_Activator {

	public static function activate() {
        self::create_cps_table();
        self::create_cps_data();
        self::add_image_size();
	}

    public static function add_image_size() {
        add_image_size( 'cps-260-185', 260, 185, true );

    }
    public static function create_cps_table() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = $wpdb->get_blog_prefix() . 'cps_custom_title';

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} ( id int(7) unsigned NOT NULL auto_increment, cps_operation int(7) unsigned NOT NULL, cps_type int(7) unsigned NOT NULL, cps_location int(7) unsigned NOT NULL, cps_minroom int(7) unsigned NOT NULL, cps_maxroom int(7) unsigned NOT NULL, cps_minbath_room int(7) unsigned NOT NULL, cps_maxbath_room int(7) unsigned NOT NULL, cps_minprice int(7) unsigned NOT NULL, cps_maxprice int(7) unsigned NOT NULL cps_amenities int(7) unsigned NOT NULL, cps_furnishings int(7) unsigned NOT NULL, cps_location2 int(7) unsigned NOT NULL, cps_location3 int(7) unsigned NOT NULL, cps_keywords varchar(255) NOT NULL default '', cps_keywords2 varchar(255) NOT NULL default '', cps_title varchar(255) NOT NULL default '', cps_url longtext , cps_meta longtext ,PRIMARY KEY (id), UNIQUE KEY (cps_operation, cps_type, cps_location, cps_minroom, cps_maxroom, cps_minbath_room, cps_maxbathroom, cps_amenities, cps_furnishings, cps_location2, cps_location3, cps_keywords, cps_keywords2) );";

        dbDelta($sql);
    }

    public static function create_cps_data() {

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
            'description'           => '',
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
            'hierarchical'          => true,
        ] );
        register_taxonomy( 'cps_type_commercial', [ 'property-commercial' ], [
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
            'description'           => '',
            'public'                => false,
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
        register_taxonomy( 'cps_amenities', [ 'property' ], [
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
            'public'                => false,
            'show_in_nav_menus'     => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'meta_box_cb'           => false,
        ] );
        register_taxonomy( 'cps_furnishings', [ 'property' ], [
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
            'public'                => false,
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
        self::create_cps_data_operation();
        self::create_cps_data_type();
        self::create_cps_data_type_commercial();
        self::create_cps_data_location();
        self::create_cps_data_amenities();
        self::create_cps_data_furnishings();
        self::create_cps_data_location2();
        self::create_cps_data_location3();
        self::create_cps_data_minroom();
        self::create_cps_data_maxroom();
        self::create_cps_mindata_bath_room();
        self::create_cps_maxdata_bath_room();
        self::create_cps_data_minprice();
        self::create_cps_data_maxprice();
    }
    public static function create_cps_data_operation() {
        $operations = array(
            'rent'              => 'Rent',
            'buy'               => 'Buy',
            'rent-commercial'   => 'Rent Commercial',
            'buy-commercial'    => 'Buy Commercial',
        );
        foreach ($operations as $slug => $operation) {
            wp_insert_term( $operation, 'cps_operation', array(
                'parent'      => 0,
                'slug'        => sanitize_title($slug),
            ) );
        }
    }
    public static function create_cps_data_type() {
        $properties = array(
            'Apartment',
            'Villa',
            'Penthouse',
            'Townhouse',
            'Duplex',
            'Compound',
            'Whole Building',
            'Full Floor',
            'Bulk Rent Unit',
            'Bungalow',
            'Hotel & Hotel Apartment',
            'Land',
        );
        foreach ($properties as $child_types) {
            wp_insert_term( $child_types, 'cps_type', array(
                'parent'      => 0,
                'slug'        => sanitize_title($child_types),
            ) );
        }
    }
    public static function create_cps_data_type_commercial() {
        $properties = array(
            'Office Space',
            'Retail',
            'Warehouse',
            'Shop',
            'Show Room',
            'Full Floor',
            'Whole Building',
            'Bulk Rent Unit',
            'Land',
            'Factory',
            'Labor Camp',
            'Staff Accommodation',
            'Business Centre',
            'Co-working space',
            'Farm',
        );
        foreach ($properties as $child_types) {
            wp_insert_term( $child_types, 'cps_type_commercial', array(
                'parent'      => 0,
                'slug'        => sanitize_title($child_types),
            ) );
        }
    }
    public static function create_cps_data_location() {
        $properties = array(
            'City 1' => array(
                'Neighborhood 1-1' => array(
                    'Community 1-1-1' => array(
                        'Tower 1-1-1-1',
                        'Tower 1-1-1-2',
                        'Tower 1-1-1-3',
                    ),
                    'Community 1-1-2' => array(
                        'Tower 1-2-2-1',
                        'Tower 1-2-2-2',
                        'Tower 1-2-2-3',
                    ),
                ),
                'Neighborhood 1-2' => array(
                    'Community 1-2-1' => array(
                        'Tower 1-2-1-1',
                        'Tower 1-2-1-2',
                        'Tower 1-2-1-3',
                    ),
                ),
            ),
            'City 2' => array(
                'Neighborhood 2-1' => array(
                    'Community 2-1-1' => array(
                        'Tower 2-1-1-1',
                        'Tower 2-1-1-2',
                        'Tower 2-1-1-3',
                    ),
                    'Community 2-1-2' => array(
                        'Tower 2-2-2-1',
                        'Tower 2-2-2-2',
                        'Tower 2-2-2-3',
                    ),
                ),
                'Neighborhood 2-2' => array(
                    'Community 2-2-1' => array(
                        'Tower 2-2-1-1',
                        'Tower 2-2-1-2',
                        'Tower 2-2-1-3',
                    ),
                ),
            ),
        );
        foreach ($properties as $parent_type=>$child_types) {
            $parent_type_ids = wp_insert_term( $parent_type, 'cps_location', array(
                'parent'      => 0,
                'slug'        => sanitize_title($parent_type),
            ) );

            if (is_object($parent_type_ids))
                continue;

            foreach ($child_types as $parent_type2=>$child_type2){
                $parent_type_ids1 = wp_insert_term( $parent_type2, 'cps_location', array(
                    'parent'      => $parent_type_ids['term_taxonomy_id'],
                    'slug'        => sanitize_title($parent_type2),
                ) );

                if (is_object($parent_type_ids1))
                    continue;

                foreach ($child_type2 as $parent_type3=>$child_type3){
                    $parent_type_ids2 = wp_insert_term( $parent_type3, 'cps_location', array(
                        'parent'      => $parent_type_ids1['term_taxonomy_id'],
                        'slug'        => sanitize_title($parent_type3),
                    ) );

                    if (is_object($parent_type_ids2))
                        continue;

                    foreach ($child_type3 as $child_type4){
                        wp_insert_term( $child_type4, 'cps_location', array(
                            'parent'      => $parent_type_ids2['term_taxonomy_id'],
                            'slug'        => sanitize_title($child_type4),
                        ) );
                    }
                }
            }
        }
    }
    public static function create_cps_data_minroom() {
        $properties = array(
            'Studio',
            '1 Bedroom',
            '2 Bedrooms',
            '3 Bedrooms',
            '4 Bedrooms',
            '5 Bedrooms',
            '6 Bedrooms',
            '7 Bedrooms',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_minroom', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
    public static function create_cps_data_maxroom() {
        $properties = array(
            'Studio',
            '1 Bedroom',
            '2 Bedrooms',
            '3 Bedrooms',
            '4 Bedrooms',
            '5 Bedrooms',
            '6 Bedrooms',
            '7 Bedrooms',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_maxroom', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
    public static function create_cps_data_amenities() {
        $properties = array(
            'Central A/C',
            'Maids Room',
            'Balcony',
            'Shared Pool',
            'Shared Spa',
            'Shared Gym',
            'Concierge Service',
            'Covered Parking',
            'View of Water',
            'View of Landmark',
            'Pets Allowed',
            'Study',
            'Private Garden',
            'Private Pool',
            'Private Gym',
            'Private Jacuzzi',
            'Built in Wardrobes',
            'Walk-in Closet',
            'Built in Kitchen Appliances',
            'Maid Service',
            'Children\'s Play Area',
            'Children\'s Pool',
            'Barbecue Area',
        );

        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_amenities', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
    public static function create_cps_data_furnishings() {
        $properties = array(
            'Furnished',
            'Unfurnished',
            'Party furnished',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_furnishings', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }

    public static function create_cps_data_location2() {
        $properties = array(
            '1',
            '2',
            '3',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_location2', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }

    public static function create_cps_data_location3() {
        $properties = array(
            '1',
            '2',
            '3',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_location3', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }

    public static function create_cps_mindata_bath_room() {
        $properties  = array(
           'None',
           '1 Bathroom',
           '2 Bathrooms',
           '3 Bathrooms',
           '4 Bathrooms',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_minbath_room', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
    public static function create_cps_maxdata_bath_room() {
        $properties  = array(
           'None',
           '1 Bathroom',
           '2 Bathrooms',
           '3 Bathrooms',
           '4 Bathrooms',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_maxbath_room', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
    public static function create_cps_data_minprice() {
        $properties  = array(
           'None',
           '1',
           '2',
           '3',
           '4',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_minprice', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
    public static function create_cps_data_maxprice() {
        $properties  = array(
           'None',
           '1',
           '2',
           '3',
           '4',
        );
        foreach ($properties as $property) {
            wp_insert_term( $property, 'cps_maxprice', array(
                'slug'        => sanitize_title($property),
            ) );
        }
    }
}

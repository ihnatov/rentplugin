<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    cps
 * @subpackage cps/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    cps
 * @subpackage cps/admin
 * @author     Your Name <email@example.com>
 */
class cps_Admin {
    const CPS_TABS = array(
        'cps_type'              => 'Type',
        'cps_type_commercial'   => 'Type Commercial',
        'cps_location'          => 'Location',
        'cps_room'              => 'Room',
        'cps_bath_room'         => 'Bath Room',
        'cps_amenities'         => 'Amenities',
        'cps_furnishings'       => 'Furnishings',
    );
    const CPS_PROPERTIES_NAME = array(
        'cps_operation'         => 'Operation',
        'cps_operation2'         => 'Operation2',
        'cps_type'              => 'Type',
        'cps_location'          => 'Location',
        'cps_room'              => 'Room',
        'cps_bath_room'         => 'Bath Room',
        'cps_amenities'         => 'Amenities',
        'cps_furnishings'       => 'Furnishings',
        'cps_keywords'          => 'Keywords',
    );
    const CPS_PROPERTIES_NAMES = array(
        'cps_operation'         => 'Operations',
        'cps_operation2'         => 'Operations2',
        'cps_type'              => 'Types',
        'cps_location'          => 'Locations',
        'cps_room'              => 'Rooms',
        'cps_bath_room'         => 'Bath Rooms',
        'cps_amenities'         => 'Amenities',
        'cps_furnishings'       => 'Furnishings',
        'cps_keywords'          => 'Keywords',
    );

    const OPERATION_TYPE = array(
        'rent'            => 'property',
        'buy'             => 'property',
        'rent-commercial' => 'property-commercial',
        'buy-commercial'  => 'property-commercial',
    );

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cps    The ID of this plugin.
	 */
	private $cps;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $config_permalink_setup    The array of taxonomy row.
	 */

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
	 * @param      string    $cps       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $cps, $version ) {

		$this->cps = $cps;
		$this->version = $version;

        add_action( 'admin_menu', array( $this, 'cps_Menu' ), 100 );
        add_action( 'admin_menu', array( $this, 'customize_tags_edit' ), 100 );
        add_action( 'init', 'register_settings' );

        add_action('wp_ajax_get_cps_fields_for_operation'   , array( $this, 'get_cps_fields_for_operation' ));
        add_action('wp_ajax_get_cps_fields_for_type'        , array( $this, 'get_cps_fields_for_type' ));
        add_action('wp_ajax_get_cps_operations'  , array( $this, 'get_cps_operations' ));
        add_action('wp_ajax_get_cps_locations'   , array( $this, 'get_cps_locations' ));

    }

	/**
	 * Get permalinks order
	 *
	 * @since    1.0.0
	 */
	public static function get_permalink_order() {

        return get_option('cps_setup_permalink', array_keys(self::CPS_PROPERTIES_NAME));

    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->cps, plugin_dir_url( __FILE__ ) . 'css/cps-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fontAwesome', plugin_dir_url( __FILE__ ) . 'css/fontawesome/css/all.min.css', array(), '5.11.2', 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        wp_enqueue_script( 'sortable', plugin_dir_url( __FILE__ ) . 'js/sortable/Sortable.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->cps, plugin_dir_url( __FILE__ ) . 'js/cps-admin.js', array( 'jquery' ), $this->version, true );

	}

    public function cps_Menu()
    {
        $menu_title = '';
        $taxname = '';

        if (isset($_GET['taxonomy'])) {
            if ( in_array( $_GET['taxonomy'], array_keys(self::CPS_TABS) ) ) {
                $taxname = $_GET['taxonomy'];
                $menu_title = 'Property Search';
            }
        }
        $taxonomies = isset($_GET['taxonomy']) && $_GET['taxonomy'] === $taxname;

        if( $taxonomies ) add_filter( 'parent_file', '__return_false' );

        add_menu_page( 'Welcome to Property Search', 'Property Search', 'manage_options', 'cps_Menu', 'cps_Page', 'dashicons-admin-home' );
        add_submenu_page( 'cps_Menu', 'General',      'General',       'manage_options', "cps_Menu", 'cps_Page');
        foreach (self::CPS_TABS as $name=>$label){
            add_submenu_page( 'cps_Menu', $label, $label, 'manage_options', "edit-tags.php?taxonomy=" . $name, null);
        }
        add_submenu_page( 'cps_Menu', 'Custom titles','Custom titles', 'manage_options', "cps_Menu_Custom_Title", 'cps_Page_Custom_Title');

        $menu_item = & $GLOBALS['menu'][ key(wp_list_filter( $GLOBALS['menu'], [$menu_title] )) ];
        foreach( $menu_item as & $val ) {
            if( false !== strpos($val, 'menu-top') )
                $val = 'menu-top wp-has-current-submenu'. ( $taxonomies ? ' current activated-submenu' : '' );
        }
    }

    public function customize_tags_edit() {
	    foreach (self::CPS_TABS as $taxonomy=>$label)
        add_action( $taxonomy.'_pre_add_form', function() {
            cps_Admin::cps_tabs(get_admin_page_title());
        });
    }
    public static function cps_tabs()
    {
        ?>
        <div id="cps_header-wrapper">
            <h1 class="cps_header">
                <span style="padding-right: 5px; color: #cf2a28;">
                  <i class="fas fa-building fa-2x"></i>
                </span>
                Welcome to Custom Property Search
            </h1>
            <div class="nav-tabs">
            <?php
            echo self::new_nav_tab($_GET, 'cps_Menu', 'General');
            foreach (self::CPS_TABS as $taxonomy => $label) {
                $active = isset($_GET['taxonomy']) && $_GET['taxonomy'] == $taxonomy ? ' active' : '';
                echo '<a class="nav-tab' . $active .'" href="edit-tags.php?taxonomy=' . $taxonomy .'">' . $label . '</a>';
            }
            echo self::new_nav_tab($_GET, 'cps_Menu_Custom_Title', 'Custom titles');
            ?>
            </div>
        </div>
        <script type="text/javascript">
            let after = document.getElementById('screen-meta-links');
            let html = document.getElementById('cps_header-wrapper');
            if (after)
                after.after(html);
        </script>
        <?php
    }

    public static function new_nav_tab($pages, $page_slug, $page_name) {
        $active = isset($pages['page']) && $pages['page'] == $page_slug ? ' active' : '';
        return '<a class="nav-tab' . $active . '" href="admin.php?page=' . $page_slug . '">' . $page_name . '</a>';
    }

    //ajax
    public function get_cps_fields_for_type() {
        $cps_type = isset($_POST['cps_type']) ? (array)$_POST['cps_type'] : array('property', 'property-commercial');
        $cps_operation_val = isset($_POST['cps_operation_val']) ? $_POST['cps_operation_val'] : 0;
        $cps_fields['cps_operation'] = $this->get_cps_operations($cps_type, $cps_operation_val);

        $property_prop = in_array('property-commercial', $cps_type) ? false : true;

        $cps_fields['cps_amenities'] =  $property_prop;
        $cps_fields['cps_furnishings'] = $property_prop;
        $cps_fields['cps_room'] = $property_prop;
        $cps_fields['cps_bath_room'] = $property_prop;

        wp_send_json($cps_fields);
    }
    public function get_cps_fields_for_operation() {
        $cps_operation = isset($_POST['cps_operation']) ? $_POST['cps_operation'] : 0;
        $cps_type_val = isset($_POST['cps_type_val']) ? $_POST['cps_type_val'] : 0;
        $cps_type = $cps_operation ? (array)self::OPERATION_TYPE[$cps_operation] : array('property', 'property-commercial');

        $cps_fields['cps_type'] = $this->get_cps_types($cps_type, $cps_type_val);

        $property_prop = in_array('property-commercial', $cps_type) ? false : true;

        $cps_fields['cps_amenities'] =  $property_prop;
        $cps_fields['cps_furnishings'] = $property_prop;
        $cps_fields['cps_room'] = $property_prop;
        $cps_fields['cps_bath_room'] = $property_prop;

        wp_send_json($cps_fields);
    }
    public function get_cps_types ($cps_type, $cps_type_val) {

        $output  = '';

        $output .= '<select name="cps_type">';
        $output .= '<option value="0">Type not selected</option>';

        if (in_array('property', $cps_type)) {

            $terms = get_terms( array(
                'taxonomy'      => 'cps_type',
                'orderby'       => 'id',
                'order'         => 'ASC',
                'hide_empty'    => false,
            ) );

            foreach( $terms as $term ) {
                $parent_name = in_array('property-commercial', $cps_type) ? 'Residential - ' : '';
                $output .= '<option data-type="property" value="' . $term->term_id . '"' . selected($cps_type_val, $term->term_id, false) . ' >' . $parent_name . $term->name . '</option>';
            }

        }

        if (in_array('property-commercial', $cps_type)) {

            $terms = get_terms( array(
                'taxonomy'      => array( 'cps_type_commercial' ),
                'orderby'       => 'id',
                'order'         => 'ASC',
                'hide_empty'    => false,
            ) );

            foreach( $terms as $term ) {
                $parent_name = in_array('property', $cps_type) ? 'Commercial - ' : '';
                $output .= '<option data-type="property-commercial" value="' . $term->term_id . '"' . selected($cps_type_val, $term->term_id, false) . ' >' . $parent_name . $term->name . '</option>';
            }

        }
        $output .= '</select>';

        return $output;
    }
    public function get_cps_operations ($cps_type, $cps_operation_val) {

        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_operation' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
        ) );
        $output  = '<select name="cps_operation">';
        $output .= '<option value="0">Operation not selected</option>';

        foreach( $terms as $term ) {
            if ($cps_type && !in_array(self::OPERATION_TYPE[$term->slug], $cps_type) )  continue;
            $output .= '<option data-operation="' . $term->slug . '" value="' . $term->term_id . '"' . selected($cps_operation_val, $term->term_id, false) . ' >' . $term->name . '</option>';
        }

        $output .= '</select>';

        return $output;
    }

    public function get_cps_operations2 ($cps_type, $cps_operation_val) {


        $output  = '<select name="cps_operation">';
        $output .= '<option value="0">Operation not selected</option>';


        $output .= '</select>';

        return $output;
    }

    public static function get_cps_locations () {
	    $children = isset($_POST['cps_location']) ? absint( $_POST['cps_location']) : 0;
        $parents = array_reverse(get_ancestors($children, 'cps_location', 'taxonomy'));
        $parents[] = $children;
        $selects = self::get_cps_location_level(0, $parents, $output, 0);
        //print_r($selects);
        wp_send_json($selects);
        wp_die();

    }
    public static function get_cps_location_level($level = 0, $parents, &$output, $parent) {
        $level_terms = array();

        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_location' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
            'parent'        => $parent,
        ) );
        switch ($level) {
            case 0:
                $output[$level][0] = '<option value="">City not selected</option>';
                break;
            case 1:
                $output[$level][0] = '<option value="">Neighborhood not selected</option>';
                break;
            case 2:
                $output[$level][0] = '<option value="">Community not selected</option>';
                break;
            case 3:
                $output[$level][0] = '<option value="">Tower not selected</option>';
                break;
        }

        if( $terms && ! is_wp_error($terms) ){
            foreach( $terms as $term ) {
                self::get_cps_location_level($level+1, $parents,$output, $term->term_id);
                $output[$level][] .= '<option value="' . $term->term_id . '"' . selected($term->term_id, @ $parents[$level], 0) .'>' . $term->name . '</option>';
            }
        }
        return $output;

    }

}
function register_settings() {
    register_setting( 'cps_setup',  'cps_setup_widget',   'cps_setup_widget' );
    register_setting( 'cps_setup',  'cps_setup_permalink','cps_setup_permalink' );
}
function cps_Page()
{
    $displayWidgetSidebar = get_option('cps_setup_widget');
    $taxonomyNames = cps_Admin::CPS_PROPERTIES_NAME;

    cps_Admin::cps_tabs(get_admin_page_title());

    ?>
    <div class="wrap warp-cps">
        <form action="options.php" method="POST">
            <table class="form-table" role="presentation">
                <tbody>
                <tr class="edit-shortcode-cps">
                    <th scope="row"><label for="shortcode-search-form">Search from shortcode</label></th>
                    <td><input name="shortcode-search-form" type="text" id="shortcode-search-form" value="[cps-search-form]" disabled></td>
                </tr>
                <tr class="edit-display-widget-sidebar">
                    <th scope="row"><label for="display-widget-sidebar">Display widget in the sidebar</label></th>
                    <td>
                        <input name="cps_setup_widget" type="checkbox" id="display-widget-sidebar"<?php echo $displayWidgetSidebar ? ' checked' : '' ?>>
                    </td>
                </tr>
                </tbody>
            </table>
            <fieldset>
                <h3>Custom Permalinks Setup</h3>
                <div class="permalinks-items">
                    <?php foreach (cps_Admin::get_permalink_order() as $taxonomy) : ?>
                        <div class="permalinks-item">
                            <input type="hidden" name="cps_setup_permalink[]" value="<?php echo $taxonomy ?>">
                            <i class="fas fa-arrows-alt"></i>
                            <?php echo $taxonomyNames[$taxonomy]; ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            </fieldset>
            <?php
            settings_fields('cps_setup');
            submit_button("Save Settings", "primary", "submit", true, ["id" => "updatePermalinks"]);
            ?>

        </form>
    </div>
    <?php
}
function cps_Page_Custom_Title()
{

    //print_r($_POST);

    if (isset($_POST['custom-title-add'])) {
        if( ! wp_verify_nonce( $_POST['_wpnonce'], 'custom-title-add' ) ) {
            print_r('Verify update error');
        } else {
            if ($_POST['cps_title']) {
                cps_add_title($_POST);
            } else {
               // print_r('res=');
            }
        }
    }
    if (isset($_POST['custom-title-update']) && $_POST['update']) {
        if( ! wp_verify_nonce( $_POST['_wpnonce'], 'custom-title-add' ) ) {
            print_r('Verify update error');
        } else {
            if ($_POST['cps_title']) {
                cps_update_title($_POST);
            }
        }
    }

    wp_enqueue_script( 'custom-title-js', plugins_url( '/js/admin-custom-title.js', __FILE__ ), array( 'jquery' ) );
    require_once __DIR__ . '/class-cps-title.php';

    cps_Admin::cps_tabs(get_admin_page_title());
    $taxonomyNames = cps_Admin::CPS_PROPERTIES_NAME;
    $customTitle = new Custom_Title_List_Table();
    ?>
    <div class="wrap warp-cps">
        <h2><?php echo get_admin_page_title() ?></h2>

        <form id="add-new-title-box" action="" method="POST">
            <h2>Add new title</h2>
            <div class="add-new-title-properties mb-10">
                <div class="title-properties">
                    <div class="text-start">If</div>
                    <div class="title-properties-if">
                        <?php
                        $customTitle->get_property_inputs();
                        ?>
                    </div>
                </div>
                <div class="title-properties">
                    <label class="text-start">Then</label>
                    <div class="input-wrapper">
                        <input type="text" name="cps_title">
                    </div>
                </div>
                <div class="title-properties">
                    <label class="text-start">URL</label>
                    <div class="permalinks-items">
                        <?php foreach (cps_Admin::get_permalink_order() as $taxonomy) : ?>
                            <div class="permalinks-item">
                                <input type="hidden" name="cps_url[]" value="<?php echo $taxonomy ?>">
                                <i class="fas fa-arrows-alt"></i>
                                <?php echo $taxonomyNames[$taxonomy]; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
            submit_button( 'Add New Title', 'btn-primary', 'custom-title-add', false, array( 'id' => "custom-title-add") );
            wp_nonce_field( 'custom-title-add' );

            ?>
            <span id="additional-button"></span>
        </form>

        <?php
        echo '<form id="custom-titles-table" action="" method="POST">';
        $customTitle->display();
        echo '</form>';
        ?>

    </div>

    <?php
}
function cps_add_title($data) {
    global $wpdb;

    $cps_location = prepare_cps_location ($_POST['cps_location']);

    $wpdb->replace(
        $wpdb->prefix . "cps_custom_title",
        array(
            'cps_title'        => $data['cps_title'],
            'cps_operation'    => isset($data['cps_operation'])     ? absint($data['cps_operation']) :   '',
            'cps_type'         => isset($data['cps_type'])          ? absint($data['cps_type']) :        '',
            'cps_location'     => isset($data['cps_location'])      ? absint($cps_location) :            '',
            'cps_room'         => isset($data['cps_room'])          ? absint($data['cps_room']) :        '',
            'cps_bath_room'    => isset($data['cps_bath_room'])     ? absint($data['cps_bath_room']) :   '',
            'cps_amenities'    => isset($data['cps_amenities'])     ? absint($data['cps_amenities']) :   '',
            'cps_furnishings'  => isset($data['cps_furnishings'])   ? absint($data['cps_furnishings']) : '',
            'cps_keywords'     => $data['cps_keywords'],
            'cps_url'          => $data['cps_url'],
        ),
        array( '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s')
    );
}
function cps_update_title($data) {
    global $wpdb;

    $cps_location = prepare_cps_location ($_POST['cps_location']);

    $wpdb->update(
        $wpdb->prefix . "cps_custom_title",
        array(
            'cps_title'        => $data['cps_title'],
            'cps_operation'    => isset($data['cps_operation'])     ? absint($data['cps_operation']) :   '',
            'cps_type'         => isset($data['cps_type'])          ? absint($data['cps_type']) :        '',
            'cps_location'     => isset($data['cps_location'])      ? absint($cps_location) :            '',
            'cps_room'         => isset($data['cps_room'])          ? absint($data['cps_room']) :        '',
            'cps_bath_room'    => isset($data['cps_bath_room'])     ? absint($data['cps_bath_room']) :   '',
            'cps_amenities'    => isset($data['cps_amenities'])     ? absint($data['cps_amenities']) :   '',
            'cps_furnishings'  => absint($data['cps_furnishings']),
            'cps_keywords'     => $data['cps_keywords'],
            'cps_url'          => $data['cps_url'],
        ),
        array( 'id' => $data['update'] ),
        array( '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s')
    );
}
function prepare_cps_location ($cps_location) {
    if (is_array($cps_location)) {
        foreach ( $cps_location as $key => $item) {
            $cps_location = array_pop($_POST['cps_location']);
            if ($cps_location) break;
        }
    } else {
        $cps_location = '';
    }
    return $cps_location;
}
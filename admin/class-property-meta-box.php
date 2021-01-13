<?php
function call_propertyMetaBox() {
    new propertyMetaBox();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_propertyMetaBox' );
    add_action( 'load-post-new.php', 'call_propertyMetaBox' );
}

add_action('wp_ajax_get_fields'  , 'propertyMetaBox::get_fields' );

class propertyMetaBox {

    public $location_level_0 = array();
    public $location_level_1 = array();
    public $location_level_2 = array();
    public $location_level_3 = array();

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function add_meta_box( $post_type ){
        $property_types = array('property', 'property-commercial');

        if (in_array($post_type, $property_types) )
            add_meta_box(
                'property_meta_box',
                'Property parameters',
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'advanced',
                'high'
                );
    }

    public function enqueue_scripts() {

        wp_enqueue_script( 'property-meta-box', plugin_dir_url( __FILE__ ) . 'js/admin-property-meta-box.js', array( 'jquery' ), '', true );

    }

    public function save( $post_id ) {
        if ( ! isset( $_POST['cps_property_meta_box_nonce'] ) )
            return $post_id;

        $nonce = $_POST['cps_property_meta_box_nonce'];

        if ( ! wp_verify_nonce( $nonce, 'cps_property_meta_box_action' ) )
            return $post_id;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;

        if ( !isset($_POST['cps_rent']) && !isset($_POST['cps_buy'] ) )
            return $post_id;

        $meta = array();

        if ($_POST['post_type'] === 'property') {
            $meta = array(
                'cps_ref',
                'cps_call',
                'cps_completion',
                'cps_type',
                'cps_price_buy',
                'cps_price_rent',
                'cps_contract',
                'cps_location',
                'cps_furnishings',
                'cps_area',
                'cps_minroom',
                'cps_maxroom',
                'cps_minbath_room',
                'cps_maxbath_room',
                'cps_amenities',
                'cps_minprice',
                'cps_maxprice',
                'cps_keywords',
                'cps_keywords2',
            );

            // cps_operation
            $cps_operations = array();
            if ( isset($_POST['cps_rent']) ) $cps_operations[] = 'rent';
            if ( isset($_POST['cps_buy']) )  $cps_operations[] = 'buy';

            if ($cps_operations)
                wp_set_object_terms( $post_id, $cps_operations, 'cps_operation' );

            // cps_type
            if ( isset($_POST['cps_type']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_type']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_type' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_type' );
            }

            // cps_furnishings
            if ( isset($_POST['cps_furnishings']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_furnishings']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_furnishings' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_furnishings' );
            }

            if ( isset($_POST['cps_location2']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_location2']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_location2' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_location2' );
            }

            if ( isset($_POST['cps_location3']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_location3']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_location3' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_location3' );
            }

            // cps_room
            if ( isset($_POST['cps_minroom']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_minroom']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_minroom' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_minroom' );
            }

            // cps_room
            if ( isset($_POST['cps_maxroom']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_maxroom']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_maxroom' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_maxroom' );
            }

            // cps_bath_room
            if ( isset($_POST['cps_minbath_room']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_minbath_room']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_minbath_room' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_minbath_room' );
            }
            if ( isset($_POST['cps_maxbath_room']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_maxbath_room']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_maxbath_room' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_maxbath_room' );
            }
            if ( isset($_POST['cps_minprice']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_minprice']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_minprice' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_minprice' );
            }
            if ( isset($_POST['cps_maxprice']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_maxprice']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_maxprice' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_maxprice' );
            }
        } elseif ($_POST['post_type'] === 'property-commercial') {
            $meta = array(
                'cps_ref',
                'cps_call',
                'cps_rent',
                'cps_buy',
                'cps_price_buy',
                'cps_price_rent',
                'cps_area',
                'cps_keywords',
                'cps_keywords2',
            );

            // cps_operation
            $cps_operations = array();
            if ( isset($_POST['cps_rent']) ) $cps_operations[] = 'rent-commercial';
            if ( isset($_POST['cps_buy']) )  $cps_operations[] = 'buy-commercial';

            if ($cps_operations)
                wp_set_object_terms( $post_id, $cps_operations, 'cps_operation' );

            // cps_type_commercial
            if ( isset($_POST['cps_type_commercial']) ) {
                $integerIDs = array_map('intval', (array)$_POST['cps_type_commercial']);
                wp_set_post_terms( $post_id, $integerIDs, 'cps_type_commercial' );
            } else {
                wp_set_post_terms( $post_id, NULL, 'cps_type_commercial' );
            }

        }

        // cps_post_meta
        foreach ( $meta as $item ) {
            if ( isset($_POST[$item]) ) {
                update_post_meta($post_id, $item, $_POST[$item]);
            } else {
                delete_post_meta($post_id, $item);
            }
        }

        // cps_amenities
        if ( isset($_POST['cps_amenities']) ) {
            $integerIDs = array_map('intval', (array)$_POST['cps_amenities']);
            wp_set_post_terms( $post_id, $integerIDs, 'cps_amenities' );
        } else {
            wp_set_post_terms( $post_id, NULL, 'cps_amenities' );
        }

        // cps_location
        if ( isset($_POST['cps_location']) ) {
            $cps_location = prepare_cps_location ($_POST['cps_location']);
            $integerIDs = array_map('intval', (array)$cps_location);
            wp_set_post_terms( $post_id, $integerIDs, 'cps_location' );
        } else {
            wp_set_post_terms( $post_id, NULL, 'cps_location' );
        }
    }

    public function render_meta_box_content( $post ) {
        wp_nonce_field( 'cps_property_meta_box_action', 'cps_property_meta_box_nonce' );
        ?>
<div class="cps-container">
    <?php echo get_field_cps_ref($post); ?>
    <?php echo get_field_cps_operation($post); ?>
    <div id="cps-column-type" class="cps-row">
        <div class="cps-columns-head cps-columns">Property Type</div>
        <div class="cps-row cps-row-prop">
            <div class="cps-columns">
                <?php echo get_cps_type($post); ?>
            </div>
        </div>
    </div>
    <div id="cps-column-common-field">
        <?php echo $this->get_fields_cps_common_field($post); ?>
    </div>
</div>
<?php
    }

    public function get_cps_location($post) {
        $cps_location = 0;
        $output = get_field_cps_header('cps_location', 'Property Location');

        $product_terms = wp_get_object_terms($post->ID, 'cps_location');
        if( $product_terms && ! is_wp_error($product_terms) ){
            $cps_location = $product_terms[0]->term_id;
        }

        $cps_locations = self::get_cps_locations($cps_location);
        foreach ($cps_locations as $cps_location) {
            $output .= '<div class="cps-columns">';
            $output .= '<select name="cps_location[]">';
            foreach ($cps_location as $option) {
                $output .= $option;
            }
            $output .= '</select>';
            $output .= '</div>';

        }
        $output .= get_field_cps_footer();

        return $output;

    }

    public static function get_cps_locations ($cps_location) {
        $cps_locations = array_reverse(get_ancestors($cps_location, 'cps_location', 'taxonomy'));
        $cps_locations[] = $cps_location;
        $selects = cps_Admin::get_cps_location_level(0, $cps_locations, $output, 0);

        return $selects;
    }
    function get_fields_cps_commercial($post){
        $output = '';

        $output .= get_field_cps_price($post, 'property-commercial');

        $output .= $this->get_cps_location($post);
        $output .= get_field_cps_area($post);
        $output .= get_field_cps_keywords($post);
        $output .= get_field_cps_keywords2($post);

        return $output;

    }
    function get_fields_cps_residential($post){
        $output = '';

        $output .= get_field_cps_price($post, 'property');

        $output .= $this->get_cps_location($post);
        $output .= get_field_cps_furnishings($post);
        $output .= get_field_cps_location2($post);
        $output .= get_field_cps_location3($post);
        $output .= get_field_cps_area($post);
        $output .= get_field_cps_minroom($post);
        $output .= get_field_cps_maxroom($post);
        $output .= get_field_cps_minbath_room($post);
        $output .= get_field_cps_maxbath_room($post);
        $output .= get_field_cps_minprice($post);
        $output .= get_field_cps_maxprice($post);
        $output .= get_field_cps_amenities($post);
        $output .= get_field_cps_keywords($post);
        $output .= get_field_cps_keywords2($post);

        return $output;

    }
    function get_fields_cps_common_field($post){
        if ( $post->post_type === 'property' ) {
            return $this->get_fields_cps_residential($post);
        } elseif ($post->post_type === 'property-commercial') {
            return $this->get_fields_cps_commercial($post);
        } else {
            print_r('Unknown property type');
        }
    }

}


function get_cps_type($post) {

    if ($post->post_type == 'property-commercial') {
        $cps_type = 'cps_type_commercial';
    } elseif ($post->post_type == 'property') {
        $cps_type = 'cps_type';
    } else {
        print_r('Unknown property type');
        return;
    }

    $terms = get_terms( array(
        'taxonomy'      => array( $cps_type ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = '<select name="' . $cps_type . '" required="required">';

    $output .= '<option value="">Choose Property Type</option>';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, $cps_type, $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '</select>';
    return $output;
}

function get_field_cps_operation($post){

    if ($post->post_type == 'property-commercial') {

        $cps_rent_checked = is_object_in_term( $post->ID, 'cps_operation','rent-commercial' );
        $cps_buy_checked = is_object_in_term( $post->ID, 'cps_operation', 'buy-commercial' );

    } elseif ($post->post_type == 'property') {

        $cps_rent_checked = is_object_in_term( $post->ID, 'cps_operation','rent' );
        $cps_buy_checked = is_object_in_term( $post->ID, 'cps_operation', 'buy' );

    } else {
        print_r('Unknown property type');
        return;
    }

    $output = get_field_cps_header('cps_operation', 'Property Operation');

    $output .= '         <label class="cps-columns">';
    $output .= '             <input type="checkbox" name="cps_rent"' . checked( $cps_rent_checked, true, false ) . '>';
    $output .= '             Rent';
    $output .= '         </label>';
    $output .= '         <label class="cps-columns">';
    $output .= '             <input type="checkbox" name="cps_buy"' . checked( $cps_buy_checked, true, false ) . '>';
    $output .= '             Buy';
    $output .= '         </label>';

    if ( $post->post_type === 'property' ) {

        $output .= get_field_cps_completion($post);

    }

    $output .= get_field_cps_footer();

    return $output;

}

function get_field_cps_ref($post){

    $cps_ref = get_post_meta($post->ID, 'cps_ref', true);
    $cps_call = get_post_meta($post->ID, 'cps_call', true);

    $output = '';

    $output .= '<label class="cps-columns cps-columns cps-columns-4" style="float: left;"><div class="cps-columns-head cps-columns">Ref. Number:</div>';
    $output .= '    <input type="text" name="cps_ref" value="' . $cps_ref . '" required="required">';
    $output .= '</label>';
    $output .= '<label class="cps-columns cps-columns cps-columns-4"><div class="cps-columns-head cps-columns">Call Number:</div>';
    $output .= '    <input type="text" name="cps_call" value="' . $cps_call . '" required="required">';
    $output .= '</label><br><br>';


    return $output;

}

function get_field_cps_header($slug, $title){

    $output = '';

    $output .= '<div id="field_' . $slug . '" class="cps-row">';
    $output .= '    <div class="cps-columns-head cps-columns">' . $title . '</div>';
    $output .= '    <div class="cps-row cps-row-prop">';

    return $output;

}
function get_field_cps_footer(){

    $output = '';

    $output .= '</div></div>';

    return $output;

}
function get_field_cps_minbath_room($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_minbath_room' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_minbath_room', 'Min Bathrooms');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_minbath_room">';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_minbath_room', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

    return $output;
}

function get_field_cps_maxbath_room($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_maxbath_room' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_maxbath_room', 'Max Bathrooms');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_maxbath_room">';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_maxbath_room', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

    return $output;
}

function get_field_cps_minprice($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_minprice' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_minprice', '');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_minprice">';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_minprice', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

    return $output;
}

function get_field_cps_maxprice($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_maxprice' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_maxprice', '');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_maxprice">';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_maxprice', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

    return $output;
}

function get_field_cps_minroom($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_minroom' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_minroom', 'Min Rooms');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_minroom">';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_minroom', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

}

function get_field_cps_maxroom($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_maxroom' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_maxroom', 'Max Rooms');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_maxroom">';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_maxroom', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

}

function get_field_cps_area($post){
    $cps_area = get_post_meta($post->ID, 'cps_area', true);

    $output = get_field_cps_header('cps_area', 'Property Area, sqft');

    $output .= '<label class="cps-columns">';
    $output .= '    <input type="number" name="cps_area" value="' . $cps_area . '" required="required">';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

}
function get_field_cps_completion($post){

    $cps_completion = get_post_meta($post->ID, 'cps_completion', true);

    $output  = '<div id="field_cps_completion" class="cps-columns cps_hidden">';
    $output .= '    <label>Completion status</label>';
    $output .= '    <select name="cps_completion">';
    $output .= '        <option value="0"' . selected($cps_completion, 0, false) . '>Off plan</option>';
    $output .= '        <option value="1"' . selected($cps_completion, 1, false) . '>Ready</option>';
    $output .= '    </select>';
    $output .= '</div>';

    return $output;

}
function get_field_cps_furnishings($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_furnishings' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_furnishings', 'Furnishings');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_furnishings">';
    $output .= '        <option value="0">All furnishings</option>';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_furnishings', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;
}

function get_field_cps_location2($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_location' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_location2', 'Location 2');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_location2">';
    $output .= '        <option value="0">All Locations</option>';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_location2', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;
}

function get_field_cps_location3($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_location' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );

    $output = get_field_cps_header('cps_location3', 'Location 3');

    $output .= '<label class="cps-columns">';
    $output .= '    <select name="cps_location3">';
    $output .= '        <option value="0">All Locations</option>';

    foreach( $terms as $term ) {

        $is_selected = is_object_in_term( $post->ID, 'cps_location3', $term->slug);
        $output .= '<option value="' . $term->term_id . '"' . selected( $is_selected, true, false ) . '>' . $term->name . '</option>';

    }

    $output .= '    </select>';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;
}

function get_field_cps_keywords($post){

    $cps_keywords = get_post_meta($post->ID, 'cps_keywords', true);

    $output = get_field_cps_header('cps_keywords', 'All Keywords');

    $output .= '<label class="cps-columns">';
    $output .= '    <input type="text" name="cps_keywords" value="' . $cps_keywords . '">';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

}
function get_field_cps_keywords2($post){

    $cps_keywords = get_post_meta($post->ID, 'cps_keywords2', true);

    $output = get_field_cps_header('cps_keywords2', 'All Keywords 2');

    $output .= '<label class="cps-columns">';
    $output .= '    <input type="text" name="cps_keywords2" value="' . $cps_keywords . '">';
    $output .= '</label>';

    $output .= get_field_cps_footer();

    return $output;

}
function get_field_cps_amenities($post){

    $terms = get_terms( array(
        'taxonomy'      => array( 'cps_amenities' ),
        'orderby'       => 'name',
        'order'         => 'ASC',
        'hide_empty'    => false,
    ) );
    $output = get_field_cps_header('cps_amenities', 'All Amenities');

    foreach( $terms as $term ) {
        $is_checked = is_object_in_term( $post->ID, 'cps_amenities', $term->slug);

        $output .= '<label class="cps-columns cps-columns-4"><input type="checkbox" name="cps_amenities[]"  value="' . $term->term_id . '"' . checked( $is_checked, true, false ) . '>' . $term->name . '</label>';
    }

    $output .= get_field_cps_footer();

    return $output;

}

function get_field_cps_price($post, $property_type){

    $cps_price_buy = get_post_meta($post->ID, 'cps_price_buy', true);
    $cps_price_rent = get_post_meta($post->ID, 'cps_price_rent', true);

    $output = '';
    $output .= '<div id="field_cps_price" class="cps-row cps_hidden">';
    $output .= '    <div class="cps-columns-head cps-columns">Property Price</div>';
    $output .= '    <div class="cps-row cps-row-prop">';

    $output .= '<div id="field_cps_price_buy" class="cps-columns cps-columns-4 cps_hidden">';
    $output .= '    <div class="cps-columns-12 font-bold">Buy:</div>';
    $output .= '    <div class="cps-row-mini">';
    $output .= '        <label class="cps-columns-head-mini-left">Price, AED</label>';
    $output .= '        <div class="cps-row cps-row-prop">';
    $output .= '            <input class="input-small" type="number" name="cps_price_buy" value="' . $cps_price_buy . '">';
    $output .= '        </div>';
    $output .= '    </div>';
    $output .= '</div>';

    $output .= '<div id="field_cps_price_rent" class="cps-columns cps-columns-5 cps_hidden">';
    $output .= '    <div class="cps-columns-12 font-bold">Rent:</div>';

    $output .= '<div class="cps-row-mini">';

    if ($property_type === 'property') {

        $cps_contract = get_post_meta($post->ID, 'cps_contract', true);

        $output .= '    <div class="cps-columns-head-mini-right">Contract</div>';
        $output .= '    <div class="cps-row-prop">';
        $output .= '        <select name="cps_contract">';
        $output .= '            <option value="">Choose contract period</option>';
        $output .= '            <option ' . selected('year' ,   $cps_contract, false) . ' value="year">Yearly</option>';
        $output .= '            <option ' . selected('month',   $cps_contract, false) . ' value="month">Monthly</option>';
        $output .= '            <option ' . selected('week' ,   $cps_contract, false) . ' value="week">Weekly</option>';
        $output .= '            <option ' . selected('day'  ,   $cps_contract, false) . ' value="day">Daily</option>';
        $output .= '        </select>';
        $output .= '    </div>';
    }

    $output .= '</div>';

    $output .= '<div class="cps-row-mini">';
    $output .= '    <div class="cps-columns-head-mini-right">Price per contract, AED</div>';
    $output .= '    <div class="cps-row-prop">';
    $output .= '        <input class="input-small" type="number" name="cps_price_rent" value="' . $cps_price_rent . '">';
    $output .= '    </div>';
    $output .= '</div>';

    $output .= '</div>';

    $output .= '</div></div>';

    return $output;

}






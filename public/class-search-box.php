<?php
class SearchBox {

    public $property_operation = '';
    public function __construct() {
        add_action('wp_head', array( $this, 'js_variables' ));
        add_shortcode( 'cps-search-form', array( $this, 'display' ) );

        add_action('wp_ajax_get_search_fields'       , array( $this, 'get_search_fields' ));
        add_action('wp_ajax_nopriv_get_search_fields', array( $this, 'get_search_fields' ));

        add_action('wp_ajax_get_select_locations'       , array( $this, 'get_select_locations' ));
        add_action('wp_ajax_nopriv_get_select_locations', array( $this, 'get_select_locations' ));

        $this->activate_sidebar();
    }



    public $cps_data = array(
        'cps_operation'     => '',
        'cps_locations'     => '',
        'cps_type'          => '',
        'cps_contract'      => '',
        'cps_furnishings'   => '',
        'cps_minprice'   => '',
        'cps_maxprice'   => '',
        'cps_amenities'     => '',
        'cps_minroom'     => '',
        'cps_maxroom'     => '',
        'cps_area_min'     => '',
        'cps_area_max'     => '',
        'cps_minbath_room'     => '',
        'cps_maxbath_room'     => '',
        'cps_keywords'     => '',
    );

    public $cps_post_type;

    ///
    public $contract = array(
        'yearly'   => 'Yearly',
        'monthly'  => 'Monthly',
        'weekly'   => 'Weekly',
        'daily'    => 'Daily',
    );
    public $price = array(
        30000,
        40000,
        50000,
        60000,
        70000,
        80000,
        90000,
        100000,
        110000,
        120000,
        130000,
        140000,
        150000,
        160000,
        170000,
        180000,
        190000,
        200000,
        225000,
        250000,
        275000,
        300000,
        350000,
        400000,
        500000,
        750000,
        1000000,
    );
    public $area = array(
        500,
        600,
        700,
        800,
        900,
        1000,
        1100,
        1200,
        1300,
        1400,
        1500,
        1600,
        1800,
        2000,
        2200,
        2400,
        2600,
        2800,
        3000,
        3200,
        3400,
        3600,
        3800,
        4200,
        4600,
        5000,
        5400,
        5800,
        6200,
        6600,
        7000,
        7400,
        7800,
        8200,
        9000,
    );
    public $operation = array();

    public function display() {
        $this->get_data($_GET);

        $operations = $this->get_operation();
        $operation  = isset($_GET['cps_operation']) ? $_GET['cps_operation'] : $operations[0]->slug;
        ob_start();
        ?>
        <form method="get" id="cps_search_form" action="" class="hidden">
            <div class="search-property--style1">
                <div class="cps-row-narrow search-property-row-1 d-flex align-items-center">
                    <?php $this->get_field_operation(); ?>
                    <?php $this->get_search_field(); ?>
                </div>
                <div class="cps-row-narrow search-property-row-2 d-flex align-items-center">
                    <?php $this->get_first_fields($operation); ?>
                </div>
            </div>
        </form>
        <?php

        $markup = ob_get_contents();
        ob_end_clean();
    return $markup;
    }
    function get_search_property_rent() {
        $fields = array(
            'type',
            'contract',
            'furnished',
            'price',
            'amenities',
            'bed_room',
            'area',
            'bath_room',
            'keywords',
        );
        $this->get_fields($fields);
    }
    function get_search_property_buy() {
        $fields = array(
            'type',
            'price',
            'bed_room',
            'area',
            'amenities',
            'keywords',
        );
        $this->get_fields($fields);
    }
    function get_search_property_commercial_buy() {
        $fields = array(
            'type_commercial',
            'price',
            'area',
            'keywords',
        );
        $this->get_fields($fields);
    }
    function get_search_property_commercial_rent() {
        $fields = array(
            'type_commercial',
            'price',
            'area',
            'keywords',
        );
        $this->get_fields($fields);
    }

    //AJAX
    function get_search_fields() {

        if( wp_doing_ajax() ){
            if ( empty($_POST) || ! wp_verify_nonce( $_POST['get_search_fields_nonce'], 'get_search_fields') ){
                exit;
            } else {
                $field_type = $_POST['field_type'];
                if ($field_type){
                    $this->property_operation = $field_type;

                    $form['action'] = 'get_search_fields';

                    ob_start();
                    $this->get_first_fields($field_type);
                    $form['content'] = ob_get_contents();
                    ob_end_clean();

                    wp_send_json($form);
                }
            }
        }
        wp_die();
    }
    function get_select_locations() {

        if( wp_doing_ajax() ){
            if ( empty($_POST) || ! wp_verify_nonce( $_POST['get_search_fields_nonce'], 'get_search_fields') ){
                print_r('error verification');
                exit;
            } else {
                $terms = get_terms( array(
                    'taxonomy'      => array('cps_location'),
                    'orderby'       => 'name',
                    'order'         => 'ASC',
                    'hide_empty'    => false,
                    'fields'        => 'all',
                    'exclude'       => $_POST['selected'],
                    'search'        => $_POST['search'],
                ) );

                $output = array();
                foreach( $terms as $term ) {
                    $parents = get_term_parents_list( $term->term_id, 'cps_location', $args = array(
                        'format'    => 'name',
                        'separator' => ', ',
                        'link'      => false,
                        'inclusive' => false,
                    ) );
                    $output[] = array(
                        'id'    => $term->term_id,
                        'text'  => $parents ? $term->name . ' (' . mb_substr($parents, 0, -2) . ')' : $term->name,
                        'parents'  => get_ancestors( $term->term_id, 'cps_location' ),
                    );
                }
                wp_send_json($output);

            }
        }
        wp_die();

    }

    function get_first_fields ($fields){
        switch ($fields) {
            case 'buy':
                return $this->get_search_property_buy();
                break;
            case 'buy-commercial':
                return $this->get_search_property_commercial_buy();
                break;
            case 'rent-commercial':
                return $this->get_search_property_commercial_rent();
                break;
            default:
                return $this->get_search_property_rent();
        }
    }

    function get_select_cps_type() {
        global $wp, $wp_query;
        $name = get_query_var('cps_type2');

        $pieces = explode("-", $name);
        if ($pieces[1] == 'Bedroom' || $pieces[1] == 'Bedrooms') {
            $name = '';
            for ($i = 2; $i <= count($pieces); $i++) {
                $name = $name.'-'.$pieces[$i];
            }
            $name = trim($name, '-');
        } else {
            $name = get_query_var('cps_type2');
        }
        $name = cps_Admin::get_id('cps_type', $name);
        $selected = isset($name) ? $name : 0;
        if ($selected == 0) {
            $var = get_query_var('cps_type2');
            if (isset($var)) { $selected = get_query_var('cps_type2'); }
        }
        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_type' ),
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false,
        ) );
        $output  = '<select id="cps_type_field" name="cps_type">';
        $output .= '<option value="">Property Type</option>';

        foreach( $terms as $term ) {
            $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
        }

        $output .= '</select>';

        return $output;
    }
    function get_select_cps_type_commercial() {

        $selected = isset($_GET['cps_type']) ? $_GET['cps_type'] : 0;

        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_type_commercial' ),
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false,
        ) );
        $output  = '<select id="cps_type_field" name="cps_type_commercial">';
        $output .= '<option value="">Property Type</option>';

        foreach( $terms as $term ) {
            $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
        }

        $output .= '</select>';

        return $output;
    }
    function get_select_property($name, $properties, $first = false, $after = '', $multiple = false) {

        $selected = isset($_GET[$name]) ? $_GET[$name] : '';
        $multiple_attr = $multiple ? 'multiple="multiple" ' : '';
        $output  = '<select ' . $multiple_attr . 'name="' . $name . '">';
        if ($first)
            $output .= '<option value="">' . $first . '</option>';

        foreach( $properties as $property_key => $property ) {
            $output .= '<option value="' . $property_key . '"' . selected($selected, $property_key,false ) . '>' . $property . ($after ? ' ' . $after : '') . '</option>';
        }

        $output .= '</select>';
        return $output;
    }
    function get_select_field ($name, $property, $first = false, $after = '', $multiple = false) {
        if($property == 'cps_amenitie') { $property = 'cps_amenities'; }
        if($property == 'cps_furnishing') { $property = 'cps_furnishings'; }
        $selected = isset($_GET[$name]) ? $_GET[$name] : 0;
        if ($selected == 0) {
            $var = get_query_var($name);
            if (isset($var)) { $selected = get_query_var($name); }
        }
        $multiple_attr = $multiple ? 'multiple="multiple" ' : '';
        $output  = '<select ' . $multiple_attr . 'name="' . $name .  ($multiple ? "[]" : "") .'">';
        if ($first)
            $output .= '<option value="">' . $first . '</option>';

        $terms = get_terms( array(
            'taxonomy'      =>  array( $property ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
        ) );
        foreach( $terms as $term ) {
            if (is_object($term)) {
                $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . ($after ? ' ' . $after : '') . '</option>';
            }
        }

        $output .= '</select>';
        return $output;
    }

    function get_fields($fields) {
        foreach ($fields as $field){
            $action = 'get_field_' . $field;
            $this->$action();
        }
    }
    function get_operation(){
        $terms = get_terms( array(
            'taxonomy'      =>  array( 'cps_operation' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
        ) );

        return $terms;
    }
    function get_field_operation() {
        global $wp, $wp_query;
        $name = get_query_var('cps_operation');
        $selected = isset($name) ? $name : 0;
        if ($selected == 0) {
            $var = get_query_var('cps_operation');
            if (isset($var)) { $selected = get_query_var('cps_operation'); }
        }
        ?>
        <div id="cps_operation" class="search-form-column select-single cps-columns cps-columns-4">
            <?php
            $output  = '<select name="cps_operation">';
            foreach( $this->get_operation() as $operation ) {
                $output .= '<option value="' . $operation->slug . '"' . selected($selected, $operation->slug, false ) . '>' . $operation->name . '</option>';
            }
            $output .= '</select>';

            echo $output;
            ?>
        </div>
        <?php wp_nonce_field('get_search_fields','get_search_fields_nonce');
    }
    function get_search_field() {
        ?>
        <div class="search-form-column cps-columns cps-columns-8">
        <?php
            $name = get_query_var('cps_locations');
            if ($name == 'uae') { $name = ''; }
            $city = '';
            if (is_numeric($name)) {
                $city = cps_Admin::get_name('cps_locations', $name);
            }else{
                $city = $name;
            }
            $selected = isset($name) ? $name : 0;
        ?>
            <select name="cps_locations" class="input-search">
            <?php if ($selected !== 0) { ?>
                <option value="1" selected disabled><?php echo str_replace('-', ' ', $city); ?></option>
            <?php } ?>
            </select>
            <?php 
                $order = '';
                foreach (cps_Admin::get_permalink_order() as $key =>  $taxonomy) {
                    if ($key == 0) {
                        $order = $taxonomy;
                    }else{
                        $order = $order.','.$taxonomy;
                    }
                }
            ?>
            <input id="cps_order" type="hidden" value="<?php echo $order ?>" />
            <button name="cps_search" id="search-find" class="button-search">Find</button>
        </div>
        <?php
    }
    function get_field_type() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <?php echo $this->get_select_cps_type() ?>
        </div>
        <?php
    }
    function get_field_type_commercial() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <?php echo $this->get_select_cps_type_commercial() ?>
        </div>
        <?php
    }
    function get_field_price() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <div class="column-half">
                <?php
                    $selected = isset($_GET['cps_minprice']) ? $_GET['cps_minprice'] : 0;
                    if ($selected == 0) {
                        $var = get_query_var('cps_price_min');
                        if (isset($var)) { $selected = get_query_var('cps_price_min'); }
                    }
                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_minprice' ),
                        'orderby'       => 'name',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                    ) );
                    $output  = '<select id="cps_minprice_field" name="cps_minprice">';
                    $output .= '<option value="">Min. price</option>';

                    foreach( $terms as $term ) {
                        $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
                    }

                    $output .= '</select>';
                    echo $output;
                    $output = '';
                ?>
            </div>
            <div class="column-half">
                <?php
                    $selected = isset($_GET['cps_maxprice']) ? $_GET['cps_maxprice'] : 0;
                    if ($selected == 0) {
                        $var = get_query_var('cps_price_max');
                        if (isset($var)) { $selected = get_query_var('cps_price_max'); }
                    }
                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_maxprice' ),
                        'orderby'       => 'name',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                    ) );
                    $output  = '<select id="cps_maxprice_field" name="cps_maxprice">';
                    $output .= '<option value="">Max. price</option>';

                    foreach( $terms as $term ) {
                        $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
                    }

                    $output .= '</select>';
                    echo $output;
                    $output = '';
                ?>
                
            </div>
        </div>
        <?php
    }
    function get_field_bed_room() {
        ?>

        <div class="search-form-column select-single cps-columns cps-columns-4">
            <div class="column-half">
            <?php
                global $wp, $wp_query;
                $name = get_query_var('cps_type2');
                $pieces = explode("-", $name);
                $name = $pieces[0].'-'.$pieces[1];
                
                $name = cps_Admin::get_id('cps_minroom', $name);
                $selected = isset($name) ? $name : 0;
                if ($selected == 0) {
                    $var = get_query_var('cps_minroom');
                    if (isset($var)) { $selected = get_query_var('cps_minroom'); }
                }
                $terms = get_terms( array(
                    'taxonomy'      => array( 'cps_minroom' ),
                    'orderby'       => 'name',
                    'order'         => 'ASC',
                    'hide_empty'    => false,
                ) );
                $output  = '<select id="cps_minroom_field" name="cps_minroom">';
                $output .= '<option value="">Min. bed</option>';

                foreach( $terms as $term ) {
                    $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
                }

                $output .= '</select>';
                echo $output;
                $output = '';
            ?>
            </div>
            <div class="column-half">
            <?php
                $selected = isset($_GET['cps_maxroom']) ? $_GET['cps_maxroom'] : 0;
                if ($selected == 0) {
                    $var = get_query_var('cps_maxroom');
                    if (isset($var)) { $selected = get_query_var('cps_maxroom'); }
                }
                $terms = get_terms( array(
                    'taxonomy'      => array( 'cps_maxroom' ),
                    'orderby'       => 'name',
                    'order'         => 'ASC',
                    'hide_empty'    => false,
                ) );
                $output  = '<select id="cps_maxroom_field" name="cps_maxroom">';
                $output .= '<option value="">Max. bed</option>';

                foreach( $terms as $term ) {
                    $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
                }

                $output .= '</select>';
                echo $output;
                $output = '';
            ?>
            </div>
        </div>
        <?php
    }
    function get_field_bath_room() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <div class="column-half">
            <?php
                $selected = isset($_GET['cps_minbath_room']) ? $_GET['cps_minbath_room'] : 0;
                if ($selected == 0) {
                    $var = get_query_var('cps_minbath_room');
                    if (isset($var)) { $selected = get_query_var('cps_minbath_room'); }
                }

                $terms = get_terms( array(
                    'taxonomy'      => array( 'cps_minbath_room' ),
                    'orderby'       => 'name',
                    'order'         => 'ASC',
                    'hide_empty'    => false,
                ) );
                $output  = '<select id="cps_minbath_room_field" name="cps_minbath_room">';
                $output .= '<option value="">Min. bath</option>';

                foreach( $terms as $term ) {
                    $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
                }

                $output .= '</select>';
                echo $output;
                $output = '';
            ?>
            </div>
            <div class="column-half">
                <?php
                    $selected = isset($_GET['cps_maxbath_room']) ? $_GET['cps_maxbath_room'] : 0;
                    if ($selected == 0) {
                        $var = get_query_var('cps_maxbath_room');
                        if (isset($var)) { $selected = get_query_var('cps_maxbath_room'); }
                    }
                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_maxbath_room' ),
                        'orderby'       => 'name',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                    ) );
                    $output  = '<select id="cps_maxbath_room_field" name="cps_maxbath_room">';
                    $output .= '<option value="">Max. bath</option>';

                    foreach( $terms as $term ) {
                        $output .= '<option value="' . $term->term_id . '"' . selected($selected, $term->term_id,false ) . '>' . $term->name . '</option>';
                    }

                    $output .= '</select>';
                    echo $output;
                ?>
            </div>
        </div>
        <?php
    }
    function get_field_area() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <div class="column-half">
                <?php echo $this->get_select_property('cps_area_min', $this->area, $first = 'Min. area', $after = 'sqft') ?>
            </div>
            <div class="column-half">
                <?php echo $this->get_select_property('cps_area_max', $this->area, $first = 'Max. area', $after = 'sqft') ?>
            </div>
        </div>
        <?php
    }
    function get_field_amenities() {
        ?>
        <div class="search-form-column cps-columns cps-columns-4 select2-selection select2-selection--single">
        <?php
                    $selected = isset($_GET['cps_amenitie']) ? $_GET['cps_amenitie'] : 0;
                    if ($selected == 0) {
                        $var = get_query_var('cps_amenitie');
                        if (isset($var)) { $selected = get_query_var('cps_amenitie'); }
                    }
                    
                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_amenities' ),
                        'orderby'       => 'name',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                    ) );
                    $output  = '<select id="cps_amenities_field" name="cps_amenitie">';
                    $output .= '<option value="">Amenities</option>';

                    foreach( $terms as $term ) {
                        if ($term->term_id == $selected) {
                            $output .= '<option value="' . $term->term_id . '" selected>' . $term->name . '</option>';
                        }else{
                            $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                        }
                    }

                    $output .= '</select>';
                    echo $output;
                ?>
            <?php //echo $this->get_select_field('cps_amenities', 'cps_amenities', $first = '', $after = '', true); ?>
        </div>
        <?php

    }
    function get_field_keywords() {
        $value = isset($_GET['cps_keywords']) ? $_GET['cps_keywords'] : '';
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <input class="search-keywords" type="text" name="cps_keywords" placeholder="Keywords" value="<?php echo $value ?>">
        </div>
        <?php
    }

    function get_field_furnished() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <?php echo $this->get_select_field('cps_furnishing', 'cps_furnishing', $first = 'All furnishings', $after = '') ?>
        </div>
        <?php
    }
    function get_field_contract() {
        ?>
        <div class="search-form-column select-single cps-columns cps-columns-4">
            <?php echo $this->get_select_property('cps_contract', $this->contract, $first = 'Contract period', $after = '') ?>
        </div>
        <?php
    }

    function js_variables(){
        $variables = array (
            'ajax_url' => admin_url('admin-ajax.php'),
        );
        echo(
            '<script type="text/javascript">window.wp_data = '.
            json_encode($variables).
            ';</script>'
        );
    }

    public function get_parameters() {
        //var_dump($this->cps_data);
        $data_filter = array();
        foreach ($this->cps_data as $index=>$data) {
            if ($index == 'cps_locations') {
                if ($data !== 'uae') {
                    $index = 'cps_location';
                    $data_filter[] = array(
                        'taxonomy' => $index,
                        'field'    => 'slug',
                        'terms'    => array( $data ),
                    );
                }
            }else {
                if ($data) {
                    $data_filter[] = array(
                        'taxonomy' => $index,
                        'field'    => 'id',
                        'terms'    => array( $data ),
                    );
                }
            }
            


        }
        //echo '<pre>' . var_export($data_filter, true) . '</pre>';

        return $data_filter;
    }

    function get_data($GET) {
        error_reporting(0);

        $check = get_query_var('cps_operation');
         if (isset($check)) {
            foreach ($this->cps_data as $index=>$data) {
                if ($index == 'cps_type') { $index = 'cps_type2'; }
                if ($index == 'cps_amenities') { $index = 'cps_amenitie'; }
                $var = get_query_var($index);
                if ($index == 'cps_type2') { $index = 'cps_type'; }
                if ($index == 'cps_amenitie') { $index = 'cps_amenities'; }
                if ( isset($GET[$index]) ) {
                    $this->cps_data[$index] = $GET[$index];
                }else{
                    //echo $index.'-'.$var.'<br>';
                    if ($index == 'cps_locations') {
                        $var = strtolower($var);
                    }
                    if ($index == 'cps_minroom') {
                        $var = get_query_var('cps_type2');
                        $pieces = explode("-", $var);
                        $var = $pieces[0].'-'.$pieces[1];
                        $var = cps_Admin::get_id('cps_minroom', $var);
                    }
                    if ($index == 'cps_type') {
                        $pieces = explode("-", $var);
                        if ($pieces[1] == 'Bedroom' || $pieces[1] == 'Bedrooms') {
                            $name = '';
                            for ($i = 2; $i <= count($pieces); $i++) {
                                $name = $name.'-'.$pieces[$i];
                            }
                            $name = trim($name, '-');
                        } else {
                            $name = $var;
                        }
                        $var = $name;
                        $var = cps_Admin::get_id('cps_type', $var);
                    }
                    $this->cps_data[$index] = $var;
                }

            }

            if ($this->cps_data['cps_operation']) {

                if ($this->cps_data['cps_operation'] == 'rent' || $this->cps_data['cps_operation'] == 'buy') {
                    $this->cps_post_type = 'property';
                } elseif ($this->cps_data['cps_operation'] == 'rent-commercial' || $this->cps_data['cps_operation'] == 'buy-commercial') {
                    $this->cps_post_type = 'property-commercial';
                }
            }
        }
    }

    public function setUrl_loc ($loc) {
        $loc = str_replace(' ', '-', $loc);
        $true_url = get_site_url().'';
        $link = '';
		foreach (cps_Admin::get_permalink_order() as $key =>  $taxonomy) {
            if ($taxonomy == 'cps_operation') { $link = $link.'/rent'; }
            if ($taxonomy == 'cps_location') { $link = $link.'/'.$loc; }
            if ($taxonomy == 'cps_type') { $link = $link.'/property'; }
                      
          }

        return $true_url.$link;
    }

    public function setUrl_type ($type) {
        $type = str_replace(' ', '-', $type);
        $true_url = get_site_url().'';
        $link = '';
		foreach (cps_Admin::get_permalink_order() as $key =>  $taxonomy) {
            if ($taxonomy == 'cps_operation') { $link = $link.'/rent'; }
            if ($taxonomy == 'cps_location') { $link = $link.'/uae'; }
            if ($taxonomy == 'cps_type') { $link = $link.'/'.$type; }
                      
          }

        return $true_url.$link;
    }

    public static function get_content_price ($post_type, $post_meta) {
        if ($post_type['cps_operation'] == 'rent') {
            return $post_meta['cps_price_rent'][0];
        }
        if ($post_type['cps_operation'] == 'buy') {
            return $post_meta['cps_price_buy'][0];
        }
        return;
    }
    public function activate_sidebar () {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cps-sidebar-widget.php';
        new CPS_Sidebar_Widget;
    }
}
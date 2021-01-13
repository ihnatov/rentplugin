<?php

class Custom_Title_List_Table extends WP_List_Table {
    const DIVIDER = '<span class="p-5">or</span>';
    public $property = array();
    public $properties = array();

    public $location_level_0 = array();
    public $location_level_1 = array();
    public $location_level_2 = array();
    public $location_level_3 = array();

    function __construct(){
        parent::__construct(array(
            'singular' => 'custom_title',
            'plural'   => 'custom_titles',
            'ajax'     => false,
        ));
        $this->property = cps_Admin::CPS_PROPERTIES_NAME;
        $this->properties = cps_Admin::CPS_PROPERTIES_NAMES;

        $this->bulk_action_handler();

        $this->prepare_items();

    }

    function prepare_items(){
        // $this->_column_headers = $this->get_column_info();
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );
        /** Process bulk action */
        $this->process_bulk_action();
        $per_page = $this->get_items_per_page('records_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();
        $data = self::get_records($per_page, $current_page);

        $this->set_pagination_args(
            ['total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page // WE have to determine how many items to show on a page
            ]);

        $this->items = $data;

    }

    public static function get_records($per_page = 10, $page_number = 1) {
        global $wpdb;
        $sql = "SELECT * FROM `" . $wpdb->prefix . "cps_custom_title`";
        $add = '';

        if (isset($_REQUEST['s']) && $_REQUEST['s']) {
            $add .= ' cps_title LIKE "%' . $_REQUEST['s'] . '%"';
        }
        if (isset($_POST['filter_action'])) {
            if ($_POST['cps_operation'])
                $add .= ($add ? ' AND' : '') . ' `cps_operation` = ' . $_POST['cps_operation'];
            if ($_POST['cps_type'])
                $add .= ($add ? ' AND' : '')  . ' `cps_type` = ' . $_POST['cps_type'];
            if ($_POST['cps_location'])
                $add .= ($add ? ' AND' : '')  . ' `cps_location` = ' . $_POST['cps_location'];
            if ($_POST['cps_minroom'])
                $add .= ($add ? ' AND' : '')  . ' `cps_minroom` = ' . $_POST['cps_minroom'];
            if ($_POST['cps_maxroom'])
                $add .= ($add ? ' AND' : '')  . ' `cps_maxroom` = ' . $_POST['cps_maxroom'];
            if ($_POST['cps_minbath_room'])
                $add .= ($add ? ' AND' : '')  . ' `cps_minbath_room` = ' . $_POST['cps_minbath_room'];
            if ($_POST['cps_maxbath_room'])
                $add .= ($add ? ' AND' : '')  . ' `cps_maxbath_room` = ' . $_POST['cps_maxbath_room'];
            if ($_POST['cps_minprice'])
                $add .= ($add ? ' AND' : '')  . ' `cps_minprice` = ' . $_POST['cps_minprice'];
            if ($_POST['cps_maxprice'])
                $add .= ($add ? ' AND' : '')  . ' `cps_maxprice` = ' . $_POST['cps_maxprice'];
            if ($_POST['cps_amenities'])
                $add .= ($add ? ' AND' : '')  . ' `cps_amenities` = ' . $_POST['cps_amenities'];
            if ($_POST['cps_furnishings'])
                $add .= ($add ? ' AND' : '')  . ' `cps_furnishings` = ' . $_POST['cps_furnishings'];
            if ($_POST['cps_location2'])
                $add .= ($add ? ' AND' : '')  . ' `cps_location2` = ' . $_POST['cps_location2'];
            if ($_POST['cps_location3'])
                $add .= ($add ? ' AND' : '')  . ' `cps_location3` = ' . $_POST['cps_location3'];
            if ($add)
                $sql.= ' where' . $add;
        }
        if (!empty($_REQUEST['orderby'])) {
            $sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }
        $sql.= " LIMIT " . $per_page;
        $sql.= ' OFFSET ' . ($page_number - 1) * $per_page;

        $result = $wpdb->get_results($sql);
        return $result;
    }

    public static function del_records($rows = false) {
        global $wpdb;

        if (!$rows)
            return;

        $array = (string)implode ( ", ", $rows);
        $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cps_custom_title` WHERE `id` IN ( $array )" );
    }

    public static function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM `" . $wpdb->prefix . "cps_custom_title`";
        return $wpdb->get_var($sql);
    }

    function get_columns(){
        return array(
            'cb'            => '<input type="checkbox" />',
            'cps_title'     => 'Title',
            'cps_url'     => 'Url',
            'cps_meta'  => 'Meta',
        );
    }

    public function get_hidden_columns(){
        return array();
    }

    function get_sortable_columns(){
        return array(
            'cps_title'         => array( 'cps_title'       , 'true' ),
            'cps_operation'     => array( 'cps_operation'   , 'true' ),
            'cps_type'          => array( 'cps_type'        , 'true' ),
            'cps_location'      => array( 'cps_location'    , 'true' ),
            'cps_minroom'          => array( 'cps_minroom'        , 'true' ),
            'cps_maxroom'          => array( 'cps_maxroom'        , 'true' ),
            'cps_minbath_room'     => array( 'cps_minbath_room'   , 'true' ),
            'cps_maxbath_room'     => array( 'cps_maxbath_room'   , 'true' ),
            'cps_minprice'     => array( 'cps_minprice'   , 'true' ),
            'cps_maxprice'     => array( 'cps_maxprice'   , 'true' ),
            'cps_amenities'     => array( 'cps_amenities'   , 'true' ),
            'cps_furnishings'   => array( 'cps_furnishings' , 'true' ),
            'cps_location2'   => array( 'cps_location2' , 'true' ),
            'cps_location3'   => array( 'cps_location3' , 'true' ),
            'cps_keywords'      => array( 'cps_keywords'    , 'true' ),
            'cps_keywords2'      => array( 'cps_keywords2'    , 'true' ),
        );
    }

    protected function get_bulk_actions() {
        return array(
            'delete' => 'Delete',
        );
    }

    function extra_tablenav( $which ){
        // echo '<div class="alignleft actions">';
        // foreach ( $this->properties as $name=>$label)
        //     echo $this->property_select_filter_factory($name, $label);

        // submit_button( __( 'Filter' ), 'action', 'filter_action', false, array( 'id' => "post-query-submit" ) );
        // echo '<a href="' . site_url() . '/wp-admin/admin.php?page=cps_Menu_Custom_Title" style="vertical-align: sub;">Reset</a>';
        // echo '</div>';

        // if ( 'top' === $which ) {

        //     $this->search_box( __( 'Search' ), 'all-properties' );
        // }
    }

    protected function display_tablenav( $which ) {

        if ( 'top' === $which ) {
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
        }
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php if ( $this->has_items() ) : ?>
                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
            <?php
            endif;
            $this->extra_tablenav( $which );
            if ( 'top' !== $which ) {
                $this->pagination( $which );
            }
            ?>

            <br class="clear" />
        </div>
        <?php
    }
    protected function handle_row_actions( $item, $column_name, $primary ) {

        return $column_name === $primary ? '<a class="cps_title-edit">Edit</a><div>' . $this->get_url($item->cps_url, $item) . '</div><input type="hidden" value='.json_encode($item->cps_url).'>' : '';
    }
    function column_default( $item, $colname ){
        if ($item->$colname == false) {
            return;
        } elseif ($colname === 'cps_title' || $colname === 'cps_keywords' || $colname === 'cps_keywords2') {
            return isset($item->$colname) ? '<span>' . $item->$colname . '</span>': print_r($item, 1);
        } elseif ($colname === 'cps_url' || $colname === 'cps_meta') {
            return isset($item->$colname) ? '<span>' . $item->$colname . '</span>': print_r($item, 1);
        } elseif ($colname === 'cps_location') {
            if (!$item->$colname) return;
            $locations_url = get_term_parents_list( $item->$colname, $colname, $args = array(
                'format'    => 'name',
                'separator' => ' > ',
                'link'      => false,
            ) );
            return mb_substr($locations_url, 0, -3) . '<input type="hidden" value="' . $item->$colname .'">';
        } else {
            $term = get_term($item->$colname, $colname);
            return $term->name . '<input type="hidden" value="' . $item->$colname .'">';
        }
    }
    function get_url($order_url, $items){
        $order_url = $order_url;
        if (!is_array($order_url)) return;
        $url = '<hr>';
        foreach ($order_url as $taxonomy){
            $url .= $this->get_url_term_factory($items, $taxonomy);
        }
        return $url;
    }
    function get_url_term_factory($items, $taxonomy){
        // print_r($items);
        if ($items->$taxonomy == false) {
            return;
        } elseif ($taxonomy=='cps_location') {
            if (!$items->$taxonomy) return;
            $locations_url = get_term_parents_list( $items->$taxonomy, $taxonomy, $args = array(
                'format'    => 'slug',
                'separator' => '/',
                'link'      => false,
                ) );
            return '/' . mb_substr($locations_url, 0, -1);
        } elseif ($taxonomy=='cps_keywords' || $taxonomy=='cps_keywords2') {
            return '/' . sanitize_title( $items->$taxonomy );
        } else {
            $term = get_term( $items->$taxonomy, $taxonomy);
        }
        if (isset($term->errors)){
            print_r($taxonomy);
            print_r('<hr>');
            print_r($term->errors);
            return;
        }
        return '/' . $term->slug;

    }
    function column_cb( $item ){
        echo '<input type="checkbox" name="licids[]" id="cb-select-'. $item->id .'" value="'. $item->id .'" />';
    }

    private function bulk_action_handler(){

        
        if( empty($_POST['licids']) || empty($_POST['_wpnonce']) ) return;

        if ( ! $action = $this->current_action() ) return;

        if( ! wp_verify_nonce( $_POST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) )
            wp_die('nonce error');

        if ($_POST['action'] === '-1'){
            self::del_records($_POST['licids']);
        }

    }

    public function property_select_cps_operation () {

        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_operation' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'parent'        => 0,
        ) );
        $output  = '<select name="cps_operation">';
        $output .= '<option value="0">Operation not selected</option>';

        foreach( $terms as $term ) {
            $output .= '<option data-operation="' . $term->slug . '" value="' . $term->term_id . '">' . $term->name . '</option>';
        }

        $output .= '</select>';

        return $output;
    }
    public function property_select_cps_type($cps_type = 0) {
        $output  = '';

        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_type' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
        ) );

        $output .= '<select name="cps_type">';
        $output .= '<option value="">Type not selected</option>';

        foreach( $terms as $term ) {
            $parent_name = 'Residential - ';
            $output .= '<option data-type="property" value="' . $term->term_id . '"' . selected($term->term_id, $cps_type, false) . '>' . $parent_name . $term->name . '</option>';
        }

        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_type_commercial' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
        ) );

        foreach( $terms as $term ) {
            $parent_name = 'Commercial - ';
            $output .= '<option data-type="property-commercial" value="' . $term->term_id . '"' . selected($term->term_id, $cps_type, false) . '>'. $parent_name . $term->name . '</option>';
        }

        $output .= '</select>';

        return $output;
    }
    public function property_select_cps_location_level_0 () {
        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_location' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
            'parent'        => 0
        ) );
        $output  = '<select name="cps_location[level_0]">';
        $output .= '<option value="">City not selected</option>';

        foreach( $terms as $term ) {
            $this->location_level_0[] = $term->term_id;
            $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
        }

        $output .= '</select>';
        return $output;
    }
    public function property_select_cps_location2 () {
        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_location' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
        ) );
        $output  = '<select name="cps_location2" class="js-example-basic-single">';
        $output .= '<option value="">Location 2 not selected</option>';

        foreach( $terms as $term ) {
            $this->location_level_0[] = $term->term_id;
            $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
        }

        $output .= '</select>';
        return $output;
    }
    public function property_select_cps_location3 () {
        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_location' ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
        ) );
        $output  = '<select name="cps_location3" class="js-example-basic-single">';
        $output .= '<option value="">Location 3 not selected</option>';

        foreach( $terms as $term ) {
            $this->location_level_0[] = $term->term_id;
            $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
        }

        $output .= '</select>';
        return $output;
    }
    public function property_select_cps_location_level_1 () {
        $output  = '<select name="cps_location[level_1]">';
        $output .= '<option value="">Neighborhood not selected</option>';
        foreach ($this->location_level_0 as $parent) {

            $terms = get_terms( array(
                'taxonomy'      => array( 'cps_location' ),
                'orderby'       => 'id',
                'order'         => 'ASC',
                'hide_empty'    => false,
                'hierarchical'  => true,
                'parent'        => $parent
            ) );

            foreach( $terms as $term ) {
                $this->location_level_1[] = $term->term_id;
                $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
            }

        }
        $output .= '</select>';
        return $output;
    }
    public function property_select_cps_location_level_2 () {
        $output  = '<select name="cps_location[level_2]">';
        $output .= '<option value="">Community not selected</option>';
        foreach ($this->location_level_1 as $parent) {

            $terms = get_terms( array(
                'taxonomy'      => array( 'cps_location' ),
                'orderby'       => 'id',
                'order'         => 'ASC',
                'hide_empty'    => false,
                'hierarchical'  => true,
                'parent'        => $parent
            ) );

            foreach( $terms as $term ) {
                $this->location_level_2[] = $term->term_id;
                $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
            }

        }
        $output .= '</select>';
        return $output;
    }
    public function property_select_cps_location_level_3 () {
        $output  = '<select name="cps_location[level_3]">';
        $output .= '<option value="">Tower not selected</option>';
        foreach ($this->location_level_2 as $parent) {

            $terms = get_terms( array(
                'taxonomy'      => array( 'cps_location' ),
                'orderby'       => 'id',
                'order'         => 'ASC',
                'hide_empty'    => false,
                'hierarchical'  => true,
                'parent'        => $parent
            ) );

            foreach( $terms as $term ) {
                $this->location_level_3[] = $term->term_id;
                $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
            }

        }
        $output .= '</select>';
        return $output;
    }
    public function property_select($property, $label) {
        $terms = get_terms( array(
            'taxonomy'      => array( $property ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'hierarchical'  => true,
        ) );

        if (isset($terms->errors)) return;

        $output  = '<select name="' . $property . '">';
        $output .= '<option value="0">' . $label . ' not selected</option>';

        foreach( $terms as $term ) {
            $output .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
        }

        $output .= '</select>';

        return $output;
    }

    public function property_select_factory($property, $label) {
        if ($property == 'cps_operation') {
            echo $this->property_select_cps_operation();
        }
        elseif ($property == 'cps_type'){
            echo $this->property_select_cps_type();
        }
        elseif ($property == 'cps_location'){
            echo $this->property_select_cps_location_level_0();
            echo self::DIVIDER;
            echo $this->property_select_cps_location_level_1();
            echo self::DIVIDER;
            echo $this->property_select_cps_location_level_2();
            echo self::DIVIDER;
            echo $this->property_select_cps_location_level_3();
        }
        elseif ($property == 'cps_location2'){
            echo $this->property_select_cps_location2();
            echo self::DIVIDER;
        }
        elseif ($property == 'cps_location3'){
            echo $this->property_select_cps_location3();
            echo self::DIVIDER;
        }
        else {
            echo $this->property_select($property, $label);
        }
    }

    public function get_property_inputs () {

        $properties = cps_Admin::CPS_PROPERTIES_NAME;
        unset($properties['cps_keywords']);
        unset($properties['cps_keywords2']);
        $count = count($properties);
        foreach ( $properties as $name=>$label ) {
            echo $this->property_select_factory($name, $label);
            echo (--$count > 0) ? self::DIVIDER : '<span class="p-5">or consistent any of</span>';
        }
        echo '<input type="text" name="cps_keywords" placeholder="input keywords">';
        echo ' <span class="p-5">or</span> <input type="text" name="cps_keywords2" placeholder="input keywords 2">';
    }

    public function property_select_filter_operation() {
        $terms = get_terms( array(
            'taxonomy'      => array( 'cps_operation' ),
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'parent'        => 0,
        ) );
        $output  = '<select name="cps_operation">';
        $output .= '<option value="">All Operations</option>';

        foreach( $terms as $term ) {
            $output .= '<option value="' . $term->term_id . '" '. selected($term->term_id, @ $_POST['filter_action'] ? $_POST['cps_operation'] : "", 0) .'>' . $term->name . '</option>';
        }

        $output .= '</select>';
        return $output;

    }
    public function property_select_filter($property, $label) {

        $terms = get_terms( array(
            'taxonomy'      => array( $property ),
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'fields'        => 'all',
        ) );
        $output  = '<select name="' . $property . '">';
        $output .= '<option value="">All ' . $label . '</option>';

        foreach( $terms as $term ) {
            if (is_object($term)) {
                $output .= '<option value="' . $term->term_id . '" '. selected($term->term_id, @ $_POST['filter_action'] ? $_POST[$property] : "", 0) .'>' . $term->name . '</option>';

            }
        }

        $output .= '</select>';
        return $output;

    }
    public function property_input_filter($property, $label) {

        $value = isset($_POST['filter_action']) ? $_POST[$property] : "";
        $output = '<input type="number" name="' . $property . '" placeholder="' . $label . '" step="1" min="0" value="' . $value . '">';
        return $output;

    }

    public function property_select_filter_factory($property, $label) {
        if ($property == 'cps_operation') {
            echo $this->property_select_filter_operation();
        }
        elseif ($property == 'cps_keywords' || $property == 'cps_keywords2'){

        }
        elseif ($property == 'cps_type' || $property == 'cps_type_commercial'){
            $cps_type = isset($_POST['cps_type']) ? $_POST['cps_type'] : 0;
            echo $this->property_select_cps_type($cps_type);
        }
        elseif ($property == 'cps_minbath_room' || $property == 'cps_maxbath_room' || $property == 'cps_minprice' || $property == 'cps_maxprice' || $property == 'cps_minroom' || $property == 'cps_maxroom'){
            echo $this->property_input_filter($property, $label);
        } else {
            echo $this->property_select_filter($property, $label);
        }
    }
}

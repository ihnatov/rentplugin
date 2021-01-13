<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header();
?>

<div class="container">

<?php
$SearchBox = new SearchBox;
echo $SearchBox->display();

$array_search = array();

?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
        $tax_query = array_merge( array('relation' => 'AND'), $SearchBox->get_parameters() );
       // $tax_query = array_merge( array('operator' => 'IN'), $SearchBox->get_parameters() );
        $args = array(
            'numberposts'       => -1,
            'posts_per_page'    => 10,
            'orderby'           => 'rand',
            'post_type'         => $SearchBox->cps_post_type,
            'post_status'       => 'publish',
            'tax_query'         => $tax_query,
        );
        //var_dump($tax_query);
        $projects = get_posts( $args );
        $operation = ucfirst(get_query_var('cps_operation'));
        $type = get_query_var('cps_type2');
        $pieces = explode("-", $type);
        if ($pieces[1] == 'Bedroom' || $pieces[1] == 'Bedrooms') {
            $type = '';
            for ($i = 2; $i <= count($pieces); $i++) {
                $type = $type.'-'.$pieces[$i];
            }
            $type = trim($type, '-');
        } else {
            $type = get_query_var('cps_type2');
        }
        $type = str_replace('-', ' ', $type);
        
        $location = str_replace('-', ' ', get_query_var('cps_locations'));
        $rooms = str_replace('-', ' ', get_query_var('cps_minroom'));
        $current_url = trim($_SERVER['REQUEST_URI'], '/');
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cps_custom_title WHERE cps_url = '$current_url'", OBJECT );
       

        $taxonomyName = "cps_location";
        $parent_terms = get_terms($taxonomyName, array('parent' => 0, 'orderby' => 'slug', 'hide_empty' => false));   
        if ($location !== 'uae' ) {
            $alist = [];

            foreach ($parent_terms as $pterm) {
                $terms = get_terms($taxonomyName, array('parent' => $pterm->term_id, 'orderby' => 'slug', 'hide_empty' => false));
                foreach ($terms as $term) {
                    $comm = get_terms($taxonomyName, array('parent' => $term->term_id, 'orderby' => 'slug', 'hide_empty' => false));
                    foreach ($comm as $com_id) {
                        $tower = get_terms($taxonomyName, array('parent' => $comm->term_id, 'orderby' => 'slug', 'hide_empty' => false));
                        foreach ($tower as $tower_id) {
                            $data = $term->name.'|'.$com_id->name.'|'.$tower_id->name;
                            $data = str_replace('-', ' ', $data);
                            array_push($alist, $data);
                        }
                    }
                }
            }

            $sort_list = '';
            foreach ($alist as $value) {
                if (strpos($value, $location) !== false) {
                    $sort_list = $sort_list.'|'.$value;
                }
            }
            $sort_list = array_filter(array_unique(explode("|", $sort_list)));
            $sort_list = array_diff($sort_list, [$location]);
        }
        ?>
            <?php if (count($results) !== 0) { ?>
            <header class="page-header">
                <h2><?php echo $results[0]->cps_title ?></h2>
            </header>
            <?php } else { ?>
            <header class="page-header">
            <div id="textbox">
                <p class="alignleft">
                <?php if ($type !== 'property' ) { echo $type; } ?>
                <?php echo $operation ?>
                <?php if ($location !== 'uae' ) { echo ' in '.$location; } ?>
                </p>
                <p class="alignright"><?php echo count($projects); ?> results</p>
            </div>
            </header>
            <?php } ?>
            <div style="clear: both;"></div>
            <?php if ($location !== 'uae' ) { ?>
                <div class="otherLoc">
                    <ul id="loc_inline">
                    <?php foreach ($sort_list as $val) { ?>
                        <li><a href="<?php echo $SearchBox->setUrl_loc($val) ?>"><?php echo $val ?></a></li>
                    <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <div style="clear: both;"></div>

            <div class="columnsContainer">
                <div class="leftColumn">
                    <?php if ($projects) {  ?>    
                        <?php
                        foreach ( $projects as $post ) {
                            setup_postdata($post);
                            require __DIR__ . '/part/content-single.php';
                        }

                        wp_reset_postdata();

                        the_posts_pagination(
                            array(
                                'prev_text'          => __( 'Previous page', 'twentysixteen' ),
                                'next_text'          => __( 'Next page', 'twentysixteen' ),
                                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>',
                            )
                        );

                    } else {

                        get_template_part( 'template-parts/content', 'none' );

                    }

                    ?>
                </div>
                <div class="rightColumn">
                <?php if ($location !== 'uae' ) {
                    
                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_type' ),
                        'orderby'       => 'id',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                        'parent'        => 0,
                    ) );
                    ?>
                    <h4>Aprartment Types</h4>
                    <ul id="rightLoc">
                        <?php foreach ($terms as $val) { ?>
                        <li><a href="<?php echo $SearchBox->setUrl_type($val->name) ?>"><?php echo $val->name ?></a></li>
                        <?php } ?>
                    </ul>

                    <h4>Near "<?php echo $location;
                    
                    $name = cps_Admin::get_id('cps_locations', $location);
                    
                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_location' ),
                        'orderby'       => 'id',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                        'hierarchical'  => true,
                        'slug'          => $location
                    ) );

                    $terms = get_terms( array(
                        'taxonomy'      => array( 'cps_location' ),
                        'orderby'       => 'id',
                        'order'         => 'ASC',
                        'hide_empty'    => false,
                        'hierarchical'  => true,
                        'parent'          => $terms[0]->parent
                    ) );
                    ?>"</h4>
                    <ul id="rightLoc">
                    <?php foreach ($terms as $val) { ?>
                        <li><a href="<?php echo $SearchBox->setUrl_loc($val->name) ?>"><?php echo $val->name ?></a></li>
                    <?php } ?>
                    </ul>

                <?php } ?>


                </div>
            </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->
    <div style="clear: both;"></div>
    </div>
<?php get_footer(); ?>


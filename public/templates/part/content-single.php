<?php


$post_meta     = get_post_meta($post->ID, '', true);
if (!empty($post_meta)) {
$locations = 'No location';


$cps_location_term  = wp_get_object_terms($post->ID, 'cps_location');
$cps_type_term      = wp_get_object_terms($post->ID, 'cps_type');

$cps_bath_term  = wp_get_object_terms($post->ID, 'cps_minbath_room');
$cps_room_term  = wp_get_object_terms($post->ID, 'cps_minroom');

$area = wp_get_object_terms($post->ID, 'cps_area');


if (isset($cps_bath_term[0]->name)){ $bath = preg_replace("/[^0-9]/", "", $cps_bath_term[0]->name ); }else{$bath='';}

if (isset($cps_room_term[0]->name)){ $room = preg_replace("/[^0-9]/", "", $cps_room_term[0]->name ); }else{$room='';}

if ($cps_location_term && !isset($cps_location_term['error'])) {

    $locations = get_term_parents_list($cps_location_term[0] , 'cps_location', $args = array(
        'format'    => 'slug',
        'separator' => ', ',
        'link'      => false,
    ) );
}

if ($cps_type_term && !isset($cps_type_term[0]->error)) {
    $cps_type_name = $cps_type_term[0]->name;
} else {
    $cps_type_name = '';
}




if(isset($_POST['submit'])){
    $to =  get_option('admin_email');
    $from = $_POST['email'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $subject = "New";
    $link = get_post_permalink($post->ID);
    $mes = $_POST['message'];
    
    $message = "$mes <br><br><br> <b>Email:</b> $from <br> <b>Name:</b> $name <br> <b>Phone: </b> $phone <br> <b>Link:</b> $link";

    $headers = "From:" . $from;
    mail($to,$subject,$message,$headers);
    }

?>

<article id="post-<?php the_ID(); ?>" class="cps-row-narrow d-flex postcard">
    <?php $widget_img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'cps-260-185'); if ($widget_img) { ?>
        <div class="cps-article-image cps-columns cps-columns-6">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <img src="<?php echo esc_url($widget_img[0]); ?>" alt="<?php the_title_attribute(); ?>">
            </a>
        </div>
    <?php } ?>
	<div class="cps-columns cps-columns-6 cps-archive-post-content">
        <div class="content-deal">
            <span class="content-currency">AED</span>
            <span class="content-price"><?php echo SearchBox::get_content_price ($SearchBox->cps_data, $post_meta) ?></span>            
            <span class="content-contract"><?php ?></span>
        </div>
        <div class="content-location content_list"><?php echo $locations ?></div>
        <div class="content-type content_list"><?php echo $cps_type_name ?></div>
        <div class="content-name content_list"><?php echo get_the_title(); ?></div>
        <div class="content-properties content_list icon_list">
            <span class="content-room"><i class="fas fa-bed"></i>       <?php echo $room ?></span>
            <span class="content-bathroom"><i class="fas fa-bath"></i>  <?php echo $bath ?></span>
            <span class="content-room"><i class="fas fa-th-large"></i>  <?php echo $post_meta['cps_area'][0] ?></span>
        </div>
        <div class="content-buttons">
            <a class="call_in" href="tel:<?php echo $post_meta['cps_call'][0] ?>"><button class="call"><i class="fas fa-phone-alt call_icon"></i>Call</button></a>
            <a href="#openModal<?php echo $post->ID ?>"><button class="call"><i class="far fa-envelope call_icon"></i>Email</button></a>
        </div>

        <div id="openModal<?php echo $post->ID ?>" class="modalDialog">
        <div>
            <a href="#close" title="Close" class="close">X</a>
            <div class="contact_info">
                <h4 style="text-align: center;">Contact for more information.</h4>
                <form action="" method="post">
                    <div class="contact_block" tabindex="-1">
                        <span class="contact_span">Name*</span>
                        <input id="contactFormName" class="contact_input" name="name" value="">
                    </div>
                    <div class="contact_block" tabindex="-1">
                        <span class="contact_span">Email*</span>
                        <input id="contactFormName" class="contact_input" name="email" value="">
                    </div>
                    <div class="contact_block" tabindex="-1">
                        <span class="contact_span">Phone*</span>
                        <input id="contactFormName" class="contact_input" name="phone" value="">
                    </div>
                    <div class="contact_block message_box" tabindex="-1">
                        <span class="contact_span">Message*</span>
                        <textarea id="contactFormMessage" class="contact_textarea" name="message"></textarea>
                    </div>
                    <div class="content-buttons">
                        <center><button type="submit" class="send_popup" name="submit">Send</button></center>
                    </div>
                </form>
            </div>
        </div>
        </div>


        <!-- <?php print_r(get_the_author()) ?> -->
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
    <?php } ?>
<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.

		while ( have_posts() ) :
			the_post();

			// Include the single post content template.
			get_template_part( 'template-parts/content', 'single' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

			if ( is_singular( 'attachment' ) ) {
				// Parent post navigation.
				the_post_navigation(
					array(
						'prev_text' => _x( '<span class="meta-nav">Published in</span><span class="post-title">%title</span>', 'Parent post link', 'twentysixteen' ),
					)
				);
			} elseif ( is_singular( 'post' ) ) {
				// Previous/next post navigation.
				the_post_navigation(
					array(
						'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'twentysixteen' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Next post:', 'twentysixteen' ) . '</span> ' .
							'<span class="post-title">%title</span>',
						'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'twentysixteen' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Previous post:', 'twentysixteen' ) . '</span> ' .
							'<span class="post-title">%title</span>',
					)
				);
			}

			// End of the loop.
		endwhile;
		?>

<?php
	$SearchBox = new SearchBox;
	$locations = 'No location';

	$post_meta     = get_post_meta($post->ID, '', true);

	if (isset($post_meta['gallery_data'][0])) {
		$gallery = unserialize($post_meta['gallery_data'][0]);
	} else {
		$gallery = '';
	}
	

	$cps_location_term  = wp_get_object_terms($post->ID, 'cps_location');
	$cps_type_term      = wp_get_object_terms($post->ID, 'cps_type');

	$cps_bath_term  = wp_get_object_terms($post->ID, 'cps_minbath_room');
	$cps_room_term  = wp_get_object_terms($post->ID, 'cps_minroom');

	$area = wp_get_object_terms($post->ID, 'cps_area');
	$price = wp_get_object_terms($post->ID, 'cps_price');

	$amenities = wp_get_object_terms($post->ID, 'cps_amenities');

	$bath = preg_replace("/[^0-9]/", "", $cps_bath_term[0]->name );
	$room = preg_replace("/[^0-9]/", "", $cps_room_term[0]->name );

	$link = get_page_link();

	if ($cps_location_term && !isset($cps_location_term['error'])) {

		$locations = get_term_parents_list($cps_location_term[0] , 'cps_location', $args = array(
			'format'    => 'slug',
			'separator' => ', ',
			'link'      => false,
		) );
		$locations = substr($locations, 0, -2);
		$locations = str_replace('-', ' ', $locations);
	}

	if ($cps_location_term && !isset($cps_location_term['error'])) {

		$bread = get_term_parents_list($cps_location_term[0] , 'cps_location', $args = array(
			'separator' => '/',
			'link'      => false,
		) );
		$BreadArray = array_filter(explode('/', $bread));
	}

	if ($cps_type_term && !isset($cps_type_term[0]->error)) {
		$cps_type_name = $cps_type_term[0]->name;
	} else {
		$cps_type_term = '';
	}

	if(isset($_POST['submit'])){
		$to =  get_option('admin_email');
		$from = $_POST['email'];
		$name = $_POST['name'];
		$phone = $_POST['phone'];
		$subject = "New";
		$link = get_page_link();
		$mes = $_POST['message'];
		
		$message = "$mes <br><br><br> <b>Email:</b> $from <br> <b>Name:</b> $name <br> <b>Phone: </b> $phone <br> <b>Link:</b> $link";

		$headers = "From:" . $from;
		mail($to,$subject,$message,$headers);
		}

?>

<div class="row">
  <div class="column left">
  <div class="bread">
  <div class="searchback" aria-label="Link delimiter"></div><span itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem" dir="auto"></span>
  <span itemprop="name" class="c5051fb4 searchback"><a href="#" id="historyback">Back to search</a></span>	
	<?php 
		foreach ($BreadArray as $key => $value) { reset($BreadArray);?>
		<span><a href="<?php echo $SearchBox->setUrl_loc($value) ?>" class="_94807c44" title="Bayut" itemprop="item"><span itemprop="name" class="c5051fb4"><?php echo $value ?></span></a>
			<meta itemprop="position" content="1">
		</span>
		<?php end($BreadArray); if ($key !== key($BreadArray)) { ?>
			<div class="fd327256" aria-label="Link delimiter"></div><span itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem" dir="auto">
		<?php } ?>
	<?php } ?>
</div>

<?php if (isset($gallery['image_url'])) { ?>
<!-- Slideshow container -->
<div class="slideshow-container">

  <!-- Full-width images with number and caption text -->
  <?php foreach ($gallery['image_url'] as &$value) { ?>
	<div class="mySlides fade">
		<img src="<?php echo $value ?>" style="width:100%">
	</div>
  <?php } ?>

  <!-- Next and previous buttons -->
  <a class="prev" id="plusS">&#10094;</a>
  <a class="next" id="minusS">&#10095;</a>
</div>
  <?php } ?>
	<div class="title_block">
		<h1 class="title_h"><?php echo get_the_title(); ?></h1>
		<div class="title_buttons">
			<div class="title_share_block">
				<div class="dropdown dropbtn"><a class="dropdown__trigger d5fc92c7"><span></span><span><div class="_0e54df3f"><i class="fas fa-share icon_but"></i><span>Share</span></div></span></a>
				<div class="submenu">
					<ul class="root">
						<li ><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $link ?>" target="_blank"><i class="fab fa-facebook"></i> Facebook</a></li>
						<li ><a href="https://twitter.com/intent/tweet?text=<?php echo get_the_title(); ?>%20-%20url:%20<?php echo $link ?>" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li>
						<li ><a href="https://wa.me/?text=<?php echo get_the_title(); ?>%20<?php echo $link ?>" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a></li>
						<li ><a href="mailto:?subject=<?php echo get_the_title(); ?>r&body=<?php echo $link ?>" target="_blank"><i class="far fa-envelope"></i> E-mail</a></li>
					</ul>
				</div>
				</div>
			</div>
		</div>
	</div>
	
		<div class="content-deal">
            <span class="post_currency">AED</span>
            <span class="post_price"><?php echo $post_meta['cps_price_rent'][0] ?></span>            
            <span class="post_currency"><?php echo $post_meta['cps_contract'][0] ?></span>
        </div>
        <div class="content-location content_list post_location"><?php echo $locations ?></div>
        <div class="content-properties content_list icon_list  post_data_in">
            <span class="content-room"><i class="fas fa-bed"></i>  <?php echo $room ?> Beds</span>
            <span class="content-bathroom"><i class="fas fa-bath"></i>  <?php echo $bath ?> Baths</span>
            <span class="content-room"><i class="fas fa-th-large"></i>  <?php echo $post_meta['cps_area'][0] ?> sqft</span>
		</div>
		
		<div class="_1aca585a bd9071d3">
    <div class="b69bbd1e">
        <h2 class="_504a7380">Overview</h2></div>
    <div class="_96aa05ec">
        <ul class="_033281ab" style="columns:2">
            <li aria-label="Property detail type"><span class="_3af7fa95">Type</span><span class="_812aa185" aria-label="Value"><?php echo $cps_type_name ?></span></li>
            <li aria-label="Property detail price"><span class="_3af7fa95">Price</span><span class="_812aa185" aria-label="Value">AED <?php echo $post_meta['cps_price_rent'][0] ?></span></li>
            <li aria-label="Property detail beds"><span class="_3af7fa95">Bedroom(s)</span><span class="_812aa185" aria-label="Value"><?php echo $room ?></span></li>
            <li aria-label="Property detail baths"><span class="_3af7fa95">Bath(s)</span><span class="_812aa185" aria-label="Value"><?php echo $bath ?></span></li>
            <li aria-label="Property detail area"><span class="_3af7fa95">Area</span><span class="_812aa185" aria-label="Value"><span><?php echo $post_meta['cps_area'][0] ?> sqft</span></span>
            </li>
            <li aria-label="Property detail purpose"><span class="_3af7fa95">Purpose</span><span class="_812aa185" aria-label="Value">For Rent</span></li>
            <li aria-label="Property detail location"><span class="_3af7fa95">Location</span><span class="_812aa185" aria-label="Value"><?php echo $locations ?></span></li>
            <li aria-label="Property detail reference"><span class="_3af7fa95">Ref. No:</span><span class="_812aa185" aria-label="Value"><?php echo $post_meta['cps_ref'][0] ?></span></li>
        </ul>
    </div>
    <div class="_96aa05ec">
        <h3 class="e0f2e1bd">Description</h3>
        <div class="_892154cd _6c5bbfd9">
            <div>
                <div class="_2015cd68 f6c93dc2" aria-label="Property description">
                    <div dir="auto"><span class="_2a806e1e"><?php the_content(); ?></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="_96aa05ec">
        <h3 class="e0f2e1bd">Features / Amenities</h3>
        <div class="b6dfac9d">
			<?php foreach ($amenities as &$value) { ?>
				<div class="_2793fb7b">
					<span class="_76ae852f"><i class="far fa-check-circle"></i> <?php echo $value->name ?></span>
				</div>
			<?php } ?>
        </div>
    </div>
</div>

  </div>
  <div class="column right">
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
				<a class="call_in" href="tel:<?php echo $post_meta['cps_call'][0] ?>" style="padding: 11px;"><b class="call_a"><i class="fas fa-phone-alt call_icon"></i>Call</b></a>
				<button type="submit" class="call_in" name="submit"><i class="far fa-envelope call_icon"></i>Email</button>
			</div>
		</form>
	</div>
  </div>
</div>


	</main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>

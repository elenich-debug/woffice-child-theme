<?php
/**
 * The Template for displaying all single posts
 */

global $post;


get_header();  ?>

	<?php // Start the Loop.
	while ( have_posts() ) : the_post(); ?>

		<div id="left-content" class="center-inner-container">

			<?php  //GET THEME HEADER CONTENT
				 // woffice_title(get_the_title());
			?> 

<?php 
/**
 * ОРИГИНАЛЬНЫЙ ДИЗАЙН WOFFICE (БЫСТРАЯ ВЕРСИЯ)
 * Используем родные id="featuredbox", class="pagetitle" и "featured-background"
 */
$bg_image_url = 'http://3d-stuff.community/wp-content/uploads/2025/06/header-bg.webp';
?>

<header id="featuredbox" class="centered">
    
    <div class="pagetitle animate-me fadeIn">
        <h1 class="entry-title"><?php the_title(); ?></h1>
    </div><style>
        @media screen and (max-width: 800px) {
            .featured-background {
                background-image: url("<?php echo $bg_image_url; ?>") !important;
                background-image: -webkit-image-set("<?php echo $bg_image_url; ?>" 1x, "<?php echo $bg_image_url; ?>" 2x) !important;
                background-image: image-set("<?php echo $bg_image_url; ?>" 1x, "<?php echo $bg_image_url; ?>" 2x) !important;
            }
        }
    </style>

    <div class="featured-background" style="background-image: url(<?php echo $bg_image_url; ?>);">
        <div class="featured-layer"></div>
    </div>

</header>				

			<!-- START THE CONTENT CONTAINER -->
			<div id="content-container">

				<!-- START CONTENT -->
				<div id="content">
					<div class="celst-blog-single-wrapper">
						<?php // We check for the role : 
						if (woffice_is_user_allowed()) { ?>
							
							<?php // Include the page content template.
								if(woffice_get_skin('celestial')){
									get_template_part('celestial-content-parts/content', 'single');
								} else {
									get_template_part('template-parts/content', 'single');
								}
							?>
							
							
							

						 <?php } else { 
							get_template_part( 'content', 'private' );
						} ?>
					</div>

				</div>
					
			</div><!-- END #content-container -->
	
		</div><!-- END #left-content -->
		
	<?php // END THE LOOP 
	endwhile; ?>

<?php 
get_footer();
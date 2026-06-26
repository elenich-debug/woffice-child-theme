<?php
/**
* Template Name: daz
*/

// CHECK IF USER CAN CREATE BLOG POST

// 🔐 Закрываем доступ неадминам
if (!current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}

// 🚫 Запрещаем индексацию
add_action('wp_head', function () {
    echo '<meta name="robots" content="noindex, nofollow">' . "\n";
});


$post_create = woffice_get_theming_option('post_create');
$woffice_role_allowed = Woffice_Frontend::role_allowed($post_create, 'post');
if ($woffice_role_allowed):
	
	$hasError = Woffice_Frontend::frontend_process('post');
	
endif;
 
get_header();  
?>

	<div id="left-content" class="center-inner-container">	
		<!-- START THE CONTENT CONTAINER -->
        <?php woffice_title(get_the_title()); ?>
		<div id="content-container">

			<!-- START CONTENT -->
			<div id="content">
                <?php
                    $show_title_box = woffice_get_reduxsettings_option('show_title_box');
                    if(!woffice_validate_bool_option($show_title_box) ){
                    ?>
					<?php the_title('<h1 class="celst-page-title">','</h1>'); ?>
					<?php } ?>
                
                <div class="row blog-main-row">
                    <?php // We check for the layout 
                    $blog_layout = woffice_get_theming_option('blog_layout');
                    $masonry_columns = woffice_get_theming_option('masonry_columns',3);
                    $masonry_columns_class = 'masonry-layout--'.$masonry_columns.'-columns';

                    if ($blog_layout === 'masonry' || isset($_GET['blog_masonry'])) { ?>
                        <div id="blog-masonsry" class="masonry-layout <?php echo esc_html($masonry_columns_class); ?>">
                    <?php } ?>

                    <?php
                    // THE LOOP :
                    $posts_per_page = woffice_get_theming_option('blog_number');

                    $pagination_slug = (is_front_page()) ? 'page' : 'paged';
                    $paged = (get_query_var($pagination_slug)) ? get_query_var($pagination_slug) : 1;

                    /**
                     * Filter args of the blog posts query
                     *
                     * @param array $args
                     * @param int $paged
                     * @param int $posts_per_page
                     */
                    $args = apply_filters('woffice_blog_query_args', array(
                        'post_type' => 'daz',
                        'paged' => $paged,
                        'posts_per_page' => $posts_per_page
                    ), $paged, $posts_per_page);

                    $blog_query = new WP_Query($args);
                    if ( $blog_query->have_posts() ) :	?>
                        <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
                            <?php // We check for the role :
                            if (woffice_is_user_allowed()) { ?>
                                <?php if (($blog_layout == "masonry")) {
                                    get_template_part( 'content-celestial-masonry' );
                                }
                                else {
                                    get_template_part( 'celestial' );
                                } ?>
                            <?php } ?>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php get_template_part( 'content', 'none' ); ?>
                    <?php endif; ?>

                    <?php if ($blog_layout === 'masonry' || isset($_GET['blog_masonry'])) { ?>
                        </div>
                    <?php } ?>
                </div>
                <!-- THE NAVIGATION --> 
                <?php woffice_paging_nav($blog_query); ?>
                
                <div class="celts-frontend-form-wrapper">
                <?php
                // CHECK IF USER CAN CREATE BLOG POST
                if ($woffice_role_allowed): ?>
                            <?php Woffice_Frontend_Celestial::frontend_render('post',$hasError); ?>                                                              
                    <?php endif; ?>
                    </div>
                </div>
			</div>
				
		</div><!-- END #content-container -->
		
	</div><!-- END #left-content -->

<?php 
get_footer(); 
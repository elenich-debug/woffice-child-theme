<?php

// CHECK IF USER CAN CREATE BLOG POST
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
                    <?php 
                    // We check for the layout 
                    $blog_layout = woffice_get_theming_option('blog_layout');
                    $masonry_columns = woffice_get_theming_option('masonry_columns', 3);
                    $masonry_columns_class = 'masonry-layout--' . esc_attr($masonry_columns) . '-columns';

                    if ($blog_layout === 'masonry' || isset($_GET['blog_masonry'])) { ?>
                        <div id="blog-masonry" class="masonry-layout <?php echo esc_attr($masonry_columns_class); ?>">
                    <?php } ?>

                    <?php

                     /**************************************************
                     * КОД ДЛЯ "ВИНТАЖНЫХ" ПОСТОВ
                     **************************************************/
                    if ( function_exists( 'vsp_display_vintage_posts' ) ) {
                        vsp_display_vintage_posts();
                    }


                    // THE LOOP
                    $posts_per_page = woffice_get_theming_option('blog_number');

                    $pagination_slug = (is_front_page()) ? 'page' : 'paged';
                    $paged = (get_query_var($pagination_slug)) ? get_query_var($pagination_slug) : 1;

                    /**
                     * Filter args of the blog posts query
                     */
                    $args = apply_filters('woffice_blog_query_args', array(
                        'post_type' => 'post',
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
                        
                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <?php get_template_part( 'content', 'none' ); ?>
                    <?php endif; ?>

                    <?php if ($blog_layout === 'masonry' || isset($_GET['blog_masonry'])) { ?>
                        </div>
                    <?php } ?>
                </div>
                
                <!-- ИСПРАВЛЕННАЯ ПАГИНАЦИЯ -->
                <?php
                // Определяем базовый URL для пагинации
                $base_url = is_front_page() ? home_url('/page/%#%/') : get_pagenum_link(1) . 'page/%#%/';
                
                // Генерируем пагинацию
                $pagination_args = array(
                    'base' => $base_url,
                    'format' => '',
                    'current' => $paged,
                    'total' => $blog_query->max_num_pages,
                    'prev_text' => __('&laquo; Previous'),
                    'next_text' => __('Next &raquo;'),
                    'show_all' => false,
                    'end_size' => 1,
                    'mid_size' => 2,
                    'type' => 'plain',
                    'add_args' => false,
                    'add_fragment' => '',
                );

                $pagination_links = paginate_links($pagination_args);
                
                if ($pagination_links) {
                    echo '<div class="navigation pagination">';
                    echo '<div class="nav-links">';
                    echo $pagination_links;
                    echo '</div>';
                    echo '</div>';
                }
                ?>
                


			</div>
				
		</div><!-- END #content-container -->
		
	</div><!-- END #left-content -->

<?php 
get_footer();

/*
=================================================================
ДОПОЛНИТЕЛЬНОЕ РЕШЕНИЕ: Исправление функции woffice_paging_nav()
=================================================================

Если хотите исправить оригинальную функцию, найдите её в файлах темы 
(обычно в functions.php или в папке inc/) и замените на:

function woffice_paging_nav($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }
    
    // Определяем текущую страницу правильно
    $pagination_slug = (is_front_page()) ? 'page' : 'paged';
    $paged = (get_query_var($pagination_slug)) ? get_query_var($pagination_slug) : 1;
    
    if ($query->max_num_pages < 2) {
        return;
    }
    
    $base_url = is_front_page() ? home_url('/page/%#%/') : get_pagenum_link(1) . 'page/%#%/';
    
    $pagination_args = array(
        'base' => $base_url,
        'format' => '',
        'current' => $paged,
        'total' => $query->max_num_pages,
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
        'show_all' => false,
        'end_size' => 1,
        'mid_size' => 2,
    );

    echo '<nav class="navigation pagination" role="navigation">';
    echo '<div class="nav-links">';
    echo paginate_links($pagination_args);
    echo '</div>';
    echo '</nav>';
}

=================================================================
*/
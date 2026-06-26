<?php
/**
 * The template used for displaying post content
 */

// CUSTOM CLASSES ADDED BY THE THEME
$post_classes = array('content', 'entry-content');
$blog_listing_content = woffice_get_theming_option('blog_listing_content','excerpt');
$hide_image_single_post = woffice_get_theming_option('hide_image_single_post', false);
$hide_author_box = woffice_get_theming_option('hide_author_box_single_post', false);
$hide_like_counter = woffice_get_theming_option('hide_like_counter_inside_author_box',false);
$hide_learndash_meta = woffice_get_theming_option('hide_learndash_meta', false);

if(get_post_status() == 'draft')
    array_push($post_classes, 'is-draft');
?>

<div class="col-md-6 col-lg-6 col-xl-4 col-celst-3 blog-col">
    <article> <!-- Добавлен открывающий тег article -->
        <?php
        // ФИНАЛЬНАЯ ВЕРСИЯ 2.0: ПРОВЕРКА ПОКУПКИ И ЛАЙКА
        $wrapper_classes = 'blog-card-wrapper';
        $post_interacted = false; // Флаг, что с постом взаимодействовали

        if ( is_user_logged_in() ) {
            
            // --- ПРОВЕРКА 1: КУПИЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПОСТ ---
            global $wpdb;
            $current_user_id = get_current_user_id();
            $current_post_id = get_the_ID();
            $mycred_log_table = $wpdb->prefix . 'myCRED_log';

            $is_purchased = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$mycred_log_table} WHERE user_id = %d AND ref = %s AND ctype = %s AND ref_id = %d",
                $current_user_id, 'buy_content', 'mycred_default', $current_post_id
            ) );

            if ( $is_purchased > 0 ) {
                $post_interacted = true;
            }

            // --- ПРОВЕРКА 2: ПОСТАВИЛ ЛИ ПОЛЬЗОВАТЕЛЬ ЛАЙК ---
            // Проверяем, только если пост еще не помечен как "купленный"
            if ( !$post_interacted && class_exists('Woffice_Blog') && method_exists('Woffice_Blog', 'like_user_has_already_voted') ) {
                if ( Woffice_Blog::like_user_has_already_voted($current_post_id) ) {
                    $post_interacted = true;
                }
            }

            // Если выполнено хотя бы одно из условий, добавляем класс
            if ( $post_interacted ) {
                // Используем тот же класс, что и раньше, чтобы не менять CSS
                $wrapper_classes .= ' content-purchased';
            }
        }
        ?>
        <div class="<?php echo esc_attr( $wrapper_classes ); ?>">

            <div class="blog-thumb">
                <?php if (has_post_thumbnail() && (!is_single() || is_single() && !$hide_image_single_post)) : ?>
                    <!-- THUMBNAIL IMAGE -->
                    <?php 
                    /*GETTING THE POST THUMBNAIL URL*/
                    $featured_height = (function_exists('woffice_get_post_rdx_option')) ? woffice_get_post_rdx_option(get_the_ID(), 'featured_height') : '';
                    Woffice_Frontend::render_featured_image_single_post(get_the_ID(), $featured_height); // Исправлено: используем get_the_ID() вместо $post->ID 			
                    ?>
                <?php else: ?>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/images/blog.png" alt="Default blog image"> <!-- Добавлен alt -->
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <div class="blog-title">
                    <?php if (strpos(get_post_type(), 'sfwd') === FALSE || is_search()) : ?>
                        <div class="intern-padding heading-container">
                            <?php if (!is_single()): ?>
                                <?php // THE TITLE
                                if (is_sticky()):
                                    the_title( '<div class="heading"><h2><a href="' . esc_url( get_permalink() ) . '" class="font-weight-bold" rel="bookmark"><i class="fa fa-star text-yellow"></i>', '</a></h2></div>' );
                                else: 
                                    the_title( '<div class="heading"><h2><a href="' . esc_url( get_permalink() ) . '" class="font-weight-bold" rel="bookmark">', '</a></h2></div>' );
                                endif; ?>
                            <?php else : ?>
                                <?php // THE TITLE
                                the_title( '<div class="heading"><h2>', '</h2></div>' ); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="post-meta">
                    <?php // We display the post meta in the top only for the blog articles
                    // Исправлено: используем get_post_type() вместо $post->post_type
                    if (get_post_type() == "post" || get_post_type() == "mature" || get_post_type() == "bundle") : ?>
                        <div class="intern-box">
                            <?php // THE POST META
                            woffice_postmetas(); 
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="blog-content">
                    <?php if (is_single() || $blog_listing_content == 'content'): ?>
                        <?php the_content(''); ?>
                    <?php elseif($blog_listing_content == 'excerpt') : ?>
                        <?php the_excerpt(); ?>
                    <?php endif; ?>
                </div>
            </div>
            
                 <?php   
                 $price = get_post_meta(get_the_ID(), 'price', true); // Используем get_the_ID()
                 // Проверяем, что значение существует и не пустое
                    if (!empty($price)) {
                 // Используем esc_html() для безопасного вывода данных
                    echo '<div class="price">reward ' . esc_html($price) . ' Diamonds</div>';
                    } elseif (get_post_type() === 'request') {
                    echo '<div class="price">Bundle Request</div>';
                    }	
                ?>			
        </div>
    </article> <!-- Закрывающий тег article -->
</div>
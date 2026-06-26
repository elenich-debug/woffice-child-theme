<?php
    /**
     * Template Name: Calendar Events
     */
    
    get_header(); 
?>
    
    <div id="left-content">
        <?php
            woffice_title(get_the_title()); ?>
        
        <!-- START THE CONTENT CONTAINER -->
        <div id="content-container">
            
            <!-- START CONTENT -->
            <div id="content">
                <?php if (woffice_is_user_allowed()) {
                    // CUSTOM CLASSES ADDED BY THE THEME
                    $post_classes = array('box','content');
                    $show_title_box = woffice_get_theming_option('show_title_box');
                    if(!woffice_validate_bool_option($show_title_box) ){
                ?>

                <?php }?>
                <div id="event-loop-content" <?php post_class(); ?>>
                    
                    <!-- LOOP ALL THE EVENTS-->
                    <?php // GET EVENT POSTS OLDER THAN TODAY
                    $is_extension_active = woffice_get_theming_option('enable_woffice_event_extenstion');
                        if (woffice_validate_bool_option($is_extension_active)) {
                            $pagination_slug = (is_front_page()) ? 'page' : 'paged';
                            $paged = (get_query_var($pagination_slug)) ? get_query_var($pagination_slug) : 1;
                            $args = array(
                                'post_type'      => 'woffice-event',
                                'paged'          => $paged,
                                'posts_per_page' => 10,
                                'meta_key'       => 'woffice_event_date_end',
                                'orderby'        => 'meta_value',
                                'order'          => 'DESC'
                            );
                            
                            /**
                             * Filter `woffice_archive_events_query_args`
                             *
                             * Add the ability to override the archive event query
                             *
                             * @param array $args - current archive event query arguments
                             *
                             * @return $array - archive event query arguments
                             */
                            $args = apply_filters('woffice_archive_events_query_args', $args);
                            
                            $query = new WP_Query($args);
                            if ($query->have_posts()) :
                                
                                echo'<ul id="event-loop-list" class="event-loop-list">';
                                // LOOP
                                while($query->have_posts()) : $query->the_post();
                                    get_template_part('celestial-content-parts/content', 'event-loop');
                                endwhile;
                                echo '</ul>';
                            else :
                                get_template_part('content','none' );
                            endif;
                            wp_reset_postdata();
                        }?>
                    
                    <?php
                        } else {
                        get_template_part('content', 'private');
                    }
                    ?>
                </div>
                <?php woffice_paging_nav($query); ?>
            </div>
        
        </div><!-- END #content-container -->
            
    </div><!-- END #left-content -->
    

<?php
    get_footer();

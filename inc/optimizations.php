<?php
/**
 * Plugin Name: Woffice Performance Optimizer (Anti-Gravity)
 * Description: Intercepts and neutralizes redundant synchronous external HTTP requests by Woffice (e.g. Google Maps API verification) to prevent 300ms+ lag on every hit. Fully update-proof.
 */

add_action('pre_get_posts', function($query) {
    // Удаляем оригинальный тяжелый фильтр темы
    remove_filter('pre_get_posts', 'woffice_remove_restricted_posts_from_query', 10);
    remove_action('pre_get_posts', 'woffice_remove_restricted_posts_from_query', 10);
}, 1);

// ДОБАВЛЯЕМ ОПТИМИЗИРОВАННУЮ ВЕРСИЮ ФИЛЬТРА ЧЕРЕЗ the_posts
add_filter('the_posts', 'woffice_optimized_filter_restricted_posts', 10, 2);
function woffice_optimized_filter_restricted_posts( $posts, $query ) {
    if ( !function_exists('woffice_is_user_allowed') ) return $posts;
    if ( empty($posts) ) return $posts;

    if (
        current_user_can( 'manage_options' )
        || $query->is_single
        || $query->is_page
        || ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] != 'post' )
        || ( isset( $query->query_vars['woffice_ignore_posts_permission'] ) && $query->query_vars['woffice_ignore_posts_permission'] )
    ) {
        return $posts;
    }

    $filtered_posts = array();
    foreach ( $posts as $post ) {
        if ( woffice_is_user_allowed( $post->ID ) ) {
            $filtered_posts[] = $post;
        }
    }

    if ( count($filtered_posts) !== count($posts) ) {
        $query->found_posts -= (count($posts) - count($filtered_posts));
    }

    return $filtered_posts;
}
add_filter('pre_http_request', function($preempt, $parsed_args, $url) {
    // If the request goes to Google Geocoding API from Woffice
    if (strpos($url, 'maps.google.com/maps/api/geocode') !== false) {
        // Return a mock successful response instantly
        return [
            'headers'  => [],
            'body'     => json_encode([
                'status' => 'DISABLED_LOCAL',
                'results' => []
            ]),
            'response' => [
                'code'    => 200,
                'message' => 'OK'
            ],
            'cookies'  => [],
            'filename' => null
        ];
    }
    
    return $preempt;
}, 10, 3);

// Unhook Woffice heavy initialization functions
add_action('init', function() {
    // This stops Woffice from attempting to sync Google Maps coordinates on admin_init, saving PHP execution time
    remove_action('admin_init', 'woffice_update_membermaps_coordinates');
}, 1);

// Override the incredibly slow Woffice function that causes ~450ms TTFB lag on every page
// by doing a full table scan LIKE query for attachment URLs
if (!function_exists('woffice_get_attachment_id_by_url')) {
    function woffice_get_attachment_id_by_url($url) {
        if(function_exists('ttfb_log_time')) ttfb_log_time('attachment_id_called');
        
        $cache_key = 'wof_att_' . md5($url);
        $attachment_id = get_transient($cache_key);
        
        if ($attachment_id !== false) {
            if(function_exists('ttfb_log_time')) ttfb_log_time('attachment_id_cached');
            return $attachment_id ? $attachment_id : null;
        }
        
        // Use native WordPress function first
        $attachment_id = attachment_url_to_postid($url);
        
        if (!$attachment_id) {
            if(function_exists('ttfb_log_time')) ttfb_log_time('attachment_id_slow_query');
            // Fallback to the slow query, but optimized with LIMIT 1 and cached
            global $wpdb;
            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid LIKE %s LIMIT 1", '%' . $wpdb->esc_like($url)));
            $attachment_id = !empty($attachment) ? $attachment[0] : 0;
        }
        
        set_transient($cache_key, $attachment_id, MONTH_IN_SECONDS);
        
        if(function_exists('ttfb_log_time')) ttfb_log_time('attachment_id_done');
        return $attachment_id ? $attachment_id : null;
    }
}

// Override woffice_get_page_by_title to add caching (fixes TTFB lag when login.php looks up 'Login' page on every request)
if (!function_exists('woffice_get_page_by_title')) {
    function woffice_get_page_by_title($title) {
        $cache_key = 'wof_pg_titl_' . md5($title);
        $page = get_transient($cache_key);
        if ($page !== false) {
            return $page ? $page : null;
        }

        $query = new WP_Query(
            array(
                'post_type'              => 'page',
                'title'                  => $title,
                'post_status'            => 'all',
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'orderby'                => 'post_date',
                'order'                  => 'ASC',
            )
        );
         
        $page_got_by_title = !empty($query->post) ? $query->post : null;
        set_transient($cache_key, $page_got_by_title ? $page_got_by_title : 0, MONTH_IN_SECONDS);

        return $page_got_by_title;
    }
}

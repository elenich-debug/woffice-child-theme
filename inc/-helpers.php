<?php
/**
 * This file contains all functions used by Woffice.
 * They're available in any PHP script loaded when the Woffice theme is enabled
 * All functions can be overwritten by a child theme
 * @author Xtendify
 */

if(!function_exists('woffice_get_settings_option')) {
	/**
	 * Get an option from the theme settings
	 *
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_settings_option($option, $default = ''){

		$option_value = (function_exists( 'fw_get_db_settings_option' )) ? fw_get_db_settings_option($option) : $default;

        /**
         * Overrides the value returned from the function woffice_get_settings_option($option)
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_get_settings_option', $option_value, $option, $default );

	}
}

/**
 * This file contains all functions used by Woffice.
 * They're available in any PHP script loaded when the Woffice theme is enabled
 * All functions can be overwritten by a child theme
 * @author Xtendify
 */

 if(!function_exists('woffice_get_theming_option')) {
	/**
	 * Get an option from the theme settings
	 *
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_theming_option($option, $default = ''){
        
        $theme_settings_options = get_option('woffice_theme_options');

		$option_value = isset($theme_settings_options[$option]) ? $theme_settings_options[$option] : $default;

        /**
         * Overrides the value returned from the function woffice_get_theming_option($option)
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_get_theming_option', $option_value, $option, $default );

	}
}

/**
 * This file contains all functions used by Woffice.
 * They're available in any PHP script loaded when the Woffice theme is enabled
 * All functions can be overwritten by a child theme
 * @author Xtendify
 */

 if(!function_exists('woffice_get_reduxsettings_option')) {
	/**
	 * Get an option from the theme settings
	 *
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_reduxsettings_option($option, $default = ''){

        $theme_settings_options = get_option('woffice_theme_options');
        
        $option_value = isset($theme_settings_options[$option]) ? $theme_settings_options[$option] : false;

        /**
         * Overrides the value returned from the function woffice_get_reduxsettings_option($option)
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_get_reduxsettings_option', $option_value, $option, $default );

	}
}

/**
 * Validate boolean from redux framework
 * @author Xtendify
 */

 if(!function_exists('woffice_validate_bool_option')) {
	/**
	 * Get an option from the theme settings
	 *
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_validate_bool_option($option, $default = ''){

        $theme_settings_options = get_option('woffice_theme_options');
        
        $option_value = !empty($option) ? filter_var($option, FILTER_VALIDATE_BOOLEAN) : false;

        /**
         * Overrides the value returned from the function woffice_get_reduxsettings_option($option)
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_validate_bool_option', $option_value, $option, $default );

	}
}

/**
 * Validate boolean from redux framework
 * @author Xtendify
 */

 if(!function_exists('woffice_convert_to_bool_option')) {
	/**
	 * Get an option from the theme settings
	 *
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_convert_to_bool_option($value, $default = ''){

        
        $option_value = !empty($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : false;

        /**
         * Overrides the value returned from the function woffice_get_reduxsettings_option($option)
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_convert_to_bool_option', $option_value,$default );

	}
}

if(!function_exists('woffice_get_post_option')) {
	/**
	 * Get the option from Unyson meta for a given post
	 *
	 * @param int $post_id
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_post_option($post_id, $option, $default = ''){

	    $post_type = get_post_type( $post_id );

		if( woffice_bp_is_buddypress() && $post_type == 'page' ) {
			$bp_post_id = woffice_get_relative_current_buddypress_page_id( true );

			if( $bp_post_id ) {
				$post_id = $bp_post_id;
			}
		}

		$option_value = ( function_exists( 'fw_get_db_post_option' ) ) ? fw_get_db_post_option($post_id, $option) : $default;

        /**
         * Overrides returned from the function woffice_get_post_option( $post_id, $option )
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param int $post_id - the post ID
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_get_post_option', $option_value, $post_id, $option, $default );

	}
}

if(!function_exists('woffice_get_post_rdx_option')) {
	/**
	 * Get the option from Unyson meta for a given post
	 *
	 * @param int $post_id
	 * @param string $option
	 * @param string $default
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_post_rdx_option($post_id,$option,$default = ''){

	    $post_type = get_post_type( $post_id );

		if( woffice_bp_is_buddypress() && $post_type == 'page' ) {
			$bp_post_id = woffice_get_relative_current_buddypress_page_id( true );

			if( $bp_post_id ) {
				$post_id = $bp_post_id;
			}
		}


		$option_value = ( function_exists( 'get_post_meta' ) ) ? get_post_meta($post_id, $option,true) : $default;

        /**
         * Overrides returned from the function woffice_get_post_rdx_option( $post_id, $option )
         *
         * @see woffice/inc/helpers.php
         *
         * @param mixed $option_value - the value returned by the database
         * @param int $post_id - the post ID
         * @param string $option - the option name
         * @param mixed $default - the default value
         */
		return apply_filters( 'woffice_get_post_rdx_option', $option_value, $post_id, $option, $default );

	}
}

if(!function_exists('woffice_is_user_allowed')) {
    /**
     * Check whether a visitor is allowed or not for a certain post
     *
     * @param null $post_id
     * @return bool|mixed
     */
    function woffice_is_user_allowed($post_id = null)
    {
        if (is_null($post_id)) {
	        $post_id = get_the_ID();
        }
        $the_user_role = '';
	    $post_type = get_post_type($post_id);

	    if ($post_type != 'post'
	        && $post_type != 'page'
	        && $post_type != 'directory'
	        && $post_type != 'wiki'
	        && $post_type != 'project'
	    ) {
		    return true;
	    }

        $user_ID = get_current_user_id();

        if ($user_ID == get_post_field( 'post_author', $post_id )) {
            return true;
        }

	    if ($post_type == 'project' && function_exists( 'woffice_is_user_allowed_projects' )) {
            /**
             * This filter is documented below
             */
            return apply_filters( 'woffice_is_user_allowed', woffice_is_user_allowed_projects( $post_id ) );
        } else if ( $post_type == 'wiki' && function_exists( 'woffice_is_user_allowed_wiki' )) {
            /**
             * This filter is documented below
             */
            return apply_filters( 'woffice_is_user_allowed', woffice_is_user_allowed_wiki( $post_id ) );
        }

        /* Fetch data from options both settings & post options */
        $exclude_members = woffice_get_post_rdx_option( $post_id, 'exclude_members');
        $exclude_roles = woffice_get_post_rdx_option( $post_id, 'exclude_roles');
        $logged_only = woffice_get_post_rdx_option( $post_id, 'logged_only');
        if (empty($logged_only)) {
            $logged_only = false;
        }

        $is_allowed = true;
        $is_user_logged_in = is_user_logged_in();

        /* We start by checking if the member is logged in and if the page allow you to view the content*/
        if (
                $logged_only == true
                && !$is_user_logged_in
                ||
                $is_user_logged_in
                && !current_user_can('woffice_read_wikies')
                && !current_user_can('woffice_read_private_wikies')
                && is_page_template('page-templates/wiki.php')
        ) {
	        $is_allowed = false;
        } else {
	        /* We check now if the user is excluded */
	        $member_allowed = true;
	        if ( ! empty( $exclude_members ) ) :
		        foreach ( $exclude_members as $exclude_member ) {
			        if ( $exclude_member == $user_ID ):
				        $member_allowed = false;
			        endif;
		        }
	        endif;
	        /* We check now if the role is excluded */
	        $role_allowed = true;
	        if ( ! empty( $exclude_roles ) ) :
		        $user = wp_get_current_user();
		        /* Thanks to BuddyPress we only keep the main role */
		        $the_user_role = (is_array($user->roles) && isset($user->roles[0])) ? $user->roles[0] : $user->roles;

		        /* We check if it's in the array, OR if it's the administrator  */
		        if ( in_array( $the_user_role, $exclude_roles ) && $the_user_role != "administrator") {
			        $role_allowed = false;
		        }
	        endif;
	        /*We check the results*/
	        if ( $role_allowed == false || $member_allowed == false ||  $the_user_role != "administrator" && !empty($exclude_roles) && in_array('nope',$exclude_roles) ) {
		        $is_allowed = false;
	        }
        }

        /**
         * Filter the value returned from the function woffice_is_user_allowed( $post_id )
         *
         * @see woffice/inc/helpers.php
         *
         * @param boolean $is_allowed - the current state
         * @param int $user_ID - the current user ID
         * @param int $post_id - the post ID
         */
        $is_allowed = apply_filters( 'woffice_is_user_allowed', $is_allowed, $user_ID, $post_id );

        return $is_allowed;
    }
}

if( ! function_exists('woffice_alerts_render') ) {
    /**
     * Render current alerts
     *
     * @return void
     */
    function woffice_alerts_render() {

        $woffice_alerts = isset($_SESSION['woffice_alerts']) ? $_SESSION['woffice_alerts'] : null;

        if(empty($woffice_alerts))
            return;

        ?>

        <div id="woffice-alerts-wrapper">

            <?php foreach ($woffice_alerts as $alert) : ?>

                <?php
                // Get our object back
                if (!$alert instanceof Woffice_Alert)
                    $alert = unserialize($alert);

                // Only if there is some content
                if (!isset($alert->content) || !isset($alert->type) || !isset($alert->id)) {
                    return;
                }

                // Icon
                if($alert->type == "success") {
                    $icon_class = "fa-check-circle";
                } else if ($alert->type == "info" || $alert->type == "updated") {
                    $icon_class = "fa-info-circle";
                } else if ($alert->type == "notice") {
                    $icon_class = "fa-comments";
                } else {
                    $icon_class = "fa-exclamation-circle";
                }

                $timeout_class = 'no-timeout';

                if (isset($alert->timing) && !empty($alert->timing))
                    $timeout_class = $alert->timing;

                ?>

                <div id="woffice-alert-<?php echo sanitize_html_class($alert->id); ?>"
                     class="woffice-main-alert clearfix woffice-alert-<?php echo sanitize_html_class($alert->type); ?> <?php echo sanitize_html_class($timeout_class); ?>">

                    <div class="container">

                        <p>
                            <i class="fa <?php echo esc_attr($icon_class); ?>"></i>
                            <?php echo wp_kses_post($alert->content); ?>
                        </p>
                        <a href="javascript:void(0)" class="woffice-alert-close float-right"><i class="fas fa-times"></i></a>

                    </div>

                </div>

                <?php
                // We remove it from the queue
                Woffice_Alert::remove($alert->id);
                ?>

            <?php endforeach; ?>

        </div>

        <?php

    }
}

if(!function_exists('woffice_get_attachment_id_by_url')) {
    /**
     * Return attachment's ID from its URL
     *
     * @param $url
     * @return mixed
     */
    function woffice_get_attachment_id_by_url($url)
    {

        global $wpdb;
        $attachment = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%".$url."';");


        if (isset($attachment[0]) && !empty($attachment[0])) {
            return $attachment[0];
        } else {
            return null;
        }

    }
}

if(!function_exists('woffice_put_revslider')) {
    /**
     * Display the HTML markup for the title bar in Woffice pages
     * It handles features images, BP titles, breadcrumbs, search bars, archives...
     *
     * @param string $title - the headline title
     * @return void - the markup
     */
    function woffice_put_revslider() {
        // The current post slug
        $theme_settings_options = get_option('woffice_theme_options');
        $show_title_box = isset($theme_settings_options['show_title_box']) ? filter_var($theme_settings_options['show_title_box'], FILTER_VALIDATE_BOOLEAN) : false;
        if($show_title_box){
            return;
        }
	    $page_id      = (is_home() && get_option('page_for_posts')) ? get_option('page_for_posts') : get_the_ID();
        $slider_featured = '';
	    if ((function_exists('bp_is_groups_directory') && bp_is_groups_directory() )
	         || (function_exists('bp_is_members_directory') && bp_is_members_directory() )
	         || (function_exists('bp_is_activity_directory') && bp_is_activity_directory() )
	         || is_page()
	         || is_singular('post')
	         || (is_home() && get_option('page_for_posts'))
	    ) {

		    if (woffice_bp_is_buddypress()) {
                $bp_post_id = woffice_get_relative_current_buddypress_page_id();

                if ($bp_post_id) {
                    $page_id = $bp_post_id;
                }
		    }

		    $slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
	    }
        if(isset( $slider_featured) && !empty( $slider_featured)){
            echo '<!-- START FEATURED IMAGE AND TITLE -->';
            echo '<header id="featuredbox" class="centered has-rev-slider">';
            if (function_exists('putRevSlider')) {
                putRevSlider($slider_featured);
            }
            echo '</header>';
        }
    }
}

if(!function_exists('woffice_title')) {
    /**
     * Display the HTML markup for the title bar in Woffice pages
     * It handles features images, BP titles, breadcrumbs, search bars, archives...
     *
     * @param string $title - the headline title
     * @return void - the markup
     */
    function woffice_title($title) {
        /*
         * 1. WE SET THE CLASS
         */
        global $post;
        $theme_settings_options = get_option('woffice_theme_options');
        $show_title_box = isset($theme_settings_options['show_title_box']) ? filter_var($theme_settings_options['show_title_box'], FILTER_VALIDATE_BOOLEAN) : false;
        $show_buddypress_cover = woffice_get_theming_option('show_buddypress_cover');
        $is_bp_avatar = '';
        if(function_exists('bp_is_user') && bp_is_user() && !woffice_validate_bool_option($show_buddypress_cover) && !$show_title_box) {
            return;
        } else if (!function_exists('bp_is_user') && !$show_title_box || function_exists('bp_is_user') && !bp_is_user() && !$show_title_box  || function_exists('bp_is_user') && bp_is_user() && !woffice_validate_bool_option($show_buddypress_cover)){
            return;
        }
        
        // Check if the user is supposed to be here
	    $is_user_allowed   = woffice_is_user_allowed();
	    $displayed_user_id = (function_exists('bp_displayed_user_id')) ? bp_displayed_user_id() : 0;
	    $show_layer        = true;
        $theme_skin = woffice_get_settings_option('theme_skin');
        if (function_exists('is_bbpress') && is_bbpress()) {
            if (function_exists('bp_is_active') && bp_is_user()){
                $is_forum_page = false;
            } else {
                $is_forum_page = true;
            }
        } else {
            $is_forum_page = false;
        }

        // The current post slug
	    $page_id      = (is_home() && get_option('page_for_posts')) ? get_option('page_for_posts') : get_the_ID();

	    // We check for Revolution Sliders
	    $slider_featured = '';
	    if ((function_exists('bp_is_groups_directory') && bp_is_groups_directory() )
	         || (function_exists('bp_is_members_directory') && bp_is_members_directory() )
	         || (function_exists('bp_is_activity_directory') && bp_is_activity_directory() )
	         || is_page()
	         || is_singular('post')
	         || (is_home() && get_option('page_for_posts'))
	    ) {

		    if (woffice_bp_is_buddypress()) {
                $bp_post_id = woffice_get_relative_current_buddypress_page_id();

                if ($bp_post_id) {
                    $page_id = $bp_post_id;
                }
		    }

		    $slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
	    }

        /**
         * Filter if the map is enabled on the current page
         *
         * @param bool
         */
        $map_enabled = (is_page_template("page-templates/page-directory.php") || is_tax('directory-category')) && $is_user_allowed && empty($slider_featured);
        $map_enabled = apply_filters( 'woffice_directory_page_map_enabled', $map_enabled);

        // Template check
        if (is_search() || is_404() || is_page_template("page-templates/wiki.php") || is_page_template("page-templates/projects.php") || $is_forum_page) {
	        $title_class = "has-search is-404";
        } elseif ($map_enabled) {
	        $title_class = "directory-header";
        } else {
	        $title_class = "";
        }

        $post_top_featured = woffice_get_post_rdx_option( get_the_ID(), 'post_top_featured');
        $hide_titlebox_for_page = woffice_get_post_rdx_option( get_the_ID(), 'hide_titlebox_for_page' );
        if($hide_titlebox_for_page) {
            return; 
        }
        /*
         * 2. HTML
         */
	    echo '<!-- START FEATURED IMAGE AND TITLE -->';
        echo '<header id="featuredbox" class="centered ' . esc_attr($title_class) . '">';

	    /**
	     * Before render the markup of the title box (the header containing the big title and the parallax image)
	     */
	    do_action('woffice_titlebox_before');

        if (empty($slider_featured) || (!shortcode_exists('rev_slider') && !class_exists('FW_Extension_Slider'))) {

	        echo '<div class="pagetitle animate-me fadeIn">';

	        if (
	                function_exists('woffice_upload_cover_btn') &&
	                $displayed_user_id !== 0 &&
	                ($displayed_user_id === get_current_user_id() || current_user_can('administrator'))
            ) {
		       echo woffice_upload_cover_btn();
	        }

	        $has_title = woffice_convert_to_bool_option(woffice_get_post_rdx_option( get_the_ID(), 'hastitle' ));
	        if (!$has_title) {

		        if (is_singular('post')) {
			        // See: https://alkaweb.ticksy.com/ticket/539682
			        if (function_exists( 'bp_is_active' ) && bp_is_user()) {
				        $title = woffice_get_name_to_display( bp_get_displayed_user() );
			        } else {
				        $title = get_the_title();
			        }
		        }

		        if (function_exists('tribe_is_month') && tribe_is_month()) {
		            $title = __('Events Calendar', 'woffice');
                }

                /**
                 * Changes the Title's content
                 *
                 * @param string $title
                 */
                $filtered_title = apply_filters('woffice_page_title_title', $title);

                if(is_tax()){
		            echo '<h1 class="entry-title">' . single_cat_title('',false) . '</h1>';
                } else {
                    echo '<h1 class="entry-title">' . $filtered_title . '</h1>';
                }

		        // CHECK FOR BREADCRUMB
		        if (
		                !is_front_page() &&
                        function_exists('fw_ext_breadcrumbs')
                ) {

		            if ((function_exists('is_woocommerce') && is_woocommerce()) || (function_exists('is_product') && is_product())) {
			            woocommerce_breadcrumb( array(
				            'delimiter'   => '',
				            'wrap_before' => '<div class="woobread breadcrumbs" itemprop="breadcrumb">',
				            'wrap_after'  => '</div>',
				            'before'      => '<span>',
				            'after'       => '</span>',
			            ) );
                    } else {
			            fw_ext_breadcrumbs();
		            }
		        }

		        // SINGULAR WIKI PAGE -> WE DISPLAY PARENT LINK
		        if ( is_singular( 'wiki' ) ) {
			        echo empty( $post->post_parent ) ? '' : get_the_title( $post->post_parent );
		        }
	        }

            if (is_search() || is_404() || is_page_template("page-templates/wiki.php") || is_page_template("page-templates/projects.php") || $is_forum_page) {
                if ($is_forum_page){
                    echo do_shortcode('[bbp-search]');
                } else {
                    get_search_form();
                }
            } else {
                do_action('woffice_header_no_search');
            }

            // Directory checks
            if ($map_enabled) {
                echo '<div class="title-box-buttons">';
                echo '<div id="directory-search">';
                get_search_form();
                echo '</div>';
                echo '<a href="javascript:void(0)" class="btn btn-default d-block mb-0" id="directory-show-search"><i class="fa fa-search"></i> ' . __('Search', 'woffice') . '</a>';
                echo '</div>';
            }

	        echo '</div><!-- .pagetitle -->';

            if (
                (
                    (function_exists('bp_is_members_directory') && bp_is_members_directory())
                    || (function_exists('bp_is_groups_directory') && bp_is_groups_directory() )
                    || (function_exists('bp_is_activity_directory') && bp_is_activity_directory() )
                    || is_page()
                    || is_singular( array('directory', 'project', 'wiki') )
                )
                 && !is_tax('directory-category')
            ) {
	            $image = wp_get_attachment_image_src(get_post_thumbnail_id($page_id), 'full');
	            if (!empty($image)) {
		            $final_image = $image[0];
	            } else {
                    
                }
                $buddypress_cover_url = '';
	            if ($displayed_user_id !== 0) {
		            $woffice_cover_url    = woffice_get_cover_image($displayed_user_id);
		            $buddypress_cover_url = woffice_get_bp_member_cover($displayed_user_id);

		            if (!empty($woffice_cover_url) && function_exists('woffice_upload_cover_btn')) {
			            $final_image = $woffice_cover_url;
			            $show_layer  = false;
                        $is_bp_avatar = ' is-bp-avatar';
                    } elseif (!empty($buddypress_cover_url)) {
			            $final_image = $buddypress_cover_url;
			            $show_layer  = false;
                    }
	            }

	            if (function_exists('bp_is_groups_component') && bp_is_single_item() && bp_is_groups_component()) {
	                global $bp;
		            $group_cover_image_url = bp_attachments_get_attachment('url', array(
			            'object_dir' => 'groups',
			            'item_id' => $bp->groups->current_group->id,
		            ));
                    if (!empty($group_cover_image_url)) {
                        $final_image = $group_cover_image_url;
	                    $show_layer  = false;
                    }
                }

            }  elseif (is_single() && !empty($post_top_featured)) {
	            $final_image = $post_top_featured["url"];
            } else {
	            if (is_home() && get_option( 'page_for_posts' )) {
		            $image       = wp_get_attachment_image_src( get_post_thumbnail_id( get_option( 'page_for_posts' ) ), 'full' );
		            $final_image = $image[0];
	            }
            }

            // Use the sidewide image set in Theme Settings
	        if (empty($final_image)) {
		        $main_featured_image = isset($theme_settings_options['title_boxbg_image']['background-image']) && !empty($theme_settings_options['title_boxbg_image']['background-image']) ? $theme_settings_options['title_boxbg_image']['background-image'] : get_template_directory_uri() . '/images/1.jpg';
		        $main_featured_image = (is_array($main_featured_image)) ? $main_featured_image : array('url' => $main_featured_image);

		        if (!empty( $main_featured_image ) && array_key_exists('url', $main_featured_image)) {
			        $final_image = $main_featured_image['url'];
		        }
	        }

            // If is enabled the map on the current page, disable the background featured image and display the map
            if ($map_enabled) {
	            $final_image = false;
	            echo '<div id="map-directory"></div>';
            }

            // Print some CSS for a better responsive performance
	        if (!empty($final_image)) {
		        $attachment = woffice_get_attachment_id_by_url($final_image );
		        woffice_print_css_breakpoints_for_image_loading($attachment);
	        }

            if(is_buddypress() && empty($buddypress_cover_url) && $displayed_user_id !== 0 && woffice_validate_bool_option($show_buddypress_cover)) {
                $default_cover = woffice_get_theming_option('buddypress_default_cover_image');
                $default_image = isset($default_cover['background-image']) ? $default_cover['background-image'] : '';
                $final_image = $default_image;                
            }

            // Display the background image, if present
	        $background_style = (!empty($final_image)) ? 'style="background-image: url(' . esc_url($final_image) .')";' : '';

	        $layer_display_class = ($show_layer) ? 'd-block' : 'd-none';
            echo '<div class="featured-background '.$is_bp_avatar.'" '. $background_style .'><div class="featured-layer"></div></div>';

        } else {

            // We look for an Unyson slider first (an numerical post id and not a revolution slider slug
            if (is_numeric($slider_featured)){
                /**
                 * Woffice Title Unyson slider shortcode
                 *
                 * @param string - the shortcode
                 */
	            $revolution_slider_shortcode = apply_filters('woffice_unyson_slider_shortcode', '[slider slider_id="'.$slider_featured.'" width="1200" height="auto" /]', $slider_featured);
                echo do_shortcode($revolution_slider_shortcode);
            } else{
                if (function_exists('putRevSlider')) {
                    putRevSlider($slider_featured);
                }
            }

        }

	    /**
	     * After rendered the markup of the title box (the header containing the big title and the parallax image)
	     */
	    do_action('woffice_titlebox_after');

        echo '</header>';
    }
}
if(!function_exists('woffice_print_css_breakpoints_for_image_loading')) {
    /**
     * Woffice CSS breakpoint for featured image
     * So we optimize which kind of image is loaded according to the device's width
     *
     * @param $attachment_id
     * @return void - the CSS
     */
	function woffice_print_css_breakpoints_for_image_loading($attachment_id){
		if(is_null($attachment_id))
			return;

		// Full
		$image_full_url = wp_get_attachment_url(  $attachment_id ) ;

		// 800x600?
		$image_large_url = wp_get_attachment_image_src( $attachment_id, array(800,600) );

		//Set breakpoints to see a different image depending on window size and retina display
		echo '<style>
	        @media screen and (max-width: 800px) {
		        .featured-background {
			        background-image: url("'.$image_large_url[0].'") !important;
				  background-image: 
				    -webkit-image-set(
					    "'.$image_large_url[0].'" 1x,
				      "'.$image_full_url.'" 2x,
				    ) !important;
				  background-image: 
				    image-set(
					    "'.$image_large_url[0].'" 1x,
				      "'.$image_full_url.'" 2x,
				    ) !important;
				}
	        }
			</style>';

	}
}

if(!function_exists('woffice_sort_objects_by_name')) {
    /**
     * Binary safe string comparison
     *
     * @link http://php.net/manual/en/function.strcmp.php
     * @param $a
     * @param $b
     * @return int
     */
    function woffice_sort_objects_by_name($a, $b)
    {
        return strcmp($a->name, $b->name);
    }
}

if(!function_exists('woffice_sort_objects_by_post_title')) {
    /**
     * Binary safe string comparison between posts
     *
     * @link http://php.net/manual/en/function.strcmp.php
     * @param $a
     * @param $b
     * @return int
     */
    function woffice_sort_objects_by_post_title($a, $b)
    {
        return strcmp($a->post_title, $b->post_title);
    }
}

if(!function_exists('woffice_paging_nav')) {
    /**
     * Creating custom pagination for Woffice
     *
     * @param null $custom_query
     * @return null|void
     */
    function woffice_paging_nav($custom_query = null)
    {
        if(is_singular() && is_null($custom_query))
            return;

        if(is_null($custom_query)) {
            global $wp_query;
            $custom_query = $wp_query;
        }

        $total_pages = $custom_query->max_num_pages;

        /** Stop execution if there's only 1 page */
        if ($total_pages <= 1)
            return;

        $max = intval($total_pages);
        global $paged;
        $paged = (empty($paged)) ? 1 : $paged;


        /**    Add current page to the array */
        if ($paged >= 1)
            $links[] = $paged;

        /**    Add the pages around the current page to the array */
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if (($paged + 2) <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }

        echo '<div class="blog-next-page text-right celst-pagination">' . "\n";

        echo '<ul class="navigation clearfix">' . "\n";

        /**    Previous Post Link */
        if ($paged > 1) {
            $previous_posts_link = explode('"', get_previous_posts_link());
            $npl_url = $previous_posts_link[1];
            // echo '<li><a href="' . esc_url($previous_posts_link[1]) . '" class="btn btn-default"><i class="fa fa-hand-point-left"></i> ' . __('Previous Posts', 'woffice') . '</a></li>';
            echo '<li class="page-nav-left"><a href="' . esc_url($previous_posts_link[1]) . '" class="btn"><i class="fa fa-arrow-left"></i> </a></li>';
        }

        /**    Link to first page, plus ellipses if necessary */
        if (!in_array(1, $links)) {
            $class = (1 == $paged) ? ' class="active"' : '';

            printf('<li%s><a class="btn btn-default" href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

            if (!in_array(2, $links))
                echo '<li><span class="btn btn-default disabled">...</span></li>';
        }

        /**    Link to current page, plus 2 pages in either direction if necessary */
        sort($links);
        foreach ((array)$links as $link) {
            $class = $paged == $link ? ' class="active"' : '';
            printf('<li%s><a class="btn btn-default" href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
        }

        /**    Link to last page, plus ellipses if necessary */
        if (!in_array($max, $links)) {
            if (!in_array($max - 1, $links))
                echo '<li><span class="btn btn-default disabled">...</span></li>' . "\n";

            $class = $paged == $max ? ' class="active"' : '';
            printf('<li%s><a class="btn btn-default" href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
        }

        /**    Next Post Link */
        if ($paged < $max) {
            $next_posts_link = explode('"', get_next_posts_link('', $max));
            // echo '<li><a href="' . esc_url($next_posts_link[1]) . '" class="btn btn-default">' . __('Next Posts', 'woffice') . ' <i class="fa fa-hand-point-right"></i></a></li>';
            echo '<li class="page-nav-right"><a href="' . esc_url($next_posts_link[1]) . '" class="btn"><i class="fa fa-arrow-right"></i></a></li>';
        }

        echo '</ul>' . "\n";

        echo '</div>' . "\n";
    }
}

if(!function_exists('woffice_post_nav')) {
    /**
     * Custom post navigation for Woffice
     *
     * @return void
     */
    function woffice_post_nav()
    {

        // Don't print empty markup if there's nowhere to navigate.
        $previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '',true);
        $next = get_adjacent_post(false, '', false);
        if (!$next && !$previous)
            return;

        echo '
		<hr><div class="blog-next-page center animate-me fadeInUp" role="navigation">' . "\n";
        ob_start();
        previous_post_link('%link',
            __('<i class="fa fa-hand-point-left"></i> %title', 'woffice'));
        next_post_link('%link',
            __('%title <i class="fa fa-hand-point-right"></i>', 'woffice'));
        $link = ob_get_clean();
        echo str_replace('<a ', '<a class="btn btn-default" ', $link);


        echo '</div>' . "\n";
	    wp_reset_query();

    }
}

if(!function_exists('woffice_postmetas')) {
    /**
     * Handling Woffice post metadatas
     * That's the information that goes with any WP post by default
     *
     * @return void
     */
    function woffice_postmetas()
    {
        echo '<ul class="post-metadatas list-inline">';
        echo '<li class="updated published list-inline-item"><i class="woffice-icon woffice-icon-watch"></i> ' . get_the_date() . '</li>';
        if(!empty(get_the_author())){
            echo '<li class="list-inline-item"><span class="author vcard"><i class="woffice-icon woffice-icon-blog-user"></i><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span></li>';
        }
        if (get_comment_count(get_the_ID()) > 0 && comments_open()) {
            echo '<li class="list-inline-item"><i class="woffice-icon woffice-icon-comment"></i> <a href="' . get_the_permalink() . '#respond">' . get_comments_number('0', '1', '%') . '</a></li>';
        }
        if (get_the_category_list() != "") {
            echo '<li class="list-inline-item list_catg"><i class="woffice-icon woffice-icon-stack"></i> ' . get_the_category_list(__(',', 'woffice')) . '</li>';
        }
        if (get_the_tag_list() != "") {
            echo '<li class="meta-tags list-inline-item"><i class="woffice-icon woffice-icon-pin"></i> ' . get_the_tag_list('', __(', ', 'woffice')) . '</li>';
        }
        echo '</ul>';
    }
}
if(!function_exists('woffice_celestial_postmetas')) {
    /**
     * Handling Woffice post metadatas
     * That's the information that goes with any WP post by default
     *
     * @return void
     */
    function woffice_celestial_postmetas()
    {
        echo '<ul class="post-metadatas">';
        echo '<li class="date-li"> ' . get_the_date() . '</li>';
        if(!empty(get_the_author())){
            echo '<li class="author-li"><span class="author"><span class="author-name-prefix">By:</span><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span></li>';
        }
        if (get_comment_count(get_the_ID()) > 0 && comments_open()) {
            echo '<li class="comment-li"><i class="woffice-icon woffice-icon-celst-blog-comment"></i> <a href="' . get_the_permalink() . '#respond">' . get_comments_number('0', '1', '%') . '</a></li>';
        }
        if (get_the_category_list() != "") {
            echo '<li class="list_catg"><i class="woffice-icon woffice-icon-stack"></i> ' . get_the_category_list(__(', ', 'woffice')) . '</li>';
        }
        if (get_the_tag_list() != "") {
            echo '<li class="meta-tags"><i class="woffice-icon woffice-icon-pin"></i> ' . get_the_tag_list('', __(', ', 'woffice')) . '</li>';
        }
        echo '</ul>';
    }
}

if(!function_exists('woffice_favicons')) {
    /**
     * Displaying Favicons HTML markup
     *
     * @return void
     */
    function woffice_favicons()
    {
        $favicon = woffice_get_theming_option('favicon');

        if(isset($favicon['url'])){
            echo (!empty($favicon)) ? '<link rel="shortcut icon" type="image/png" href="' . esc_url($favicon['url']) . '">' : '';
        }

        $theme_settings_options = get_option('woffice_theme_options');
        $favicon_android_1 = woffice_get_theming_option('favicon_android_1');
        $favicon_android_2 = woffice_get_theming_option('favicon_android_2');
        
        if (!empty($favicon_android_1) || !empty($favicon_android_2)):
            echo '<link rel="manifest" href="' . get_template_directory_uri() . '/js/manifest.json">';
        endif;
        $favicon_iphone = woffice_get_theming_option('favicon_iphone');
        echo (!empty($favicon_iphone)) ? '<link rel="apple-touch-icon" sizes="114x114" href="' . esc_url($favicon_iphone['url']) . '">' : '';

     
        $favicon_ipad = woffice_get_theming_option('favicon_ipad');
        echo (!empty($favicon_ipad)) ? '<link rel="apple-touch-icon" sizes="144x144" href="' . esc_url($favicon_ipad['url']) . '">' : '';
    }
}

if(!function_exists('woffice_scroll_top')) {
    /**
     * Display HTML markup for the scroll to the top button
     *
     * @return void
     */
    function woffice_scroll_top()
    {
        $sidebar_scroll = woffice_get_theming_option('sidebar_scroll');
        $is_blank_template = woffice_is_current_page_using_blank_template();

        if ($sidebar_scroll == "yep" && !$is_blank_template) {
            ?>
            <!--SCROLL TOP-->
            <div id="scroll-top-container">
                <a href="#main-header" id="scroll-top">
                    <i class="fa fa-arrow-circle-up"></i>
                </a>
            </div>
            <?php
        }
    }
}

if(!function_exists('woffice_language_switcher')) {
    /**
     * Language switcher HTML markup for WPML
     *
     * @return void
     */
    function woffice_language_switcher()
    {

        // IF IS WPML ENABLE
        if (class_exists('SitePress')) {
            function getActiveLanguage()
            {
                // fetches the list of languages
                $languages = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR');
                $activeLanguage = 'Englsih';
                // runs through the languages of the system, finding the active language
                foreach ($languages as $language) {
                    // tests if the language is the active one
                    if ($language['active'] == 1) {
                        $activeLanguage = $language['native_name'];
                    }
                }
                return $activeLanguage;
            }

            $languages = icl_get_languages('skip_missing=0&orderby=code');
            if (!empty($languages)) {
                echo '<div id="nav-languages">';
                echo '<a href="javascript:void(0)">
	    		<i class="fa fa-flag"></i><em>' . getActiveLanguage() . '</em> <i class="fa fa-angle-down"></i>
	    	</a>';
                echo '<ul>';
                foreach ($languages as $l) {
                    if (!$l['active'] == 1) {
                        echo '<li class="menu-item"><a href="' . esc_url($l['url']) . '">' . esc_html($l['translated_name']) . '</a></li>';
                    }
                }
                echo '</ul></div>';
            }
        } // ELSE IF TRANSLATE UNYSON EXTENSION IS HERE
        elseif (function_exists('fw_ext_translation_get_frontend_active_language')) {
            echo '<div id="nav-languages">
		<a href="javascript:void(0)">
			<i class="fa fa-flag"></i>' . esc_html(fw_ext('translation')->get_frontend_active_language()) . ' <i class="fa fa-angle-down"></i>
		</a>';
            fw_ext('translation')->frontend_language_switcher();
            echo '</div>';
        } // ELSE RETURN NOTHING
        else {
            return;
        }
    }
}


if (!function_exists('woffice_get_sidebar_state')) {
    /**
     * Get Woffice Right Sidebar state
     *
     * @return string : hide | show | nope
     */
    function woffice_get_sidebar_state() {

        // Get sidebar displaying options
        $sidebar_show = woffice_get_theming_option('sidebar_show');
        $sidebar_default_state = woffice_get_theming_option('sidebar_state');
        $sidebar_only_logged = woffice_get_theming_option('sidebar_only_logged');
        $blog_fullwidth = woffice_get_theming_option('blog_fullwidth',false);
        // Check if is for logged users only
        if (woffice_validate_bool_option($sidebar_only_logged) && !is_user_logged_in() || !woffice_validate_bool_option($blog_fullwidth) && get_post_type() == 'post' && !is_single()){
              return 'none';
        }

        // Check if the current pages are BuddyPress pages
	    $is_members_page = $is_groups_page = false;
        if (function_exists('bp_is_active')) {
	        $is_members_page = ( bp_is_members_component() || bp_is_user() );
	        $is_groups_page = ( woffice_bp_is_active( 'groups' ) && bp_is_groups_component() );
        }

        // Check for sidebar options from BuddyPress pages
        if ( $is_members_page || $is_groups_page ) {
            $sidebar_buddypress = woffice_get_theming_option('sidebar_buddypress');

            if ( woffice_validate_bool_option($sidebar_buddypress)) {
                $sidebar_option = 'show';

                if (!woffice_validate_bool_option($sidebar_default_state)) {
                    $sidebar_option = 'hide';
                }

            } else {
                $sidebar_option = 'none';
            }

        // Check for sidebar options from blog pages
        } else if ( is_singular( 'post' ) || is_home() || is_page_template('page-templates/blog.php') ) {
            $sidebar_blog = woffice_get_theming_option('sidebar_blog');

            if ( woffice_validate_bool_option($sidebar_blog)) {
                $sidebar_option = 'show';

                if (!woffice_validate_bool_option($sidebar_default_state)) {
                    $sidebar_option = 'hide';
                }

            } else {
                $sidebar_option = 'none';
            }

        } else if (!woffice_validate_bool_option($sidebar_show) || !is_active_sidebar('content') || is_page_template('page-templates/full-width.php')) {
            // We check then if it's hidden, not active or not the full width template
	        $sidebar_option = 'none';

        // Check for sidebar options from all other pages
        } else {

            if ( woffice_validate_bool_option($sidebar_show)) {
                $sidebar_option = 'show';

                if (!woffice_validate_bool_option($sidebar_default_state)) {
                    $sidebar_option = 'hide';
                }

            } else {
                $sidebar_option = 'none';
            }
        }

        /**
	     * Filter the value returned from the function woffice_get_sidebar_state()
	     *
	     * @see woffice/inc/helpers.php
	     *
	     * @param string $sidebar_option
	     */
	    return apply_filters( 'woffice_sidebar_state', $sidebar_option);

    }
}

if(!function_exists('validate_gravatar')) {
    /**
     * Check whether there is a Gravatar image to find or not
     * If you've many users this function will slow your server down
     *
     * @param $id_of_user
     * @return bool
     */
    function validate_gravatar($id_of_user)
    {

        //id or email code borrowed from wp-includes/pluggable.php
        $id = (int) $id_of_user;
        $user = get_userdata($id);
        $email = ($user) ? $user->user_email : '';

        $hashkey = md5(strtolower(trim($email)));
        $uri = 'http://www.gravatar.com/avatar/' . $hashkey . '?d=404';

        $data = wp_cache_get($hashkey);
        if (false === $data) {
            $response = wp_remote_head($uri);
            if (is_wp_error($response)) {
                $data = 'not200';
            } else {
                $data = $response['response']['code'];
            }
            wp_cache_set($hashkey, $data, $group = '', $expire = 60 * 5);

        }
        if ($data == '200') {
            return true;
        } else {
            //Check if link is not a gravat link, so it mean that is an avatar uploaded on site, in this case return true
            $avatar_url = get_avatar($id, 80);
            return (strpos($avatar_url, 'gravatar') === FALSE);

        }
    }
}

if(!function_exists('woffice_extrafooter')) {
    /**
     * Display the extrafooter markup in the footer
     *
     * @return void
     */
    function woffice_extrafooter()
    {
        // GET THE OPTIONS
        $extrafooter_content = woffice_get_theming_option('extrafooter_content');
        $extrafooter_link = woffice_get_theming_option('extrafooter_link');

        /**
         * Use an AJAX request to load the extra footer avatars
         *
         * @param boolean
         */
        $ajax_loading = apply_filters( 'woffice_ajax_extrafooter_enabled', true );

        $ajax_loading_attr = ($ajax_loading) ? 'data-woffice-ajax-load="true"' : '';

          // FRONTEND DISPLAY
          echo '<!-- START EXTRAFOOTER -->';
          echo '<section id="extrafooter" '.$ajax_loading_attr.'>';
          echo '<div id="extrafooter-layer" class="animate-me fadeIn" >';
          echo '<a href="' . esc_url($extrafooter_link) . '"><h1>' . $extrafooter_content . '</h1></a>';
          echo '</div>';
          echo '<div id="familiers">';

        if (!$ajax_loading)
            woffice_extrafooter_print_avatars();

          echo '</div>';
          echo '</section>';
    }
}

if(!function_exists('woffice_extrafooter_print_avatars')) {
	/**
	 * Print the HTML of the avatars of the extrafooter
	 */
	function woffice_extrafooter_print_avatars() {

		$members_ids = get_transient( 'woffice_extrafooter_member_ids' );
		$extrafooter_random             = woffice_get_theming_option( 'extrafooter_random' );

		if($members_ids && apply_filters('woffice_use_transient_in_extrafooter', true)) {
			if ( $extrafooter_random == "yep" ) {
				shuffle( $members_ids );
			}

			foreach($members_ids as $id ) {
				print get_avatar( $id, 80 );
			}
			return;
		}

		$member_ids = array();
		$extrafooter_avatar_only        = woffice_get_theming_option( 'extrafooter_avatar_only' );
		$extrafooter_repetition_allowed = woffice_get_theming_option( 'extrafooter_repetition_allowed' );

		// GET USERS
		$woffice_wp_users        = get_users( array( 'fields' => array( 'ID', 'user_url' ),'cache_results' => false ) );
		$users_already_displayed = array();

		// If is set random faces, shuffle array of users
		if ( $extrafooter_random == "yep" ) {
			shuffle( $woffice_wp_users );
		}

		/**
		 * Filter the ids of the users excluded by the extrafooter
		 *
		 * @param array
		 */
		$excluded_users = apply_filters('woffice_exclude_user_ids_from_extrafooter', array());
		$total_users = count($woffice_wp_users);

		// Do this for each user, max 100 because are not displayed more than 100 users in the extrafooter
		// $j is max counter; $x is users array index
		for ($x = 0, $j = 0; $j < 99 && $x < $total_users; $x++) {

			// Excluded users from extrafoter
			if (in_array($woffice_wp_users[$x]->ID, $excluded_users)) {
				continue;
			}

			// If repetition of faces are not allowed, display only if is not already displayed
			if ( $extrafooter_repetition_allowed == 'yep' || ! in_array( $woffice_wp_users[ $x ]->ID, $users_already_displayed ) ) {
				if ( $extrafooter_avatar_only == "yep" ) {
					if ( function_exists('bp_get_user_has_avatar') && bp_get_user_has_avatar($woffice_wp_users[ $x ]->ID)
		          || validate_gravatar( $woffice_wp_users[ $x ]->ID ) ) {
						print get_avatar( $woffice_wp_users[ $x ]->ID, 80 );
						array_push( $users_already_displayed, $woffice_wp_users[ $x ]->ID );
						array_push( $member_ids, $woffice_wp_users[ $x ]->ID );
						$j ++;
					}
				} else {
					print get_avatar( $woffice_wp_users[ $x ]->ID, 80 );
					array_push( $users_already_displayed, $woffice_wp_users[ $x ]->ID );
					array_push( $member_ids, $woffice_wp_users[ $x ]->ID );
					$j ++;
				}
			}
		}

		// If repetitive faces are allowed and it need more faces to reach 100, than get more faces randomly from already inserted user
		if ( $extrafooter_repetition_allowed == 'yep' && $j < 99 ) {
			if ( ! empty( $users_already_displayed ) ) {
				for ( $x = 0; $x < ( 99 - $j ); $x ++ ) {
					$woffice_wp_selected = $users_already_displayed[ array_rand( $users_already_displayed ) ];
					print get_avatar( $woffice_wp_selected, 80 );
					array_push( $member_ids, $woffice_wp_selected );
				}
			}
		}

		set_transient( 'woffice_extrafooter_member_ids', $member_ids, 86400 );
	}
}

if(!function_exists('woffice_get_string_between')) {
    /**
     * Get the content of a string between two substrings
     *
     * @param string $string
     * @param string $start
     * @param string $end
     * @return string
     */
    function woffice_get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}

if (!function_exists('woffice_current_is_admin')) {
    /**
     * Check if the current user is an administrator
     *
     * @return bool
     */
    function woffice_current_is_admin()
    {
        return (current_user_can('administrator')) ? true : false;
    }
}

if(!function_exists('woffice_check_meta_caps')) {
    /**
     * Check if meta caps override Woffice settings by frontend
     *
     * @param null|string $post_type
     * @return bool
     */
    function woffice_check_meta_caps($post_type = null) {

        //TODO when add here new post type available to manage by meta caps, they have to be added also in woffice_frontend_proccess(), around line 115
        if (!is_null($post_type) && ($post_type == 'wiki' || $post_type == 'post'))
            return woffice_get_theming_option('override_' . $post_type . '_by_caps', false);
        else
            return false;
    }
}

if(!function_exists('woffice_get_slug_for_meta_caps')) {
    /**
     * Return the corresponding plural slug used in meta capabilities of each post type
     *
     * @param null|string $post_type
     * @return string
     */
    function woffice_get_slug_for_meta_caps($post_type = null) {

        switch ($post_type) {
            case "wiki":
                return 'wikies';
            case 'post':
                return 'posts';
            default:
                return '';
        }

    }
}

if(!function_exists('woffice_get_children_count')) {
    /**
     * Return the number of posts inside a category (recursively)
     *
     * @param $category_id
     * @param $taxonomy
     * @param array $excluded
     * @return int
     */
    function woffice_get_children_count($category_id, $taxonomy, $excluded = array()){
        $cat = get_category($category_id);
        $count = (int) $cat->count;
        $args = array(
            'child_of' => $category_id,
            'exclude' => $excluded
        );
        $tax_terms = get_terms($taxonomy,$args);
        foreach ($tax_terms as $tax_term) {
            $count += $tax_term->count ;
        }
        return 0;
    }
}

if(!function_exists('woffice_get_name_to_display')) {
    /**
     * Get the name to user name to display according with Woffice Buddypress Settings
     *
     * @param null|object|int $user
     * @return string
     */
    function woffice_get_name_to_display($user = null)
    {
        if (is_object($user)) {
            $user_info = $user;
        } elseif (is_numeric($user)) {
            $user_info = get_userdata($user);
        } else {
	        $user_info = wp_get_current_user();
        }
        $buddy_directory_name = woffice_get_theming_option('buddy_directory_name');

        if (!isset($user_info->user_login)) {
            return 'N/A';
        }

        if($buddy_directory_name == "name" && (!empty($user_info->user_firstname) || !empty($user_info->user_lastname)) ){
            $display = $user_info->user_firstname .' '. $user_info->user_lastname;
        } else {
            $display = $user_info->user_login;
        }

        /**
         * Filter the result of the function woffice_get_name_to_display( $user )
         *
         * @see woffice/inc/helpers.php
         *
         * @param string $display The name displayed
         * @param WP_User $user_info
         */
        return esc_html( apply_filters('woffice_get_name_to_display', $display, $user_info) );
    }
}

if(!function_exists('woffice_get_adjust_brightness')){
    /**
     * Take a color and make it brighter or darker
     *
     * @link http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
     * @author Torkil Johnsen
     * @return string
     */
    function woffice_get_adjust_brightness($hex, $steps) {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}

if(!function_exists('woffice_get_navigation_state')) {
    /**
     * Return the state of the navigation default state. Return true if it is showed and false if it is hidden
     *
     * @return bool
     */
    function woffice_get_navigation_state() {
        $menu_default = woffice_get_theming_option('menu_default');
        $cookie_enabled = apply_filters( 'woffice_cookie_vertical_menu_enabled', true );

        $nav_opened_state = (isset($_COOKIE['Woffice_nav_position'])
                             && $_COOKIE['Woffice_nav_position'] == 'navigation-hidden'
                             && $cookie_enabled
                             || $menu_default == "close")
            ? false : true;

        return $nav_opened_state;
    }
}

if(!function_exists('woffice_get_navigation_class')) {
    /**
     * Return the class for the navigation default state. It compare the cookies and the them options
     *
     * @return string
     */
    function woffice_get_navigation_class() {
        $nav_opened_state = woffice_get_navigation_state();
        return (!$nav_opened_state) ? ' navigation-hidden ' : '';
    }
}

if(!function_exists('woffice_get_navigation_toggle_class')){
    function woffice_get_navigation_toggle_class(){
        $class=  "";
        $cookie_enabled = apply_filters( 'woffice_cookie_vertical_menu_enabled', true );
        $menu_default = woffice_get_theming_option('menu_default');
        if( (isset($_COOKIE['Woffice_hasnav_position']) && $_COOKIE['Woffice_hasnav_position'] == 'has-navigation-hidden' && $cookie_enabled || $menu_default == "close")) {
            $class = "has-navigation-hidden";
        }
        return $class;
    }
}

if(!function_exists('woffice_redirect_to_login')) {
    /**
     * Redirect to login page and preserve the previous page url for a potential redirect
     *
     * @param string $param the parameter to add to login page (For instance: 'type=lost-password&foo=bar')
     * @param bool $disable_redirect_to
     */
    function woffice_redirect_to_login( $param = '', $disable_redirect_to = true ) {

        // Get the login url
        $login_page_slug = woffice_get_login_page_name();
        $login_page      = esc_url( home_url( '/' . $login_page_slug . '/' ) );

        // Add other parameters if they are present
        $param = (empty($param)) ? '' : '&' . $param;
        if (!empty( $param)) {
            $param = '?' . $param;
        }

	    /**
	     * Filter `woffice_login_has_redirect_param`
         *
         * Whether we add the the redirect GET param for the redirection
         *
         * @param boolean
	     */
        $has_redirect_helper = apply_filters('woffice_login_has_redirect_param', false);

        // If previous url is set change $has_redirect_helper to true.
	    $aft_login    = woffice_get_theming_option('aft_login');
	    if ( 'previous' == $aft_login ) {
            $has_redirect_helper = true;
        }

	    if (!$disable_redirect_to && $has_redirect_helper) {
		    $http        = "http://";
		    $redirect_to = $http . woffice_get_http_host() . woffice_get_request_uri();
		    $encoded     = urlencode($redirect_to);

		    if ( strpos($encoded, 'wp-admin') === false && ( strpos($encoded, 'redirect') === false || strpos($param, 'redirect') === false) ) {
			    $param = ((strpos($param, '?') === false) ? '?' : '&') . 'redirect=' . $encoded;
            }

	    }

	    $url = $login_page . $param;
        $url = sanitize_url( $url, array( 'http', 'https' ) );
	    /**
	     * Filter `woffice_login_redirection_param`
         *
         * Woffice login redirection URL, with its parameter
         *
         * @param string
         * @param string
	     */
	    $url = apply_filters('woffice_login_redirection_url', $url, $param);

        wp_redirect($url);
        exit();

    }
}

if(!function_exists('woffice_get_login_page_name')) {
    /**
     * Get the login page slug
     *
     * @return string
     */
    function woffice_get_login_page_name(){
        /* We fetch the data from the settings */
        $the_login_page = woffice_get_theming_option('login_page');
	    $slug = 'login';
        if (!empty($the_login_page)) {
            /* We have the ID we need the name */
            $login_post = get_post($the_login_page);
            $slug = $login_post->post_name;
        }

	    return $slug;

    }
}

if(!function_exists('woffice_is_custom_login_page_enabled')) {
	/**
	 * Check if the custom login page is enabled and working
	 *
	 * @return string
	 */
	function woffice_is_custom_login_page_enabled(){

	    if( Woffice_Security::$custom_login_enabled !== null )
	        return Woffice_Security::$custom_login_enabled;

		$custom_login_option = woffice_validate_bool_option(woffice_get_theming_option('login_custom'));

		$result = false;


		if( $custom_login_option) {
			/* We fetch the data from the settings */
			$the_login_page = woffice_get_theming_option('login_page');

			if ( !empty($the_login_page) && isset($the_login_page) ) {
				$login_post = get_post($the_login_page);
				$post_template = get_page_template_slug( $login_post);

				// The login page is enabled by option, exists and it's valid
				if( $login_post->post_status === 'publish' && $post_template == 'page-templates/login.php')
					$result = true;
            }

        }

        Woffice_Security::$custom_login_enabled = $result;
		return $result;

	}
}

if(!function_exists('woffice_unyson_is_required')) {
    /**
     * Function to inform the user to enable Unyson before using Woffice...
     *
     * @since 2.1.0.1
     * @return void
     */
    function woffice_unyson_is_required(){

        ?>

        <div id="woffice_unyson_required">

            <style type="text/css">
                #woffice_unyson_required{
                    position: fixed;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1000;
                    background: #ffffff;
                    overflow-y: scroll;
                }
                #woffice_unyson_required div.text-center{
                    text-align: center;
                    margin-top: 10%;
                    padding: 20px 80px;
                }
                #woffice_unyson_required div.text-center i{
                    font-size: 200px;
                    color: #5c84a2;
                }
                #woffice_unyson_required div.text-center{
                    font-family: "Helvetica Neue", "Arial", sans-serif;
                }
                #woffice_unyson_required div.text-center h1{
                    font-weight: lighter;
                    font-size: 48px;
                }
                #woffice_unyson_required div.text-center h3{
                    font-weight: bold;
                    font-size: 24px;
                }
                #woffice_unyson_required div.text-center a{
                    color: #5c84a2;
                }
            </style>

            <div class="text-center">
                <i class="fa fa-cogs"></i>
                <?php if(woffice_current_is_admin()) : ?>
                    <h1><?php _e('Almost here, you need to active the Woffice framework (Unyson).', 'woffice'); ?></h1>
                    <h3>
                        <?php _e('You can enable Unyson', 'woffice'); ?>
                        <a href="<?php echo admin_url('themes.php?page=tgmpa-install-plugins&plugin_status=activate'); ?>"><b><?php _e('here', 'woffice'); ?></b></a>
                         <?php _e('or follow the documentation', 'woffice'); ?>
                        <a href="https://docs.woffice.io/"><b><?php _e('here', 'woffice'); ?></b></a>
                        <?php _e('where all is explained with details.', 'woffice'); ?>
                    </h3>
                    <p>
                        <?php _e('Why ? Woffice is based on Unyson framework to enhance Wordpress features and to provide a much better interface as well as better performances. We use it to build Woffice extensions and all its settings so you can customize it as you like. Feel free to get in touch with us if you\'ve any question.', 'woffice'); ?>
                    </p>
                <?php else : ?>
                    <h1><?php _e('Site is under construction, thanks for your patience...', 'woffice'); ?></h1>
                <?php endif; ?>
            </div>

        </div>

        <?php

    }
}

if(!function_exists('woffice_is_current_page_using_blank_template')) {
    /**
     * Check if the page is using the blank template or not
     *
     * @return bool
     */
	function woffice_is_current_page_using_blank_template() {
		$is_blank_page        = is_page_template('page-templates/blank-page.php');
		$is_activation_page   = (function_exists('bp_is_activation_page') && bp_is_activation_page());
		$is_registration_page = (function_exists('bp_is_register_page') && bp_is_register_page());

		return ($is_blank_page || $is_registration_page);
	}
}

if(!function_exists('woffice_online_users')) {
    /**
     * Displaying the number of the online users
     * Note used so far
     *
     * @return void
     */
    function woffice_online_users()
    {
        global $bp;
        if (bp_has_members('type=online')):
            echo '<li><span class="has-online"></span> <strong>' . bp_has_members('type=online') . '</strong></li>';
        else :
            echo '<li><span class="has-online"></span> <strong>0</strong></li>';
        endif;
    }
}

if(!function_exists('woffice_current_time')) {
    /**
     * Return the current time,
     * used to be in the user menu
     *
     * @return void
     */
    function woffice_current_time()
    {
        echo current_time('mysql');
    }
}

if(!function_exists('woffice_bp_is_active')) {
	/**
	 * It's a wrapper for the function bp_is_active(). Checks if a given BuddyPress component is active
	 *
	 * @param $component
	 *
	 * @return bool
	 */
	function woffice_bp_is_active($component) {
		return (function_exists('bp_is_active') && bp_is_active( $component ));
	}
}

if(!function_exists('woffice_get_users_ids_by_xprofile_fields')) {
	/**
	 * Return an aray of ids of members that fit the search criterias.
	 *
	 * Example of Search Criterias:
	 * array (
	 *        [0] => array (
	 *            [key] => '8268'
	 *            [value] => 'Rome'
	 *            [value_max] => ''
	 *            )
	 *        [1] => array (
	 *            [key] => '8264'
	 *            [value] => '20'
	 *            [value_max] => '50'
	 *        )
	 *    )
	 *
	 * @param $fields
	 *
	 * @return array|string
	 */
	function woffice_get_users_ids_by_xprofile_fields( $fields ) {

		if (empty($fields) && (!isset($_POST['wordpress_email']) || empty($_POST['wordpress_email'])) ) {
			return '';
		}

		//Check for xProfile fields
		$ids_found_by_xprofile_fields = array();

		if (!empty($fields)) {
            global $wpdb;

            $query = "SELECT user_id FROM {$wpdb->prefix}bp_xprofile_data";
            $where_added = false;
            $key = 0;
            $params = array();

            foreach ($fields as $field) {
                if (!isset($field['key']) || !isset($field['value'])) {
                    continue;
                }

                $field_id = $field['key'];

                $field_object = xprofile_get_field($field['key']);

                if (empty($field_id)) {
                    continue;
                }

                // Add WHERE only one time
                if (!$where_added) {
                    $query .= ' WHERE';
                    $where_added = true;
                } else {
                    $query .= ' OR';
                }

                $query .= " (field_id = %s";

                $params[] = $field_id;

                if (!empty($field['value'])) {
                    switch ($field_object->type) {
                        case 'selectbox':
                            $query .= " AND value = %s";
                            $params[] = $field['value'];
                            break;
                        case 'number':
                            if (!empty($field['value'])) {
                                $query .= " AND value >= %s";
                                $params[] = $field['value'];
                            }
                            if (!empty($field['value_max'])) {
                                $query .= " AND value <= %s";
                                $params[] = $field['value_max'];
                            }
                            break;
                        default:
                            $query .= " AND (";
                            $like_conditions = array();
                            if (!is_array($field['value'])) {
                                $like_conditions[] = " value LIKE %s";
                                $search_terms_clean = bp_esc_like( wp_kses_normalize_entities( $field['value'] ) );
                                $search_terms_nospace = '%' . $search_terms_clean . '%';
                                $params[] = $search_terms_nospace;
                            } else {
                                foreach ($field['value'] as $value) {
                                    $like_conditions[] = " value LIKE %s";
                                    $params[] = '%' . $value . '%';
                                }
                            }
                            $query .= implode(" OR ", $like_conditions);
                            $query .= ")";
                    }
                }

                $query .= ")";
            }

            if (apply_filters( 'woffice_advanced_members_search_compare', 'AND')){
                $query .= " GROUP BY user_id HAVING COUNT(user_id) > %d";
                $params[] = $key;
            }
            
            $prepared_query = $wpdb->prepare($query, $params);

            $ids_found_by_xprofile_fields = $wpdb->get_col($prepared_query);



            // If no user found with xProfile_fields
            if (empty($ids_found_by_xprofile_fields) || ($ids_found_by_xprofile_fields === null)) {
	            return array();
            }
		}

        // Either empty array or array with ids
        $all_ids_found = $ids_found_by_xprofile_fields;

		// Check by wordpress email
		if (isset($_POST['wordpress_email']) && !empty($_POST['wordpress_email'])) {
			$email = $_POST['wordpress_email'];

			$users = new WP_User_Query( array(
				'fields' => 'user_id',
				'search'         => '*'.esc_attr( $email ).'*',
				'search_columns' => array(
					'user_email',
				),
                // Only include array of ids (if isset - otherwise null and won't cause any issue)
                'include' => $ids_found_by_xprofile_fields
			) );

			//Add WHERE only one time
			$ids_found_by_email = $users->get_results();

            // Email set so we include the 2 parameters in the search
            // This is the final result
            $all_ids_found = $ids_found_by_email;

		}

		return $all_ids_found;

	}
}

if (!function_exists('woffice_get_user_role')) {
	/**
     * Returns the primary role for a given user id
     *
	 * @param int $id
     * @return string
	 */
    function woffice_get_user_role($id) {

	    $user = get_userdata($id);

	    // We remove BBPress roles
	    $roles = array();

	    global $wp_roles;

	    /**
	     * Filter `woffice_members_directory_excluded_displayed_roles`
	     *
	     * Lets you filter the displayed roles on the badge
	     *
	     * @param array
	     */
	    $excluded_roles = apply_filters('woffice_members_directory_excluded_displayed_roles', array());

	    foreach ($user->roles as $key => $role) {
		    if (substr($role, 0, 4) != 'bbp_' && !in_array($role, $excluded_roles)) {
			    array_push($roles, translate_user_role($wp_roles->roles[$role]['name']));
		    }
	    }

	    return (empty($roles)) ? '' : $roles[0];
    }
}

if( !function_exists('woffice_get_redirect_page_after_login')) {
	/**
	 * Return the url where redirect the user once he has signed in
	 *
	 * @return mixed|null|string
	 */
	function woffice_get_redirect_page_after_login() {
		if (isset($_SESSION['redirect_url']) && !empty($_SESSION['redirect_url'])) {
		    return $_SESSION['redirect_url'];
        }

		$aft_login    = woffice_get_theming_option('aft_login');
		$redirect_url = home_url();

		if ($aft_login === 'custom') {
			$custom_redirect_url = woffice_get_theming_option('custom_redirect_url');
			$redirect_url = $custom_redirect_url;
		} elseif ($aft_login == 'previous' && isset($_GET['redirect'])) {
			$redirect_url = $_GET['redirect'];
		}

		return $redirect_url;
	}
}

if( !function_exists('woffice_get_random')) {
    /**
     * Generate a random string
     *
     * @link http://stackoverflow.com/a/5444902/4309746
     * @param int $length
     * @return string
     */
    function woffice_get_random($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }
}

if ( !function_exists( 'woffice_get_footer_widgets_layout' ) ) {
	/**
	 * Get the layout of the footer
	 *
	 * @return string
	 *
	 * @since 2.5.0
	 */
	function woffice_get_footer_widgets_layout() {

		$layout = woffice_get_theming_option('footer_widgets_layout', '3-3-3-3');

		// Support for version older than 2.5.0
		$footer_widgets_columns = woffice_get_theming_option('footer_widgets_columns');

		if ( $footer_widgets_columns != null ) {
			if ($footer_widgets_columns == "1")
				$layout = '1';
			elseif ($footer_widgets_columns == "2")
				$layout = '6-6';
			elseif ($footer_widgets_columns == "3")
				$layout = '4-4-4';
		}

		return $layout;

	}
}

if ( !function_exists( 'woffice_is_core_update_page' ) ) {
    /**
     * Determine if we're on a WP Core installation/upgrade page.
     *
     * @see woffice_pro_notice()
     *
     * @return bool True when on a WP Core installation/upgrade page, false otherwise.
     */
    function woffice_is_core_update_page()
    {
        // Current screen is not always available, most notably on the customizer screen.
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();

        if ('update-core' === $screen->base) {
            // Core update screen.
            return true;
        } elseif ('plugins' === $screen->base && !empty($_POST['action'])) { // WPCS: CSRF ok.
            // Plugins bulk update screen.
            return true;
        } elseif ('update' === $screen->base && !empty($_POST['action'])) { // WPCS: CSRF ok.
            // Individual updates (ajax call).
            return true;
        }

        return false;
    }
}

if( !function_exists('woffice_bp_is_buddypress') ) {
	/**
	 * Wrap the function is_buddypress() in order to avoid the fatal error if buddypress is not active
	 *
	 * @return bool
	 */
	function woffice_bp_is_buddypress() {
		return ( function_exists('is_buddypress') && is_buddypress() );
	}
}

if ( !function_exists('woffice_get_relative_current_buddypress_page_id')) {
	/**
	 * Get the id of the WordPress page corresponding to the assigned BuddyPress page component
	 * (Members, Groups, Activity pages assignations)
	 *
	 * If the current page isn't one of these pages or if isn't possible to get the ID of the assigned page,
	 * then return FALSE
	 *
	 * @param bool $force
	 *
	 * @return bool|int
	 */
	function woffice_get_relative_current_buddypress_page_id( $force = false ) {

		$slug = false;

		/*
		 * TODO the bp_is_my_profile() create troubles here, it doesn't allow to get the correct value on own profile page
		 * it have to be moved from there to the Eo_security check, where this function is used but out of it
		 */
		//if ( ( bp_is_members_component() || bp_is_user() ) && ! bp_is_my_profile() ) {

		if (
		    function_exists('bp_is_members_directory')
            && bp_is_members_directory() || ( $force && bp_is_user() )
        ) {

			$slug = bp_get_members_root_slug();

		} else if (
		    function_exists('bp_is_groups_directory')
            && bp_is_groups_directory() || ( $force && bp_is_groups_component() )
        ) {

			$slug = bp_get_groups_root_slug();

		} else if (
            function_exists('bp_is_activity_directory')
            && bp_is_activity_directory() || ( $force && bp_is_activity_component() )
        ) {

			$slug = bp_get_activity_root_slug();

		}

		if ( ! $slug ) {
			return $slug;
		}

		$page = get_page_by_path( $slug );

        if($page) {
            return $page->ID;
        } else {
            return $slug;
        }

		

	}
}

if ( !function_exists( 'woffice_manual_user_approve_enabled') ) {
	/**
	 * Check if the plugin Eonet Manual User Approve is active
	 *
	 * @return bool
	 */
	function woffice_manual_user_approve_enabled() {

		return ( function_exists( 'eonet_manual_user_approve' ) );

	}
}

if( !function_exists('woffice_is_enabled_confirmation_email') ) {
	/**
	 * Check if confirmation is enabled
	 *
	 * @return bool
	 */
	function woffice_is_enabled_confirmation_email() {
		global $bp;

		$email_verification_enabled = woffice_get_theming_option( 'email_verification', true);

		return (
			class_exists('BP_Signup')
			&& isset($bp->pages->activate)
			&& !(
				defined( 'WOFFICE_IS_CONFIRMATION_EMAIL_DISABLED')
				&& true == 'WOFFICE_IS_CONFIRMATION_EMAIL_DISABLED'
			)
			&& $email_verification_enabled
		);
	}
}

if (!function_exists('woffice_convert_fa4_to_fa5')) {
	/**
     * Convert a Font Awesome 4 icon to Font Awesome 5
     *
	 * @param $icon
     *
     * @return string
	 */
    function woffice_convert_fa4_to_fa5($icon)
    {
        $icon = explode(' ', $icon);
	    $icon = (isset($icon[1])) ? $icon[1] : $icon[0];

	    if ($icon === 'fa-tachometer' || $icon === 'fa-dashboard') {
	        $icon = 'fa-tachometer-alt';
        }

	    else if ($icon === 'fa-chain') {
		    $icon = 'fa-link';
	    }

	    else if ($icon === 'fa-cloud-download') {
		    $icon = 'fa-cloud-download-alt';
	    }

	    else if ($icon === 'fa-thumbs-o-up') {
		    $icon = 'fa-thumbs-up';
	    }

	    else if ($icon === 'fa-thumbs-o-down') {
		    $icon = 'fa-thumbs-down';
	    }

	    else if ($icon === 'fa-sort-amount-asc') {
		    $icon = 'fa-sort-amount-down';
	    }

	    else if ($icon === 'fa-bar-chart') {
		    $icon = 'fa-chart-bar';
	    }

	    else if ($icon === 'fa-thumb-tack') {
		    $icon = 'fa-thumbtack';
	    }

	    else if ($icon === 'fa-group') {
		    $icon = 'fa-users';
	    }

	    else if ($icon === 'fa-commenting-o') {
		    $icon = 'fa-comment-dots';
	    }

	    else if ($icon === 'fa-sign-out') {
		    $icon = 'fa-sign-out-alt';
	    }

	    else if ($icon === 'fa-cutlery') {
	        $icon = 'fa-utensils';
        }

	    else if ($icon === 'fa-diamond') {
		    $icon = 'fa-gem';
	    }

	    else if ($icon === 'fa-refresh') {
		    $icon = 'fa-sync';
	    }

	    else if ($icon === 'fa-facebook-official') {
		    $icon = 'fa-facebook';
	    }

	    else if (substr($icon, -2) === '-o') {
		    $icon = str_replace('-o', '', $icon);
        }

	    $prefix = (in_array($icon, [
            'fa-500px',
            'fa-adn',
            'fa-amazon',
            'fa-android',
            'fa-angellist',
            'fa-apple',
            'fa-behance',
            'fa-behance-square',
            'fa-bitbucket',
            'fa-bitbucket-square',
            'fa-black-tie',
            'fa-buysellads',
            'fa-chrome',
            'fa-codepen',
            'fa-codiepie',
            'fa-connectdevelop',
            'fa-contao',
            'fa-css3',
            'fa-dashcube',
            'fa-delicious',
            'fa-deviantart',
            'fa-digg',
            'fa-dribbble',
            'fa-dropbox',
            'fa-drupal',
            'fa-edge',
            'fa-empire',
            'fa-envira',
            'fa-expeditedssl',
            'fa-fa',
            'fa-facebook',
            'fa-facebook-f',
            'fa-facebook-official',
            'fa-facebook-square',
            'fa-firefox',
            'fa-first-order',
            'fa-flickr',
            'fa-font-awesome',
            'fa-fonticons',
            'fa-fort-awesome',
            'fa-forumbee',
            'fa-foursquare',
            'fa-ge',
            'fa-get-pocket',
            'fa-git',
            'fa-git-square',
            'fa-github',
            'fa-github-alt',
            'fa-github-square',
            'fa-gitlab',
            'fa-gittip',
            'fa-glide',
            'fa-glide-g',
            'fa-google',
            'fa-google-plus',
            'fa-google-plus-circle',
            'fa-google-plus-official',
            'fa-google-plus-square',
            'fa-gratipay',
            'fa-hacker-news',
            'fa-houzz',
            'fa-html5',
            'fa-instagram',
            'fa-internet-explorer',
            'fa-ioxhost',
            'fa-joomla',
            'fa-jsfiddle',
            'fa-lastfm',
            'fa-lastfm-square',
            'fa-leanpub',
            'fa-linkedin',
            'fa-linkedin-square',
            'fa-linux',
            'fa-maxcdn',
            'fa-meanpath',
            'fa-medium',
            'fa-mixcloud',
            'fa-modx',
            'fa-odnoklassniki',
            'fa-odnoklassniki-square',
            'fa-opencart',
            'fa-openid',
            'fa-opera',
            'fa-optin-monster',
            'fa-pagelines',
            'fa-pied-piper',
            'fa-pied-piper-alt',
            'fa-pied-piper-pp',
            'fa-pinterest',
            'fa-pinterest-p',
            'fa-pinterest-square',
            'fa-product-hunt',
            'fa-qq',
            'fa-ra',
            'fa-rebel',
            'fa-reddit',
            'fa-reddit-alien',
            'fa-reddit-square',
            'fa-renren',
            'fa-resistance',
            'fa-safari',
            'fa-scribd',
            'fa-sellsy',
            'fa-shirtsinbulk',
            'fa-simplybuilt',
            'fa-skyatlas',
            'fa-skype',
            'fa-slack',
            'fa-slideshare',
            'fa-snapchat',
            'fa-snapchat-ghost',
            'fa-snapchat-square',
            'fa-soundcloud',
            'fa-spotify',
            'fa-stack-exchange',
            'fa-stack-overflow',
            'fa-steam',
            'fa-steam-square',
            'fa-stumbleupon',
            'fa-stumbleupon-circle',
            'fa-tencent-weibo',
            'fa-themeisle',
            'fa-trello',
            'fa-tripadvisor',
            'fa-tumblr',
            'fa-tumblr-square',
            'fa-twitch',
            'fa-twitter',
            'fa-twitter-square',
            'fa-usb',
            'fa-viacoin',
            'fa-viadeo',
            'fa-viadeo-square',
            'fa-vimeo',
            'fa-vimeo-square',
            'fa-vine',
            'fa-vk',
            'fa-wechat',
            'fa-weibo',
            'fa-weixin',
            'fa-whatsapp',
            'fa-wikipedia-w',
            'fa-windows',
            'fa-wordpress',
            'fa-wpbeginner',
            'fa-wpforms',
            'fa-xing',
            'fa-xing-square',
            'fa-y-combinator',
            'fa-y-combinator-square',
            'fa-yahoo',
            'fa-yc',
            'fa-yc-square',
            'fa-yelp',
            'fa-yoast',
            'fa-youtube',
            'fa-youtube-square',
        ])) ? 'fab' : 'fas';

        return $prefix .' '. $icon;
    }
}

if (! function_exists('woffice_date_php_to_moment_js')) {
	/**
     * Convert a PHP date to Moment js
     *
     * @link https://stackoverflow.com/questions/30186611/php-dateformat-to-moment-js-format
     *
	 * @param string $php_format
	 *
	 * @return string
	 */
	function woffice_date_php_to_moment_js($php_format) {
		$replacements = array(
			'A' => 'A',      // for the sake of escaping below
			'a' => 'a',      // for the sake of escaping below
			'B' => '',       // Swatch internet time (.beats), no equivalent
			'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
			'D' => 'ddd',
			'd' => 'DD',
			'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
			'F' => 'MMMM',
			'G' => 'H',
			'g' => 'h',
			'H' => 'HH',
			'h' => 'hh',
			'I' => '',       // Daylight Saving Time? => moment().isDST();
			'i' => 'mm',
			'j' => 'D',
			'L' => '',       // Leap year? => moment().isLeapYear();
			'l' => 'dddd',
			'M' => 'MMM',
			'm' => 'MM',
			'N' => 'E',
			'n' => 'M',
			'O' => 'ZZ',
			'o' => 'YYYY',
			'P' => 'Z',
			'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
			'S' => 'o',
			's' => 'ss',
			'T' => 'z',      // deprecated since version 1.6.0 of moment.js
			't' => '',       // days in the month => moment().daysInMonth();
			'U' => 'X',
			'u' => 'SSSSSS', // microseconds
			'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
			'W' => 'W',      // for the sake of escaping below
			'w' => 'e',
			'Y' => 'YYYY',
			'y' => 'YY',
			'Z' => '',       // time zone offset in minutes => moment().zone();
			'z' => 'DDD',
        );

		// Converts escaped characters.
		foreach ($replacements as $from => $to) {
			$replacements['\\' . $from] = '[' . $from . ']';
		}

		return strtr($php_format, $replacements);
	}
}

/**
 * Removes Undefined option type: table error by Unyson.
 *
 * @param $type
 *
 * @return mixed|void
 */
function woffice_disable_table_warning($type) {
	if ( 'table' == $type ) {
		return apply_filters('woffice_fw_backend_undefined_option_type_warn_user', false, $type);
	}
}
add_filter('fw_backend_undefined_option_type_warn_user','woffice_disable_table_warning');

/**
 * Add parent content in learnpress pages
 */

if(class_exists('LearnPress')){

    add_action(
        'learn-press/before-main-content',
        LP()->template( 'general' )->text( '<div id="left-content"><div id="content-container" class="woffice-lp-container"><div id="content">', 'lp-archive-courses-open' ),
        -1010
    );

    add_action(
        'learn-press/after-main-content',
        LP()->template( 'general' )->text( '</div></div></div>', 'lp-archive-courses-close' ),
        1010
    );

    function woffice_deregister_learnpress_style() {
		wp_deregister_style( 'lp-font-awesome-5' );
		wp_register_style( 'lp-font-awesome-5', get_template_directory_uri() . '/css/font-awesome-5.min.css' );
	}

    add_action( 'wp_print_styles', 'woffice_deregister_learnpress_style', 30);
}

add_action('after_setup_theme', 'woffice_use_widgets_block_editor');

function woffice_use_widgets_block_editor(){
	add_filter( 'use_widgets_block_editor', '__return_false' );
}

function woffice_main_content_classes(){
    $class = "";
    $left_bar = isset($_COOKIE['Woffice_hasnav_position']) ? $_COOKIE['Woffice_hasnav_position'] : false;
    $right_bar = isset($_COOKIE['Woffice_hassidebar_position']) ? $_COOKIE['Woffice_hassidebar_position'] : false;
    $sidebar_state = woffice_get_sidebar_state();
    $menu_default = woffice_get_theming_option('menu_default');

    if($left_bar === 'has-navigation-hidden' && $right_bar === 'has-sidebar-hidden' || $sidebar_state !== 'show' && $menu_default === 'close' ) {
        $class = "w-100";
    }

    return $class;
}

if(!function_exists('woffice_top_navbar')) {
    function woffice_top_navbar(){

        //IF Fixed we add a nav class
        $header_fixed = woffice_get_theming_option('header_fixed');
        $extra_navbar_class = ( $header_fixed == "yep" ) ? 'has_fixed_navbar' :'';

        $nav_opened_state = woffice_get_navigation_state();
        $sidebar_state = woffice_get_sidebar_state();
        $sidebar_show_class = ($sidebar_state != 'show') ? 'sidebar-hidden' : '';

        $is_blank_template = woffice_is_current_page_using_blank_template();
        $blank_template_class = ($is_blank_template) ? 'is-blank-template' : '';

        $hentry_class = apply_filters('woffice_hentry_class', 'hentry');
        // We add a class if the menu is closed by default
        $navigation_hidden_class = woffice_get_navigation_class();
        $menu_layout = get_option('woffice_theme_options');
        $menu_layout = $menu_layout['menu_layout'];

    ?>
        <!-- START HEADER -->
        <?php // CHECK FROM OPTIONS
                $header_user = woffice_get_theming_option('header_user');
                $header_user_class = (woffice_validate_bool_option($header_user)) ? 'has-user': 'user-hidden';
                ?>
                            
                 <header id="main-header" class="<?php echo esc_attr($navigation_hidden_class) . ' ' . esc_attr($header_user_class ).' '. esc_attr($sidebar_show_class); ?>">
                    <nav class="navbar navbar-expand-lg p-0">
                        <div class="navbar-collapse" id="navbarColor02">
                            <!-- NAVIGATION TOGGLE -->
                        <?php 
                         $is_bar_icon = "";
                         $is_arrow ="";
                         if($nav_opened_state){
                            $is_bar_icon ="d-none";
                            $is_arrow ="";
                         }else{
                            $is_bar_icon ="";
                            $is_arrow ="d-none";
                         }
                         $arrow_left = '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">

                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                        <path d="M2370 5113 c-371 -35 -653 -114 -961 -269 -406 -203 -782 -548 -1029 -944 -179 -286 -309 -655 -362 -1025 -17 -118 -17 -512 0 -630 42 -295 120 -553 242 -800 137 -280 272 -468 494 -691 221 -220 412 -357 681 -489 188 -92 309 -137 500 -185 500 -126 1002 -102 1490 71 150 53 408 183 540 271 302 202 573 480 769 788 72 113 188 353 235 486 235 662 194 1372 -115 1993 -124 250 -263 447 -458 648 -214 222 -430 379 -711 518 -296 146 -572 225 -900 255 -102 9 -333 11 -415 3z m545 -342 c628 -106 1158 -448 1511 -977 179 -267 296 -573 351 -909 24 -153 24 -497 0 -650 -108 -668 -474 -1222 -1042 -1580 -243 -153 -537 -261 -850 -312 -154 -24 -497 -24 -650 1 -657 107 -1198 456 -1557 1006 -168 257 -281 557 -335 885 -24 153 -24 497 0 650 81 497 291 912 636 1255 382 381 862 605 1401 654 108 10 418 -4 535 -23z"/>
                        <path d="M2655 3506 c-41 -18 -83 -69 -91 -111 -16 -86 -14 -89 289 -392 l281 -283 -796 -2 c-787 -3 -797 -3 -824 -24 -53 -39 -69 -71 -69 -134 0 -63 16 -95 69 -134 27 -21 37 -21 824 -24 l796 -2 -281 -283 c-304 -304 -305 -306 -288 -394 9 -49 69 -109 118 -118 91 -17 76 -30 549 443 383 384 438 442 443 474 17 92 29 78 -433 542 -242 242 -441 434 -459 442 -40 17 -89 17 -128 0z"/>
                        </g>
                        </svg>';
                        $nav_trigger_icon = (!$nav_opened_state) ? '<i class="fa fa-bars"></i>' : $arrow_left;?>
                        <a href="javascript:void(0)" id="nav-trigger">
                        <i class="fa fa-bars <?php echo esc_attr($is_bar_icon); ?>"></i>
                        <svg class="nav-arrow-left <?php echo esc_attr($is_arrow); ?>" xmlns="http://www.w3.org/2000/svg" version="1.0" width="30px" height="30px" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                         <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                         <path d="M2370 5113 c-371 -35 -653 -114 -961 -269 -406 -203 -782 -548 -1029 -944 -179 -286 -309 -655 -362 -1025 -17 -118 -17 -512 0 -630 42 -295 120 -553 242 -800 137 -280 272 -468 494 -691 221 -220 412 -357 681 -489 188 -92 309 -137 500 -185 500 -126 1002 -102 1490 71 150 53 408 183 540 271 302 202 573 480 769 788 72 113 188 353 235 486 235 662 194 1372 -115 1993 -124 250 -263 447 -458 648 -214 222 -430 379 -711 518 -296 146 -572 225 -900 255 -102 9 -333 11 -415 3z m545 -342 c628 -106 1158 -448 1511 -977 179 -267 296 -573 351 -909 24 -153 24 -497 0 -650 -108 -668 -474 -1222 -1042 -1580 -243 -153 -537 -261 -850 -312 -154 -24 -497 -24 -650 1 -657 107 -1198 456 -1557 1006 -168 257 -281 557 -335 885 -24 153 -24 497 0 650 81 497 291 912 636 1255 382 381 862 605 1401 654 108 10 418 -4 535 -23z"/>
                         <path d="M2655 3506 c-41 -18 -83 -69 -91 -111 -16 -86 -14 -89 289 -392 l281 -283 -796 -2 c-787 -3 -797 -3 -824 -24 -53 -39 -69 -71 -69 -134 0 -63 16 -95 69 -134 27 -21 37 -21 824 -24 l796 -2 -281 -283 c-304 -304 -305 -306 -288 -394 9 -49 69 -109 118 -118 91 -17 76 -30 549 443 383 384 438 442 443 474 17 92 29 78 -433 542 -242 242 -441 434 -459 442 -40 17 -89 17 -128 0z"/>
                         </g>
                         </svg>
                        </a>
                        <?php // CHECK FROM OPTIONS
                                $header_search = woffice_get_theming_option('header_search');
                                if (woffice_validate_bool_option($header_search)) :  ?>
                                    <!-- START SEARCH CONTAINER - WAITING FOR FIRING -->
                                    <div id="main-search">
                                            <?php //GET THE SEARCH FORM
                                            get_search_form(); ?>
                                    </div>
                            <?php endif; ?>
                            <ul class="ml-auto mb-0 p-0">
                                <div id="nav-buttons">
                                    <?php // Notification
                                     $show_notification_icon = woffice_get_theming_option('show_notification_icon');
                                        if ( woffice_bp_is_active( 'notifications' ) && is_user_logged_in() && $show_notification_icon ) : ?>
                                            <a href="javascript:void(0)" id="nav-notification-trigger" title="<?php _e( 'View your notifications', 'woffice' ); ?>" class="<?php echo (bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ) == 0) ? "" : "active" ?>">
                                                <p class="stellar-bell">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="svg_bell svg_show" width="21" height="23" viewBox="0 0 21 23" fill="none">
                                                    <path d="M20.9999 15.6072C20.9999 16.9672 19.9123 18.0716 18.5728 18.0716H2.42709C1.08677 18.0716 0 16.9689 0 15.6072C0 14.2476 1.08597 13.1428 2.42399 13.1428C2.4223 13.1428 2.42314 8.21413 2.42314 8.21413C2.42314 3.67648 6.03875 0 10.5 0C14.9612 0 18.5768 3.67736 18.5768 8.21413V13.1422C19.9121 13.1428 21 14.2469 21 15.6071L20.9999 15.6072ZM18.5759 14.7859C17.6835 14.7859 16.9615 14.0519 16.9615 13.1423V8.2142C16.9615 4.58502 14.069 1.64334 10.4999 1.64334C6.93071 1.64334 4.03827 4.58414 4.03827 8.2142V13.1423C4.03827 14.0493 3.3146 14.7859 2.42384 14.7859C1.97792 14.7859 1.61537 15.1549 1.61537 15.6072C1.61537 16.0607 1.97791 16.4286 2.42694 16.4286H18.5727C19.0206 16.4286 19.3843 16.0593 19.3843 15.6072C19.3843 15.1552 19.0206 14.7859 18.5758 14.7859H18.5759ZM6.86523 19.3035H8.48046C8.48046 20.4377 9.38441 21.357 10.4997 21.357C11.6149 21.357 12.5189 20.4377 12.5189 19.3035H14.1341C14.1341 21.345 12.507 23 10.4997 23C8.49228 23 6.86523 21.345 6.86523 19.3035Z" fill="#2D3342"/>
                                                    </svg>
                                                    <i class="fa fa-times"></i>
                                                </p>                                                
                                            </a>
                                    <?php endif; ?>
                                    <?php  if ( woffice_bp_is_active( 'notifications' ) && is_user_logged_in() ) { woffice_notifications_menu(); } ?>
                                    <?php // WOOCOMMERCE CART TRIGGER
                                    /**
                                        * You can disable the minicart in the header form there
                                        *
                                        * @param bool
                                        */
                                    $minicart_header_enabled = apply_filters('woffice_show_minicart_in_header', true);
                                    $show_mini_Cart = woffice_get_theming_option('show_mini_Cart');
                                    if (function_exists('is_woocommerce') && $minicart_header_enabled && $show_mini_Cart) : ?>
                                        <?php //is cart empty ?
                                        if ( WC()->cart->get_cart_contents_count() > 0 ) :
                                            $cart_url_topbar = "javascript:void(0)";
                                            $cart_classes = 'active cart-content';
                                        else :
                                            $cart_url_topbar = get_permalink( wc_get_page_id( 'shop' ) );
                                            $cart_classes = "";
                                        endif; ?>
                                        <a href="<?php echo esc_url($cart_url_topbar); ?>"
                                        id="nav-cart-trigger"
                                        title="<?php _e( 'View your shopping cart', 'woffice' ); ?>"
                                        class="<?php echo esc_attr($cart_classes); ?>">
                                            <p class="stellar-mini-cart">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none">
                                                    <path d="M23.4624 4.64725C23.0079 4.06641 22.3005 3.71283 21.5682 3.71283H6.18748L5.70758 1.84386C5.42975 0.75796 4.47 0.000244141 3.35889 0.000244141H0.78276C0.353435 0.000244141 -0.00012207 0.353815 -0.00012207 0.783126C-0.00012207 1.21245 0.353449 1.56601 0.78276 1.56601H3.35889C3.73765 1.56601 4.06605 1.81864 4.16716 2.1974L7.24835 14.4717C7.52618 15.5576 8.48593 16.3153 9.59704 16.3153H19.6741C20.7854 16.3153 21.7703 15.5576 22.0228 14.4717L23.9169 6.69291C24.0936 5.98575 23.9421 5.22822 23.4622 4.64725H23.4624ZM22.3765 6.33938L20.4823 14.1181C20.3813 14.4969 20.053 14.7495 19.674 14.7495H9.597C9.21823 14.7495 8.88984 14.4969 8.78873 14.1181L6.59152 5.30389H21.5682C21.8208 5.30389 22.0732 5.43021 22.2248 5.63227C22.3764 5.83434 22.4522 6.08697 22.3764 6.33943L22.3765 6.33938Z" fill="#2D3342"/>
                                                    <path d="M10.1274 17.3254C8.68775 17.3254 7.50092 18.5125 7.50092 19.9519C7.50092 21.3916 8.68793 22.5784 10.1274 22.5784C11.5671 22.5786 12.7541 21.3916 12.7541 19.9519C12.7541 18.5125 11.5671 17.3254 10.1274 17.3254ZM10.1274 20.9875C9.54658 20.9875 9.09189 20.533 9.09189 19.9519C9.09189 19.3711 9.5464 18.9164 10.1274 18.9164C10.7083 18.9164 11.1629 19.3709 11.1629 19.9519C11.1629 20.5076 10.683 20.9875 10.1274 20.9875Z" fill="#2D3342"/>
                                                    <path d="M18.8153 17.3254C17.3757 17.3254 16.1888 18.5125 16.1888 19.9519C16.1888 21.3916 17.3759 22.5784 18.8153 22.5784C20.255 22.5784 21.4418 21.3914 21.4418 19.9519C21.4167 18.5125 20.255 17.3254 18.8153 17.3254ZM18.8153 20.9875C18.2345 20.9875 17.7798 20.533 17.7798 19.9519C17.7798 19.3711 18.2343 18.9164 18.8153 18.9164C19.3962 18.9164 19.8509 19.3709 19.8509 19.9519C19.8509 20.5076 19.371 20.9875 18.8153 20.9875Z" fill="#2D3342"/>
                                                </svg>
                                            </p>
                                            <span class="woocommerce-cart-count count"><bdi>
                                                <?php echo (sizeof( WC()->cart->get_cart()) > 0) ? WC()->cart->get_cart_contents_count() : ''; ?>
                                            </bdi></span>
                                        </a>
                                    <?php endif; ?>
                                    <?php // FETCHING SIDEBAR INFO
                                    if($sidebar_state == 'show' || $sidebar_state == 'hide') :
                                        $arrow_right = '<svg class="nav-arrow-right " xmlns="http://www.w3.org/2000/svg" version="1.0" width="30px" height="30px" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                        <path d="M2370 5113 c-371 -35 -653 -114 -961 -269 -406 -203 -782 -548 -1029 -944 -179 -286 -309 -655 -362 -1025 -17 -118 -17 -512 0 -630 42 -295 120 -553 242 -800 137 -280 272 -468 494 -691 221 -220 412 -357 681 -489 188 -92 309 -137 500 -185 500 -126 1002 -102 1490 71 150 53 408 183 540 271 302 202 573 480 769 788 72 113 188 353 235 486 235 662 194 1372 -115 1993 -124 250 -263 447 -458 648 -214 222 -430 379 -711 518 -296 146 -572 225 -900 255 -102 9 -333 11 -415 3z m545 -342 c628 -106 1158 -448 1511 -977 179 -267 296 -573 351 -909 24 -153 24 -497 0 -650 -108 -668 -474 -1222 -1042 -1580 -243 -153 -537 -261 -850 -312 -154 -24 -497 -24 -650 1 -657 107 -1198 456 -1557 1006 -168 257 -281 557 -335 885 -24 153 -24 497 0 650 81 497 291 912 636 1255 382 381 862 605 1401 654 108 10 418 -4 535 -23z"/>
                                        <path d="M2655 3506 c-41 -18 -83 -69 -91 -111 -16 -86 -14 -89 289 -392 l281 -283 -796 -2 c-787 -3 -797 -3 -824 -24 -53 -39 -69 -71 -69 -134 0 -63 16 -95 69 -134 27 -21 37 -21 824 -24 l796 -2 -281 -283 c-304 -304 -305 -306 -288 -394 9 -49 69 -109 118 -118 91 -17 76 -30 549 443 383 384 438 442 443 474 17 92 29 78 -433 542 -242 242 -441 434 -459 442 -40 17 -89 17 -128 0z"/>
                                        </g>
                                        </svg>';
                                    ?>
                                     <!-- -----------new user detail place----------- -->
                                     <?php
                                        $userinfo_position = woffice_get_theming_option('header_user_position');
                                        $header_user = woffice_get_theming_option('header_user');
                                        if(is_user_logged_in() && woffice_validate_bool_option($header_user) && $userinfo_position == 'in_topbar'){
                                     ?>
                                        <div class="sidebar-userinfo">
                                            <div class="sidebar-userinfo_row">            
                                                <div class="user-infodetail">
                                                    <a href="javascript:void(0);" id="user-thumb">
                                                        <?php // GET CURRENT USER ID
                                                            $user_ID = get_current_user_id();
                                                            echo get_avatar($user_ID);
                                                        ?>
                                                    </a>
                                                    <?php
                                                        $name_to_display = woffice_get_name_to_display();
                                                        $user_info = get_userdata($user_ID);
                                                        $user_email = $user_info->user_email;
                                                    ?>
                                                    <figcaption>
                                                        <h5 class="mb-0 user-name"><?php  echo $name_to_display; ?></h5>
                                                            <small class="user-email"><?php  echo $user_email; ?></small>
                                                            <span class="user-profile-trigger">
                                                                <a href="javascript:void(0)">
                                                                    <p class="stellar-angle-down">
                                                                    <svg width="12" height="6" viewBox="0 0 12 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M11 0.5L6 5.5L1 0.5" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        </svg>
                                                                    </p>
                                                                </a>
                                                            </span>
                                                    </figcaption>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <!-- SIDEBAR TOGGLE -->
                                        <?php if(!woffice_sidebar_disabled() || empty(woffice_sidebar_disabled())) : ?>
                                            <a href="javascript:void(0)" id="nav-sidebar-trigger"><?php echo $arrow_right; ?></a>
                                        <?php endif; endif; ?>
                                </div>
                            </ul>
                        </div>
                    </nav>
                    <?php // WOOCOMMERCE CART CONTENT
                    if (function_exists('is_woocommerce')) { Woffice_WooCommerce::print_mini_cart(); } ?>
                     <?php
                    /*
                     * We render the alerts
                     */
                    woffice_alerts_render(); ?>
                </header>
                
                <!-- END NAVBAR -->
    <?php
    }
}
if(!function_exists('woffice_top_celestial_navbar')) {
    function woffice_top_celestial_navbar(){

        //IF Fixed we add a nav class
        $header_fixed = woffice_get_theming_option('header_fixed');
        $extra_navbar_class = ( $header_fixed == "yep" ) ? 'has_fixed_navbar' :'';

        $nav_opened_state = woffice_get_navigation_state();
        $sidebar_state = woffice_get_sidebar_state();
        $sidebar_show_class = ($sidebar_state != 'show') ? 'sidebar-hidden' : '';

        $is_blank_template = woffice_is_current_page_using_blank_template();
        $blank_template_class = ($is_blank_template) ? 'is-blank-template' : '';

        $hentry_class = apply_filters('woffice_hentry_class', 'hentry');
        // We add a class if the menu is closed by default
        $navigation_hidden_class = woffice_get_navigation_class();
        $menu_layout = get_option('woffice_theme_options');
        $menu_layout = $menu_layout['menu_layout'];

    ?>
        <!-- START HEADER -->
        <?php // CHECK FROM OPTIONS
                $header_user = woffice_get_theming_option('header_user');
                $header_user_class = (woffice_validate_bool_option($header_user)) ? 'has-user': 'user-hidden';
                ?>
                            
                 <header id="main-header" class="celst-top-bar <?php echo esc_attr($navigation_hidden_class) . ' ' . esc_attr($header_user_class ).' '. esc_attr($sidebar_show_class); ?>">
                    <nav class="top-bar-nav">
                        <div class="top-bar-container">
                        <div class="top-bar-left-content">
                            <!-- NAVIGATION TOGGLE -->
                        <span class="left-sidebar-toggle">
                            <i class="woffice-icon woffice-icon-celst-arrow-left lg-toggle-icon"></i>
                            <i class="woffice-icon woffice-icon-celst-toggle md-toggle-icon"></i>
                        </span>
                        <?php // CHECK FROM OPTIONS
                                $header_search = woffice_get_theming_option('header_search');
                                if (woffice_validate_bool_option($header_search)) :  ?>
                                    <!-- START SEARCH CONTAINER - WAITING FOR FIRING -->
                                    <div id="main-search">
                                            <?php //GET THE SEARCH FORM
                                            get_search_form(); ?>
                                    </div>
                            <?php endif; ?>
                            </div>
                            <div class="top-bar-right-content">
                                <div id="nav-buttons" class="top-bar-icons">
                                    <span class="celst-search-toggle">
                                    <i class="woffice-icon woffice-icon-celst-search celst-search-bar"></i>
                                    <i class="woffice-icon woffice-icon-celst-cross celst-search-close"></i>
                                    </span>
                                    <span class="full-screen-toggle" id="full-screen-toggle">
                                    <i class="woffice-icon woffice-icon-celst-enter-screen" data-toggle="tooltip" data-placement="top" title="Full Screen"></i>
                                    <i class="woffice-icon woffice-icon-celst-exit-screen" data-toggle="tooltip" data-placement="top" title="Exit Full Screen"></i>
                                    </span>
                                    <?php // Notification
                                     $show_notification_icon = woffice_get_theming_option('show_notification_icon');
                                        if ( woffice_bp_is_active( 'notifications' ) && is_user_logged_in() && $show_notification_icon ) : ?>
                                            <a href="javascript:void(0)" id="nav-notification-trigger" title="<?php _e( 'View your notifications', 'woffice' ); ?>" class="<?php echo (bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ) == 0) ? "" : "active" ?>">
                                                <p class="stellar-bell svg_bell svg_show">
                                                <i class="woffice-icon woffice-icon-celst-bell"></i>
                                                </p>                                                
                                            </a>
                                    <?php endif; ?>
                                    <?php  if ( woffice_bp_is_active( 'notifications' ) && is_user_logged_in() ) { woffice_notifications_menu(); } ?>
                                    <?php // WOOCOMMERCE CART TRIGGER
                                    /**
                                        * You can disable the minicart in the header form there
                                        *
                                        * @param bool
                                        */
                                    $minicart_header_enabled = apply_filters('woffice_show_minicart_in_header', true);
                                    $show_mini_Cart = woffice_get_theming_option('show_mini_Cart');
                                    if (function_exists('is_woocommerce') && $minicart_header_enabled && $show_mini_Cart) : ?>
                                        <?php //is cart empty ?
                                        if ( WC()->cart->get_cart_contents_count() > 0 ) :
                                            $cart_url_topbar = "javascript:void(0)";
                                            $cart_classes = 'active cart-content';
                                        else :
                                            $cart_url_topbar = get_permalink( wc_get_page_id( 'shop' ) );
                                            $cart_classes = "";
                                        endif; ?>
                                        <a href="<?php echo esc_url($cart_url_topbar); ?>"
                                        id="nav-cart-trigger"
                                        title="<?php _e( 'View your shopping cart', 'woffice' ); ?>"
                                        class="<?php echo esc_attr($cart_classes); ?>">
                                            <p class="stellar-mini-cart">
                                            <i class="woffice-icon woffice-icon-celst-cart"></i>
                                            </p>
                                            <span class="woocommerce-cart-count count"><bdi>
                                                <?php echo (sizeof( WC()->cart->get_cart()) > 0) ? WC()->cart->get_cart_contents_count() : ''; ?>
                                            </bdi></span>
                                        </a>
                                    <?php endif; ?>
                                    <?php // FETCHING SIDEBAR INFO
                                    if($sidebar_state == 'show' || $sidebar_state == 'hide') :
                                        $arrow_right = '<svg class="nav-arrow-right " xmlns="http://www.w3.org/2000/svg" version="1.0" width="30px" height="30px" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                                        <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                        <path d="M2370 5113 c-371 -35 -653 -114 -961 -269 -406 -203 -782 -548 -1029 -944 -179 -286 -309 -655 -362 -1025 -17 -118 -17 -512 0 -630 42 -295 120 -553 242 -800 137 -280 272 -468 494 -691 221 -220 412 -357 681 -489 188 -92 309 -137 500 -185 500 -126 1002 -102 1490 71 150 53 408 183 540 271 302 202 573 480 769 788 72 113 188 353 235 486 235 662 194 1372 -115 1993 -124 250 -263 447 -458 648 -214 222 -430 379 -711 518 -296 146 -572 225 -900 255 -102 9 -333 11 -415 3z m545 -342 c628 -106 1158 -448 1511 -977 179 -267 296 -573 351 -909 24 -153 24 -497 0 -650 -108 -668 -474 -1222 -1042 -1580 -243 -153 -537 -261 -850 -312 -154 -24 -497 -24 -650 1 -657 107 -1198 456 -1557 1006 -168 257 -281 557 -335 885 -24 153 -24 497 0 650 81 497 291 912 636 1255 382 381 862 605 1401 654 108 10 418 -4 535 -23z"/>
                                        <path d="M2655 3506 c-41 -18 -83 -69 -91 -111 -16 -86 -14 -89 289 -392 l281 -283 -796 -2 c-787 -3 -797 -3 -824 -24 -53 -39 -69 -71 -69 -134 0 -63 16 -95 69 -134 27 -21 37 -21 824 -24 l796 -2 -281 -283 c-304 -304 -305 -306 -288 -394 9 -49 69 -109 118 -118 91 -17 76 -30 549 443 383 384 438 442 443 474 17 92 29 78 -433 542 -242 242 -441 434 -459 442 -40 17 -89 17 -128 0z"/>
                                        </g>
                                        </svg>';
                                    ?>
                                    </div>
                                     <!-- -----------new user detail place----------- -->
                                     <div class="top-bar-user-wrapper">
                                     <?php if (is_user_logged_in()) { ?>
                                                <div class="user-info">
                                                    <a href="javascript:void(0);" id="user-thumb" class="user-profile-trigger">
                                                        <?php // GET CURRENT USER ID
                                                            $user_ID = get_current_user_id();
                                                            echo get_avatar($user_ID);
                                                        ?>
                                                    </a>
                                                    <?php
                                                        $name_to_display = woffice_get_name_to_display();
                                                        $user_info = get_userdata($user_ID);
                                                        $user_email = $user_info->user_email;
                                                    ?>
                                                    <figcaption>
                                                        <h5 class="user-name user-profile-trigger"><?php  echo $name_to_display; ?></h5>
                                                            <span class="user-profile-trigger">
                                                                <a href="javascript:void(0)">
                                                                    <i class="woffice-icon woffice-icon-celst-arrow-down"></i>
                                                                </a>
                                                            </span>
                                                    </figcaption>
                                                    <!-- ---user-details-popup start------ -->
                                                    <?php
                                                            $header_user = woffice_get_theming_option('header_user');
                                                            if (woffice_validate_bool_option($header_user) && function_exists('bp_is_active')) :
                                                                woffice_user_sidebar(true);
                                                            endif; 
                                                        ?>
                                                </div>
                                                <?php }else{ 
                                                        // SHOW LOGIN BUTTON
                                                        $header_login = woffice_get_theming_option('header_login');
                                                        if (!empty($header_login) && woffice_validate_bool_option($header_login)) {
                                                            echo '<a href="'.wp_login_url().'" id="user-login"><i class="fa fa-sign-in-alt"></i></a>';
                                                        }} ?>
                                                
                                        <!-- SIDEBAR TOGGLE -->
                                        <span class="r-sidebar-toggle">
                                            <i class="woffice-icon woffice-icon-celst-toggle"></i>   
                                        </span>

                                    <?php endif; ?>
                                </div>
                           
                            </div>
                        </div>
                    </nav>
                    <?php // WOOCOMMERCE CART CONTENT
                    if (function_exists('is_woocommerce')) { Woffice_WooCommerce::print_mini_cart(); } ?>
                     <?php
                    /*
                     * We render the alerts
                     */
                    woffice_alerts_render(); ?>

                    <div id="progress-wrap" class="progress-wrap">
                        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                            <path id="progress-path" d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
                        </svg>
                        <i class="woffice-icon woffice-icon-celst-arrow-right"></i>
                    </div>
                </header>
                
                <!-- END NAVBAR -->
    <?php
    }
}

if(!function_exists('woffice_get_page_slider')) {
    function woffice_get_page_slider(){
        // We check for Revolution Sliders
	    $slider_featured = '';
        $page_id = get_the_ID();
	    if ((function_exists('bp_is_groups_directory') && bp_is_groups_directory() )
	         || (function_exists('bp_is_members_directory') && bp_is_members_directory() )
	         || (function_exists('bp_is_activity_directory') && bp_is_activity_directory() )
	         || is_page()
	         || is_singular('post')
	         || (is_home() && get_option('page_for_posts'))
	    ) {

		    if (woffice_bp_is_buddypress()) {
                $bp_post_id = woffice_get_relative_current_buddypress_page_id();

                if ($bp_post_id) {
                    $page_id = $bp_post_id;
                }
		    }

		    $slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
	    }

        // We look for an Unyson slider first (an numerical post id and not a revolution slider slug
        if (is_numeric($slider_featured)){
            /**
             * Woffice Title Unyson slider shortcode
             *
             * @param string - the shortcode
             */
            $revolution_slider_shortcode = apply_filters('woffice_unyson_slider_shortcode', '[slider slider_id="'.$slider_featured.'" width="1200" height="auto" /]', $slider_featured);
            echo do_shortcode($revolution_slider_shortcode);
        } else{
            $slider_featured = woffice_get_post_rdx_option($page_id, 'revslider_featured');
            if (function_exists('putRevSlider')) {
                putRevSlider($slider_featured);
            }
        }
    }
}

/**
 * @param string it's default param of body_class filter
 * 
 * @return body classes 
*/

if(!function_exists('woffice_add_body_class')) {

    function woffice_add_body_class($classes, $class) { 

        $theme_settings_options = get_option('woffice_theme_options');
        
        $show_title_box = isset($theme_settings_options['show_title_box']) ? filter_var($theme_settings_options['show_title_box'], FILTER_VALIDATE_BOOLEAN) : false;
        $show_cover_image = isset($theme_settings_options['show_buddypress_cover']) ? woffice_validate_bool_option($theme_settings_options['show_buddypress_cover']) : false;
        $profile_layout = woffice_get_theming_option('profile_layout');

        if($show_title_box) {
            $classes[] = 'has-title-box';
        }

        if($show_cover_image && $profile_layout == 'horizontal' ) {
            $classes[] = 'has-buddypress-cover';
        }

        return $classes; 
    }
    
}

add_filter( "body_class", "woffice_add_body_class", 10, 2 );

/**
 * 
 * Add filter to excerpt_length
 * 
 * @return post excerpt length
 * 
*/

if(!function_exists('woffice_post_excerpt_length')) {

    function woffice_post_excerpt_length() {

        $post_excerpt_lenght = woffice_get_theming_option('post_excerpt_lenght',30);

        return $post_excerpt_lenght;
    }
}

add_filter( 'excerpt_length', 'woffice_post_excerpt_length');

/*
 * validate option migration of yep/nope to boolean
 *
 * @param option which contain yep/nope
 *
 * @return boolean
 */
if(!function_exists('woffice_unyson_switch_to_bool')) {

    function woffice_unyson_switch_to_bool($option) {
        
        $result = '';

        if($option == 'yep' || $option == 'show' ) {
            $result = true;
        }

        if($option == 'none' || $option == 'hide' || $option == 'nope' || $option == 'off') {
            $result = false;
        }

        return $result;
    }
}

/*
 * Get post/page by title
 *
 *
 * @return boolean
 */

if(!function_exists('woffice_get_page_by_title')) {

    function woffice_get_page_by_title($title) {
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
         
        if ( ! empty( $query->post ) ) {
            $page_got_by_title = $query->post;
        } else {
            $page_got_by_title = null;
        }

        return $page_got_by_title;
    }

}
/*
 * Get list of assigned project members
 *
 * @return HTML of project members loop
 */
if(!function_exists('woffice_loop_project_members')){
    function woffice_loop_project_members($project_members){
        $members = 5;
        if(woffice_get_skin('celestial')){
            $members = 2;
        }

        if(is_array($project_members)) {
            $first_mem_list = array_splice($project_members, 0, $members);
            foreach($first_mem_list as $project_member) {
                if (function_exists('bp_is_active')):
                    $user_info = get_userdata($project_member);
                    $mem_domain = function_exists('bp_members_get_user_url') ? bp_members_get_user_url($project_member) : bp_core_get_user_domain($project_member);
                    if (!empty($user_info->display_name)){
                        $name = woffice_get_name_to_display($user_info);
                        echo'<a href="'. esc_url($mem_domain) .'" title="'. $name .'" data-toggle="tooltip" data-placement="top">';
                        echo get_avatar($project_member);
                        echo'</a>';
                    }
                    else {
                        echo'<div class="img-papper"><a href="'. esc_url($mem_domain) .'">';
                        echo get_avatar($project_member);
                        echo'</a></div>';
                    }
                else :
                    echo get_avatar($project_member);
                endif;
            }
            $remaining_members = $project_members;
         
            if($remaining_members){
                echo '<a class="wo_remaining_member_toggle"> +'. count($remaining_members).'</a>';
            }
            echo '<div class="remaining_m_container members_popup" id="remaining_m_container">';
                foreach($project_members as $project_member) {
                    if (function_exists('bp_is_active')):
                        $user_info = get_userdata($project_member);
                        $mem_domain = function_exists('bp_members_get_user_url') ? bp_members_get_user_url($project_member) : bp_core_get_user_domain($project_member);
                        if (!empty($user_info->display_name)){
                            $name = woffice_get_name_to_display($user_info);
                            echo'<a href="'. esc_url($mem_domain) .'" title="'. $name .'" data-toggle="tooltip" data-placement="top">';
                            echo get_avatar($project_member);
                            echo'</a>';
                        }
                        else {
                            echo'<a href="'. esc_url($mem_domain) .'">';
                            echo get_avatar($project_member);
                            echo'</a>';
                        }
                    else :
                        echo get_avatar($project_member);
                    endif;
                }
            echo "</div>";
        }
    }
}

if(!function_exists('woffice_get_skin')) {
    function woffice_get_skin($skin){
        $theme_skin = woffice_get_theming_option('woffice_theme_skin');
        if($skin === $theme_skin){
            return true;   
        } else {
            return false;
        }
    }
}

if(!function_exists('woffice_sidebar_disabled')){
    function woffice_sidebar_disabled() {
        $post_id = get_the_ID();
        if(!$post_id) {
            $post_id = get_queried_object_id();
        }
        $disable_post_sidebar = woffice_validate_bool_option(get_post_meta($post_id,'disable_sidebar',true));
        $disable_page_sidebar = woffice_validate_bool_option(get_post_meta($post_id,'disable_page_sidebar',true));
        $disable_wiki_sidebar = woffice_validate_bool_option(get_post_meta($post_id,'disable_wiki_sidebar',true));
        $disable_directory_sidebar = woffice_validate_bool_option(get_post_meta($post_id,'disable_directory_sidebar',true));
        $disable_project_sidebar = woffice_validate_bool_option(get_post_meta($post_id,'disable_project_sidebar',true));
        
        if($disable_page_sidebar || $disable_post_sidebar || $disable_wiki_sidebar || $disable_directory_sidebar || $disable_project_sidebar){
            return true;
        } else{
            return false;
        }
    }
}
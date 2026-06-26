<?php
   //IF Fixed we add a nav class
   
   $nav_opened_state = woffice_get_navigation_state();
   $sidebar_state = woffice_get_sidebar_state();
   $sidebar_show_class = ($sidebar_state != 'show') ? 'sidebar-hidden' : '';
   
   $is_blank_template = woffice_is_current_page_using_blank_template();
   $blank_template_class = ($is_blank_template) ? 'is-blank-template' : '';
   $navigation_toggle_class = woffice_get_navigation_toggle_class();
   $hentry_class = apply_filters('woffice_hentry_class', 'hentry');
   // We add a class if the menu is closed by default
   $navigation_hidden_class = woffice_get_navigation_class();
   
   $nav_toggle_class = woffice_get_navigation_toggle_class();
   $content_class = '';
   $main_content_class = woffice_main_content_classes();
   $menu_layout = woffice_get_theming_option('menu_layout','left');
   if($nav_toggle_class === 'has-navigation-hidden'){
   $content_class = "";
   }
   
   $is_full_height = woffice_get_theming_option('left_menu_height',true);
   
   if($menu_layout == 'left' && !woffice_validate_bool_option($is_full_height)) {
       $navigation_toggle_class .= ' is-left-sidebar-fixed';
   }
   
   $copyright = '';
   if(function_exists('fw')){
   $copyright = woffice_get_theming_option('footer_copyright_content');
   }
   ?>



    <?php 
        $page_loading_celestial = woffice_get_theming_option('page_loading_celestial',true);
        if(woffice_get_skin('celestial') && woffice_validate_bool_option($page_loading_celestial)){ 
    ?>
        <!-- ---/--preloader goes here--/- -->
        <div id="preloader">
            <div class="loader_line"></div>
        </div>
    <?php } ?>
<div id="page-wrapper" <?php echo (!$nav_opened_state) ? 'class="menu-is-closed"':''; ?>>
<div class="wo-row">
<!-- Main row start---->

<aside class="celst-left-sidebar">
   <?php
      /*
                * The header part is removed on the blank template
                */
      if(!$is_blank_template): ?>
   <?php // CHECK IF LOGO NEEDS TO BE SHOW
      $header_logo_hide = woffice_get_theming_option('header_logo_hide');
      if (woffice_validate_bool_option($header_logo_hide) == false) { ?>
   <!-- START LOGO -->
   <div id="nav-logo" class="nav-brand-logo">
      <?php
         /**
         * The url of the logo in the header. By default, returns the home url
         *
         * @param string $url
         */
         $logo_link = apply_filters('woffice_logo_link_to', home_url( '/' ) );
         ?>
      <a href="<?php echo esc_url( $logo_link ); ?>" class="theme-logo logo-lg">
         <?php
            $header_logo = woffice_get_theming_option('header_logo');
            // IF THERE IS A LOGO :
            if(isset($header_logo["url"]) && !empty($header_logo["url"])) :
                echo'<img src="'. esc_url($header_logo["url"]) .'" alt="Logo Image">';
            else:
                echo'<img src="'. get_template_directory_uri() .'/images/logo.png" alt="Logo Image">';
            endif; ?>
      </a>
      <?php
         $header_collapse_logo = woffice_get_theming_option('header_collapse_logo');
         if(woffice_get_skin('celestial')){
             ?>
      <a href="<?php echo esc_url( $logo_link ); ?>" class="theme-logo logo-xs">
         <?php
            // IF THERE IS A LOGO :
            if(isset($header_collapse_logo["url"]) && !empty($header_collapse_logo["url"])) :
                echo'<img src="'. esc_url($header_collapse_logo["url"]) .'" alt="Logo Image">';
            else:
                echo'<img src="'. get_template_directory_uri() .'/images/logo.png" alt="Logo Image">';
            endif; ?>
      </a>
      <span class="md-menu-toggle left-sidebar-toggle">
         <i class="woffice-icon woffice-icon-celst-cross"></i>
      </span>
      <?php } ?>
   </div>
   <?php } 
      ?>
   <!-- STARTING THE MAIN NAVIGATION (left side) -->
   <nav id="navigation" class="celst-nav <?php echo esc_attr($navigation_hidden_class); ?> mobile-hidden">
      <?php
         /*
          * Display the menu
          */
         if ( !is_user_logged_in() && has_nav_menu('public')) :
             $settings_menu_public = array('theme_location' => 'public','menu_class' => 'main-modern-menu', 'menu' => '','container' => '','menu_id' => 'main-modern-menu','link_after' => '<span class="ss menu-icon fa fa-angle-down"></span>');
             wp_nav_menu( $settings_menu_public );
         else :
             if ( has_nav_menu('primary') ) :
         $settings_menu_on = array('theme_location' => 'primary','menu_class' => 'main-modern-menu', 'menu' => '','container' => '','menu_id' => 'main-modern-menu','link_after' => '<span class="sk menu-icon fa fa-angle-down"></span>');
                 wp_nav_menu( $settings_menu_on );
             else :
                 wp_page_menu(array('menu_id' => 'main-modern-menu', 'menu_class'  => 'main-modern-menu', 'show_home' => true));
             endif;
         endif; ?>

        <div class="arrows hr-arrow">
            <div class="l-arrow-btn">
                <button id="leftArrow" class="left-arrow arrow hidden">
                    <i class="woffice-icon woffice-icon-celst-arrow-left"></i>
                </button>
            </div>
            <div class="r-arrow-btn">
                <button id="rightArrow" class="right-arrow arrow">
                    <i class="woffice-icon woffice-icon-celst-arrow-right"></i>
                </button>
            </div>
	    </div>
   </nav>
   <!-- END MAIN NAVIGATION -->
</aside>
<!--End of left sidebar--->
<?php
   // FETCHING SIDEBAR POSITION
   if ($sidebar_state == "show"){
       $class = 'with-sidebar';
   } elseif ($sidebar_state == "hide") {
       /*We need to check if the user has already clicked the button*/
       if( !isset($_COOKIE['Woffice_sidebar_position']) || ! apply_filters( 'woffice_cookie_sidebar_enabled', false ) ) {
           $class = 'sidebar-hidden';
       }
       else {
           $class = '';
       }
   } else {
       $class = 'full-width';
   }
   ?>
<!-- START CENTER CONTENT -->
<div class="celst-main-content">
<div id="main-content" class="celst-main-content-container" style="position: relative;">

<?php woffice_top_celestial_navbar(); ?>


    <!-- christmas garland 2.1 -->
<div class="b-page_newyear">
    <div class="b-page__content">
        <i class="b-head-decor">
      <i class="b-head-decor__inner b-head-decor__inner_n1">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="0"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="7"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="8"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        </i>
            <i class="b-head-decor__inner b-head-decor__inner_n2">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="9"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="10"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="11"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="12"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="13"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="14"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="15"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="16"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="17"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
      </i>
            <i class="b-head-decor__inner b-head-decor__inner_n3">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="18"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="19"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="20"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="21"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="22"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="23"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="24"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="25"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="26"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
      </i>
            <i class="b-head-decor__inner b-head-decor__inner_n4">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="27"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="28"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="29"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="30"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="31"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="32"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="33"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="34"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="35"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
      </i>
            <i class="b-head-decor__inner b-head-decor__inner_n5">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="0"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="7"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="8"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
      </i>
            <i class="b-head-decor__inner b-head-decor__inner_n6">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="9"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="10"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="11"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="12"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="13"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="14"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="15"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="16"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="17"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
      </i>
            <i class="b-head-decor__inner b-head-decor__inner_n7">
        <div class="b-ball b-ball_n1 b-ball_bounce" data-note="18"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n2 b-ball_bounce" data-note="19"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n3 b-ball_bounce" data-note="20"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n4 b-ball_bounce" data-note="21"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n5 b-ball_bounce" data-note="22"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n6 b-ball_bounce" data-note="23"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n7 b-ball_bounce" data-note="24"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n8 b-ball_bounce" data-note="25"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_n9 b-ball_bounce" data-note="26"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i1"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i2"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i3"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i4"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i5"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
        <div class="b-ball b-ball_i6"><div class="b-ball__right"></div><div class="b-ball__i"></div></div>
      </i>
        </i>
    </div>
</div>       
















<?php else:
echo '
<section id="main-content" class="full-width navigation-hidden '. esc_attr($hentry_class) .'">
';
endif;
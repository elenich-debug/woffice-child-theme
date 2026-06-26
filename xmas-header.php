<?php
/**
 * The Header of WOFFICE
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!-- MAKE IT RESPONSIVE -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<?php
			$hide_seo = woffice_get_theming_option('hide_seo');
			
			if(woffice_validate_bool_option($hide_seo)){
				echo '<meta name="robots" content="noindex">';
			}
		?>
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		
				
		<?php // GET FAVICONS
		woffice_favicons();
		?>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>

	<?php // We add a class if the navigation horizontal :
	$menu_layout = get_option('woffice_theme_options');
    $menu_layout = $menu_layout['menu_layout'];
    $menu_class = ($menu_layout == "top" || isset($_GET['horizontal_menu'])) ? "menu-is-horizontal" : "vertical-modern-menu";

    /**
     * Menu layout for Woffice
     *
     * @param string
     *
     * @return string - must be either "menu-is-horizontal" or "menu-is-vertical"
     */
    $menu_class = apply_filters('woffice_menu_layout', $menu_class);
	$nav_opened_state = woffice_get_navigation_class();
	if($nav_opened_state) {
		$nav_opened_state = ' navigation-hidden';
	}
	$menu_class .= $nav_opened_state;
    //IF Fixed we add a nav class
    $header_fixed = woffice_get_theming_option('header_fixed');
    $extra_navbar_class = ( $header_fixed == "yep" ) ? 'has_fixed_navbar' :'';

    
    $sidebar_state = woffice_get_sidebar_state();
    $sidebar_show_class = ($sidebar_state != 'show') ? 'sidebar-hidden' : '';

	$design_update = woffice_get_theming_option('design_update');
	$design_update_class = ($design_update == "2.X") ? "woffice-2-5" : "";

    /**
     * Filter to change the design version
     *
     * @param string $design_update_class - you can use "woffice-2-x"
     */
    $design_update_class = apply_filters('woffice_design_version', $design_update_class);

	$is_blank_template = woffice_is_current_page_using_blank_template();
	$blank_template_class = ($is_blank_template) ? 'is-blank-template' : '';


    /**
     * SEO hentry class applied to the container
     *
     * @param string
     */
    $hentry_class = apply_filters('woffice_hentry_class', 'hentry');
     // We add a class if the menu is closed by default
    $navigation_hidden_class = woffice_get_navigation_class();
	$theme_settings_options = get_option('woffice_theme_options');
    $horizontal_on_click = isset($theme_settings_options['show_top_on_click']) ? filter_var($theme_settings_options['show_top_on_click'], FILTER_VALIDATE_BOOLEAN) : false;
	$vertical_on_hover = isset($theme_settings_options['show_left_on_hover']) ? filter_var($theme_settings_options['show_left_on_hover'], FILTER_VALIDATE_BOOLEAN) : false;
	if($vertical_on_hover && $menu_layout == 'left') {
		$menu_class .= " verical-menu-on-hover";
	}

	if($horizontal_on_click && $menu_layout == 'top' || $horizontal_on_click && isset($_GET['horizontal_menu'])) {
		$menu_class .= " horizontal-menu-on-click";
	}
	if(woffice_get_skin('celestial')){
		$menu_class .= " woffice-skin-celestial";
	}
	if(woffice_get_skin('classic')){
		$menu_class .= " woffice-skin-classic";
	}
	?>
	

	<!-- START BODY -->
	<body <?php body_class($menu_class . ' ' . $sidebar_show_class . ' ' . $extra_navbar_class .' '.$design_update_class . ' ' . $blank_template_class); ?>>

        <?php wp_body_open(); ?>

		<?php
		
			if(woffice_get_skin('celestial')){
				get_template_part('header/header', 'celestial');

			} else {
				get_template_part('header/modern-header/header', 'vertical-modern');
			 }
		
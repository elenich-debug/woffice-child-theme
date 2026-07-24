<?php

// If the user is already logged we redirect him back to the login page
if (is_user_logged_in()) {
    if (!empty($_GET['redirect'])) {
        wp_safe_redirect($_GET['redirect']);
    } else {
        wp_safe_redirect(home_url());
    }
    exit;
}

// We get the logo image
$login_logo_image = woffice_get_theming_option('login_logo_image');

// We save the classes in an array
$classes = array();

// Second landing :
$login_layout = woffice_get_theming_option('login_layout');
array_push($classes, $login_layout);

// Design version
$design_update = woffice_get_settings_option('design_update');
$design_update_class = ($design_update == "2.X") ? "woffice-2-5" : "";

/**
 * Filter to change the design version
 *
 * @param string $design_update_class - you can use "woffice-2-x"
 */
$design_update_class = apply_filters('woffice_design_version', $design_update_class);
$theme_skin = woffice_get_settings_option('theme_skin');

array_push($classes, $design_update_class);

?>
<html <?php language_attributes(); ?> style="margin-top: 0 !important;">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<!-- MAKE IT RESPONSIVE -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php // GET FAVICONS
		woffice_favicons();
		?>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/flexie.min.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>

	<body <?php body_class($classes); ?>>

		<div id="page-wrapper">
			<div id="content-container">

				<?php // Check for Revolution Slider :
				$login_revslider = woffice_get_theming_option('login_revslider'); ?>

				<div class="logo-head">
					<?php if (!empty($login_logo_image)) : ?>
						<a href="<?php echo home_url(); ?>" id="login-logo"><img src='<?php echo esc_url($login_logo_image["url"]); ?>'/></a>
					<?php endif; ?>
				</div>
				<!-- START CONTENT -->
				<section id="woffice-login" class="<?php echo (!empty($login_revslider) ? 'revslider-enabled' : 'revslider-disabled'); ?>">
					<div id="woffice-login-left">
						<div class="form-wrapper">
							<header>
								<?php if (!empty($login_logo_image)) : ?>
									<a href="<?php echo home_url(); ?>" id="login-logo"><img src='<?php echo esc_url($login_logo_image["url"]); ?>'/></a>
								<?php endif; ?>

								<?php woffice_login_render_errors(); ?>

								<?php // Login Text
								$login_text = woffice_get_theming_option('login_text');
								if (!empty($login_text)): ?>

									<div id="login-text"><?php echo wp_kses_post($login_text); ?></div>

								<?php endif; ?>

								<?php if (defined('WOFFICE_CORE_ENABLED')): ?>
									<?php woffice_login_social_render(); ?>
								<?php endif; ?>

								<!-- ---/-----copy paste user pass new feature------/---  -->
								 <?php if(woffice_get_theming_option('enable_demo_login')) {?>
									<div class="loginmmessage">
										<ul>
											<li><?php _e('Username: ','woffice');?><span class="clipboard-text" id="loginusername"><?php echo woffice_get_theming_option('demo_login_user');?></span><span class="copy-icon" data-code="loginusername"></span></li>
											<li><?php _e('Password: ','woffice'); ?> <span class="clipboard-text" id="loginPassword"><?php echo woffice_get_theming_option('demo_login_password');?></span><span class="copy-icon" data-code="loginPassword"></span></li>
										</ul>
									</div>
								<?php } ?>
								<script>
								jQuery('.copy-icon').click(function() {
										var loginId = jQuery(this).data('code');
										var loginCode = jQuery('#' + loginId).text();
										var $tempInput = jQuery('<input>');

										jQuery('body').append($tempInput);
										$tempInput.val(loginCode).select();
										document.execCommand('copy');
										$tempInput.remove();
										var copied_item = jQuery(this);
										copied_item.addClass('copied');
											setTimeout(function() {
												copied_item.removeClass('copied');
											}, 3000);
									});
								</script>
							</header>
							<div class="login-tabs-wrapper">

								<?php // Checking what form to display
								$type = (isset($_GET['type'])) ? $_GET['type'] : "";
								if ($type == "lost-password") {

									woffice_login_render_lost_password();

								} elseif ($type == "reset-password") {

									woffice_login_render_reset_password();

								} else {

									woffice_login_render_form();

								}
								?>

								<?php woffice_login_render_register(); ?>

							</div>
<!-- НАЧАЛО ЛОВУШКИ ДЛЯ БОТОВ -->
							<a href="/silok/" style="display:none;" aria-hidden="true" tabindex="-1"></a>
							<!-- КОНЕЦ ЛОВУШКИ ДЛЯ БОТОВ -->							
							<?php woffice_login_render_footer(); ?>
						</div>
					</div>
					<div id="woffice-login-right">
						<?php // Revslider :
						if (!empty($login_revslider) && shortcode_exists('rev_slider')) :
							putRevSlider($login_revslider);
						endif; ?>
					</div>
				</section>
				<!-- END CONTENT -->
			</div>
		</div>

		<?php wp_footer(); ?>

		<?php woffice_login_render_script(); ?>

		<style>
			/* Hide the login text by default, show it when class is present */
			#login-text { display: none !important; }
			.show-register-text #login-text { display: block !important; }
		</style>
		<script>
			jQuery(document).ready(function($) {
				function toggleWelcomeText() {
					if ($('#register-form').is(':visible')) {
						$('body').addClass('show-register-text');
						$('#loginform, #lostpasswordform, #go-back-to-login, #register-wrapper, a.password-lost, .social-login-btns').hide();
					} else {
						$('body').removeClass('show-register-text');
					}
				}

				// Check when buttons are clicked (Woffice uses 1000ms timeouts)
				$('#register-trigger, #goback-trigger a').on('click', function() {
					setTimeout(toggleWelcomeText, 1050);
				});

				// Initial checks on page load (important after form POST errors)
				toggleWelcomeText();
				setTimeout(toggleWelcomeText, 100);
				setTimeout(toggleWelcomeText, 500);
				setTimeout(toggleWelcomeText, 1100);

				// Monkey-patch the global Woffice functions for robustness
				if (typeof window.show_login === 'function') {
					var orig_show_login = window.show_login;
					window.show_login = function(loader) {
						orig_show_login(loader);
						$('body').removeClass('show-register-text');
					};
				}
				if (typeof window.show_register === 'function') {
					var orig_show_register = window.show_register;
					window.show_register = function() {
						orig_show_register();
						$('#loginform, #lostpasswordform, #go-back-to-login, #register-wrapper, a.password-lost, .social-login-btns').hide();
						setTimeout(function() {
							$('body').addClass('show-register-text');
						}, 1050);
					};
				}
			});
		</script>
	</body>
</html>

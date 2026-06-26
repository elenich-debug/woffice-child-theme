<?php
   /**
    * The template for displaying the footer
    */
   ?>
<?php
   $footer_content = get_option('woffice_theme_options');

   $is_blank_template = woffice_is_current_page_using_blank_template();
   ?>
<?php
   /**
    * Action 'woffice_main_container_end'
    *
    * Used to output content within the #main-content div
    *
    */
   do_action('woffice_main_container_end');
   ?>
<!-- -/-----footer start------/- -->
<footer id="main-footer">
   <?php if(isset($footer_content['show_footer_hero']) && filter_var($footer_content['show_footer_hero'], FILTER_VALIDATE_BOOLEAN)) { ?>
   <section class="woffice-hero-section">
      <div class="woffice-hero-overlay"></div>
      <div class="footer-hero-content">
         <?php echo sprintf('%s',$footer_content['footer_hero_content']);?>
      </div>
   </section>
   <?php } ?>
   <?php // IF YOU WANT TO DISPLAY WIDGET AREA IN THE FOOTER
      if(isset($footer_content['show_footer']) && filter_var($footer_content['show_footer'], FILTER_VALIDATE_BOOLEAN)) {
          $footer_layout = isset($footer_content['footer_layout']) ? $footer_content['footer_layout'] : '4-4-4';
          ?>

   <!-- START FOOTER WIDGETS -->
   <div id="widgets" class="woffice-footer" data-widgets-layout="<?php echo esc_attr( $footer_layout) ; ?>">
      <?php get_sidebar( 'footer' ); ?>
   </div>
   <?php } ?>
   <?php if(isset($footer_content['show_copyright_bar']) && filter_var($footer_content['show_copyright_bar'], FILTER_VALIDATE_BOOLEAN)) { ?>
   <!-- START COPYRIGHT -->
   <div id="copyright2">

  <?php if ( is_singular( array( 'post', 'mature' ) ) ) : ?>
    <div class="flex-container">
        <?php echo do_shortcode('[crp]'); ?>
        <div class="celst-comment-wrapper blog-single-comment">
            <?php
                // Если комментарии открыты или есть хотя бы один комментарий, загружаем шаблон комментариев.
                 if ( comments_open() || get_comments_number() ) {
                    comments_template();
                 }
            ?>
        </div>
    </div>
<?php endif; ?>

</div>

   <!-- END COPYRIGHT -->
   <?php } ?>
</footer>
</div>
</div>
<!-- END of CENTER CONTENT -->
<?php // GET SIDEBAR
   $sidebar_state = woffice_get_sidebar_state();
   $sidebar_show_class = ($sidebar_state != 'show') ? 'sidebar-hidden' : '';    
   if($sidebar_state == 'show' || $sidebar_state == 'hide') :
       get_sidebar();
   endif; 
   ?>
</div>
<!-- END row -->
<div id="celst-overlay"></div>
<div id="celst-form-modal">
   <div class="celst-form-wrapper">
      <span class="celst-form-modal-close">
            <i class="woffice-icon woffice-icon-celst-cross"></i>
      </span>
      <div class="celst-form-content"></div>
   </div>
</div>
</div>
<!-- END Wrapper -->
<!-- JAVSCRIPTS BELOW AND FILES LOADED BY WORDPRESS -->
<a href="/silok-bt/" style="display:none;"></a>

<?php wp_footer(); ?>
</body>
<!-- END BODY -->
</html>
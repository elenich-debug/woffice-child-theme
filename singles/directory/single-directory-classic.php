<?php
   /**
    * The Template for displaying all single directory item
    */
   
   global $post;
   
   $single_map             = woffice_get_directory_single_map();
   $single_fields          = woffice_get_directory_single_fields('single');
   $current_user_is_admin  = woffice_current_is_admin();
   $edit_allowed           = (Woffice_Frontend::edit_allowed('directory') == true) ? true : false;
   $delete_allowed         = (Woffice_Frontend::edit_allowed('directory', 'delete') == true) ? true : false;
   if ($edit_allowed) {
   	$process_result = Woffice_Frontend::frontend_process('directory', $post->ID);
   }
   
   get_header();  ?>
<?php // Start the Loop.
   while ( have_posts() ) : the_post(); ?>
<div id="left-content">
   <?php  //GET THEME HEADER CONTENT
      woffice_top_navbar();
      woffice_title(get_the_title());
      ?> 	
   <!-- START THE CONTENT CONTAINER -->
   <div id="content-container">
      <!-- START CONTENT -->
      <div id="content">
         <?php $post_classes = array('box','content'); ?>
         <article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
            <div class="intern-padding">
               <div class="direcotry-container">
                  <div class="row d_single_row">
                     <?php if ( $single_map || $single_fields )
                        echo '<div class="col-md-6">';
                        ?>
                     <div class="directory-content">
                        <?php /* The content + the excerpt */
                           if(is_single('directory') || is_singular('directory')){
                           	the_content('');
                           } else {
                           	the_excerpt();
                           }
                           ?>
                        <?php /* Categories */ 
                           if( has_term('', 'directory-category')): 
                           echo '<span class="directory-category"><i class="fa fa-tag"></i>';
                           echo get_the_term_list( $post->ID, 'directory-category', '', ', ' );
                           echo '</span>';
                            endif; ?>
                        <?php /* The Button */
                           $item_button_link = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'directory_item_button_link',true) : '';
                           if (!empty($item_button_link)) {
                           	$item_button_text = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'directory_item_button_text',true)  : '';
                           	$item_button_icon = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'directory_item_button_icon',true) : '';
                           	$icon = (!empty($item_button_icon)) ? '<i class="'. $item_button_icon .'"></i> ' : '';
                           
                           	echo '<div class="d_single_btn">';
                           		echo '<a href="'.$item_button_link.'" class="directory_btn">' . $item_button_text.$icon.'</a>';
                           	echo '</div>';
                           } ?>
                     </div>
                     <?php if ( $single_map || $single_fields) {
                        echo '</div>';
                        echo '<div class="col-md-6"><div class="directory-content">';
                        }
                        ?>
                     <?php echo woffice_get_directory_single_map(); ?>
                     <?php echo woffice_get_directory_single_fields('single'); ?>
                     <?php if ( $single_map || $single_fields) {
                        echo '</div></div>';
                        }
                        ?>
                  </div>
               </div>
               <?php woffice_post_nav(); ?>
            </div>
         </article>
         <div class="d_single_edit_wrapper ">
           
               <div class="d_single_edit_wrapper_inner">
                  <div class="directory_edit_btn" id="directory-bottom">
                     <?php if ($edit_allowed) : ?>
                     <a href="#" class="btn btn-default frontend-wrapper__toggle" data-action="display"><i class="fa fa-edit"></i> <?php _e("Edit Item", "woffice"); ?></a>
                     <?php endif; ?>
                  </div>
                  <?php if ($edit_allowed) : ?>
                  <?php Woffice_Frontend::frontend_render('directory', $process_result, get_the_ID()); ?>
                  <?php endif; ?>
               </div>
           
         </div>
         <div class="directory-comment wo_common_comment_wrapper">
            <?php
               // If comments are open or we have at least one comment, load up the comment template.
               if ( comments_open() || get_comments_number() ) {
               	comments_template();
               }
               ?>
         </div>
      </div>
   </div>
   <!-- END #content-container -->
</div>
<!-- END #left-content -->
<?php // END THE LOOP 
   endwhile; ?>
<?php 
get_footer();
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
      woffice_title(get_the_title());
      ?> 	
   <!-- START THE CONTENT CONTAINER -->
   <div id="content-container">
      <!-- START CONTENT -->
      <div id="content">
         <?php $post_classes = array('box','content'); ?>
         <article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
               <div class="direcotry-container">
                     <?php if ( $single_map || $single_fields )
                        echo '<div class="directory-single-top">';
                        ?> 
                     <?php echo woffice_get_directory_single_map(); ?>
                     <div class="directory-content row">
                     <div class="directory-title col-lg-6">
                        <?php
					             echo'<h2 class="directory-title"><a href="'. get_the_permalink() .'">'.get_the_title().'</a></h2>';
									?>
                        <?php /* The content + the excerpt */
                           if(is_single('directory') || is_singular('directory')){
                           	the_content('');
                           } else {
                           	the_excerpt();
                           }
                           ?>
                           </div>
                           <div class="directory-catg  col-lg-6">
                              <?php /* Categories */ 
                                 if( has_term('', 'directory-category')): 
                                 echo '<span class="directory-category">';
                                 echo get_the_term_list( $post->ID, 'directory-category', '', ', ' );
                                 echo '</span>';
                                 endif; ?>
                           </div>
                     </div>
                     <?php if ( $single_map || $single_fields) {
                        echo '</div>';
                        echo '<div class="directory-single-bottom"><div class="directory-inner">';
                        }
                        ?>
                     <?php echo woffice_get_directory_single_fields('single'); ?>
                     <div class="directory-bottom-right">
                      <?php /* The Button */
                           $item_button_link = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'directory_item_button_link',true) : '';
                           if (!empty($item_button_link)) {
                           	$item_button_text = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'directory_item_button_text',true)  : '';
                           	$item_button_icon = ( function_exists( 'get_post_meta' ) ) ? get_post_meta(get_the_ID(), 'directory_item_button_icon',true) : '';
                           	$icon = (!empty($item_button_icon)) ? '<i class="'. $item_button_icon .'"></i> ' : '';
                           
                           	echo '<div class="directory-btn">';
                           		echo '<a href="'.$item_button_link.'" class="celst-edit-btn">' . $item_button_text.$icon.'</a>';
                           	echo '</div>';
                           } ?>
                           <?php if ($edit_allowed) : ?>
                           <a href="#" class="celst-edit-btn celst-form-edit-toggle">Edit Item</a>
                           <?php endif; ?>
                     </div>
                     <?php if ( $single_map || $single_fields) {
                        echo '</div></div>';
                        }
                        ?>
               </div>  
         </article>
         <div class="celts-frontend-form-wrapper">
            <?php if ($edit_allowed) : ?>
               <?php Woffice_Frontend_Celestial::frontend_render('directory', $process_result, get_the_ID()); ?>
            <?php endif; ?> 
         </div>
       
         <div class="celst-comment-wrapper directory-single-comment">
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
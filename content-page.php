<?php
/**
 * The template used for displaying page content
 */
?>
<?php 
// CUSTOM CLASSES ADDED BY THE THEME
$post_classes = array('content', 'entry-content');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>
	<div class="intern-padding clearfix">
		<?php 
		// THE CONTENT
		the_content();
		//DISABLED IN THIS THEME
		wp_link_pages(array('echo'  => 1));
		?>
	</div>
</article>

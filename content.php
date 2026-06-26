<?php
/**
 * The template used for displaying post content
 */
?>
<?php 
// CUSTOM CLASSES ADDED BY THE THEME
$post_classes = array('content', 'entry-content');
$blog_listing_content = woffice_get_theming_option('blog_listing_content','excerpt');
$hide_image_single_post = woffice_get_theming_option('hide_image_single_post', false);
$hide_author_box = woffice_get_theming_option('hide_author_box_single_post', false);
$hide_like_counter = woffice_get_theming_option('hide_like_counter_inside_author_box',false);
$hide_learndash_meta = woffice_get_theming_option('hide_learndash_meta', false);

if(get_post_status() == 'draft')
    array_push($post_classes, 'is-draft');
?>
	<div class="col-md-6 col-lg-6 col-xl-4 blog-col">
		<div class="blog-card-wrapper d-flex h-100 mb-3">
			<div class="card blog_card">
				<div class="blog-thumb">
					<?php if (has_post_thumbnail() && (!is_single() || is_single() && !$hide_image_single_post)) : ?>
						<!-- THUMBNAIL IMAGE -->
						<?php /*GETTING THE POST THUMBNAIL URL*/
							$featured_height = (function_exists('woffice_get_post_rdx_option')) ? woffice_get_post_rdx_option(get_the_ID(), 'featured_height') : '';
							Woffice_Frontend::render_featured_image_single_post($post->ID, $featured_height);
						?>
						<?php else: ?>
						<img src="<?php echo get_stylesheet_directory_uri() ?>/images/blog.png">
				
					<?php endif; ?>
				</div>
				<div class="card-body">
					<div class="blog-title">
						<?php if (strpos(get_post_type(), 'sfwd') === FALSE || is_search()) : ?>
							<div class="intern-padding heading-container">
								<?php if (!is_single()): ?>
									<?php // THE TITLE
									if (is_sticky()):
										the_title( '<div class="heading"><h2><a href="' . esc_url( get_permalink() ) . '" class="font-weight-bold" rel="bookmark"><i class="fa fa-star text-yellow"></i>', '</a></h2></div>' );
									else: 
										the_title( '<div class="heading"><h2><a href="' . esc_url( get_permalink() ) . '" class="font-weight-bold" rel="bookmark">', '</a></h2></div>' );
									endif; ?>
								<?php else : ?>
								
									<?php // THE TITLE
									
									the_title( '<div class="heading"><h2>', '</h2></div>' ); ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="post-meta">
						<?php // We display the post meta in the top only for the blog articles
							if ($post->post_type == "post") : ?>
								<div class="intern-box">
									<?php // THE POST META
									woffice_postmetas(); ?>
								</div>
								
						<?php endif; ?>
					</div>
					<div class="blog-content">
						<?php if (is_single() || $blog_listing_content == 'content'): ?>
							<?php the_content(''); ?>
						<?php elseif($blog_listing_content == 'excerpt') : ?>
							<?php the_excerpt(); ?>

							
						<?php endif; ?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	
	<?php
	$price = get_post_meta(get_the_ID(), 'price', true); // Используем get_the_ID()
	// Проверяем, что значение существует и не пустое
	if (!empty($price)) {
		// Используем esc_html() для безопасного вывода данных
		echo '<div class="price">reward ' . esc_html($price) . ' Diamonds</div>';
	} elseif (get_post_type() === 'request') {
		echo '<div class="price">Bundle Request</div>';
	}
	?>
</article>

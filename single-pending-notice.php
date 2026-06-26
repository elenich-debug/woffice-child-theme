<?php
/**
 * Шаблон-уведомление для неопубликованных постов.
 * Создан на основе content-single.php темы Woffice.
 * Отображает всю структуру поста, но заменяет контент на уведомление.
 */

// Убеждаемся, что у нас есть доступ к глобальному объекту поста
global $post;

// Подключаем шапку сайта (header.php)
get_header(); 
?>

<div id="content" class="woofice-main-content">
	<div class="container">
		<div class="row">
			<div id="primary" class="content-area col-md-12">
				<main id="main" class="site-main" role="main">

					<?php // Начало основного цикла WordPress (хотя у нас только один пост) ?>
					<?php while ( have_posts() ) : the_post(); ?>

						<?php
						// CUSTOM CLASSES ADDED BY THE THEME
						$post_classes = array('content', 'entry-content');
						$blog_listing_content = woffice_get_theming_option('blog_listing_content','excerpt');
						$hide_image_single_post = woffice_convert_to_bool_option(woffice_get_theming_option('hide_image_single_post', false));
						$hide_author_box = woffice_get_theming_option('hide_author_box_single_post', false);
						$hide_like_counter = woffice_get_theming_option('hide_like_counter_inside_author_box', false);
						$hide_learndash_meta = woffice_get_theming_option('hide_learndash_meta', false);
						$edit_allowed           = (Woffice_Frontend::edit_allowed('post') == true) ? true : false;
						$delete_allowed         = (Woffice_Frontend::edit_allowed('post', 'delete') == true) ? true : false;
						if ($edit_allowed) {
							$process_result = Woffice_Frontend::frontend_process('post', $post->ID);
						}

						if(get_post_status() == 'draft')
							array_push($post_classes, 'is-draft');
						?>
							<div class="blog-single-col">		
									<div class="blog-single-item">
										<div class="blog-thumb">
											<?php if (is_single() && !woffice_validate_bool_option($hide_image_single_post) && has_post_thumbnail()) : ?>
												<!-- THUMBNAIL IMAGE -->
												<?php /*GETTING THE POST THUMBNAIL URL*/
													$featured_height = (function_exists('woffice_get_post_rdx_option')) ? woffice_get_post_rdx_option(get_the_ID(), 'featured_height') : '';
													Woffice_Frontend::render_featured_image_single_post($post->ID, $featured_height);
												?>
											<?php endif; ?>
										</div>
										<div class="blog-single-content">
												<div class="blog-single-meta">
													<div class="post-meta">
														<?php // We display the post meta in the top only for the blog articles
															if ($post->post_type == "post") : ?>
																<ul>
																	<?php // THE POST META
																	woffice_postmetas(); 
																	?>
																</ul>

																

																<?php
																/*
																* FRONT END EDIT
																*/
																if ($edit_allowed || $delete_allowed) { ?>
																	<div class="blog-meta-right">
																		<div class="blog-action-btn">
																			<?php
																			/**
																			 * Delete Button
																			 * From version 1.8.6 if an user is allowed to edit then is allowed also to delete
																			 * if (is_user_logged_in() && (current_user_can('edit_others_posts') || $current_user->ID == $post->post_author) ) {
																			 */
																			if($delete_allowed) {
																				echo '<a onclick="return confirm(\'' . __('Are you sure you wish to delete article :', 'woffice') . ' ' . get_the_title() . ' ?\')" href="' . get_delete_post_link(get_the_ID(), '') . '" class="celst-delete-btn">
																					 ' . __("Delete", "woffice") . '
																					</a>';
																			}
																			?>
																			<?php if($edit_allowed) : ?>
																				<a href="#" class="celst-edit-btn celst-form-edit-toggle" data-action="display"> <?php _e("Edit", "woffice"); ?></a>
																			<?php endif; ?>	
																		</div>
																	</div>
																<?php } ?>
															<?php endif; ?>
													</div>
												</div>

												<div class="blog-single-title">
													<?php if (strpos(get_post_type(), 'sfwd') === FALSE || is_search()) : ?>
														<div class="intern-padding heading-container">
															<?php // THE TITLE
															$show_title_box = woffice_get_reduxsettings_option('show_title_box');
															if(!woffice_validate_bool_option($show_title_box) ){
															the_title( '<div class="heading"><h2>', '</h2></div>' );}?>
														</div>
													<?php endif; ?>
												</div>
											
											<!--ЭТОТ БЛОК МЫ ЗАМЕНЯЕМ-->
											<div class="blog-content">
												<div class="entry-content" style="padding: 30px; border: 2px dashed #ffc107; text-align: center; margin: 20px 0; background-color: #fffaf0;">
													<h3 style="margin-top:0;">Эта статья готовится к публикации!</h3>
													<p>Мы уже завершили работу над материалом, и сейчас он ожидает своей очереди на публикацию.</p>
													<p>Вы можете добавить эту страницу в закладки, чтобы вернуться к ней позже. Спасибо за ваш интерес!</p>
												</div>
											</div>
											<!--КОНЕЦ ЗАМЕНЕННОГО БЛОКА-->

						<?php
						// Проверяем, является ли текущая запись типом "request"
						if (get_post_type() === 'request') {
							// Если да, отображаем блок
							echo '<div class="note">Note: Community members need a <a href="http://3d-stuff.community/wiki/community-membership-structure/">Subscriber </a>role or higher to complete requests and vote.</div>';
						}
						?>
											<?php if (is_single() && get_post_type() == 'post' && !woffice_validate_bool_option($hide_author_box)) : ?>
											<div class="blog-authorbox">
												<div class="blog-authorbox-left">
												<?php echo get_avatar(get_the_author_meta('ID'), 96, '', '', array('class' => 'rounded-circle')); ?>
												</div>
												<div class="blog-authorbox-right">
													<?php 
														$display = woffice_get_name_to_display(get_the_author_meta('ID'));
													?>
													<?php 
													echo '<div class="author-title">';
														if (function_exists('bp_is_active')) {
															$mem_domain = function_exists('bp_members_get_user_url') ? bp_members_get_user_url(get_the_author_meta('ID')) : bp_core_get_user_domain(get_the_author_meta('ID'));
															echo '<h3><a href="' . $mem_domain . '">'.$display.'</a></h3>';
														} else {
															echo '<h3>'.$display.'</h3>';
														}
														echo '<p class="like-text">'.__('Did you like this stuff ?','woffice').'</p>';
														echo '</div>';
													?>
													<?php 
													$desc = get_the_author_meta('description');
													if(!empty($desc)) {
														echo '<p>'.get_the_author_meta('description').'</p>';	
													} ?>
													<?php if(!woffice_validate_bool_option($hide_like_counter)): ?>
													<div class="blog-like-container">
														<?php 
															$post_ID = get_the_id();
															$vote_count = get_post_meta($post_ID, "votes_count", true);
															$vote_count_disp = (empty($vote_count)) ? '0' : $vote_count; 
															echo '<p class="wiki-like">';
																if(Woffice_Blog::like_user_has_already_voted($post_ID)) {
																	echo ' <span title="'.__('I like this post', 'woffice').'" class="like alreadyvoted">
																		<i class="woffice-icon woffice-icon-like"></i>
																	</span>';
																} else { 
																	echo '<a href="javascript:void(0)" data-post_id="'.$post_ID.'">
																		<i class="woffice-icon woffice-icon-like"></i>
																	</a>';
																}
																echo '<span class="count">'.$vote_count_disp.'</span>';
															echo '</p>';
														?>
													</div>

													<?php 
													$website_url = get_post_meta($post->ID, "website", true);
													if (!empty($website_url)) : ?>
														<div class="author_btn">
															<div class="btn btn-bmark no-hover"> <?php echo do_shortcode('[cbxwpbookmarkbtn]'); ?> </div>
															<a href="<?php echo esc_url($website_url); ?>" target="_blank" rel="noopener sponsored" class="btn btn-buy">
																<i class="fa fa-shopping-cart"></i>Buy product
															</a>
														</div>
													<?php endif; ?>

													<?php endif; ?>
												</div>
											</div>	
										<?php endif; ?>
									


									</div>
							</div>

					<?php endwhile; // Конец цикла ?>

				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- .row -->
	</div><!-- .container -->
</div><!-- #content -->

<?php
// Подключаем подвал сайта (footer.php)
get_footer();
?>
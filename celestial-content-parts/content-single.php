<?php
/**
 * The template used for displaying post content
 */
?>
<?php 
// CUSTOM CLASSES ADDED BY THE THEME
$post_classes = array('content', 'entry-content');
$blog_listing_content = woffice_get_theming_option('blog_listing_content','excerpt');
$hide_image_single_post = woffice_convert_to_bool_option(woffice_get_theming_option('hide_image_single_post', false));
$hide_author_box = woffice_get_theming_option('hide_author_box_single_post', false);
$hide_like_counter = woffice_get_theming_option('hide_like_counter_inside_author_box', false);
$hide_learndash_meta = woffice_get_theming_option('hide_learndash_meta', false);
$edit_allowed           = (Woffice_Frontend::edit_allowed($post->post_type) == true) ? true : false;
$delete_allowed         = (Woffice_Frontend::edit_allowed($post->post_type, 'delete') == true) ? true : false;
// Дополнительная проверка: разрешаем редактирование/удаление для постов
// со статусом Pending Review, если пользователь — автор или администратор.
// Родительская тема учитывает только 'publish' и 'draft', игнорируя 'pending'.
if ( ! $edit_allowed && get_post_status() === 'pending' && is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	if ( $post->post_author == $current_user->ID || woffice_current_is_admin() ) {
		$edit_allowed   = true;
		$delete_allowed = true;
	}
}
if ($edit_allowed) {
	$process_result = Woffice_Frontend::frontend_process($post->post_type, $post->ID);
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
									if ($post->post_type == "post" || $post->post_type == "mature" || $post->post_type == "bundle") : ?>
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
									<?php if (!is_single()): ?>
										<?php // THE TITLE
										if (is_sticky()):
											the_title( '<div class="heading"><h2><a href="' . esc_url( get_permalink() ) . '" class="font-weight-bold" rel="bookmark"><i class="fa fa-star text-yellow"></i>', '</a></h2></div>' );
										else: 
											the_title( '<div class="heading"><h2><a href="' . esc_url( get_permalink() ) . '" class="font-weight-bold" rel="bookmark">', '</a></h2></div>' );
										endif; ?>
									<?php else : ?>
										<?php // THE TITLE
										$show_title_box = woffice_get_reduxsettings_option('show_title_box');
										if(!woffice_validate_bool_option($show_title_box) ){
										the_title( '<div class="heading"><h2>', '</h2></div>' );}?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<div class="blog-content">
						<?php if (is_single() || $blog_listing_content == 'content'): ?>
							<?php the_content(''); ?>
						<?php elseif($blog_listing_content == 'excerpt') : ?>
							<?php the_excerpt(5); ?>
						<?php endif; ?>
					</div>
<?php
// Проверяем, является ли текущая запись типом "request"
if (get_post_type() === 'request') {
    // Если да, отображаем блок
    echo '<div class="note">Note: Community members need a <a href="https://3d-stuff.community/wiki/community-membership-structure/">Contributor </a>role or higher to complete requests and vote.</div>';
}
?>
					<?php if (is_single() && (get_post_type() == 'post' || get_post_type() == 'mature' || get_post_type() == 'bundle') && !woffice_validate_bool_option($hide_author_box)) : ?>
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
											echo '<a href="javascript:void(0)" rel="nofollow" data-post_id="'.$post_ID.'">
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

<?php 
							// НАЧАЛО БЛОКА УВЕДОМЛЕНИЯ
							if (is_single()) {
							    $post_datetime = new DateTime(get_the_date('Y-m-d'));
							    $current_datetime = new DateTime('now');
							    $interval = $current_datetime->diff($post_datetime);
							    $total_months_passed = ($interval->y * 12) + $interval->m;
							
							    if ($total_months_passed >= 24) {
							        $years_passed = $interval->y;
							        $author_name = get_the_author();
							        
							        $notification_text = sprintf(
							            '<div class="opn-icon-container">
							                <i class="fa fa-archive"></i>
							            </div>
							            <div class="opn-content">
							                <div class="opn-title">Archived Community Asset</div>
							                <div class="opn-description">
							                    Community member <strong>%s</strong> shared this asset %d years ago, so the file may no longer be instantly available for download.<br>
							                    But don’t worry — we preserve all community uploads in our secure archives. You can simply <a href="#respond" class="opn-link">send a request to restore the file</a>, and we will bring it back online.
							                </div>
							            </div>',
							            esc_html($author_name),
							            $years_passed
							        );
							        
							        echo '<div id="old-post-notification-wrapper"><div id="old-post-notification">' . $notification_text . '</div></div>';
							    }
							}
							// КОНЕЦ БЛОКА УВЕДОМЛЕНИЯ
							?>

						</div> <!-- Закрытие .blog-authorbox-right -->
			</div>
	</div>
</article>

<!-- Обновленный блок пагинации -->
<div class="blog-next-page center animate-me fadeInUp" role="navigation">
    <?php
    $pagination_type = $_COOKIE['pagination_type'] ?? 'date';
    $current_author_id = get_the_author_meta('ID');
    
    if($pagination_type === 'author') {
        // Исправленный запрос для автора
        $args = array(
            'post_type'      => 'post',
            'author'         => $current_author_id,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'ASC' // Изменено с DESC на ASC
        );
        
        $author_posts = get_posts($args);
        $current_index = array_search(get_the_ID(), array_column($author_posts, 'ID'));
        
        // Логика теперь идентична другим типам пагинации
        $prev_post = ($current_index > 0) ? $author_posts[$current_index - 1] : null; // Более старый пост
        $next_post = ($current_index < count($author_posts) - 1) ? $author_posts[$current_index + 1] : null; // Более новый пост
    } else {
        $in_same_term = ($pagination_type === 'category');
        $prev_post = get_previous_post($in_same_term);
        $next_post = get_next_post($in_same_term);
    }
    ?>
    
    <?php if ($prev_post) : ?>
        <a class="btn btn-default prev-post" href="<?php echo get_permalink($prev_post->ID); ?>" data-post-url="<?php echo get_permalink($prev_post->ID); ?>">
            <i class="fa fa-hand-point-left"></i> <?php echo $prev_post->post_title; ?>
        </a>
    <?php endif; ?>
    
<!-- Фильтры для пагинации -->
<div class="pagination-type-switcher">
    <label>
        <input type="radio" name="pagination_type" value="date"> By Date
    </label>
    <label>
        <input type="radio" name="pagination_type" value="category"> By Category
    </label>
    <label>
        <input type="radio" name="pagination_type" value="author"> By Author
    </label>
</div>


    <?php if ($next_post) : ?>
        <a class="btn btn-default next-post" href="<?php echo get_permalink($next_post->ID); ?>" data-post-url="<?php echo get_permalink($next_post->ID); ?>">
            <?php echo $next_post->post_title; ?> <i class="fa fa-hand-point-right"></i>
        </a>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка нажатий клавиш
    document.addEventListener('keydown', function(event) {
        // Стрелка влево (предыдущий пост)
        if (event.key === 'ArrowLeft') {
            const prevLink = document.querySelector('.blog-next-page .prev-post');
            if (prevLink) {
                window.location.href = prevLink.getAttribute('data-post-url');
            }
        }
        
        // Стрелка вправо (следующий пост)
        if (event.key === 'ArrowRight') {
            const nextLink = document.querySelector('.blog-next-page .next-post');
            if (nextLink) {
                window.location.href = nextLink.getAttribute('data-post-url');
            }
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const getCookie = (name) => {
        const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`));
        return match ? decodeURIComponent(match[2]) : 'date';
    };

    const setCookie = (name, value) => {
        document.cookie = `${name}=${encodeURIComponent(value)}; max-age=31536000; path=/; SameSite=Lax`;
    };

    // Set current selection
    const currentType = getCookie('pagination_type');
    const radioInputs = document.querySelectorAll(`input[name="pagination_type"]`);

    if (radioInputs.length) {
        radioInputs.forEach(radio => {
            if (radio.value === currentType) {
                radio.checked = true;
            }

            // Handle changes
            radio.addEventListener('change', (e) => {
                setCookie('pagination_type', e.target.value);
                window.location.reload();
            });
        });
    }
});

</script>

<?php echo do_shortcode('[voting_buttons]'); ?>	
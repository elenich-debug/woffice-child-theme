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
										// ADMIN QUICK-EDIT BUTTONS — только для администраторов и редакторов
										if ( current_user_can('edit_others_posts') ) : ?>
											<div class="blog-meta-right">
												<div class="blog-action-btn">
													<button
														type="button"
														class="admin-edit-btn pqem-open-btn"
														data-post-id="<?php echo get_the_ID(); ?>"
														data-nonce="<?php echo wp_create_nonce('3ds_admin_panel_nonce'); ?>"
														title="Edit post">
														<i class="fa fa-pencil-alt"></i> Edit
													</button>
													<button
														type="button"
														class="admin-delete-btn pqem-delete-btn"
														data-post-id="<?php echo get_the_ID(); ?>"
														data-post-title="<?php echo esc_attr(get_the_title()); ?>"
														data-nonce="<?php echo wp_create_nonce('3ds_admin_panel_nonce'); ?>"
														title="Move to trash">
														<i class="fa fa-trash"></i> Delete
													</button>
												</div>
											</div>
										<?php endif; ?>

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
    echo '<div class="note">Note: A <a href="https://3d-stuff.community/wiki/community-membership-structure/">Contributor</a> role or higher is required to fulfill requests and vote.</div>';
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

<?php if ( current_user_can('edit_others_posts') ) : ?>
<!-- ADMIN QUICK-EDIT MODAL -->
<div id="pqem-overlay" role="dialog" aria-modal="true" aria-labelledby="pqem-dialog-title">
	<div class="pqem-dialog">
		<div class="pqem-header">
			<span class="pqem-status-msg" id="pqem-status"></span>
			<div class="pqem-footer-left">
				<button type="button" class="pqem-btn pqem-btn-cancel" id="pqem-cancel">Cancel</button>
				<button type="button" class="pqem-btn pqem-btn-draft" id="pqem-save-draft">
					<i class="fa fa-save"></i> Save Draft
				</button>
				<button type="button" class="pqem-btn pqem-btn-publish" id="pqem-publish">
					<i class="fa fa-check"></i> Publish
				</button>
			</div>
		</div>
		<div class="pqem-body">
			<input type="hidden" id="pqem-post-id" value="">
			<input type="hidden" id="pqem-nonce" value="">
			<input type="hidden" id="pqem-slug" value="">

			<div class="pqem-field">
				<label class="pqem-label" for="pqem-title">Title</label>
				<input type="text" id="pqem-title" class="pqem-input" placeholder="Post title...">
			</div>

			<div class="pqem-row-2">
				<div class="pqem-field">
					<label class="pqem-label" for="pqem-post-type">Post Type</label>
					<select id="pqem-post-type" class="pqem-select">
						<option value="post">post</option>
						<option value="mature">mature</option>
					</select>
				</div>
				<div class="pqem-field">
					<label class="pqem-label" for="pqem-post-status">Status</label>
					<select id="pqem-post-status" class="pqem-select">
						<option value="publish">Published</option>
						<option value="pending">Pending Review</option>
						<option value="draft">Draft</option>
					</select>
				</div>
			</div>

			<!-- Категории + Теги -->
			<div class="pqem-tax-row">
				<details class="pqem-tax-panel" id="pqem-cats-panel">
					<summary class="pqem-tax-summary">
						<span class="dashicons dashicons-category"></span>
						Categories <span class="pqem-tax-badge" id="pqem-cats-badge">0</span>
					</summary>
					<div class="pqem-tax-list" id="pqem-cats-list"></div>
				</details>
				<details class="pqem-tax-panel" id="pqem-tags-panel">
					<summary class="pqem-tax-summary">
						<span class="dashicons dashicons-tag"></span>
						Tags <span class="pqem-tax-badge" id="pqem-tags-badge">0</span>
					</summary>
					<div class="pqem-tax-list" id="pqem-tags-list"></div>
				</details>
			</div>

			<div class="pqem-field">
				<label class="pqem-label" for="pqem-content">Content <span style="font-weight:400;text-transform:none;letter-spacing:0;">(HTML)</span></label>
				
				<div class="pqem-toolbar">
					<button type="button" id="pqem-warp-btn" class="pqem-editor-btn" title="Обернуть изображения"><span class="dashicons dashicons-cover-image" style="color:#F44336;"></span></button>
					<button type="button" id="pqem-swap-btn" class="pqem-editor-btn" title="Поменять изображения местами"><span class="dashicons dashicons-image-flip-horizontal" style="color:#FF9800;"></span></button>
					<button type="button" id="pqem-paste-second-img-btn" class="pqem-editor-btn" title="Вставить 2-е изображение из буфера"><span class="dashicons dashicons-clipboard" style="color:#2271b1;"></span></button>
					<button type="button" id="pqem-replace-dl-link-btn" class="pqem-editor-btn" title="Заменить ссылку на скачивание из буфера"><span class="dashicons dashicons-download" style="color:#673AB7;"></span></button>
					<button type="button" id="pqem-inject-text-btn" class="pqem-editor-btn" title="Вставить текст из буфера после изображений"><span class="dashicons dashicons-text-page" style="color:#607D8B;"></span></button>
					<button type="button" id="pqem-clear-btn" class="pqem-editor-btn" title="Очистить алмазы (mycred_sell_this)"><span class="dashicons dashicons-star-filled" style="color:#8BC34A;"></span></button>
				</div>

				<textarea id="pqem-content" class="pqem-textarea" placeholder="Post content (HTML)..."></textarea>
			</div>
		</div>
	</div>
</div>

<div id="pqem-delete-overlay" role="dialog" aria-modal="true">
	<div class="pqem-delete-dialog">
		<h3 class="pqem-delete-title">Move to Trash</h3>
		<p class="pqem-delete-desc" id="pqem-delete-desc"></p>
		<div class="pqem-delete-actions">
			<button type="button" class="pqem-btn pqem-btn-cancel" id="pqem-delete-cancel">Cancel</button>
			<button type="button" class="pqem-btn pqem-btn-delete" id="pqem-delete-confirm">Move to Trash</button>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Обновленный блок пагинации -->
<div class="blog-next-page center animate-me fadeInUp" role="navigation">
    <?php
    $pagination_type = $_COOKIE['pagination_type'] ?? 'date';
    $current_author_id = get_the_author_meta('ID');
    
    if($pagination_type === 'author') {
        global $wpdb;
        $current_post_date = get_post_field('post_date', get_the_ID());
        
        $prev_post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND post_author = %d AND post_date < %s ORDER BY post_date DESC LIMIT 1",
            $current_author_id, $current_post_date
        ));
        $prev_post = $prev_post_id ? get_post($prev_post_id) : null;

        $next_post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND post_author = %d AND post_date > %s ORDER BY post_date ASC LIMIT 1",
            $current_author_id, $current_post_date
        ));
        $next_post = $next_post_id ? get_post($next_post_id) : null;
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
    // --- 1. Логика переключения типа пагинации ---

    // Надежная функция получения куки
    const getCookie = (name) => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return decodeURIComponent(parts.pop().split(';').shift());
        }
        return 'date'; // значение по умолчанию
    };

    const setCookie = (name, value) => {
        document.cookie = `${name}=${encodeURIComponent(value)}; max-age=31536000; path=/; SameSite=Lax`;
    };

    const currentType = getCookie('pagination_type');
    const radioInputs = document.querySelectorAll('input[name="pagination_type"]');

    radioInputs.forEach(radio => {
        if (radio.value === currentType) {
            radio.checked = true;
        }

        radio.addEventListener('change', (e) => {
            // Блокируем кнопки для визуального отклика
            radioInputs.forEach(r => r.disabled = true);
            
            setCookie('pagination_type', e.target.value);
            window.location.reload();
        });
    });

    // --- 2. Логика навигации по стрелкам клавиатуры ---

    document.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') {
            const prevLink = document.querySelector('.blog-next-page .prev-post');
            if (prevLink) {
                window.location.href = prevLink.getAttribute('data-post-url') || prevLink.href;
            }
        }
        
        if (event.key === 'ArrowRight') {
            const nextLink = document.querySelector('.blog-next-page .next-post');
            if (nextLink) {
                window.location.href = nextLink.getAttribute('data-post-url') || nextLink.href;
            }
        }
    });
});
</script>

<?php if ( current_user_can('edit_others_posts') ) : ?>
<script>
(function() {
	'use strict';

	const ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';

	// Перемещаем модальные окна прямо в <body>, чтобы избежать проблем со stacking context 
	// (перекрытие белой шапкой темы)
	const overlay = document.getElementById('pqem-overlay');
	const deleteOverlay = document.getElementById('pqem-delete-overlay');
	if (overlay) document.body.appendChild(overlay);
	if (deleteOverlay) document.body.appendChild(deleteOverlay);

	// ---- Helpers ----
	function setStatus(msg, type = '') {
		const el = document.getElementById('pqem-status');
		if (!el) return;
		el.textContent = msg;
		el.className = 'pqem-status-msg' + (type ? ' is-' + type : '');
	}

	function setBusy(busy) {
		['pqem-save-draft', 'pqem-publish', 'pqem-cancel'].forEach(id => {
			const btn = document.getElementById(id);
			if (btn) btn.disabled = busy;
		});
	}

	// ---- Открытие/закрытие модала ----
	function lockScroll() {
		if (document.body.classList.contains('pqem-scroll-locked')) return;
		const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
		document.body.style.overflow = 'hidden';
		if (scrollbarWidth > 0) {
			document.body.style.paddingRight = scrollbarWidth + 'px';
		}
		document.body.classList.add('pqem-scroll-locked');
	}

	function unlockScroll() {
		// Разблокируем только если оба окна закрыты
		const mainOpen = document.querySelector('#pqem-overlay.is-open');
		const delOpen  = document.querySelector('#pqem-delete-overlay.is-open');
		if (!mainOpen && !delOpen) {
			document.body.style.overflow = '';
			document.body.style.paddingRight = '';
			document.body.classList.remove('pqem-scroll-locked');
		}
	}

	function openModal() {
		const overlay = document.getElementById('pqem-overlay');
		if (!overlay) return;

		lockScroll();
		overlay.classList.add('is-open');
		setStatus('');
	}

	function closeModal() {
		const overlay = document.getElementById('pqem-overlay');
		if (!overlay) return;

		overlay.classList.remove('is-open');
		unlockScroll();
	}

	// ---- Заполнить форму данными поста ----
	function populateForm(data) {
		document.getElementById('pqem-post-id').value   = data.ID;
		document.getElementById('pqem-title').value     = data.title;
		document.getElementById('pqem-slug').value      = data.slug;
		
		const contentEl = document.getElementById('pqem-content');
		contentEl.value = data.content;

		const typeSelect   = document.getElementById('pqem-post-type');
		const statusSelect = document.getElementById('pqem-post-status');
		if (typeSelect)   typeSelect.value   = data.post_type;
		if (statusSelect) statusSelect.value = data.post_status;

		// --- Чекбоксы категорий (отмеченные — в начале каждой группы) ---
		const catsList  = document.getElementById('pqem-cats-list');
		const catsBadge = document.getElementById('pqem-cats-badge');
		if (catsList && data.categories) {
			catsList.innerHTML = '';
			let checkedCount = 0;

			// Разбиваем массив на группы: [ { header, items[] }, ... ]
			const groups = [];
			let currentGroup = null;
			data.categories.forEach(cat => {
				if (cat.group) {
					currentGroup = { header: cat, items: [] };
					groups.push(currentGroup);
				} else if (currentGroup) {
					currentGroup.items.push(cat);
				}
			});

			// Рендерим: внутри каждой группы сначала отмеченные, потом остальные
			groups.forEach(g => {
				const sep = document.createElement('div');
				sep.className = 'pqem-tax-group-label';
				sep.textContent = g.header.name;
				catsList.appendChild(sep);

				const sorted = [
					...g.items.filter(c => c.checked),
					...g.items.filter(c => !c.checked),
				];
				sorted.forEach(cat => {
					const label = document.createElement('label');
					label.className = 'pqem-tax-item';
					const cb = document.createElement('input');
					cb.type      = 'checkbox';
					cb.value     = cat.id;
					cb.dataset.taxType = 'category';
					cb.className = 'pqem-tax-cb';
					if (cat.checked) { cb.checked = true; checkedCount++; }
					cb.addEventListener('change', updateTaxBadges);
					label.appendChild(cb);
					label.appendChild(document.createTextNode(' ' + cat.name));
					catsList.appendChild(label);
				});
			});

			if (catsBadge) catsBadge.textContent = checkedCount;
		}

		// --- Чекбоксы тегов (отмеченные — в начале списка) ---
		const tagsList  = document.getElementById('pqem-tags-list');
		const tagsBadge = document.getElementById('pqem-tags-badge');
		if (tagsList && data.tags) {
			tagsList.innerHTML = '';
			let checkedCount = 0;

			// Сортируем: сначала отмеченные, потом остальные (алфавит внутри каждой группы сохраняется)
			const sortedTags = [
				...data.tags.filter(t => t.checked),
				...data.tags.filter(t => !t.checked),
			];
			sortedTags.forEach(tag => {
				const label = document.createElement('label');
				label.className = 'pqem-tax-item';
				const cb = document.createElement('input');
				cb.type      = 'checkbox';
				cb.value     = tag.id;
				cb.dataset.taxType = 'tag';
				cb.className = 'pqem-tax-cb';
				if (tag.checked) { cb.checked = true; checkedCount++; }
				cb.addEventListener('change', updateTaxBadges);
				label.appendChild(cb);
				label.appendChild(document.createTextNode(' ' + tag.name));
				tagsList.appendChild(label);
			});

			if (tagsBadge) tagsBadge.textContent = checkedCount;
		}

		// Ставим курсор в конец и скроллим текст вниз
		requestAnimationFrame(() => {
			contentEl.focus();
			contentEl.selectionStart = contentEl.selectionEnd = contentEl.value.length;
			contentEl.scrollTop = contentEl.scrollHeight;
		});
	}

	// Обновить цифры в бэджах при переключении чекбокса
	function updateTaxBadges() {
		const catsBadge = document.getElementById('pqem-cats-badge');
		const tagsBadge = document.getElementById('pqem-tags-badge');
		if (catsBadge) {
			catsBadge.textContent = document.querySelectorAll('#pqem-cats-list .pqem-tax-cb:checked').length;
		}
		if (tagsBadge) {
			tagsBadge.textContent = document.querySelectorAll('#pqem-tags-list .pqem-tax-cb:checked').length;
		}
	}

	// ---- Загрузить данные поста по AJAX ----
	function loadPost(postId, nonce) {
		setStatus('Loading…');
		setBusy(true);
		document.getElementById('pqem-nonce').value = nonce;

		const body = new URLSearchParams({
			action:  '3ds_admin_get_post',
			post_id: postId,
			nonce:   nonce,
		});

		fetch(ajaxUrl, { method: 'POST', body })
			.then(r => r.json())
			.then(json => {
				if (json.success) {
					populateForm(json.data);
					setStatus('');
				} else {
					setStatus(json.data?.message || 'Error loading post.', 'error');
				}
			})
			.catch(() => setStatus('Network error.', 'error'))
			.finally(() => setBusy(false));
	}

	// ---- Сохранить пост ----
	function savePost(forceStatus) {
		const postId  = document.getElementById('pqem-post-id').value;
		const nonce   = document.getElementById('pqem-nonce').value;
		const title   = document.getElementById('pqem-title').value.trim();
		const slug    = document.getElementById('pqem-slug').value.trim();
		const content = document.getElementById('pqem-content').value;
		const type    = document.getElementById('pqem-post-type').value;
		const status  = forceStatus || document.getElementById('pqem-post-status').value;

		if (!title) {
			setStatus('Title cannot be empty.', 'error');
			return;
		}

		setBusy(true);
		setStatus('Saving...', '');

		// Собираем категории/теги
		const catIds = Array.from(document.querySelectorAll('#pqem-cats-list .pqem-tax-cb:checked')).map(cb => cb.value);
		const tagIds = Array.from(document.querySelectorAll('#pqem-tags-list .pqem-tax-cb:checked')).map(cb => cb.value);

		const body = new URLSearchParams({
			action:       '3ds_admin_update_post',
			post_id:      postId,
			nonce:        nonce,
			title:        title,
			slug:         slug,
			content:      content,
			post_type:    type,
			post_status:  status,
		});
		catIds.forEach(id => body.append('category_ids[]', id));
		tagIds.forEach(id => body.append('tag_ids[]', id));

		fetch(ajaxUrl, { method: 'POST', body })
			.then(r => r.json())
			.then(json => {
				setBusy(false);
				if (json.success) {
					setStatus('Saved!', 'success');
					setTimeout(() => window.location.reload(), 600);
				} else {
					setStatus(json.data?.message || 'Error saving post.', 'error');
				}
			})
			.catch(err => {
				setBusy(false);
				setStatus('Network error.', 'error');
			});
	}

	// ---- Логика удаления ----
	let _deleteBtnPending = null;

	function openDeleteModal(btn) {
		const overlay = document.getElementById('pqem-delete-overlay');
		if (!overlay) return;

		_deleteBtnPending = btn;
		const postTitle = btn.dataset.postTitle || 'this post';
		document.getElementById('pqem-delete-desc').textContent = 'Are you sure you want to move "' + postTitle + '" to the trash?';

		lockScroll();
		overlay.classList.add('is-open');
	}

	function closeDeleteModal() {
		const overlay = document.getElementById('pqem-delete-overlay');
		if (!overlay) return;

		overlay.classList.remove('is-open');
		unlockScroll();
	}

	function performDelete() {
		if (!_deleteBtnPending) return;
		const btn = _deleteBtnPending;
		closeDeleteModal();

		const postId = btn.dataset.postId;
		const nonce  = btn.dataset.nonce;

		btn.disabled = true;
		btn.innerHTML = '<span class="pqem-spinner"></span> Deleting…';

		const body = new URLSearchParams({
			action:  '3ds_admin_delete_post',
			post_id: postId,
			nonce:   nonce,
		});

		fetch(ajaxUrl, { method: 'POST', body })
			.then(r => r.json())
			.then(json => {
				if (json.success) {
					window.location.href = json.data.redirect_url;
				} else {
					alert(json.data?.message || 'Could not delete post.');
					btn.disabled = false;
					btn.innerHTML = '<i class="fa fa-trash"></i> Delete';
				}
			})
			.catch(() => {
				alert('Network error.');
				btn.disabled = false;
				btn.innerHTML = '<i class="fa fa-trash"></i> Delete';
			});
	}

	// ---- Event listeners ----
	document.addEventListener('click', function(e) {

		// Открыть редактор
		const editBtn = e.target.closest('.pqem-open-btn');
		if (editBtn) {
			openModal();
			loadPost(editBtn.dataset.postId, editBtn.dataset.nonce);
			return;
		}

		// Удалить пост (открыть модал подтверждения)
		const delBtn = e.target.closest('.pqem-delete-btn');
		if (delBtn) {
			openDeleteModal(delBtn);
			return;
		}

		// Подтверждение или отмена удаления
		if (e.target.closest('#pqem-delete-cancel')) {
			closeDeleteModal();
			return;
		}
		if (e.target.closest('#pqem-delete-confirm')) {
			performDelete();
			return;
		}

		// Закрыть модал: кнопка Cancel или X
		if (e.target.closest('#pqem-close') || e.target.closest('#pqem-cancel')) {
			closeModal();
			return;
		}

		// Закрыть модалы: клик по оверлею вне диалога
		const overlay = document.getElementById('pqem-overlay');
		if (overlay && e.target === overlay) {
			closeModal();
			return;
		}
		const delOverlay = document.getElementById('pqem-delete-overlay');
		if (delOverlay && e.target === delOverlay) {
			closeDeleteModal();
			return;
		}

		// Сохранить как черновик
		if (e.target.closest('#pqem-save-draft')) {
			savePost('draft');
			return;
		}

		// Опубликовать
		if (e.target.closest('#pqem-publish')) {
			savePost('publish');
			return;
		}

		// --- Кнопки редактора (Editor Tools) ---
		const processEditorContent = (callback) => {
			const el = document.getElementById('pqem-content');
			const tempDiv = jQuery('<div>').html(el.value);
			if (callback(tempDiv) === false) return;
			el.value = tempDiv.html();
		};

		// 1. Обернуть изображения
		if (e.target.closest('#pqem-warp-btn')) {
			e.preventDefault();
			processEditorContent(function($dom) {
				if ($dom.find('.image-container').length > 0) { setStatus('Изображения уже обёрнуты!', 'warning'); return false; }
				const i = $dom.find('img');
				if (i.length === 0) { setStatus('Нет изображений в тексте!', 'error'); return false; }
				if (i.length > 2) { setStatus('В тексте больше двух изображений!', 'warning'); return false; }
				const f = i.first();
				f.wrap('<div class="image-container"></div>');
				if (i.length === 2) { f.after(i.eq(1)); }
				setStatus('Изображения успешно обёрнуты!', 'success');
			});
			return;
		}

		// 2. Поменять местами изображения
		if (e.target.closest('#pqem-swap-btn')) {
			e.preventDefault();
			processEditorContent(function($dom) {
				const c = $dom.find('.image-container');
				if (c.length === 0) { setStatus('Не найден контейнер для обмена.', 'warning'); return false; }
				const i = c.find('img');
				if (i.length < 2) { setStatus('В контейнере меньше двух изображений для обмена.', 'warning'); return false; }
				i.eq(1).insertBefore(i.eq(0));
				setStatus('Изображения поменялись местами!', 'success');
			});
			return;
		}

		// 3. Вставить 2-е изображение из буфера
		if (e.target.closest('#pqem-paste-second-img-btn')) {
			e.preventDefault();
			if (!navigator.clipboard || !navigator.clipboard.readText) { setStatus('API буфера обмена не поддерживается.', 'error'); return; }
			navigator.clipboard.readText().then(t => {
				const u = t.trim();
				if (!u) { setStatus('Буфер обмена пуст!', 'warning'); return; }
				if (!/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i.test(u)) { setStatus('Ссылка не ведет на изображение (jpg, png, webp)!', 'error'); return; }
				processEditorContent(function($dom) {
					const c = $dom.find('.image-container');
					if (c.length === 0) { setStatus('Контейнер .image-container не найден!', 'error'); return false; }
					const m = c.find('img');
					if (m.length >= 2) { m.eq(1).attr('src', u); setStatus('Ссылка на 2-е изображение заменена!', 'success'); }
					else if (m.length === 1) { c.append(jQuery('<img>').attr('src', u)); setStatus('2-е изображение добавлено!', 'success'); }
					else { setStatus('В контейнере нет изображений!', 'warning'); return false; }
				});
			}).catch(() => setStatus('Ошибка чтения буфера обмена!', 'error'));
			return;
		}

		// 4. Заменить ссылку на скачивание (qtyfiles)
		if (e.target.closest('#pqem-replace-dl-link-btn')) {
			e.preventDefault();
			if (!navigator.clipboard || !navigator.clipboard.readText) { setStatus('API буфера обмена не поддерживается.', 'error'); return; }
			navigator.clipboard.readText().then(t => {
				const u = t.trim();
				if (!u) { setStatus('Буфер обмена пуст!', 'warning'); return; }
				if (!u.includes('qtyfiles.com')) { setStatus('Ссылка должна быть с сервиса qtyfiles.com!', 'warning'); return; }
				processEditorContent(function($dom) {
					const l = $dom.find('div#dl a');
					if (l.length === 0) { setStatus('Блок <div id="dl"> со ссылкой не найден!', 'error'); return false; }
					l.attr('href', u);
					setStatus('Ссылка на скачивание успешно заменена!', 'success');
				});
			}).catch(() => setStatus('Ошибка чтения буфера обмена!', 'error'));
			return;
		}

		// 5. Вставить текст из буфера (Inject text)
		if (e.target.closest('#pqem-inject-text-btn')) {
			e.preventDefault();
			if (!navigator.clipboard || !navigator.clipboard.readText) { setStatus('API буфера обмена не поддерживается.', 'error'); return; }
			navigator.clipboard.readText().then(t => {
				const textToInsert = t;
				if (!textToInsert.trim()) { setStatus('Буфер обмена пуст!', 'warning'); return; }
				const el = document.getElementById('pqem-content');
				let content = el.value;
				const mainRegex = /(\.jpg">\s*<\/div>)([\s\S]*?)(\[mycred_sell_this\]\s*\[member\]|\[member\])/;
				const updatedContent = content.replace(mainRegex, function(fullMatch, startBlock, middleContent, endBlock) {
					const linkRegex = /(https:\/\/3d-stuff\.community\/[^\s<]+)/g;
					const foundLinks = middleContent.match(linkRegex);
					let linksToKeep = foundLinks ? foundLinks.join('\n') : "";
					let newMiddleContent;
					const hrPosition = textToInsert.indexOf('<hr>');
					if (hrPosition !== -1) {
						newMiddleContent = textToInsert.substring(0, hrPosition);
						if (linksToKeep) newMiddleContent += '\n' + linksToKeep;
						newMiddleContent += '\n' + textToInsert.substring(hrPosition);
					} else {
						newMiddleContent = textToInsert;
						if (linksToKeep) newMiddleContent += '\n' + linksToKeep;
					}
					return startBlock + '\n' + newMiddleContent + '\n' + endBlock;
				});
				if (content === updatedContent) {
					setStatus('Структура для вставки текста не найдена!', 'error');
				} else {
					el.value = updatedContent;
					setStatus('Текст из буфера успешно вставлен!', 'success');
				}
			}).catch(() => setStatus('Ошибка чтения буфера обмена!', 'error'));
			return;
		}

		// 6. Очистить алмазы
		if (e.target.closest('#pqem-clear-btn')) {
			e.preventDefault();
			const el = document.getElementById('pqem-content');
			let c = el.value;
			const r = /\[mycred_sell_this\]([\s\S]*?)\[\/mycred_sell_this\]/g;
			if (!r.test(c)) { setStatus('Блоки [mycred_sell_this] не найдены.', 'warning'); return; }
			const u = c.replace(r, (m, i) => {
				const n = i.match(/(\[member\][\s\S]*?\[\/member\])/);
				return n ? n[0] : '';
			});
			el.value = u;
			setStatus('Блоки [mycred_sell_this] удалены!', 'success');
			return;
		}

	});

	// Закрыть по Escape
	document.addEventListener('keydown', function(e) {
		const overlay = document.getElementById('pqem-overlay');
		if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) {
			closeModal();
		}
	});

})();
</script>
<?php endif; ?>

<?php echo do_shortcode('[voting_buttons]'); ?>	

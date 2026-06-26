<?php
/**
 * Template Name: Christmas Greeting Page (test)
 */

get_header(); 
$current_page_id = get_the_ID();
$greetings = get_post_meta($current_page_id, '_holiday_greetings', true);
if (!is_array($greetings)) $greetings = [];
$is_admin = current_user_can('manage_options');
$user_logged_in = is_user_logged_in();
?>

<link href="https://fonts.googleapis.com/css2?family=Mountains+of+Christmas:wght@700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<div id="magic-v10-wrapper">

    <!-- 1. VIDEO HERO -->
    <section class="v10-hero">
        <div id="sound-toggle" class="v10-sound-btn">
            <div class="v10-sound-icon">
                <i class="fa fa-volume-off"></i>
            </div>
            <span class="v10-sound-text">Unmute</span>
        </div>
        <div class="v10-scroll-hint" id="scroll-hint">
            <div class="v10-mouse"><div class="v10-wheel"></div></div>
        </div>
        <video id="bg-video" playsinline loop autoplay muted>
            <source src="http://3d-stuff.community/img/XmasDance25.mp4" type="video/mp4">
        </video>
    </section>

    <!-- 2. MAIN CONTENT AREA -->
    <div class="v10-main-content">

   <div class="v10-xmas-header">
    
    <button id="v10-video-prev" class="v10-nav-arrow v10-nav-left" title="Previous Video">
        <i class="fa fa-chevron-left"></i>
    </button>

    <div class="v10-xmas-flex-row">
        <div class="v10-xmas-column v10-align-right">
            <span class="v10-xmas-item v10-c-red">Chr!Stm!s!</span>
            <span class="v10-xmas-separator">|</span>
            <span class="v10-xmas-item v10-c-green">Jingle Bells</span>
        </div>

        <div class="v10-xmas-spacer"></div>

        <div class="v10-xmas-column v10-align-left">
            <span class="v10-xmas-item v10-c-orange">Winter Magic</span>
            <span class="v10-xmas-separator">|</span>
            <span class="v10-xmas-item v10-c-blue">Happy New Year</span>
        </div>
    </div>

    <button id="v10-video-next" class="v10-nav-arrow v10-nav-right" title="Next Video">
        <i class="fa fa-chevron-right"></i>
    </button>

</div>
        
        <div class="container">
            
            <!-- КНОПКА CTA -->
            <div class="v10-trigger-wrap">
                <?php if ($user_logged_in) : ?>
                    <button id="form-trigger" class="v10-cta">
                        <i class="fa fa-gift"></i>
                        <span>Share Your Holiday Wishes</span>
                        <i class="fa fa-star star-spin"></i>
                    </button>
                <?php else : ?>
                    <a href="<?php echo wp_login_url(get_permalink()); ?>" class="v10-cta guest-mode">
                        <i class="fa fa-lock"></i> <span>Login to join the magic</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- ФОРМА -->
            <?php if ($user_logged_in) : ?>
            <div id="expandable-form" class="v10-form-hidden">
                <div class="v10-glass-form">
                    <div class="v10-form-head">
                        <i class="fa fa-envelope-o"></i>
                        <h3>Light up our Wall</h3>
                    </div>
                    <form id="greeting-form">
                        <div class="v10-input-group">
                            <label>From</label>
                            <input type="text" name="sender_name" value="<?php echo bp_get_user_firstname(); ?>" readonly>
                        </div>
                        <div class="v10-input-group">
                            <label>Your Message</label>
                            <textarea name="sender_message" rows="4" placeholder="Be sincere and don’t copy phrases from the video above." required></textarea>
                        </div>
                        <input type="hidden" name="action" value="submit_holiday_greeting">
                        <input type="hidden" name="page_id" value="<?php echo $current_page_id; ?>">
                        <input type="hidden" name="security" value="<?php echo wp_create_nonce('festive_nonce'); ?>">
                        <button type="submit" class="v10-send-btn">Post My Wish <i class="fa fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- ЗАГОЛОВОК С ПЕРЕЛИВОМ -->
            <div class="v10-header-box">
                <div class="v10-deco-top">
                    <i class="fa fa-snowflake-o"></i> <i class="fa fa-star"></i> <i class="fa fa-snowflake-o"></i>
                </div>
                <h2 class="v10-title-rainbow">Holiday Wishes Wall</h2>
                <div class="v10-line"></div>
                <p class="v10-subtitle">Spreading joy and warmth from our community</p>
            </div>

            <!-- СТЕНА (MASONRY) -->
            <div id="greetings-list" class="v10-grid">
                <?php if(!empty($greetings)): ?>
                    <?php foreach($greetings as $index => $item): 
                        $rotate = (rand(0, 4) - 2);
                        $greet_id = isset($item['id']) ? $item['id'] : 'v10-'.$index;
                        $styles = [
                            ['c' => '#e74c3c', 'b' => '#fff5f5'],
                            ['c' => '#27ae60', 'b' => '#f5fff7'],
                            ['c' => '#2980b9', 'b' => '#f5faff'],
                            ['c' => '#f1c40f', 'b' => '#fffdf5'],
                        ];
                        $st = $styles[$index % 4];
                    ?>
                        <div class="v10-card" id="greet-<?php echo $greet_id; ?>" 
                             style="transform: rotate(<?php echo $rotate; ?>deg); background: <?php echo $st['b']; ?>; border-top: 5px solid <?php echo $st['c']; ?>;">
                            
                            <?php if ($is_admin) : ?>
                                <button class="v10-del" onclick="v10Delete('<?php echo $greet_id; ?>')"><i class="fa fa-times"></i></button>
                            <?php endif; ?>

                            <div class="v10-user-row">
                                <div class="v10-bauble" style="--ball-c: <?php echo $st['c']; ?>;">
                                    <div class="v10-thread"></div>
                                    <div class="v10-cap"></div>
                                    <img src="<?php echo esc_url($item['avatar']); ?>" class="v10-avatar">
                                </div>
                                <div class="v10-meta">
                                    <strong><?php echo esc_html($item['name']); ?></strong>
                                    <span><?php echo date('M d, Y', strtotime($item['date'])); ?></span>
                                </div>
                            </div>
                            <div class="v10-msg">
                                <p><?php echo nl2br(esc_html($item['message'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>



<?php
    $has_posted = false;
    if (is_user_logged_in()) {
        $current_uid = get_current_user_id();
        foreach ($greetings as $item) {
            if (isset($item['user_id']) && $item['user_id'] == $current_uid) {
                $has_posted = true;
                break;
            }
        }
    }
    $coupon_code = "XMAS-MAGIC-2025";
    ?>

    <div class="v10-xmas-divider">
        <div class="v10-line"></div>
        <div class="v10-diamond-icon">
            <i class="fa fa-diamond"></i>
            <div class="v10-glow"></div>
        </div>
        <div class="v10-line"></div>
    </div>

    <section class="v10-reward-section">
        <div class="v10-reward-card">
            <div class="v10-reward-info">
                <h2 class="v10-reward-title">Magic Diamond Gift</h2>
                <p class="v10-reward-subtitle">Activate your coupon and get <b>2025 Diamonds</b> credited to your balance!</p>
            </div>

            <div class="v10-coupon-container">
                <form class="v10-real-coupon-form" id="v10-coupon-form">
                    <div class="v10-input-group">
                        <input type="text" value="<?php echo $coupon_code; ?>" readonly class="v10-field-coupon">
                        <span class="v10-badge">COUPON</span>
                    </div>
<div class="v10-status-area">
<?php
    // Базовые флаги
    $already_claimed = false;

    // 🔒 ЖЁСТКАЯ ПРОВЕРКА — ТОЛЬКО user_meta
    if ( is_user_logged_in() ) {
        $already_claimed = (bool) get_user_meta(
            get_current_user_id(),
            'xmas_coupon_2025_claimed',
            true
        );

        // DEBUG ТОЛЬКО ДЛЯ АДМИНА
        if ( current_user_can('manage_options') ) {
            echo "<script>console.log('XMAS DEBUG: user_id=" 
                . get_current_user_id() 
                . " claimed=" 
                . ($already_claimed ? 'YES' : 'NO') 
                . "');</script>";
        }
    }
?>

<?php if (!$user_logged_in) : ?>

    <div class="v10-msg warning">
        <i class="fa fa-lock"></i> Sign in to unlock your holiday gift
    </div>
    <button type="button" disabled class="v10-btn-activate">
        Activate
    </button>

<?php elseif (!$has_posted) : ?>

    <div class="v10-msg info">
        <i class="fa fa-heart"></i> Share your holiday wish to unlock this reward
    </div>
    <button type="button" disabled class="v10-btn-activate">
        Activate
    </button>

<?php elseif ($already_claimed) : ?>

    <div class="v10-msg success">
        <i class="fa fa-check-circle"></i> You have already received your gift!
    </div>
    <button type="button"
            disabled
            class="v10-btn-activate"
            style="background:#ccc !important; color:#666 !important; cursor:default !important; transform:none !important;">
        Gift Received
    </button>

<?php else : ?>

    <div class="v10-msg success">
        <i class="fa fa-magic"></i> Thank you for spreading the joy! Your gift is ready.
    </div>
    <button type="submit" class="v10-btn-activate active">
        Claim My Reward
    </button>

<?php endif; ?>
</div>


                </form>
            </div>
            <p class="v10-reward-footer">* This festive reward is available throughout the holiday season!</p>
        </div>
    </section>

    <style>

/* Убеждаемся, что родительский блок позволяет позиционирование */
.v10-xmas-header {
    position: relative !important; /* Обязательно для работы absolute внутри */
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 1200px; /* Ограничим ширину, чтобы кнопки не улетали в самые углы монитора */
    margin: 0 auto 30px auto;
    padding: 0 60px; /* Отступы по бокам, чтобы текст не наезжал на кнопки на мобильных */
    box-sizing: border-box;
}

/* Стили самих кнопок */
.v10-nav-arrow {
    position: absolute !important;
    top: 50%;
    transform: translateY(-50%); /* Центровка по вертикали */
    background: rgba(0, 0, 0, 0.4); /* Темная подложка */
    border: 2px solid rgba(255, 215, 0, 0.5); /* Золотая рамка */
    color: #ffd700;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    outline: none;
}

/* Эффект при наведении */
.v10-nav-arrow:hover {
    background: rgba(255, 215, 0, 0.8);
    color: #000;
    box-shadow: 0 0 15px #ffd700;
    border-color: #ffd700;
}

/* Позиционирование слева и справа */
.v10-nav-left {
    left: 10px;
}

.v10-nav-right {
    right: 10px;
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .v10-xmas-header {
        padding: 0 40px; /* Уменьшаем отступы контейнера */
    }
    .v10-nav-arrow {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
    .v10-nav-left { left: 0; }
    .v10-nav-right { right: 0; }
}














    .v10-xmas-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 80px 0 40px;
        gap: 20px;
        clear: both;
    }
    .v10-line { flex: 1; height: 1px; background: linear-gradient(90deg, transparent, rgba(130, 180, 63, 0.4), transparent); }
    
    /* Иконка алмаза теперь вашего цвета #82b43f */
    .v10-diamond-icon { position: relative; font-size: 32px; color: #82b43f; animation: v10-float 3s infinite ease-in-out; }
    .v10-glow { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 45px; height: 45px; background: rgba(130, 180, 63, 0.2); filter: blur(15px); border-radius: 50%; }

    .v10-reward-section { padding: 20px 20px 120px; display: flex; justify-content: center; width: 100%; }
    .v10-reward-card { 
        background: rgba(255, 255, 255, 0.95); 
        backdrop-filter: blur(10px); 
        border-radius: 40px; 
        padding: 50px; 
        width: 100%; 
        max-width: 650px; 
        text-align: center; 
        box-shadow: 0 30px 70px rgba(0,0,0,0.1);
        border: 2px solid #fff;
    }
    .v10-reward-title { 
        font-family: 'Mountains of Christmas', cursive !important; 
        font-size: 54px !important; 
        color: #d42424; 
        margin: 0 0 15px 0 !important; /* Увеличен отступ снизу */
        line-height: 1 !important;
    }
    .v10-reward-subtitle {
        color: #555; 
        font-size: 19px; 
        margin-bottom: 25px !important;
        line-height: 1.4;
    }
    
    .v10-input-group { position: relative; margin: 20px 0; }
    .v10-field-coupon { 
        width: 100% !important; 
        padding: 22px !important; 
        border-radius: 20px !important; 
        border: 2px dashed #82b43f !important; /* Пунктир в цвет бренда */
        background: #f9fbf2 !important; 
        font-size: 28px !important; 
        font-weight: 800 !important; 
        text-align: center !important; 
        color: #2c3e50 !important; 
        letter-spacing: 2px;
    }
    .v10-badge { position: absolute; top: -12px; left: 30px; background: #82b43f; color: #fff; font-size: 11px; font-weight: 900; padding: 3px 12px; border-radius: 6px; }
    
    /* Кнопка активации вашего цвета #82b43f */
    .v10-btn-activate { 
        width: 100%; padding: 20px !important; border-radius: 20px !important; border: none !important; 
        background: #e0e0e0 !important; color: #999 !important; font-weight: 800 !important; font-size: 20px !important; 
        cursor: not-allowed; transition: 0.4s all; text-transform: uppercase;
    }
    .v10-btn-activate.active { 
        background: #82b43f !important; 
        color: #fff !important; cursor: pointer; 
        box-shadow: 0 10px 25px rgba(130, 180, 63, 0.4); 
    }
    .v10-btn-activate.active:hover { background: #6f9a35 !important; transform: translateY(-4px); box-shadow: 0 15px 35px rgba(130, 180, 63, 0.5); }
    
    .v10-msg { font-size: 14px; margin-bottom: 15px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .v10-msg.warning { color: #d42424; }
    .v10-msg.info { color: #2980b9; }
    .v10-msg.success { color: #82b43f; }
    
    .v10-reward-footer { margin-top: 25px; font-size: 13px; color: #888; font-style: italic; }

    @keyframes v10-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
    </style>

        </div>
    </div>
</div>

<style>
/* --- ПРИНУДИТЕЛЬНЫЙ СБРОС --- */
#left-content, #content-container, #content, .woffice-section { 
    background: transparent !important; border: none !important; padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; 
}

/* --- ФОН --- */
#magic-v10-wrapper {
    background-color: #f0f4f8 !important;
    background: radial-gradient(circle at 50% 50%, #ffffff 0%, #d6e4f0 100%) !important;
    min-height: 100vh; position: relative; font-family: 'Montserrat', sans-serif;
}

/* --- HERO & VIDEO --- */
.v10-hero { position: sticky; top: 0; width: 100%; height: 96vh; z-index: 1; background: #000; overflow: hidden; }
#bg-video {
    position: absolute;
    top: 42%; left: 50%;
    min-width: 100%; min-height: 100%;
    transform: translate(-50%, -50%);
    object-fit: cover;
    object-position: center bottom !important;
}

/* --- УЛУЧШЕННАЯ КНОПКА MUTE --- */
.v10-sound-btn { 
    position: absolute; top: 100px; right: 25px; z-index: 10; 
    background: linear-gradient(135deg, rgba(212,36,36,0.9), rgba(160,26,26,0.9));
    color: #fff; padding: 12px 24px; border-radius: 50px; cursor: pointer; 
    border: 2px solid rgba(255,215,0,0.6);
    backdrop-filter: blur(10px); 
    font-size: 14px; font-weight: 600; 
    display: flex; align-items: center; gap: 10px;
    box-shadow: 0 8px 25px rgba(212,36,36,0.4), 0 0 40px rgba(255,215,0,0.3);
    transition: all 0.3s ease;
}
.v10-sound-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(212,36,36,0.5), 0 0 50px rgba(255,215,0,0.4);
    border-color: rgba(255,215,0,0.9);
}
.v10-sound-icon {
    width: 24px; height: 24px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
}
.v10-sound-text {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* --- КОНТРАСТНЫЙ СКРОЛЛ ИНДИКАТОР --- */
.v10-scroll-hint { 
    position: absolute; bottom: 47px; left: 50%; transform: translateX(-50%); 
    z-index: 10; display: flex; flex-direction: column; align-items: center; gap: 8px;
    transition: opacity 0.5s ease;
}
.v10-scroll-hint.hidden {
    opacity: 0;
    pointer-events: none;
}
.v10-mouse { 
    width: 28px; height: 44px; 
    border: 3px solid #ffd700; 
    border-radius: 14px; 
    position: relative;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(10px);
    box-shadow: 
        0 0 30px rgba(255,215,0,0.6),
        0 0 50px rgba(255,215,0,0.4),
        inset 0 0 20px rgba(255,215,0,0.1);
}
.v10-wheel { 
    width: 5px; height: 10px; 
    background: #ffd700; 
    position: absolute; 
    top: 8px; left: 50%; 
    margin-left: -2.5px; 
    border-radius: 3px; 
    animation: move 2s infinite;
    box-shadow: 
        0 0 10px #ffd700,
        0 0 20px #ffd700;
}

/* --- ОСНОВНОЙ КОНТЕНТ --- */
.v10-main-content { 
    position: relative; z-index: 10; padding: 100px 0; border-radius: 40px 40px 0 0; margin-top: -100px;
    background: linear-gradient(180deg, rgba(255,255,255,0.9) 0%, rgba(224,239,255,0.9) 100%) !important;
    box-shadow: 0 -30px 100px rgba(0,0,0,0.1); overflow: hidden;
}



/* --- ЗАГОЛОВОК --- */
.v10-header-box { text-align: center; margin-bottom: 60px; position: relative; }
.v10-title-rainbow {
    font-family: 'Mountains of Christmas', cursive !important;
    font-size: 76px !important; font-weight: 700 !important; margin: 0 !important; line-height: 1.1 !important;
    background: linear-gradient(90deg, #d42424, #ffd700, #27ae60, #2980b9, #d42424) !important;
    background-size: 200% auto !important;
    -webkit-background-clip: text !important;
    background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    animation: v10-shine 4s linear infinite !important;
    display: inline-block !important;
}
@keyframes v10-shine { to { background-position: 200% center; } }
.v10-line { width: 100px; height: 4px; background: #ffd700; margin: 15px auto; border-radius: 10px; }
.v10-subtitle { color: #7f8c8d; font-size: 18px; font-weight: 300; }
.v10-deco-top { font-size: 24px; color: #ffd700; margin-bottom: 15px; }





/* --- CTA КНОПКА --- */
.v10-trigger-wrap { margin-top: 0px; margin-bottom: 60px; display: flex; justify-content: center; position: relative; z-index: 100; }
.v10-cta {
    background: linear-gradient(135deg, #d42424, #a01a1a) !important;
    color: #fff !important; padding: 18px 45px !important; border-radius: 50px !important; border: 3px solid #ffd700 !important;
    font-size: 18px !important; font-weight: 700 !important; text-transform: uppercase !important; cursor: pointer !important;
    box-shadow: 0 10px 30px rgba(212,36,36,0.4) !important; display: flex; align-items: center; gap: 15px; transition: 0.3s !important;
}

/* --- ФОРМА --- */
.v10-form-hidden { max-height: 0; overflow: hidden; transition: 0.8s ease; opacity: 0; }
.v10-form-visible { max-height: 1000px; opacity: 1; margin-bottom: 80px; }
.v10-glass-form { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 25px; padding: 40px; box-shadow: 0 40px 100px rgba(0,0,0,0.1); border: 1px solid #eee; position: relative; z-index: 110; }
.v10-form-head { text-align: center; margin-bottom: 30px; }
.v10-form-head i { font-size: 48px; color: #d42424; margin-bottom: 10px; }
.v10-form-head h3 { font-size: 28px; color: #333; margin: 0; }
.v10-input-group { margin-bottom: 20px; }
.v10-input-group label { display: block; font-weight: 600; color: #555; margin-bottom: 8px; }
.v10-input-group input, .v10-input-group textarea { 
    width: 100% !important; padding: 15px !important; border-radius: 12px !important; 
    border: 1px solid #eee !important; background: #fdfdfd !important; 
    font-size: 16px !important; margin: 0 !important; color: #8d8bff !important;
    font-family: 'Montserrat', sans-serif !important;
}
.v10-send-btn { 
    width: 100% !important; 
    padding: 20px !important; /* Увеличил высоту для соответствия кнопке купона */
    background: #82b43f !important; 
    color: #fff !important; 
    border: none !important; 
    border-radius: 20px !important; /* Более скругленные углы, как у купона */
    font-weight: 800 !important; /* Сделал текст жирнее */
    cursor: pointer !important; 
    font-size: 18px !important;
    text-transform: uppercase !important; /* Капсом, как у купона */
    letter-spacing: 1px !important;
    transition: all 0.4s ease !important;
    box-shadow: 0 10px 25px rgba(130, 180, 63, 0.3) !important;
    display: block !important;
    margin-top: 10px !important;
}

.v10-send-btn:hover { 
    background: #6f9a35 !important; /* Тот же оттенок при наведении, что и у купона */
    transform: translateY(-4px) !important; /* Эффект приподнимания */
    box-shadow: 0 15px 35px rgba(130, 180, 63, 0.5) !important;
}

.v10-send-btn:active {
    transform: translateY(-1px) !important;
}

/* Если кнопка нажата и идет отправка */
.v10-send-btn:disabled {
    background: #e0e0e0 !important;
    color: #999 !important;
    cursor: not-allowed !important;
    transform: none !important;
    box-shadow: none !important;
}

/* --- КАРТОЧКИ --- */
.v10-grid { column-count: 3; column-gap: 30px; padding: 0 40px; }
.v10-card { 
    break-inside: avoid; display: inline-block; width: 100%; 
    margin-bottom: 30px; padding: 25px; border-radius: 20px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
    transition: 0.3s; position: relative; 
}
.v10-card:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.08); }

.v10-thread { position: absolute; top: -35px; left: 50%; width: 1px; height: 35px; background: #ccc; }
.v10-bauble { position: relative; width: 55px; height: 55px; }
.v10-avatar { 
    width: 100%; height: 100%; border-radius: 50%; object-fit: cover; 
    border: 3px solid var(--ball-c); position: relative; z-index: 2; background: #fff; 
}
.v10-cap { 
    position: absolute; top: -6px; left: 50%; width: 12px; height: 8px; 
    background: #aaa; border-radius: 2px; z-index: 3; transform: translateX(-50%); 
}

.v10-user-row { 
    display: flex; align-items: center; gap: 15px; margin-bottom: 15px; 
    border-bottom: 1px dashed rgba(0,0,0,0.05); padding-bottom: 15px; 
}
.v10-meta strong { display: block; font-size: 15px; color: #333; }
.v10-meta span { font-size: 11px; color: #bbb; }
.v10-msg p { font-size: 14px; line-height: 1.6; color: #555; margin: 0; }

/* УДАЛЕНИЕ */
.v10-del { 
    position: absolute; top: 10px; right: 10px; width: 26px; height: 26px; 
    background: #fff !important; color: #d42424 !important; 
    border: 1px solid #eee !important; border-radius: 50%; cursor: pointer; 
    display: flex; align-items: center; justify-content: center; font-size: 12px;
    transition: 0.3s;
}
.v10-del:hover { background: #d42424 !important; color: #fff !important; }

@keyframes move { 0% { opacity: 1; top: 8px; } 100% { opacity: 0; top: 28px; } }

@media (max-width: 1024px) { .v10-grid { column-count: 2; } }
@media (max-width: 768px) { 
    .v10-grid { column-count: 1; padding: 20px; } 
    .v10-title-rainbow { font-size: 44px !important; }
    .v10-cta { padding: 14px 30px !important; font-size: 16px !important; }
}
.woffice-footer {display: none;}
#copyright2 {display: none;}

/* --- ГАРАНТИРОВАННОЕ ЦЕНТРИРОВАНИЕ --- */
/* --- РОЖДЕСТВЕНСКИЙ ЗАГОЛОВОК (ИСПРАВЛЕННЫЙ) --- */
/* --- ПРАЗДНИЧНЫЙ ЗАГОЛОВОК С СИММЕТРИЕЙ --- */
.v10-xmas-header {
    width: 100%;
    position: relative;
    z-index: 100;
    /* Поднимаем к скруглениям, учитывая ваш новый margin у кнопки */
    margin-top: -93px; 
    margin-bottom: 30px;
    padding: 0 30px;
}

.v10-xmas-flex-row {
    display: flex !important;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.v10-xmas-column {
    flex: 1; /* Гарантирует равную ширину левой и правой сторон */
    display: flex;
    align-items: center;
    /* Принудительное восстановление шрифта */
    font-family: 'Mountains of Christmas', cursive !important; 
    font-size: 32px !important;
    font-weight: 700 !important;
    white-space: nowrap;
}

/* Выравнивание текста от центра */
.v10-align-right { justify-content: flex-end; gap: 20px; }
.v10-align-left { justify-content: flex-start; gap: 20px; }

/* Увеличенный зазор для иконки скроллинга */
.v10-xmas-spacer {
    width: 140px; 
    flex-shrink: 0;
}

/* --- ОБЩАЯ АНИМАЦИЯ ПОКАЧИВАНИЯ --- */
.v10-xmas-item {
    display: inline-block;
    animation: v10-global-swing 3s infinite ease-in-out;
    transform-origin: center center;
}

/* Разные фазы анимации для живости */
.v10-xmas-column.v10-align-left .v10-xmas-item {
    animation-delay: -0.5s; /* Правая сторона качается с небольшим опозданием */
}

/* Цвета элементов */
.v10-c-red { color: #d42424 !important; }
.v10-c-green { color: #27ae60 !important; }
.v10-c-orange { color: #f39c12 !important; }
.v10-c-blue { color: #2980b9 !important; }

.v10-xmas-separator { 
    color: #ffd700 !important; 
    opacity: 0.4; 
    font-family: Arial, sans-serif !important; /* Разделитель обычным шрифтом */
}

/* KEYFRAMES */
@keyframes v10-global-swing {
    0%, 100% { transform: rotate(-3deg) translateY(0); }
    50% { transform: rotate(3deg) translateY(-2px); }
}

/* Адаптивность */
@media (max-width: 1024px) {
    .v10-xmas-column { font-size: 26px !important; }
    .v10-xmas-spacer { width: 140px; }
}

@media (max-width: 768px) {
    .v10-xmas-header { margin-top: -20px; }
    .v10-xmas-flex-row { flex-direction: column; gap: 10px; }
    .v10-xmas-spacer { display: none; }
    .v10-xmas-column { justify-content: center; flex: none; }
    .v10-xmas-separator { display: none; }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Настройка купона/алмазов
    const cForm = document.getElementById('v10-coupon-form');
    if (cForm) {
        cForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('.v10-btn-activate');
            btn.disabled = true;
            btn.innerText = 'Processing...';

            const formData = new FormData();
            formData.append('action', 'v10_activate_xmas_coupon');
            formData.append('security', '<?php echo wp_create_nonce('festive_nonce'); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    confetti({ particleCount: 150, spread: 70, origin: { y: 0.7 } });
                    btn.innerHTML = '<i class="fa fa-check"></i> Gift Received';
                    btn.style.background = '#ccc';
                    btn.classList.remove('active');
                } else {
                    alert(data.data);
                    btn.disabled = false;
                    btn.innerText = 'Claim My Reward';
                }
            });
        });
    }

    // 2. Видео и звук (Исправляем логику звука, чтобы она работала с переменной ниже)
    // Оставляем как есть, но ID видео должен совпадать везде
    const soundBtn = document.getElementById('sound-toggle');
    const mainVideo = document.getElementById('bg-video'); // Используем правильный ID
    
    soundBtn?.addEventListener('click', function() {
        if(mainVideo) {
            mainVideo.muted = !mainVideo.muted;
            this.querySelector('.v10-sound-text').innerText = mainVideo.muted ? 'Unmute' : 'Mute';
            this.querySelector('i').className = mainVideo.muted ? 'fa fa-volume-off' : 'fa fa-volume-up';
        }
    });

    // 3. Форма поздравления
    const trigger = document.getElementById('form-trigger');
    const fWrapper = document.getElementById('expandable-form');
    trigger?.addEventListener('click', () => {
        fWrapper.classList.toggle('v10-form-visible');
        if(fWrapper.classList.contains('v10-form-visible')) {
            setTimeout(() => { fWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 300);
        }
    });

    const gForm = document.getElementById('greeting-form');
    if (gForm) {
        gForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = gForm.querySelector('.v10-send-btn');
            btn.disabled = true;
            btn.innerText = 'Sending...';
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: new FormData(gForm) })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    confetti({ particleCount: 200, spread: 80, origin: { y: 0.6 } });
                    setTimeout(() => { window.location.href = window.location.pathname + '?status=success'; }, 1500);
                }
            });
        });
    }

    // 4. Скролл после перезагрузки
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        setTimeout(() => {
            const reward = document.querySelector('.v10-reward-section');
            if (reward) reward.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 1000);
    }

    // --- ИСПРАВЛЕННАЯ ЛОГИКА ПЕРЕКЛЮЧЕНИЯ ВИДЕО ---
    // Теперь код внутри DOMContentLoaded и увидит кнопки
    const btnPrev = document.getElementById('v10-video-prev');
    const btnNext = document.getElementById('v10-video-next');
    // Используем mainVideo, который мы нашли выше по ID 'bg-video'
    
    // Список видео
    const playlist = [
        "http://3d-stuff.community/img/XmasDance25.mp4", 
        "http://3d-stuff.community/img/NY2026.mp4" 
    ];

    let currentVideoIndex = 0;

    function changeVideo(direction) {
        if (!mainVideo) return; // Защита если видео не найдено

        // Вычисляем новый индекс
        if (direction === 'next') {
            currentVideoIndex++;
            if (currentVideoIndex >= playlist.length) currentVideoIndex = 0;
        } else {
            currentVideoIndex--;
            if (currentVideoIndex < 0) currentVideoIndex = playlist.length - 1;
        }

        // Плавная смена
        mainVideo.style.opacity = 0;
        
        setTimeout(() => {
            mainVideo.src = playlist[currentVideoIndex];
            
            let playPromise = mainVideo.play();
            if (playPromise !== undefined) {
                playPromise.then(_ => {
                    // Autoplay started
                }).catch(error => {
                    console.log("Autoplay prevented");
                });
            }
            
            mainVideo.style.opacity = 1;
        }, 300);
    }

    if(btnPrev && btnNext && mainVideo) {
        btnNext.addEventListener('click', () => changeVideo('next'));
        btnPrev.addEventListener('click', () => changeVideo('prev'));
        console.log('Video navigation initialized'); // Для проверки в консоли
    } else {
        console.log('Video nav elements missing:', {btnPrev, btnNext, mainVideo});
    }
    // --- КОНЕЦ ВИДЕО ЛОГИКИ ---

}); // <-- Закрывающая скобка DOMContentLoaded теперь ЗДЕСЬ

// 5. Функция удаления (вне очереди, так как вызывается из onclick в HTML)
function v10Delete(greetId) {
    if (!confirm('Delete?')) return;
    const fd = new FormData();
    fd.append('action', 'delete_holiday_greeting');
    fd.append('greet_id', greetId);
    fd.append('page_id', '<?php echo get_the_ID(); ?>');
    fd.append('security', '<?php echo wp_create_nonce('festive_nonce'); ?>');
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: fd })
    .then(() => location.reload());
}

// 6. Скрытие scroll-индикатора
const scrollHint = document.getElementById('scroll-hint');
if (scrollHint) {
    const hideAt = 150;
    window.addEventListener('scroll', () => {
        if (window.scrollY > hideAt) {
            scrollHint.classList.add('hidden');
        } else {
            scrollHint.classList.remove('hidden');
        }
    }, { passive: true });
}
</script>

<?php get_footer(); ?>
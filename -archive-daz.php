<?php
get_header();

// Только для администратора
if (!current_user_can('administrator')) {
    wp_redirect(home_url());
    exit;
}
?>

<main id="main" class="site-main" role="main">
    <div class="container full-width">

        <header class="page-header">
            <h1 class="page-title">DAZ Модели</h1>
        </header>

        <?php if (have_posts()) : ?>
            <div class="posts masonry-layout">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('box'); ?>>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="content">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>

                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php the_posts_pagination([
                    'prev_text' => __('« Назад'),
                    'next_text' => __('Вперёд »'),
                ]); ?>
            </div>

        <?php else : ?>
            <div class="box">
                <p>Пока нет моделей.</p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>

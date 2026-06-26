<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php if ( have_comments() ) : ?>

	<div id="comments-container" class="box">
		<div class="intern-padding">
		
			<!-- THE TITLE -->
			<div class="heading">
				<h2 class="p-0"><?php printf( _n( '1 comment', '%1$s comments', get_comments_number(), 'woffice' ),
					number_format_i18n( get_comments_number() ), get_the_title() ); ?>
				</h2>
			</div>
			
			<!-- THE COMMENTS LIST -->
			<ol class="comment-list">
				<?php
					wp_list_comments( array(
						'style'      => 'ol',
						'reply_text'  => '<i class="fa fa-reply"></i> '. __('Reply','woffice'),
						'short_ping' => true,
						'avatar_size'=> 75,
						'type'       => 'comment', // Показывать только обычные комментарии
					) );
				?>
			</ol><!-- .comment-list -->
			
			<!-- THE COMMENTS NAVIGATION -->
			<!-- NEED CHANGES -->
			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
					<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'woffice' ); ?></h1>
					<div class="nav-previous"><?php previous_comments_link( __( '<i class="fa fa-chevron-left"></i> Older Comments', 'woffice' ) ); ?></div>
					<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <i class="fa fa-chevron-right"></i>', 'woffice' ) ); ?></div>
				</nav><!-- #comment-nav-below -->
			<?php endif; // Check for comment navigation. ?>
	
			<?php if ( ! comments_open() ) : ?>
				<p class="no-comments"><?php _e( 'Comments are closed.', 'woffice' ); ?></p>
			<?php endif; ?>
			
		</div>
	</div>
	
<?php endif; // have_comments() ?>

<!-- THE COMMENT FORM --> 
<?php if ( current_user_can('administrator') ) : ?>
    <div style="display: flex; flex-direction: row; gap: 6px; margin-bottom: 10px;">
        <button id="copy-id-button" style="flex: 2;">Copy ID</button>
        <button id="copy-sql-button" style="flex: 1;">Copy SQL</button>
    </div>
<?php endif; ?>
<?php if ( current_user_can('administrator') ) : ?>
<div id="ssh-cheatsheet-overlay"></div>
<div id="ssh-cheatsheet">
    <button id="ssh-cheatsheet-close">✕</button>
    <h4>🖥 Шпаргалка подключения</h4>
    <div class="cheat-row">
    <code>ssh root@<?php echo QTY_SSH_HOST; ?></code>
    <button class="cheat-copy" data-text="ssh root@<?php echo QTY_SSH_HOST; ?>">Copy</button>
</div>
<div class="cheat-row">
    <code>mysql -u <?php echo QTY_MYSQL_USER; ?> -p <?php echo QTY_MYSQL_DB; ?></code>
    <button class="cheat-copy" data-text="mysql -u <?php echo QTY_MYSQL_USER; ?> -p <?php echo QTY_MYSQL_DB; ?>">Copy</button>
</div>
<div class="cheat-row">
    <code>••••••••</code>
    <button class="cheat-copy" data-text="<?php echo QTY_MYSQL_PASS; ?>">Copy</button>
</div>
</div>
<?php endif; ?>

		<?php 
		$args = array(
		  'id_form'           => 'comment-form',
		  'id_submit'         => 'submit',
		  'title_reply'       => __( 'Request to restore file (File Not Found)', 'woffice' ),
		  'title_reply_to'    => __( 'Leave a Reply to %s', 'woffice' ),
		  'cancel_reply_link' => __( 'Cancel Reply', 'woffice' ),
		  'label_submit'      => __( 'Send', 'woffice'),
		  'comment_notes_before' => '',
		  'comment_notes_after' => '',
		); ?>
	
		<?php 
		ob_start();
		comment_form($args);
		echo str_replace('class="comment-form"','class="comment-form form-horizontal"',ob_get_clean());
		?>
	</div>
</div>
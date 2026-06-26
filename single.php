<?php
/**
 * The Template for displaying all single posts
 */

global $post;

$current_user_is_admin  = woffice_current_is_admin();
$edit_allowed           = (Woffice_Frontend::edit_allowed('post') == true) ? true : false;
$delete_allowed         = (Woffice_Frontend::edit_allowed('post', 'delete') == true) ? true : false;
if ($edit_allowed) {
	$process_result = Woffice_Frontend::frontend_process('post', $post->ID);
}
///
if(woffice_get_skin('celestial')){
	get_template_part('singles/content', 'celestial');
} else {
	get_template_part('singles/content', 'classic');
}
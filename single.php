<?php
/**
 * The Template for displaying all single posts
 */

global $post;

if(woffice_get_skin('celestial')){
	get_template_part('singles/content', 'celestial');
} else {
	get_template_part('singles/content', 'classic');
}
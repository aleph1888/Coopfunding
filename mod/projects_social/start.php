<?php
/**
 * Elgg projects social share plugin
 *
 * @package Coopfunding
 * @subpackage Projects.Social
 */

elgg_register_event_handler('init', 'system', 'projects_social_init');

/**
 * Initialize the projects plugin.
 */
function projects_social_init() {

	elgg_set_config('projects_social', array('facebook', 'twitter'));
	elgg_set_config('projects_social_lorea_seeds', array('n-1', 'anillosur'));
	elgg_extend_view('projects/sidebar/tagcloud', 'projects/sidebar/projects_social');

}



<?php
/**
 * Tag cloud widget
 */


elgg_push_context('tags');
$options = array(
	'type' => 'group',
	'subtype' => 'project',
	'tag_name' => 'interests',
	'threshold' => 1,
);
$tagcloud = elgg_view_tagcloud($options);
elgg_pop_context();
echo elgg_view_module('aside', elgg_echo('tags'), $tagcloud);

<?php
/**
 * Elgg custom index page
 * 
 */

elgg_push_context('front');

elgg_push_context('widgets');

$last_projects = elgg_list_entities(array(
	'type' => 'group',
	'subtype' => 'project',
	'limit' => 3,
	'full_view' => false,
	'pagination' => false,
	'list_type' => 'gallery',
));

$featured_projects = elgg_list_entities_from_metadata(array(
	'type' => 'group',
	'subtype' => 'project',
	'limit' => 3,
	'full_view' => false,
	'metadata_name' => 'featured_project',
	'metadata_value' => 'yes',
	'pagination' => false,
	'list_type' => 'gallery',
));


$categories = elgg_view('output/longtext', array(
	'value' => elgg_view('output/categories'),
	'class' => 'phl pbl',
));

$tagcloud = elgg_view('output/longtext', array(
	'value' => elgg_view_tagcloud(array(
		'type' => 'group',
		'subtype' => 'project',
	)),
	'class' => 'phl pbl',
));

elgg_pop_context();

// lay out the content
$params = array(
	'projects_categories' => $categories,
	'projects_latest' => $last_projects,
	'projects_featured' => $featured_projects,
	'projects_tagcloud' => $tagcloud,
);
$body = elgg_view_layout('custom_index', $params);

// no RSS feed with a "widget" front page
global $autofeed;
$autofeed = FALSE;

echo elgg_view_page('', $body);

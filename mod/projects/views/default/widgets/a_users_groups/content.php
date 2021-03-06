<?php
/**
 * Project widget view
 *
 * @package Coopfunding
 * @subpackage Projects
 */


$num = $vars['entity']->num_display;

$options = array(
	'type' => 'group',
	'subtype' => 'project',
	'relationship' => 'member',
	'relationship_guid' => $vars['entity']->owner_guid,
	'limit' => $num,
	'full_view' => FALSE,
	'pagination' => FALSE,
);
$content = elgg_list_entities_from_relationship($options);

echo $content;

if ($content) {
	$url = "projects/member/" . elgg_get_page_owner_entity()->username;
	$more_link = elgg_view('output/url', array(
		'href' => $url,
		'text' => elgg_echo('projects:more'),
		'is_trusted' => true,
	));
	echo "<span class=\"elgg-widget-more\">$more_link</span>";
} else {
	echo elgg_echo('projects:none');
}

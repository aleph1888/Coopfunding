<?php

$project = $vars['entity'];

if (!$project) {
	return true;
}

elgg_load_library('elgg:fundcampaigns');

$fundcampaign = fundcampaigns_get_active_campaign ($project->guid);

$all_link = elgg_view('output/url', array(
	'href' => "campaign_collective/owner/{$fundcampaign->guid}",
	'text' =>  elgg_echo('campaign_collective:view all'),
	'is_trusted' => true,
));

if (!$fundcampaign) {
	$content = '<p>' . elgg_echo('campaign_collective:none') . '</p>';
} else {

	$entities = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'campaign_collective',
		'container_guid' => $fundcampaign->guid,
		'limit'=> 50
	));

	$content .= "<ul>";
	if ($entities) {
		foreach ($entities as $entity){
			elgg_load_library("coopfunding:fundraising");
			$content.= "<li><div><b>" . $entity->title . "</b><br>" . $entity->description . "</div></li><br>";
		}
	}

	$content .= "</ul>";
}

echo elgg_view('projects/profile/module', array(
	'title' => elgg_echo('campaign_collective:items'),
	'content' => $content,
	'all_link' => $all_link,
));



<?php

$project = $vars['entity'];

if (!$project) {
	return true;
}
$fundcampaign = fundcampaigns_get_active_campaign ($project->guid);

$all_link = elgg_view('output/url', array(
	'href' => "campaign_reward/owner/{$fundcampaign->guid}",
	'text' =>  elgg_echo('campaign_reward:view all'),
	'is_trusted' => true,
));

elgg_load_library('elgg:fundcampaigns');

if (!$fundcampaign) {
	$content = '<p>' . elgg_echo('rewards:none') . '</p>';
} else {

	$entities = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'campaign_reward',
		'container_guid' => $fundcampaign->guid,
		'order_by_metadata' => array('name' => 'amount', 'direction' => 'ASC', 'as' => 'integer'),
		'limit'=> 5
	));

	if (elgg_is_admin_logged_in()){
		$url = elgg_get_site_url() . "campaign_reward/books/{$fundcampaign->guid}";
		$content .= "<a href=" .  $url . ">" . elgg_echo('campaign_reward:books:view all') . "</a>";
	}

	$content .= "<ul>";

	if ($entities) {
		foreach ($entities as $entity){
			elgg_load_library("coopfunding:fundraising");
			if ($fundcampaign->is_active) {
				$donatebuttons_link = fundraising_get_contribute_button ($fundcampaign->guid, $entity->amount, $entity->guid);
			}
		    $content.= "<li><div><b>" . $entity->title . "</b><br>" . $entity->description . $donatebuttons_link . "</div></li><br>";
		}
	}

	$content .= "</ul>";
}

echo elgg_view('projects/profile/module', array(
	'title' => elgg_echo('campaign_reward:items'),
	'content' => $content,
	'all_link' => $all_link,
));



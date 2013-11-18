<?php
/**
 * View for campaign_reward objects
 *
 * @package campaign_reward
 */
$donatebutton = elgg_extract('donatebutton', $vars, FALSE);
//$full = elgg_extract('full_view', $vars, FALSE);
$campaign_reward = elgg_extract('entity', $vars, FALSE);

if (!$campaign_reward) {
	return TRUE;
}

$owner = $campaign_reward->getOwnerEntity();
$container = $campaign_reward->getContainerEntity();

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'campaign_reward',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if (elgg_is_active_plugin('fundraising')) {
	elgg_load_library('coopfunding:fundraising');
	$fundcampaign = get_entity ($campaign_reward->container_guid);
	
	if ($donatebutton) {
		$metadata .= fundraising_get_contribute_button ($fundcampaign->guid, $campaign_reward->amount);
	}	
}

$subtitle = "$campaign_reward->amount" . "€";
$excerpt = "$campaign_reward->description";

$params = array(
		'entity' => $campaign_reward,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
);

$params = $params + $vars;
$list_body = elgg_view('object/summary', $params);

echo elgg_view_image_block($owner_icon, $list_body);


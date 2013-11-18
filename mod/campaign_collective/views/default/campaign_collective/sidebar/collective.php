<?php
/**
 * campaign_collective sidebar list
 */

$fundcampaign = $vars['entity'];
if ($fundcampaign) {
	$entities = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'campaign_collective',
		'container_guid' => $fundcampaign->guid,
		'order_by_metadata' => array('name' => 'amount', 'direction' => 'ASC', 'as' => 'integer'),
		'limit'=> 50
	));

	$url = elgg_get_site_url() . "campaign_collective/owner/{$fundcampaign->guid}";
	$content = "<a href=" .  $url . ">" . elgg_echo('campaign_collective:view all') . "</a> <br>";
	$content .= "<ul>";
		
	if ($entities) {
		foreach ($entities as $entity){
		$content.= "<li><div><b>" . $entity->title . "</b><br>" . $entity->description . "</div></li><br>";
		}
	}

	$content .= "</ul>";

	$title = elgg_echo('campaign_collective:items');
	echo elgg_view_module('aside', $title, $content);

}


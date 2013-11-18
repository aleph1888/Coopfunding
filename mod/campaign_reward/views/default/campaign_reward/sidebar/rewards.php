<?php
/**
 * Campaign_reward sidebar list
 */

$fundcampaign = $vars['entity'];
if ($fundcampaign) {
	$entities = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'campaign_reward',
		'container_guid' => $fundcampaign->guid,
		'order_by_metadata' => array('name' => 'amount', 'direction' => 'ASC', 'as' => 'integer'),
		'limit'=> 100
	));

	$url = elgg_get_site_url() . "campaign_reward/owner/{$fundcampaign->guid}";
	$content = "<a href=" .  $url . ">" . elgg_echo('campaign_reward:view all') . "</a> <br>";
	if (elgg_is_admin_logged_in()){	
		$url = elgg_get_site_url() . "campaign_reward/books/{$fundcampaign->guid}";
		$content .= "<a href=" .  $url . ">" . elgg_echo('campaign_reward:books:view all') . "</a>";
	}
	$content .= "<ul>";
		
	if ($entities) {
		foreach ($entities as $entity){
			if ($fundcampaign->is_active) {
				$donatebuttons_link = fundraising_get_contribute_button ($fundcampaign->guid, $entity->amount, $entity->guid);
			}
		$content.= "<li><div><b>" . $entity->title . "</b><br>" . $entity->description . $donatebuttons_link . "</div></li><br>";
		}
	}

	$content .= "</ul>";

	$title = elgg_echo('campaign_reward:items');
	echo elgg_view_module('aside', $title, $content);

}


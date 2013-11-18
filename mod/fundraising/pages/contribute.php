<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 */
$guid = get_input('guid');
$reward_guid = get_input('reward_guid');
$entity = get_entity($guid);
$amount = get_input('amount');

if ($entity) {
	$entity_type = $entity->getSubtype();
} else {
	forward ('', '404');
}

elgg_push_breadcrumb(elgg_echo("projects"), 'projects/all');
if ($entity_type == 'fundcampaign') {
	elgg_push_breadcrumb($entity->getContainerEntity()->name, "project/{$entity->getContainerEntity()->alias}");
	elgg_push_breadcrumb(elgg_echo("fundcampaigns"), "fundcampaigns/owner/{$entity->getContainerEntity()->guid}");
	elgg_push_breadcrumb($entity->name, "fundcampaigns/view/{$entity->guid}");
	$plugin_name = 'fundcampaigns';
}else{
	elgg_push_breadcrumb($entity->name, "project/{$entity->alias}");
	$plugin_name = 'projects';
}
elgg_push_breadcrumb(elgg_echo('fundraising:contribute'));

elgg_load_library("elgg:{$plugin_name}");
elgg_set_page_owner_guid($guid);

$content = elgg_view_form('fundraising/contribute', array(), array('entity' => $entity, 'amount' => $amount, 'reward_guid' => $reward_guid));

$title = elgg_echo("fundraising:contribute:{$entity_type}", array($entity->name));
$body = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => '',
));

echo elgg_view_page($title, $body);

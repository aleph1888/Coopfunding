<?php
/**
 * Delete campaign_collective entity
 *
 * @package campaign_collective
 */

$campaign_collective_guid = get_input('guid');
$campaign_collective = get_entity($campaign_collective_guid);

if (elgg_instanceof($campaign_collective, 'object', 'campaign_collective') && $campaign_collective->canEdit()) {
	$container = get_entity($campaign_collective->container_guid);
	if ($campaign_collective->delete()) {
		system_message(elgg_echo('campaign_collective:deleted'));
		forward("campaign_collective/owner/$container->guid");
	} else {
		register_error(elgg_echo('campaign_collective:error:cannot_delete_item'));
	}
} else {
	register_error(elgg_echo('campaign_collective:error:item_not_found'));
}

forward(REFERER);

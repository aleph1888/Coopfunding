<?php
/*
* Member asks a entity to be verified, wether project, fundcampaign, revision; from state("in_progress") to state("request")
*       See: moderation/lib
*                       function moderation_get_request_user_button ($entity_guid)
*                       { #Moderation user button to request publish the project.}
*/

$guid = (int) get_input('guid');
$entity = get_entity($guid);

if ($entity) {
	if ($entity->getSubtype() == 'revision') {
		$entity_group = get_entity($entity->container_guid);
	} else {
		$entity_group = $entity;
	}

	if ($entity_group->canEdit()){
		$entity->state = 'request';

		system_message(elgg_echo('moderation:senttoverify'));
		forward($entity->getUrl());
	}
}

register_error(elgg_echo('moderation:notsenttoverify' , "Entity- ".  $entity->guid . "; canEdit- " . $entity->canEdit()));
forward(REFERER);


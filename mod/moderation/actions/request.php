<?php
/*
* Member asks a entity to be verified.
	See: moderation/lib
			function moderation_get_request_user_button ($entity_guid) 
			{ #Moderation user button to request publish the project.}
*/
*/
$guid = (int) get_input('guid');
$entity = get_entity($guid);

if ($entity && $entity->canEdit()) {
	$entity->state = 'request';
	system_message(elgg_echo('moderation:senttoverify'));
	forward($entity->getUrl());

} else {
	register_error(elgg_echo('moderation:notsenttoverify' , "Entity- ".  $entity->guid} . "; canEdit- " . $entity->canEdit()));
	forward(REFERER);
}



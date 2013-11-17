
<?php

$fromGuid = get_input('fromGuid');
$toGuid = get_input('toGuid');

$subject = get_input('subject');
$message = get_input('message');

$contact = new ElggObject;
$contact->subtype = 'projects-contact';
$contact->access_id = ACCESS_PUBLIC;
$contact->fromGuid = $fromGuid;
$contact->toGuid = $toGuid;
$contact->owner_guid = $toGuid;
$contact->container_guid = $toGuid;

$contact->title = $subject;
$contact->description = $message;
$contact->readed = false;

if ($contact->save()) {

	system_message(elgg_echo('projects_contact:save:success'));
	
	forward($contact->getContainerEntity()->getURL());

} else {

	register_error(elgg_echo('projects_contacts:save:failed'));
	forward("projects_contacts");

}

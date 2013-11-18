<?php
/**
 * Save campaign_collective entity
 *
 *
 * @package campaign_collective
 */

// start a new sticky form session in case of failure
elgg_make_sticky_form('campaign_collective');

// store errors to pass along
$error = FALSE;
$error_forward_url = REFERER;
$user = elgg_get_logged_in_user_entity();

// edit or create a new entity
$guid = get_input('guid');

if ($guid) {
	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'campaign_collective') && $entity->canEdit()) {
		$campaign_collective = $entity;
	} else {
		register_error(elgg_echo('campaign_collective:error:item_not_found'));
		forward(get_input('forward', REFERER));
	}
} else {
	$campaign_collective = new ElggObject();
	$campaign_collective->subtype = 'campaign_collective';
	$campaign_collective->owner_guid = elgg_get_logged_in_user_entity()->guid;

	$campaign_collective->container_guid = get_input('container_guid');
	$new_campaign_collective = TRUE;
}

// set defaults and required values.
$values = array(
	'title' => '',
	'description' => '',
	'access_id' => ACCESS_PUBLIC,
);

// fail if a required entity isn't set
$required = array('title', 'description');

// load from POST and do sanity and access checking
foreach ($values as $name => $default) {
	if ($name == 'title') {
		$value = htmlspecialchars(get_input('title', $default, false), ENT_QUOTES, 'UTF-8');
	} else {
		$value = get_input($name, $default);
	}

	if (in_array($name, $required) && empty($value)) {
		$error = elgg_echo("campaign_collective:error:missing:$name");
	}

	$values[$name] = $value;	
}

// assign values to the entity
if (!$error) {
	foreach ($values as $name => $value) {
		$campaign_collective->$name = $value;
	}
}

// only try to save base entity if no errors
if (!$error) {
	if ($campaign_collective->save()) {
		// remove sticky form entries
		elgg_clear_sticky_form('campaign_collective');

		system_message(elgg_echo('campaign_collective:saved'));
 		$url =  elgg_get_site_url() . "campaign_collective/owner/{$campaign_collective->container_guid}";     
		forward($url);		
	} else {
		register_error(elgg_echo('campaign_collective:error:cannot_save'));
		forward($error_forward_url);
	}
} else {
	register_error($error);
	forward($error_forward_url);
}

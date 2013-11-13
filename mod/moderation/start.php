<?php

elgg_register_event_handler('init', 'system', 'moderation_init');

function moderation_init() {

	//Register library
	elgg_register_library('elgg:moderation', elgg_get_plugins_path() . 'moderation/lib/moderation.php');

	//Register actions
	$action_base = elgg_get_plugins_path() . 'moderation/actions';

	//TODO OR NOT convert save moderation into actions instead functions.
	elgg_register_action("moderation/request", "$action_base/request.php", 'public');

	// Set up the menu
	if (elgg_is_admin_logged_in()) {
		$item = new ElggMenuItem('moderation', elgg_echo('moderation:moderation'), 'moderation/main');
		elgg_register_menu_item('site', $item);
	}

	//page handler
	elgg_register_page_handler('moderation', 'moderation_page_handler');

	// Register plugin hooks
	elgg_register_plugin_hook_handler('moderation:save', 'entity', 'moderation_do_save');

	//Css
	elgg_extend_view('css/elgg', 'moderation/css');

}

function moderation_page_handler($page) {

	elgg_load_library('elgg:moderation');

	moderator_gate_keeper();
	elgg_push_breadcrumb(elgg_echo('moderation:moderation'), "moderation/main");

	switch ($page[0]) {
		case "main":
			moderation_register_toggle();
			moderation_handle_main_page();
			break;
		default:
			break;
	}

	return true;

}

/*
* Lists entities in "request" state. Moderator should commit them, state("commited").
* 	LIST 1, new entities. Entity is in state("request") and ACCESS_PRIVATE, then moderator can state("commited") and ACCESS_PUBLIC
*	LIST 2, edited entities. Exists a revision in state("request"), then moderator can state("commited") and map to entity object.
*/
function moderation_handle_main_page() {

	$title = elgg_echo('moderation:manage');

	elgg_push_breadcrumb($title);

	$content = "<h3>" . elgg_echo('moderation:manage:new petitions') . "</h3><br>";
	$list = elgg_list_entities_from_metadata(array(
		'metadata_name' => 'state',
		'metadata_value' => 'request',
		'full_view' => false
	));

	if (!$list) {
		$list = elgg_echo('moderation:manage:nonewpetitions') . "<br>";
	}

	$content .= $list;

	$content .= "<br><h3>" . elgg_echo('moderation:manage:revision') . "</h3><br>";
	$list= elgg_list_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'revision',
		'metadata_name' => 'state',
		'metadata_value' => 'request',
		'full_view' => false
	));

	if (!$list) {
		$list = elgg_echo('moderation:manage:norevisions');
	}
	$content .= $list;
	$sidebar = elgg_view('moderation/sidebar.php');

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
		'sidebar' => $sidebar
	);

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);

}

/*
* As coming from the same edit page, switch between moderator or user entity to save.
*/
function moderation_do_save ($hook, $type, $returnvalue, $params) {

	elgg_load_library('elgg:moderation');

	$entity = $params['entity'];
	$input = $params['input'];

	if ($entity) {
		if (elgg_is_admin_logged_in()){
			return moderation_do_admin_save($entity, $input);
		} else {
			return moderation_do_user_save($entity, $input);
		}
	} else {
		return REFERER;
	}
}


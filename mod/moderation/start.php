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

	$entity_in = $params['entity'];
	$input =  $params['input'];
	$is_new_entity = $params['is_new'];
	$entity_plugin_name = $params["plugin_name"];

	if (!$entity_in) {
		register_error(elgg_echo("moderation:nothingtosave:"));
		forward(REFERER);
	}

	$entity_update = $entity_in;
	$entity_group = $entity_in;

	if ($entity_in->getSubtype() == "revision") {
		$entity_group = get_entity($entity_in->container_guid);
		$return_url = $entity_container->getURL();
		$action = "revision";
	} else {
		if (!$entity_in->state) {
			$action = "new";			
		} elseif ($entity_in->state == "in_progress") {
			$action = "new";
		} elseif ($entity_in->state == "request") {
			$action = "commit";
		} elseif ($entity_in->state == "commited") {
			elgg_load_library("elgg:moderation");
			$revision = moderation_get_last_revision($entity_in);
			if (!$revision) {
				$revision = new ElggObject();
				$revision->type = 'object';
				$revision->subtype = 'revision';
				
				$revision->owner_guid = $entity_in->owner_guid;
				$revision->container_guid = $entity_in->guid;
			}
			$entity_update = $revision;
			$action = "revision";
			/*
			if ($entity_in->getSubtype() == 'fundcampaign') {
				#todo especific job
			}elseif ($entity_in->getSubtype() == 'project') {
				#todo especific job
			}
			*/
		}
	}
	
	elgg_load_library("elgg:{$entity_plugin_name}");
	
	if (!isset($input['alias'])) {
		register_error(elgg_echo("{$entity_plugin_name}:alias:missing"));
		forward(REFERER);
	} elseif (!preg_match("/^[a-zA-Z0-9\-]{2,32}$/", $input['alias'])) {
		register_error(elgg_echo("{$entity_plugin_name}:alias:invalidchars"));
		forward(REFERER);
	} elseif ($entity_in->alias != $input['alias'] && call_user_func("{$entity_plugin_name}_get_from_alias", $input['alias'])) {
		register_error(elgg_echo("{$entity_plugin_name}:already_used"));
		forward(REFERER);
	}

	$map_to_group_object = elgg_is_admin_logged_in() && $action == "revision";
	foreach($input as $shortname => $value) {
		// update access collection name if name changes				
		if (!$is_new_entity && $shortname == 'name' && $value != $entity_group->name) {
			$entity_name = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			$string_to_sanatice = elgg_echo("{$entity_plugin_name}:{$entity_group->getSubtype()}");
			$ac_name = sanitize_string($string_to_sanatice . ": " . $entity_name);
			$acl = get_access_collection($entity_group->group_acl);
			if ($acl) {
				// @todo Elgg api does not support updating access collection name
				$db_prefix = elgg_get_config('dbprefix');
				$query = "UPDATE {$db_prefix}access_collections SET name = '$ac_name'
					WHERE id = $entity_group->group_acl";
				update_data($query);
			}
		}
		if ($entity_update->$shortname != $value) {
			$entity_update->$shortname = $value;
			if ($map_to_group_object) {
				$entity_group->$shortname = $value;
			}
		}
	}
	// Validate create
	if (!$entity_update->name) {
		register_error(elgg_echo("{$entity_plugin_name}:notitle"));
		forward(REFERER);
	}
		
	#control user who do save.
	if (elgg_is_admin_logged_in()) {
		$entity_update->state = 'commited';
		$info_message = elgg_echo('moderation:saved:commited');
		$action = "commit";
		if ($map_to_group_object) {
			$entity_group->save();
		}
	} else {
		$entity_update->state = 'in_progress';
		$info_message = elgg_echo('moderation:saved:remember_ask_for_commit');
	}	
	
	$entity_update->save();

	
	$params = array (
		'entity' => $entity_group,
		'plugin_name' => $entity_plugin_name,
		'revision' => $entity_update,//if this object is not subtype("revision") will be ignored
		'action' => $action,
		'discard_icon' => get_input('discard_icon')
		);
	elgg_load_library("elgg:moderation");
	moderation_save_icon($params);

	system_message(elgg_echo($info_message));

	if (!$return_url) {
		 $return_url = $entity_in->getURL();
	}
	return $return_url;

/*
#TODO OPTIONS
$tool_options = elgg_get_config("{$entity_plugin_name}_tool_options");
if ($tool_options) {
	foreach ($tool_options as $entity_update_option) {
		$option_toggle_name = $entity_update_option->name . "_enable";
		$option_default = $entity_update_option->default_on ? 'yes' : 'no';
		$entity_update->$option_toggle_name = get_input($option_toggle_name, $option_default);
	}
}*/

#TODO CHANGE OWNER cascade for fundcampaigns.
}


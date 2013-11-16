<?php
/**
 * Elgg projects plugin edit action.
 *
 * @package Coopfunding
 * @subpackage Projects
 */

elgg_make_sticky_form('projects');

/**
 * wrapper for recursive array walk decoding
 */
function profile_array_decoder(&$v) {
        $v = _elgg_html_decode($v);
}

$user = elgg_get_logged_in_user_entity();

elgg_make_sticky_form('projects');

$project_guid = (int)get_input('project_guid');
$input = array();
$input['name'] = htmlspecialchars(get_input('name', '', false), ENT_QUOTES, 'UTF-8');
$input['alias'] = htmlspecialchars(get_input('alias', '', false), ENT_QUOTES, 'UTF-8');

if ($project_guid) {
	$project = new ElggGroup($project_guid);
	if (!$project->canEdit()) {
		register_error(elgg_echo("projects:cantedit"));
		forward(REFERER);
	}
} else {
	$project = new ElggGroup();
	$project->subtype = 'project';
	$is_new_project = true;

	$project->container_guid = $user->guid;
	$project->owner_guid = $user->guid;

	$project->name = $input['name'];
	$project->save();
	
	$project->join($user);

	$project->membership = ACCESS_PRIVATE;
	$project->access_id = ACCESS_PRIVATE;
}

foreach (elgg_get_config("project") as $shortname => $valuetype) {
	$input[$shortname] = get_input($shortname);

	// @todo treat profile fields as unescaped: don't filter, encode on output
	if (is_array($input[$shortname])) {
		array_walk_recursive($input[$shortname], 'profile_array_decoder');
	} else {
		$input[$shortname] = _elgg_html_decode($input[$shortname]);
	}
	if ($valuetype == 'tags') {
		$input[$shortname] = string_to_tag_array($input[$shortname]);
	}
}

// TODO CHANGE OWNER
/*$old_owner_guid = $is_new_project ? 0 : $project->owner_guid;
$new_owner_guid = (int) get_input('owner_guid');

$owner_has_changed = false;
$old_icontime = null;
if (!$is_new_project && $new_owner_guid && $new_owner_guid != $old_owner_guid) {
	// verify new owner is member and old owner/admin is logged in
	if (is_project_member($project_guid, $new_owner_guid) && ($old_owner_guid == $user->guid || $user->isAdmin())) {
		$project->owner_guid = $new_owner_guid;
		$project->container_guid = $new_owner_guid;

		$metadata = elgg_get_metadata(array(
			'guid' => $project_guid,
			'limit' => false,
		));
		if ($metadata) {
			foreach ($metadata as $md) {
				if ($md->owner_guid == $old_owner_guid) {
					$md->owner_guid = $new_owner_guid;
					$md->save();
				}
			}
		}

		// @todo Remove this when #4683 fixed
		$owner_has_changed = true;
		$old_icontime = $project->icontime;
	}
}

	$must_move_icons = ($owner_has_changed && $old_icontime);

	elgg_set_page_owner_guid($project->guid);
*/

$forward_url = "404";
if (elgg_is_active_plugin('moderation')) {	
	$params = array ('plugin_name' => 'projects', 'entity'=> $project, 'is_new'=> $is_new_project, 'input' => $input); 
	$forward_url = elgg_trigger_plugin_hook('moderation:save', 'entity', $params);
}	

elgg_clear_sticky_form('projects');
forward($forward_url);


//TODO, restore trigger original entity save method.



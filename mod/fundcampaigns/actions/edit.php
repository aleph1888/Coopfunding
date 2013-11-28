<?php
/**
 * Elgg fundcampaigns plugin edit action.
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

elgg_make_sticky_form('fundcampaigns');

/**
 * wrapper for recursive array walk decoding
 */
function profile_array_decoder(&$v) {
        $v = _elgg_html_decode($v);
}

$user = elgg_get_logged_in_user_entity();

elgg_make_sticky_form('fundcampaigns');

$fundcampaign_guid = (int)get_input('fundcampaign_guid');
$input = array();
$input['name'] = htmlspecialchars(get_input('name', '', false), ENT_QUOTES, 'UTF-8');
$input['alias'] = htmlspecialchars(get_input('alias', '', false), ENT_QUOTES, 'UTF-8');
$input['access_id'] = get_input('vis');
$input['is_active'] = get_input('is_active');

if ($fundcampaign_guid) {
	$fundcampaign = new ElggGroup($fundcampaign_guid);
	if (!$fundcampaign->canEdit()) {
		register_error(elgg_echo("fundcampaigns:cantedit"));
		forward(REFERER);
	}
} else {
	$fundcampaign = new ElggGroup();
	$fundcampaign->subtype = 'fundcampaign';
	$is_new_fundcampaign = true;

	$project = get_entity(get_input('project'));
	
	$fundcampaign->container_guid = $project->guid;
	$fundcampaign->owner_guid = $project->owner_guid;
	
	$fundcampaign->name = $input['name'];
	$fundcampaign->save();
	
	$fundcampaign->join($user);

	$fundcampaign->membership = ACCESS_PRIVATE;
	$fundcampaign->access_id = ACCESS_PRIVATE;
}

foreach (elgg_get_config("fundcampaign") as $shortname => $valuetype) {
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

//Control is_active
if (!$is_new_fundcampaign && $input['is_active'] && !$fundcampaign->is_active) {
	elgg_load_library("elgg:fundcampaigns");
	$fundcampaign_old = fundcampaigns_get_active_campaign($fundcampaign->container_guid, false);
	if ($fundcampaign_old) {
		$fundcampaign_old->is_active = false;
		$fundcampaign_old->save();
	}
}

// TODO CHANGE OWNER
/*$old_owner_guid = $is_new_fundcampaign ? 0 : $fundcampaign->owner_guid;
$new_owner_guid = (int) get_input('owner_guid');

$owner_has_changed = false;
$old_icontime = null;
if (!$is_new_fundcampaign && $new_owner_guid && $new_owner_guid != $old_owner_guid) {
	// verify new owner is member and old owner/admin is logged in
	if (is_fundcampaign_member($fundcampaign_guid, $new_owner_guid) && ($old_owner_guid == $user->guid || $user->isAdmin())) {
		$fundcampaign->owner_guid = $new_owner_guid;
		$fundcampaign->container_guid = $new_owner_guid;

		$metadata = elgg_get_metadata(array(
			'guid' => $fundcampaign_guid,
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
		$old_icontime = $fundcampaign->icontime;
	}
}

	$must_move_icons = ($owner_has_changed && $old_icontime);

	elgg_set_page_owner_guid($fundcampaign->guid);
*/

$forward_url = "404";
if (elgg_is_active_plugin('moderation')) {	
	$params = array ('plugin_name' => 'fundcampaigns', 'entity'=> $fundcampaign, 'is_new'=> $is_new_fundcampaign, 'input' => $input); 
	$forward_url = elgg_trigger_plugin_hook('moderation:save', 'entity', $params);
}	

elgg_clear_sticky_form('fundcampaigns');
forward($forward_url);


//TODO, restore trigger original entity save method.









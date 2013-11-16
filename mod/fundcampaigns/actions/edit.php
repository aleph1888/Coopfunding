<?php
/**
 * Elgg Fundcampaigns plugin edit action.
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

$input['name'] = htmlspecialchars(get_input('name', '', false), ENT_QUOTES, 'UTF-8');
$input['alias'] = htmlspecialchars(get_input('alias', '', false), ENT_QUOTES, 'UTF-8');

$fundcampaign_guid = (int)get_input('fundcampaign_guid');
var_dump($fundcampaign_guid); exit();
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

	$project = get_entity('project');
	$fundcampaign->container_guid = $project->guid;
	$fundcampaign->owner_guid = $project->owner_guid;
	$fundcampaign->save();

	$fundcampaign->join($user);

	$fundcampaign->membership = ACCESS_PRIVATE;
	$fundcampaign->access_id = ACCESS_PRIVATE;
}

$input = array();
foreach (elgg_get_config("fundcampaigns") as $shortname => $valuetype) {
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
var_dump("reate"); exit();
$forward_url = "404";
if (elgg_is_active_plugin('moderation')) {
	$params = array ('plugin_name' => 'fundcampaigns', 'entity'=> $fundcampaign, 'is_new'=> $is_new_fundcampaign, 'input' => $input); 
	$forward_url = elgg_trigger_plugin_hook('moderation:save', 'entity', $params);
}

elgg_clear_sticky_form('fundcampaigns');
forward($forward_url);	


//TODO, restore trigger original entity save method.



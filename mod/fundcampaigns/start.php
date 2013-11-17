<?php
/**
* Elgg Fundcampaigns plugin
* This plugin manage projects's campaign.
*
* @package fundcampaign
*/

elgg_register_event_handler('init', 'system', 'fundcampaigns_init');

// Ensure this runs after other plugins
elgg_register_event_handler('init', 'system', 'fundcampaigns_fields_setup', 10000);

function fundcampaigns_init() {

	elgg_register_entity_type('fundcampaign');

	$root = dirname(__FILE__);
	elgg_register_library('elgg:fundcampaigns', "$root/lib/fundcampaigns.php");

	elgg_extend_view('css/elgg', 'fundcampaigns/css');

	elgg_register_page_handler('fundcampaigns', 'fundcampaigns_page_handler');
	elgg_register_page_handler('fundcampaign', 'fundcampaigns_page_handler');

	// Register URL handlers for projects
	elgg_register_entity_url_handler('group', 'fundcampaign', 'fundcampaigns_url');	

	// Register an icon handler for projects
	elgg_register_page_handler('fundcampaignicon', 'fundcampaigns_icon_handler');

	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'fundcampaigns_block_menu');

	$action_path = elgg_get_plugins_path() . 'fundcampaigns/actions';
	elgg_register_action("fundcampaigns/edit", "$action_path/edit.php");
	elgg_register_action("fundcampaigns/delete", "$action_path/delete.php");

}

function fundcampaigns_fields_setup() {

	$profile_defaults = array(
		'description' => 'longtext',
		'briefdescription' => 'text',
		'interests' => 'tags',
		
		'paymethodBAN' => 'text',
		'paymethodCES' => 'text',
		
		'start_date' => 'date',
		'end_date' => 'date',
		'total_amount' => 'text',

		'period_one_duration' => 'text',
		'period_one_amount' => 'text',

	);

	$profile_defaults = elgg_trigger_plugin_hook('profile:fields', 'fundcampaigns', NULL, $profile_defaults);

	elgg_set_config('fundcampaign', $profile_defaults);
	$fundcampaign_profile_fields = elgg_get_config('fundcampaign');

	// register any tag metadata names
	foreach ($profile_defaults as $name => $type) {
		if ($type == 'tags') {
			elgg_register_tag_metadata_name($name);

			// only shows up in search but why not just set this in en.php as doing it here
			// means you cannot override it in a plugin
			add_translation(get_current_language(), array("tag_names:$name" => elgg_echo("fundcampaign:$name")));
		}
	}

}

/**
 * Fundcampaigns page handler
 *
 * @param array $page Array of URL components for routing
 * @return bool
 */
function fundcampaigns_page_handler($page, $handler) {

	elgg_load_library('elgg:fundcampaigns');

	if (!isset($page[1])) {
		$page[1] = $page[0];
		$page[0] = 'view';

		$fundcampaign = fundcampaigns_get_from_alias($page[1]);
	}

	elgg_push_breadcrumb(elgg_echo('projects'), "projects/all");

	switch ($page[0]) {
		case 'all';
			return false;
		case 'owner':
			elgg_set_page_owner_guid($page[1]);
			set_input('project', $page[1]);			
			$project = get_entity($page[1]);
			elgg_push_breadcrumb($project->alias, "project/{$project->alias}");
			fundcampaigns_register_toggle();
			fundcampaigns_handle_owner_page($page[1]);
			break;
		case 'view':
			if (!$fundcampaign) { $fundcampaign = get_entity($page[1]);}
			$project = get_entity($fundcampaign->container_guid);
			elgg_push_breadcrumb($project->alias, "project/{$project->alias}");
			elgg_push_breadcrumb(elgg_echo("fundcampaigns"));			
			fundcampaigns_handle_view_page($fundcampaign->guid);
			break;
		case 'edit':
			$fundcampaign = fundcampaigns_get_from_alias($page[1]);
			$project = get_entity( $fundcampaign->container_guid);
			elgg_set_page_owner_guid($project->guid);
			elgg_push_breadcrumb($project->alias, "project/{$project->alias}");
			elgg_push_breadcrumb(elgg_echo("fundcampaigns"));
			set_input('fundcampaign', $fundcampaign->guid);
			set_input('project', $fundcampaign->container_guid);			
			fundcampaigns_handle_edit_page('edit',$fundcampaign->guid);			
			break;
		case 'add':
			$project = get_entity($page[1]);
			elgg_push_breadcrumb($project->alias, "project/{$project->alias}");
			set_input('project', $page[1]);
			fundcampaigns_handle_edit_page('add', $page[1]);
			break;
		default:
			return false;
	}
	return true;

}

function fundcampaigns_url($entity) {	
	return "fundcampaigns/view/{$entity->guid}";

}

function fundcampaigns_handle_owner_page($project_guid) {

	$title = elgg_echo('fundcampaigns:campaigns');
	elgg_push_breadcrumb($title);

	elgg_register_menu_item('title', array(
		'name' => 'fundcampaigns',
		'href' => "fundcampaigns/add/{$project_guid}",
		'text' => elgg_echo('fundcampaigns:add'),
		'link_class' => 'elgg-button elgg-button-action',
	));

	$content = elgg_list_entities(array(
		'type' => 'group',
		'subtype' => 'fundcampaign',
		'container_guid' => $project_guid,
		'full_view' => false,
	));

	if (!$content) {
		$content = elgg_echo('fundcampaigns:none');
	}

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);

}

function fundcampaigns_handle_view_page($guid) {

	// turn this into a core function
	global $autofeed;
	$autofeed = true;

	elgg_push_context('fundcampaigns_profile');

	$fundcampaign = get_entity($guid);
	if (!$fundcampaign) {
		forward('fundcampaigns/all');
	}

	elgg_push_breadcrumb($fundcampaign->name);

	fundcampaigns_register_profile_buttons($fundcampaign);

	if (fundcampaigns_is_active_campaign ($fundcampaign) ) {
		elgg_trigger_plugin_hook('fundcampaigns:profilebuttons', 'fundcampaign', $fundcampaign, array('entity' => $fundcampaign));
	}

	$content = elgg_view('fundcampaigns/profile/layout', array('entity' => $fundcampaign));
	$sidebar .= elgg_view('fundcampaigns/sidebar/contribute', array('entity' => $fundcampaign));
	$sidebar .= elgg_trigger_plugin_hook('fundcampaigns:sidebarmenus', 'fundcampaign', $fundcampaign); 
	$sidebar .= elgg_view('fundcampaigns/sidebar/members', array('entity' => $fundcampaign));

	$params = array(
		'content' => $content,
		'sidebar' => $sidebar,
		'title' => $fundcampaign->name,
		'filter' => '',
	);

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($fundcampaign->name, $body);

}

function fundcampaigns_handle_edit_page($page, $guid = 0) {

	gatekeeper();

	if ($page == 'add') {
		$title = elgg_echo('fundcampaigns:add');
		elgg_push_breadcrumb($title);
		$content = elgg_view('fundcampaigns/edit');
	} else {
		$title = elgg_echo("fundcampaigns:edit");
		$fundcampaign = get_entity($guid);

		if ($fundcampaign && $fundcampaign->canEdit()) {	
			elgg_push_breadcrumb($fundcampaign->name, $fundcampaign->getURL());
			elgg_push_breadcrumb($title);
			$content = elgg_view("fundcampaigns/edit", array('entity' => $fundcampaign));
		} else {
			$content = elgg_echo('fundcampaigns:noaccess');
		}
	}

	if (elgg_is_admin_logged_in() && elgg_is_active_plugin("moderation")) {
		$sidebar = elgg_view('moderation/sidebar', array('entity' => $project));
	}
	$params = array(
		'content' => $content,
		'title' => $title,
		'sidebar' => $sidebar,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);

}

function fundcampaigns_block_menu($hook, $type, $return, $params) {
	$url = "fundcampaigns/owner/{$params['entity']->guid}";
	$text = elgg_echo('fundcampaigns:campaigns');
	$return[] = new ElggMenuItem('fundcampaigns', $text, $url);

	return $return;

}

function fundcampaigns_register_toggle() {
	set_input('list_type', get_input('list_type', 'gallery'));

	$url = elgg_http_remove_url_query_element(current_page_url(), 'list_type');

	if (get_input('list_type', 'list') == 'list') {
		$list_type = "gallery";
		$icon = elgg_view_icon('grid');
	} else {
		$list_type = "list";
		$icon = elgg_view_icon('list');
	}

	if (substr_count($url, '?')) {
		$url .= "&list_type=" . $list_type;
	} else {
		$url .= "?list_type=" . $list_type;
	}

	elgg_register_menu_item('extras', array(
		'name' => 'file_list',
		'text' => $icon,
		'href' => $url,
		'title' => elgg_echo("file:list:$list_type"),
		'priority' => 1000,
	));

}

function fundcampaigns_register_profile_buttons($fundcampaign) {

	$actions = array();

	if ($fundcampaign->canEdit()) {

		$url = elgg_get_site_url() . "fundcampaigns/edit/{$fundcampaign->alias}";
		$actions[$url] = 'fundcampaigns:edit';
	}

	if ($actions) {
		foreach ($actions as $url => $text) {
			elgg_register_menu_item('title', array(
				'name' => $text,
				'href' => $url,
				'text' => elgg_echo($text),
				'link_class' => 'elgg-button elgg-button-action',
			));
		}
	}

}

function fundcampaigns_icon_handler($page) {

	// The username should be the file we're getting
	if (isset($page[0])) {
		set_input('fundcampaign_guid', $page[0]);
	}
	if (isset($page[1])) {
		set_input('size', $page[1]);
	}
	// Include the standard profile index
	$plugin_dir = elgg_get_plugins_path();
	include("$plugin_dir/fundcampaigns/icon.php");
	return true;

}

function fundcampaigns_icon_url_override($hook, $type, $returnvalue, $params) {
	/* @var ElggObject $fundcampaign */
	$fundcampaign = $params['entity'];
	$size = $params['size'];

	$icontime = $fundcampaign->icontime;
	if ($icontime) {
		// return thumbnail
		return "fundcampaignicon/$fundcampaign->guid/$size/$icontime.jpg";
	}

	return "mod/fundcampaigns/graphics/default{$size}.gif";

}

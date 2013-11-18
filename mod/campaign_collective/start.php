<?php
/**
 * Collective Returns
 *
 * @package campaign_collective
 *
 */

elgg_register_event_handler('init', 'system', 'campaign_collective_init');

/**
 * Init campaign_collective plugin.
 */
function campaign_collective_init() {
	elgg_register_library('elgg:campaign_collective', elgg_get_plugins_path() . 'campaign_collective/lib/campaign_collective.php');

	// routing of urls
	elgg_register_page_handler('campaign_collective', 'campaign_collective_page_handler');

	// Register for search.
	elgg_register_entity_type('object', 'campaign_collective');

	elgg_register_plugin_hook_handler('fundcampaigns:profilebuttons', 'fundcampaign', 'campaign_collective_set_add_button');
	elgg_register_plugin_hook_handler('fundcampaigns:sidebarmenus', 'fundcampaign', 'campaign_collective_set_side_bar_menu');
	elgg_register_plugin_hook_handler('fundraising:rewards:save', 'campaign_collective', 'campaign_collective_save');
	
	// register actions
	$action_path = elgg_get_plugins_path() . 'campaign_collective/actions';
	elgg_register_action('campaign_collective/save', "$action_path/campaign_collective/save.php");
	elgg_register_action('campaign_collective/delete', "$action_path/campaign_collective/delete.php");

	// override the default url to view a campaign object
	elgg_register_plugin_hook_handler('entity:url', 'campaign_collective', 'campaign_collective_set_url');

}

function campaign_collective_page_handler($page) {
	elgg_load_library('elgg:campaign_collective');

	if (!isset($page[0]) || !isset($page[1])) {
		forward('', '404');
	}	
	
	$fundcampaign_guid = $page[1];

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			$params = campaign_collective_get_page_content_list($fundcampaign_guid);
			campaign_collective_set_add_button_func($fundcampaign_guid);
			break;
		case 'add':
			gatekeeper();
			$params = campaign_collective_get_page_content_edit($page_type, $fundcampaign_guid);
			break;
		case 'edit':
			gatekeeper();
			$fundcampaign_guid = get_entity($page[1])->getContainerEntity()->guid;
			$params = campaign_collective_get_page_content_edit($page_type, $page[1]);
			break;
		default:
			return false;

	}
	
	$fundcampaign = get_entity($fundcampaign_guid);
	elgg_push_breadcrumb(elgg_echo("projects"), 'projects/all');
	elgg_push_breadcrumb($fundcampaign->getContainerEntity()->name, "project/{$fundcampaign->getContainerEntity()->alias}");
	elgg_push_breadcrumb(elgg_echo("fundcampaigns"), "fundcampaigns/owner/{$fundcampaign->getContainerEntity()->guid}");
	elgg_push_breadcrumb($fundcampaign->name, "fundcampaigns/view/{$fundcampaign->guid}");
	elgg_push_breadcrumb(elgg_echo("campaign_collective:rewards"));
	elgg_set_page_owner_guid($fundcampaign->getContainerEntity()->guid);

	$params['sidebar'] .= elgg_view('campaign_collective/sidebar', array('page' => $page_type));
	
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
	return true;

}

function campaign_collective_set_side_bar_menu ($hook, $entity_type, $return_value, $params) {
	$return_value .= elgg_view('campaign_collective/sidebar/collective', array('entity' => $params));
	return $return_value;

}

function campaign_collective_set_add_button ($hook, $entity_type, $return_value, $params) {
	$return_value .= campaign_collective_set_add_button_func($params->guid);
	return $return_value;

}

function campaign_collective_set_add_button_func ($guid) {

	$fundcampaign = get_entity($guid);
	$project = $fundcampaign->getContainerEntity();

	if ($project && $project->canEdit()) {
		$text = elgg_echo("campaign_collective:addreward");
		$url = elgg_get_site_url() . "campaign_collective/add/{$guid}";

		elgg_register_menu_item('title', array(
					'name' => $text,
					'href' => $url,
					'text' => elgg_echo($text),
					'link_class' => 'elgg-button elgg-button-action',
				));
	}

	return false;

}

function campaign_collective_set_url($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'campaign_collective')) {
		return "campaign_collective/owner/{$entity->container_guid}";
	}

}



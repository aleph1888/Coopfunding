<?php
/**
 * Get page components to list all indvidual rewards
 *
 * @return array
 */
function campaign_collective_get_page_content_list($guid = NULL) {
	elgg_load_library('coopfunding:fundcampaigns');

	$fundcampaign = get_entity($guid);
	$options = array(
		'type' => 'object',
		'subtype' => 'campaign_collective',
		'container_guid' => $guid,		
		'full_view' => true,
		'no_results' => elgg_echo('campaign_collective:none')
	);	

	$return = array();
	$return['title'] = elgg_echo('campaign_collective:title', array($fundcampaign->name));
	$return['filter_context'] = 'mine';	
	$return['filter'] = false;
 	$return['content'] = elgg_list_entities_from_metadata($options);
	return $return;

}

/**
 * Get page components to edit/create a campaign_collective post.
 *
 * @param string  $page     'edit' or 'new'
 * @param int     $guid     GUID of campaing_reward
 * @return array
 */
function campaign_collective_get_page_content_edit($page, $guid = NULL) {
	$return = array(
		'filter' => '',
	);

	$vars = array();
	$vars['id'] = 'campaign_collective-post-edit';
	$vars['class'] = 'elgg-form-alt';

	$sidebar = '';
	if ($page == 'edit') {
		$campaign_collective = get_entity((int)$guid);

		$title = elgg_echo('campaign_collective:edit');

		if (elgg_instanceof($campaign_collective, 'object', 'campaign_collective') && $campaign_collective->canEdit()) {
			$vars['entity'] = $campaign_collective;
			$title .= ": " . $campaign_collective->title;
			$body_vars = campaign_collective_prepare_form_vars($campaign_collective, $guid);
			$content = elgg_view_form('campaign_collective/save', $vars, $body_vars);
		} else {
			$content = elgg_echo('campaign_collective:error:cannot_edit_item');
		}
	} else {		
		$body_vars = campaign_collective_prepare_form_vars(null, $guid);
		$title = elgg_echo('campaign_collective:add');
		$content = elgg_view_form('campaign_collective/save', $vars, $body_vars);
	}

	$return['title'] = $title;
	$return['content'] = $content;
	$return['sidebar'] = $sidebar;
	return $return;

}

/**
 * Pull together campaign_collective variables for the save form
 *
 * @param ElggObject       $campaign_collective
 * @return array
 */
function campaign_collective_prepare_form_vars($campaign_collective = NULL, $container_guid = NULL) {
	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'access_id' => ACCESS_PUBLIC,
		'container_guid' => $container_guid,
		'guid' => NULL,
	);

	if ($campaign_collective) {
		foreach (array_keys($values) as $field) {
			if (isset($campaign_collective->$field)) {
				$values[$field] = $campaign_collective->$field;
			}
		}
	}

	if (elgg_is_sticky_form('campaign_collective')) {
		$sticky_values = elgg_get_sticky_values('campaign_collective');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('campaign_collective');

	if (!$campaign_collective) {
		return $values;
	}
	return $values;

}



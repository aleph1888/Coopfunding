<?php
/**
 * Fundcampaigns function library
 *
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

/**
 * Get project entity from its alias
 */
function fundcampaigns_get_from_alias($alias) {
	return current(elgg_get_entities_from_metadata(array(
		'type' => 'group',
		'subtype' => 'fundcampaign',
		'metadata_name' => 'alias',
		'metadata_value' => $alias,
		'limit' => 1,
	)));

}

/**
 * Prepares variables for the project edit form view.
 *
 * @param mixed $campaign ElggGroup or null. If a project, uses values from the project.
 * @return array
 */
function fundcampaigns_prepare_form_vars($fundcampaign = null) {
	$values = array(
		'name' => '',
		'alias' => '',
		'is_active' => false,
		'vis' => null,
		'guid' => null,
		'entity' => null,
		'num_periods' => elgg_get_plugin_setting("num_periods", "fundcampaigns"),
		'periods_duration' => elgg_get_plugin_setting("periods_duration", "fundcampaigns"),
	);

	// handle customizable profile fields
	$fields = elgg_get_config('fundcampaign');

	if ($fields) {
		foreach ($fields as $name => $type) {
			if (!isset($values[$name])) {
				$values[$name] = '';
			}
		}
	}

	// handle tool options
	$tools = elgg_get_config('fundcampaigns_tool_options');
	if ($tools) {
		foreach ($tools as $fundcampaign_option) {
			$option_name = $fundcampaign_option->name . "_enable";
			$values[$option_name] = $fundcampaign_option->default_on ? 'yes' : 'no';
		}
	}

	// get current fundcampaigns settings
	if ($fundcampaign) {
		foreach (array_keys($values) as $field) {
			if (isset($fundcampaign->$field)) {
				$values[$field] = $fundcampaign->$field;
			}
		}

		if ($fundcampaign->access_id != ACCESS_PUBLIC && $fundcampaign->access_id != ACCESS_LOGGED_IN) {
			// fundcampaign only access - this is done to handle access not created when fundcampaign is created
			$values['vis'] = ACCESS_PRIVATE;
		} else {
			$values['vis'] = $fundcampaign->access_id;
		}

		$values['entity'] = $fundcampaign;
	}

	// get any sticky form settings
	if (elgg_is_sticky_form('fundcampaigns')) {
		$sticky_values = elgg_get_sticky_values('fundcampaigns');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('fundcampaigns');

	return $values;

}

function fundcampaigns_is_active_campaign ($fundcampaign) {
	$date = date('Y-m-d');
	return $fundcampaign->is_active && $date >= $fundcampaign->start_date && $date <= $fundcampaign->end_date;;

}

function fundcampaigns_get_active_campaign ($guid = 0) {
	return current(elgg_get_entities_from_metadata(array(
		'type' => 'group',
		'subtype' => 'fundcampaign',
		'container_guid' => $guid,
		'metadata_name' => 'is_active',
		'metadata_value' => true,
		'limit' => 1,
	)));

}

<?php
/**
 * Get page components to list all indvidual rewards
 *
 * @return array
 */
function campaign_reward_get_page_content_list($guid = NULL) {
	elgg_load_library('coopfunding:fundcampaigns');

	$fundcampaign = get_entity($guid);
	$options = array(
		'type' => 'object',
		'subtype' => 'campaign_reward',
		'container_guid' => $guid,
		'donatebutton' => fundcampaigns_is_active_campaign($fundcampaign),
		'full_view' => true,
		'no_results' => elgg_echo('campaign_reward:none'),
		'order_by_metadata' => array('name' => 'amount', 'direction' => 'ASC', 'as' => 'integer')
	);	

	$return = array();
	$return['title'] = elgg_echo('campaign_reward:title', array($fundcampaign->name));
	$return['filter_context'] = 'mine';	
	$return['filter'] = false;
 	$return['content'] = elgg_list_entities_from_metadata($options);
	return $return;

}

/**
 * Get page components to edit/create a campaign_reward post.
 *
 * @param string  $page     'edit' or 'new'
 * @param int     $guid     GUID of campaing_reward
 * @return array
 */
function campaign_reward_get_page_content_edit($page, $guid = NULL) {
	$return = array(
		'filter' => '',
	);

	$vars = array();
	$vars['id'] = 'campaign_reward-post-edit';
	$vars['class'] = 'elgg-form-alt';

	$sidebar = '';
	if ($page == 'edit') {
		$campaign_reward = get_entity((int)$guid);

		$title = elgg_echo('campaign_reward:edit');

		if (elgg_instanceof($campaign_reward, 'object', 'campaign_reward') && $campaign_reward->canEdit()) {
			$vars['entity'] = $campaign_reward;
			$title .= ": " . $campaign_reward->title;
			$body_vars = campaign_reward_prepare_form_vars($campaign_reward, $guid);
			$content = elgg_view_form('campaign_reward/save', $vars, $body_vars);
		} else {
			$content = elgg_echo('campaign_reward:error:cannot_edit_item');
		}
	} else {		
		$body_vars = campaign_reward_prepare_form_vars(null, $guid);
		$title = elgg_echo('campaign_reward:add');
		$content = elgg_view_form('campaign_reward/save', $vars, $body_vars);
	}

	$return['title'] = $title;
	$return['content'] = $content;
	$return['sidebar'] = $sidebar;
	return $return;

}

function campaign_reward_get_page_content_books ($guid) {
	$params = array();

	$params['filter_context'] = 'mine';

	$fundcampaign = get_entity($guid);
	$options = array(
		'type' => 'object',
		'subtype' => 'reward_book',
		'metadataname' => 'fundcampaign_guid',
		'metadatavalue' => $guid,
		'full_view' => false,
		'no_results' => elgg_echo('fundraising:notbookmarks'),
	);

	$title = elgg_echo('campaign_reward:reward_books');
	$content = elgg_list_entities_from_metadata($options);

	$params['title'] = $title;
	$params['content'] = $content;
	$params['filter'] = "";
	return $params;

}

/**
 * Pull together campaign_reward variables for the save form
 *
 * @param ElggObject       $campaign_reward
 * @return array
 */
function campaign_reward_prepare_form_vars($campaign_reward = NULL, $container_guid = NULL) {
	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'stock' => '0',
		'access_id' => ACCESS_PUBLIC,
		'amount' => '0',
		'container_guid' => $container_guid,
		'guid' => NULL,
	);

	if ($campaign_reward) {
		foreach (array_keys($values) as $field) {
			if (isset($campaign_reward->$field)) {
				$values[$field] = $campaign_reward->$field;
			}
		}
	}

	if (elgg_is_sticky_form('campaign_reward')) {
		$sticky_values = elgg_get_sticky_values('campaign_reward');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('campaign_reward');

	if (!$campaign_reward) {
		return $values;
	}
	return $values;

}


/**
* return boolean, wether this $reward_guid->stock will be exceed if we add another relationship. $Left will be written with count of relationship.
* return int, stock - count of relationship;
*
* in $accept_zero_value = 'NO' => restrict $left to 0 or not.
**/
function campaign_reward_is_stocked ($reward_guid, $accept_zero_value = 'NO') {

	$reward = get_entity($reward_guid);

	if ($reward) {
		$adjudicated_rewards =  get_entity_relationships ($reward_guid, "YES");
		$adjudicated = sizeof($adjudicated_rewards);

		$left = (int)$reward->stock - (int)$adjudicated;

		if ($accept_zero_value == 'NO') {
			return array ($left > 0, $left);
		} else {
			return array ($left >= 0, $left);
		}
	} else {
		return false;
	}

}

/**
* Search first relation kind 'reward' of $guid and returns the other $guid
**/
function campaign_reward_get_reward_or_transaction ($guid) {
	$relations = get_entity_relationships ($guid);
	foreach ($relations as $relation) {
		if ($relation->relationship == "reward") {
			if ($guid == $relation->guid_one) {
				return $relation->guid_two;
			}else {
				return $relation->guid_one;
			}
		}
	}
	return 0;

}

/**
* Search first book kind 'reward' of user and entity
**/
function campaign_reward_get_reward_book ($book_search_code) {
	return current(elgg_get_entities_from_metadata (array (
		'type' => 'object',
		'subtype' => 'reward_book',
		'metadataname' => 'search_code',
		'metadatavalue' => $book_search_code,
		'limit' =>1
		)));

}

/**
* gets a output block {label:select} with all campaign_reward of $params['fundcampaign_guid'], and, if transaction is supported, then selects in the dropdown.
*
* $params['fundcampaign_guid']
* $params['transaction_guid']
**/
function campaign_reward_get_selector ($params){
	//Get reward list of this campaign
	$rewards = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'campaign_reward',
		'container_guid' => $params['fundcampaign_guid'],
		'order_by_metadata' => array('name' => 'amount', 'direction' => 'ASC', 'as' => 'integer')
	));

	//Selected values
	$selected_reward_guid = campaign_reward_get_reward_or_transaction ($params['transaction_guid']);

	//Fill dropdown options
	$options = array();
	if ($rewards) {
		//none option
		$options[0] = elgg_echo('campaign_reward:select_none');

		//For each reward...
		foreach ($rewards as $reward){
			//Get reward stock
			list($is_stocked, $left) = campaign_reward_is_stocked ($reward->guid, 'YES');

			//Display if stocked
			if ($is_stocked)	{
				$left_text = " / ". elgg_echo('campaign_reward:left') . " " . $left;
				$amount_text = elgg_echo('campaign_reward:amount€') . " " . $reward->amount;
				$options[$reward->guid] = $reward-> title . " " . $amount_text . $left_text;
			}
		}
	}

	//Display input item
	$select = elgg_view("input/dropdown", array("name" => "reward_guid", "options_values" => $options, "value" => $selected_reward_guid));

	//Display selector
 	$output = "<div><label>" . elgg_echo('campaign_reward:reward') . "</label><br>" . $select . "</div>";

	return  $output;

}

/**
*
* Returns boolean, wether changing reward's stock to $new_stock could conflict with already adjudicated to transactions.
* Return int, missing number of rewards that should be conflict.
*
**/
function campaign_reward_can_change_stock($reward_guid, $new_stock) {
	$reward = get_entity ($reward_guid);

	if ( $reward->stock == $new_stock) {
		return array(true);
	} else {
		list ($is_stockable, $left) = campaign_reward_is_stocked ($reward_guid);
		$missing = $reward->stock - $left;
		return array($new_stock >= $missing, $missing);
	}

}



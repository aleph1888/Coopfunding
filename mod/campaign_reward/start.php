<?php
/**
 * Individual Returns
 *
 * @package campaign_reward
 *
 */

elgg_register_event_handler('init', 'system', 'campaign_reward_init');

/**
 * Init campaign_reward plugin.
 */
function campaign_reward_init() {

	elgg_register_library('elgg:campaign_reward', elgg_get_plugins_path() . 'campaign_reward/lib/campaign_reward.php');

	// routing of urls
	elgg_register_page_handler('campaign_reward', 'campaign_reward_page_handler');

	// Register for search.
	elgg_register_entity_type('object', 'campaign_reward');
	elgg_register_entity_type('object', 'reward_book');

	elgg_register_plugin_hook_handler('fundcampaigns:profilebuttons', 'fundcampaign', 'campaign_reward_set_add_button');
	elgg_register_plugin_hook_handler('fundcampaigns:sidebarmenus', 'fundcampaign', 'campaign_reward_set_side_bar_menu');
	elgg_register_plugin_hook_handler('fundraising:rewards:selector', 'campaign_reward', 'campaign_reward_selector');
	elgg_register_plugin_hook_handler('fundraising:rewards:save', 'campaign_reward', 'campaign_reward_save');
	elgg_register_plugin_hook_handler('fundraising:transaction:do_books', 'do_books', 'campaign_reward_do_books');

	// add a campaign_reward widget
	elgg_register_widget_type('campaign_reward', elgg_echo('campaign_reward'), elgg_echo('campaign_reward:widget:description'));

	// register actions
	$action_path = elgg_get_plugins_path() . 'campaign_reward/actions';
	elgg_register_action('campaign_reward/save', "$action_path/campaign_reward/save.php");
	elgg_register_action('campaign_reward/delete', "$action_path/campaign_reward/delete.php");
	elgg_register_action('reward_book/commit', "$action_path/reward_book/commit.php");
	elgg_register_action('reward_book/delete', "$action_path/reward_book/delete.php");

	// override the default url to view a campaign object
	elgg_register_plugin_hook_handler('entity:url', 'campaign_reward', 'campaign_reward_set_url');

	//Register for donate buttons
	if (elgg_is_active_plugin('fundraising')) {
		elgg_register_library('coopfunding:fundraising', elgg_get_plugins_path() . 'fundraising/lib/fundraising.php');
	}
	if (elgg_is_active_plugin('fundcampaigns')) {
		elgg_register_library('coopfunding:fundcampaigns', elgg_get_plugins_path() . 'fundcampaigns/lib/fundcampaigns.php');
	}

	//Clean books not commited.
	elgg_register_plugin_hook_handler('cron', 'daily', 'fundraising_books_cron');

}

/**
 * Dispatches campaign_reward pages.
 * URLs take the form of
 *  Campaigns's reward:    campaign_reward/owner/<fundcampaing>
 *
 * @param array $page
 * @return bool
 */
function campaign_reward_page_handler($page) {

	elgg_load_library('elgg:campaign_reward');

	if (!isset($page[0]) || !isset($page[1])) {
		forward('', '404');
	}	
	
	$fundcampaign_guid = $page[1];

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			$params = campaign_reward_get_page_content_list($fundcampaign_guid);
			campaign_reward_set_add_button_func($fundcampaign_guid);
			break;
		case 'add':
			gatekeeper();
			$params = campaign_reward_get_page_content_edit($page_type, $fundcampaign_guid);
			break;
		case 'edit':
			gatekeeper();
			$fundcampaign_guid = get_entity($page[1])->getContainerEntity()->guid;
			$params = campaign_reward_get_page_content_edit($page_type, $page[1]);
			break;
		case 'books':
			gatekeeper();
			$params = campaign_reward_get_page_content_books($fundcampaign_guid);
			break;
		default:
			return false;

	}	
	
	$fundcampaign = get_entity($fundcampaign_guid);
	elgg_push_breadcrumb(elgg_echo("projects"), 'projects/all');
	elgg_push_breadcrumb($fundcampaign->getContainerEntity()->name, "project/{$fundcampaign->getContainerEntity()->alias}");
	elgg_push_breadcrumb(elgg_echo("fundcampaigns"), "fundcampaigns/owner/{$fundcampaign->getContainerEntity()->guid}");
	elgg_push_breadcrumb($fundcampaign->name, "fundcampaigns/view/{$fundcampaign->guid}");
	elgg_push_breadcrumb(elgg_echo("campaign_reward:rewards"));
	elgg_set_page_owner_guid($fundcampaign->getContainerEntity()->guid);

	$params['sidebar'] .= elgg_view('campaign_reward/sidebar', array('page' => $page_type));
	
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
	return true;

}

/**
 * Register campaign_reward with ECML.
 */
function campaign_reward_ecml_views_hook($hook, $entity_type, $return_value, $params) {
	$return_value['object/campaign_reward'] = elgg_echo('campaign_reward:campaign_rewards');
	return $return_value;

}

function campaign_reward_set_side_bar_menu ($hook, $entity_type, $return_value, $params) {
	$donatebutton = false;
	if (elgg_is_active_plugin('fundraising')) {
		elgg_load_library('coopfunding:fundraising');

		if (elgg_is_active_plugin('fundcampaigns')) {
			$donatebutton = fundcampaigns_is_active_campaign($params);
			$return_value .= elgg_view('campaign_reward/sidebar/rewards', array('entity' => $params, 'donatebutton' => $donatebutton));
		}
	}

	return $return_value;

}

function campaign_reward_set_add_button ($hook, $entity_type, $return_value, $params) {
	campaign_reward_set_add_button_func($params->guid);

}

function campaign_reward_set_add_button_func ($guid) {
	$fundcampaign = get_entity($guid);
	$project = $fundcampaign->getContainerEntity();

	if ($project && $project->canEdit()) {

		$text = elgg_echo("campaign_reward:addreward");
		$url = elgg_get_site_url() . "campaign_reward/add/{$guid}";

	    	elgg_register_menu_item('title', array(
					'name' => $text,
					'href' => $url,
					'text' => elgg_echo($text),
					'link_class' => 'elgg-button elgg-button-action',
				));
	}

	return false;

}

function campaign_reward_set_url($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'campaign_reward')) {
		return "campaign_reward/owner/{$entity->container_guid}";
	}

}

/**
* gets a output block {label:select} with all campaign_reward of $params['fundcampaign_guid'], and selected $params['default_value']
* 
* $params['fundcampaign_guid']
* $params['default_value']
**/
function campaign_reward_selector ($hook, $type, $returnvalue, $params){
	elgg_load_library('elgg:campaign_reward');
	$select = campaign_reward_get_selector ($params);
	return $select;

}

/**
* creates a relationship between $params['reward_guid'] and $params['transaction_guid'] type = "reward" if reward is stocked. All * previous transaction's relations removed. If there were any book before, removes it.
* 
* $params['transaction_guid']
* $params['book_search_code'] some code stored by pay method in booking to find book entity.
* $params['referer_if_out_stock']    if yes, goes back to referer if stocked. else, inform but pass.
* 
**/
function campaign_reward_save ($hook, $type, $returnvalue, $params){
	elgg_load_library('elgg:campaign_reward');

	$reward_guid = $params['reward_guid'];

	if ($params['book_search_code']) {
		$reward_book = campaign_reward_get_reward_book ($params['book_search_code']);
		if (!$reward_guid) {
			$reward_guid = campaign_reward_get_reward_or_transaction ($reward_book_guid);
		}
	}

	$related_guid = campaign_reward_get_reward_or_transaction ($params['transaction_guid']);

	$is_edited = $related_guid != $reward_guid;
	if ($is_edited) {

		remove_entity_relationships($params['transaction_guid'], "reward");
		if ($reward_book) {
			$reward_book->delete();
		}

		$non_default_reward = $reward_guid > 0;
		if ($non_default_reward){

			list($is_stocked) = campaign_reward_is_stocked ($reward_guid);

			if ($is_stocked) {
				$relation = add_entity_relationship ($params['transaction_guid'], 'reward', $reward_guid);
			} else {
				if ($params['referer_if_out_stock']){
					forward(REFERER);
				}
				register_error(elgg_echo('campaign_reward:reward:nostocked'));
			}
		}
	}

}

/**
* Creates a bookobject on user_guid and fundcampaign_guid and a relation with reward_guid.
*
* $params['reward_guid']
* $params['user_guid']
* $params['fundcampaign_guid']
* $params['amount']
* $params['method']
**/
function campaign_reward_do_books ($hook, $type, $returnvalue, $params){
	elgg_load_library('elgg:campaign_reward');

	$is_bookable = campaign_reward_is_stocked ($params['reward_guid']);

	if ($is_bookable) {
		$book = new ElggObject();
		$book->type = 'object';
		$book->subtype = 'reward_book';
		$book->owner_guid = $params['user_guid'];
		$book->fundcampaign_guid = $params['fundcampaign_guid'];

		$book->amount = $params['amount'];
		$book->method = $params['method'];
		$book->contributor =$params['user_guid'];
		$book->save();
		add_entity_relationship ($book->guid, 'reward', $params['reward_guid']);
		system_message(elgg_echo('campaign_reward:reward_book:done'));

	}else {
		register_error(elgg_echo('campaign_reward:reward_book:error'));
	}

}

function fundraising_books_cron ($hook, $entity_type, $returnvalue, $params) {

	//Bankaccount, 10 days
	$date = new DateTime();
	$today = $date->getTimestamp();
	$upper_timestamp = strtotime('-10 days', $today);

	$init_date = new DateTime("2013-10-01");
	$lower_timestamp = $init_date->getTimestamp();
	$out_books = elgg_get_entities_from_metadata (array (
			'type' => 'object',
			'subtype' => 'reward_book',
			'created_time_lower' => $lower_timestamp,
			'created_time_upper' => $upper_timestamp,
			'metadataname' => 'method',
			'metadatavalue' => 'bankaccount',
		));

	//TODO: must do direct query to avoid bucle?
	foreach ($out_books as $book) {
		remove_entity_relationships($book->guid, "reward");
		$book->delete();
	}

	//Bitcoin, 1 days
	$date = new DateTime();
	$today = $date->getTimestamp();
	$upper_timestamp = strtotime('-1 days', $today);

	$init_date = new DateTime("2013-10-01");
	$lower_timestamp = $init_date->getTimestamp();
	$out_books = elgg_get_entities_from_metadata (array (
			'type' => 'object',
			'subtype' => 'reward_book',
			'created_time_lower' => $lower_timestamp,
			'created_time_upper' => $upper_timestamp,
			'metadataname' => 'method',
			'metadatavalue' => 'bitcoin',
		));
	//TODO: must do direct query to avoid bucle?
	foreach ($out_books as $book) {
		remove_entity_relationships($book->guid, "reward");
		$book->delete();
	}

}

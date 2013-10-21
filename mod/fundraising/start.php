<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising
 */

elgg_register_event_handler('init', 'system', 'fundraising_init');

function fundraising_init() {

	global $CONFIG;
	if (!$CONFIG->fundraising_methods) {
		$CONFIG->fundraising_methods = array();
	}
	elgg_register_entity_type ('transaction');

	elgg_register_library('coopfunding:fundraising', elgg_get_plugins_path() . 'fundraising/lib/fundraising.php');
	elgg_register_page_handler('fundraising', 'fundraising_page_handler');

    elgg_register_plugin_hook_handler('fundcampaigns:sidebarmenus', 'fundcampaign', 'fundraising_set_side_bar_menu');
	elgg_register_action('fundraising/contribute', dirname(__FILE__) . '/actions/contribute.php');
}

function fundraising_page_handler($page, $handler) {

	elgg_load_library('coopfunding:fundraising');
	switch ($page[0]) {
		case 'contribute':
			set_input('guid', $page[1]);
			include(elgg_get_plugins_path() . 'fundraising/pages/contribute.php');
			break;
		case 'contributors':
            set_input('guid', $page[1]);
            include(elgg_get_plugins_path() . 'fundraising/pages/contributors.php');
            break;
        case 'view':
            fundraising_view_transactions($page[1]);
            break;
		default:
			if (function_exists("fundraising_{$page[0]}_page_handler")) {
			    $return = call_user_func("fundraising_{$page[0]}_page_handler", $page, $handler);
				break;
			}

	}
}

function fundraising_register_method($method) {
	global $CONFIG;
	if (!isset($CONFIG->fundraising_methods)) {
		$CONFIG->fundraising_methods = array();
	}
	if (!in_array($method, $CONFIG->fundraising_methods)) {
		$CONFIG->fundraising_methods[] = $method;
	}
}

function fundraising_register_currency($currency) {
	global $CONFIG;
	if (!isset($CONFIG->fundraising_currencies)) {
		$CONFIG->fundraising_currencies = array();
	}
	if (!in_array($currency, $CONFIG->fundraising_currencies)) {
		$CONFIG->fundraising_currencies[] = $currency;
	}
}

function fundraising_set_side_bar_menu ($hook, $entity_type, $return_value, $params) {

    	if (elgg_instanceof($params, 'object', 'fundcampaign')) {
	    	$entity = get_entity($params->container_guid);
	} else {
	    	$entity = $params;
	}	
	if ($entity) {
		if ($entity->isMember() || elgg_is_admin_logged_in()) {
			$return_value .= elgg_view('fundcampaigns/sidebar/manage', array('entity' => $params));
		}
	}
	return $return_value;

}

function fundraising_view_transactions ($guid) {

    $params = array();

    $params['filter_context'] = 'mine';

	$options = array(
		'type' => 'object',
		'subtype' => 'transaction',
		'container_guid' => $guid,
		'full_view' => false,
		'no_results' => elgg_echo('fundraising:notransactions'),
	);
   
	if ($guid) {
		$options['container_guid'] = $guid;
		$container = get_entity($guid);
		
		$params['title'] = elgg_echo('fundraising:contributions', array($container->alias));

    	$params['filter'] = false;
        $content = elgg_list_entities_from_metadata($options);
       
		elgg_push_breadcrumb(elgg_echo("{$container->alias}"), $container->getURL());
		
	    $params['title'] = $title;
	    $params['content'] = $content;

	    $body = elgg_view_layout('content', $params);

	    echo elgg_view_page($params['title'], $body);
        return true;

	} else {return false;}
	
}

<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.Bitcoin
 */

elgg_register_event_handler('init', 'system', 'fundraising_bitcoin_init');

function fundraising_bitcoin_init() {
	elgg_register_library('coopfunding:fundraising:bitcoin', elgg_get_plugins_path() . 'fundraising-bitcoin/lib/fundraising-bitcoin.php');
	elgg_register_library('coopfunding:fundraising:bitcoin:addrgen', elgg_get_plugins_path() . 'fundraising-bitcoin/lib/fundraising-bitcoin-addrgen.php');
	elgg_register_action('fundraising/bitcoin-callback', dirname(__FILE__) . '/actions/bitcoin-callback.php', "public");
	elgg_register_action('fundraising/bitcoin-address', dirname(__FILE__) . '/actions/bitcoin-address.php', "public");	
	elgg_register_plugin_hook_handler('fundraising', 'sum_amount', 'fundraising_bitcoin_sum_amount');

	elgg_load_library('coopfunding:fundraising');
	fundraising_register_method('bitcoin');
	fundraising_register_currency('btc');

	if (!elgg_get_config('bitcoin_book_days')) {
		elgg_set_config('bitcoin_book_days', 1);
	}

	elgg_register_plugin_hook_handler('cron', 'hourly', 'fundraising_bitcoin_cron');
}

function fundraising_bitcoin_page_handler($page) {

	if (isset($page[1]) && $page[1] == 'contribute') {
		if (isset($page[2])){
			$entity = get_entity($page[2]);    	    
			if($entity) {	    	      		    					    
	    			elgg_set_page_owner_guid($entity->guid);			
		
				if (elgg_is_active_plugin("campaign_reward") && get_input('reward_guid') && $entity->getSubtype = 'fundcampaign' ) {	
					$params = array(
						'user_guid' => elgg_get_logged_in_user_guid(),
						'fundcampaign_guid' => $entity->guid, 
						'reward_guid' => get_input('reward_guid'),
						'amount' => get_input('amount'),
						'method' => 'bitcoin',
						'book_search_code' => $address
					);			
					$books_text = elgg_trigger_plugin_hook('fundraising:transaction:do_books', 'do_books', $params);	
				}    	    			
				forward($entity->getURL());
	    			return true;
			}
		}
	} elseif (isset($page[1]) && $page[1] == 'bitcoin-callback') {
		include(elgg_get_plugins_path() . 'fundraising-bitcoin/actions/bitcoin-callback.php');
		return true;
	}elseif (isset($page[1]) && $page[1] == 'bitcoin-address') {		
		include(elgg_get_plugins_path() . 'fundraising-bitcoin/actions/bitcoin-address.php');
		return true;
	}

}

function fundraising_bitcoin_sum_amount($hook, $type, $returnvalue, $params) {
	$market_value = elgg_get_plugin_setting('market_value', 'fundraising-bitcoin');

	if (!$market_value) {
		return $returnvalue;
	}
        return (float) $returnvalue + (float) $params['entity']->btc_amount * (float) $market_value;
}

function fundraising_bitcoin_cron($hook, $entity_type, $returnvalue, $params) {
	$data = file_get_contents('https://blockchain.info/ticker');
	if ($data && ($json = json_decode($data, true))) {
		elgg_set_plugin_setting('market_value', $json["EUR"]["last"], 'fundraising-bitcoin');
	}
}

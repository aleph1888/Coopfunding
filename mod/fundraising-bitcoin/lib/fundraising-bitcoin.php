<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.Bitcoin
 */

function fundraising_contribute_bitcoin($guid, $amount, $reward_guid = null) {
	forward(elgg_get_site_url() . "fundraising/bitcoin/contribute/$guid&amount=$amount&reward_guid=$reward_guid");
}

function fundraising_bitcoin_get_address($entity_guid, $user_guid = null) {

	if (!$user_guid) {
		$user_guid = elgg_get_logged_in_user_guid();
		if (!$user_guid) {
			elgg_load_library("coopfunding:fundraising");
			$user = fundraising_get_anonymous_usr();			
			$user_guid = $user->guid;
		}
	}

	if (!$user_guid || !$entity_guid) {
		throw new Exception("No user or entity");
	}

	$dbprefix = elgg_get_config('dbprefix');
	$address = current(get_data("SELECT * FROM {$dbprefix}fundraising_bitcoin
		WHERE `user_guid`=$user_guid AND `entity_guid`=$entity_guid;
	"));

	if ($address) {
		return $address->address;
	}

	$mpk = elgg_get_config('mpk');

	if (!$mpk) {
		throw new Exception("No mpk");
	}

	elgg_load_library("coopfunding:fundraising:bitcoin:addrgen");

	$index = insert_data("INSERT INTO {$dbprefix}fundraising_bitcoin VALUES (
		0,
		'',
		$entity_guid,
		$user_guid
	);");

	if (!$index) {
		return false;
	}

	$address = addr_from_mpk($mpk, $index);

	update_data("UPDATE {$dbprefix}fundraising_bitcoin
		SET `address`='$address'
		WHERE `id`=$index;
	");

	return $address;
}

function fundraising_bitcoin_get_project_and_user_from_address($address) {
	$dbprefix = elgg_get_config('dbprefix');
	$data = current(get_data("SELECT `entity_guid`, `user_guid`
		FROM {$dbprefix}fundraising_bitcoin
		WHERE `address`='$address'
		LIMIT 1
	;"));

	return array($data->entity_guid, $data->user_guid);
}

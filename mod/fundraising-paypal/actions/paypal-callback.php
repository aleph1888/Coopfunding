<?php

$token = get_input('token');

if ($token && isset($_SESSION['paypal_contrib'][$token])) { // Token parameter exists

	$guid = $_SESSION['paypal_contrib'][$token]['project_guid'];
	$amount = $_SESSION['paypal_contrib'][$token]['amount'];
	$reward_guid = $_SESSION['paypal_contrib'][$token]['reward_guid'];	

	$entity = get_entity($guid);

	// Get checkout details, including buyer information.
	// We can save it for future reference or cross-check with the data we have
	$paypal = new Paypal();
	$checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $token));

	// Complete the checkout transaction
	$requestParams = array(
		'TOKEN' => $token,
		'PAYMENTACTION' => 'Sale',
		'PAYERID' => get_input('PayerID'),
		'PAYMENTREQUEST_0_AMT' => $amount,
		'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR' // Same currency as the original request
	);

	$response = $paypal->request('DoExpressCheckoutPayment', $requestParams);
	if (is_array($response) && $response['ACK'] == 'Success') { // Payment successful
		// We'll fetch the transaction ID for internal bookkeeping
		$transactionId = $response['PAYMENTINFO_0_TRANSACTIONID'];
	}


	elgg_load_library('coopfunding:fundraising');
	$contributions_set = fundraising_get_contributions_set($guid);

	$user = elgg_get_logged_in_user_guid();
	if (!$user) {
		elgg_load_library("coopfunding:fundraising");
		$user = fundraising_get_anonymous_usr();
	}

	if (!$contributions_set) {
		$contributions_set = new ElggObject();
		$contributions_set->subtype = "contributions_set";
		$contributions_set->owner_guid = $user->guid;
		$contributions_set->container_guid = $guid;
	}

	if ($contributions_set_guid = $contributions_set->save()) {
		$transaction = new ElggObject();
		$transaction->subtype = "transaction";
		$transaction->owner_guid = $user->guid;
		$transaction->container_guid = $guid;
		$transaction->eur_amount = $amount;
		$transaction->method = 'paypal';
		$transaction->paypal_transaction_id = $transactionId;
		$transaction->contributor = $user->guid;
		$transaction->save();
	}

	$contributions_set->eur_amount += $amount;
	$entity->eur_amount += $amount;

	system_message(elgg_echo('fundraising:contribute:success'));

	if (elgg_is_active_plugin("campaign_reward")) {	
		$params = array('transaction_guid' => $transaction->guid, 'reward_guid' => $reward_guid);		
		elgg_trigger_plugin_hook('fundraising:rewards:save', 'campaign_reward', $params);	
	}

	forward($entity->getURL());
}

forward();

<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.Paypal
 */

function fundraising_contribute_paypal($project_guid, $amount) {

	$project = get_entity($project_guid);
	$amount = $amount;

	$return_url = elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/fundraising/paypal-callback');

	if (!$project || !$amount) {
		forward(REFERER);
	}

	$paypal = new Paypal();

	$response = $paypal->request('SetExpressCheckout', array(
		'RETURNURL' => $return_url,
		'CANCELURL' => $return_url,

		'PAYMENTREQUEST_0_AMT' => $amount,
		'PAYMENTREQUEST_0_ITEMAMT' => $amount,

		'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',

		'PAYMENTREQUEST_0_DESC' => elgg_echo('fundraising:contribute:desc', array($project->name)),

		'L_PAYMENTREQUEST_0_NAME0' => elgg_echo ('fundraising:contribute:desc', array($project->name)),
		'L_PAYMENTREQUEST_0_AMT0' => $amount,
		'L_PAYMENTREQUEST_0_QTY0' => '1',

		//'EMAIL' => elgg_logged_user -> email , email del contributor

		//'HDRIMG' =>  'projects/' . $project ->guid . 'medium' . 'jpg'  imagen para pner en la pga de paypal
		//'LOGOIMG' => Site -> imgLogo;
		//https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/SetExpressCheckout_API_Operation_NVP/

	));

	if (is_array($response) && $response['ACK'] == 'Success') { //Request successful
		$token = $response['TOKEN'];

		if (!isset($_SESSION['paypal_contrib']) || !is_array($_SESSION['paypal_contrib'])) {
			$_SESSION['paypal_contrib'] = array();
		}

		$_SESSION['paypal_contrib'][$token] = array(
			'project_guid' => $project_guid,
			'amount' => $amount,
		);

		forward('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token));
	}

	forward(REFERER);
}

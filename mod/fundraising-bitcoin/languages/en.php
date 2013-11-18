<?php
/**
* Fundraising-bitcoin language file
*/

$language = array(

	/**
	* Content
	*/

	'fundraising:bitcoin:title' => "Contribute with bitcoins to %s",
	'fundraising:bitcoin:contributeToaddress' => "Send bitcoins to this address: ",
	'fundraising:bitcoin:contributeToQRcode' => "Scan this QRcode:",
	'fundraising:bitcoin:contributeNoAddress' => "This entity is not configured to recieve bitcoins.",
	'fundraising:bitcoin:or' => 'or',
	'fundraising:contributions:btc' => '%.4f BTC',
	
	'fundraising:bitcoin:message_anonymous_contribute' => 'Thanks for contribute. Login to get rewards for contribution.',
	'fundraising:bitcoin:message_contribute' => 'Thanks for contribute. Choose a reward before contributing if you want any.',
	'fundraising:bitcoin:message_contribute_rewards' => 'By clicking the button below, you can book the reward during %s day(s) while you do the transfer.',

);
add_translation(basename(__FILE__, '.php'), $language);

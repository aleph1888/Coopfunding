<?php

$user = elgg_get_logged_in_user_entity();
if (!$user) {
	$is_anonymous = true;
	elgg_load_library("coopfunding:fundraising");
	$user = fundraising_get_anonymous_usr();
}

$address = $vars["address"]; 

echo elgg_view('input/hidden', array(
			'name' => "entity_guid",
			'id' => "entity_guid",
	                'value' => $vars['entity']->guid
			));

echo elgg_view('input/hidden', array(
			'name' => "callback_url",
			'id' => "callback_url",
	                'value' => elgg_get_site_url() . 'fundraising/bitcoin/bitcoin-address'
			));

//show btc address and qr image
echo "<div class='fundraising-bitcoin-contribute-form fundraising-hidden'>";
echo "<hr>";
	echo '<div>';
	echo elgg_echo("fundraising:bitcoin:contributeToaddress");
	echo "<label id='bitcoin_address'></label>";
	echo '<br>' . elgg_echo("fundraising:bitcoin:or") . '<br>';
	echo elgg_echo("fundraising:bitcoin:contributeToQRcode");
	echo '<br>';
	echo '<img id="bitcoin_qrcode">';
	echo '</div>';

	if ($is_anonymous) {
		echo '<div>';
			echo elgg_echo("fundraising:bitcoin:message_anonymous_contribute");
		echo '</div>';
		
	} elseif (!$vars['reward_guid']){
		echo '<div>';
			echo elgg_echo("fundraising:bitcoin:message_contribute");
		echo '</div>';	
	} else {
		echo '<div>';
		echo elgg_echo("fundraising:bitcoin:message_contribute_rewards", array(elgg_get_config('bitcoin_book_days')));
		echo '</div>';

		echo elgg_view('input/submit', array(
			'name' => 'method',
			'value' => elgg_echo('fundraising:contribute:button:book', array('bitcoin')), #change this literal will disconfigure in fundraising/actions/contribute.php
		));	
	}
	echo "</div>";




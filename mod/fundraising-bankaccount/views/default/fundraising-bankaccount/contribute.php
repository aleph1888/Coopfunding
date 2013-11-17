<?php


elgg_load_library('coopfunding:fundraising:bankaccount');

$entity = $vars['entity'];
$user = elgg_get_logged_in_user_entity();
if (!$user) {
	$is_anonymous = true;
	elgg_load_library("coopfunding:fundraising");
	$user = fundraising_get_anonymous_usr();
}

$code = fundraising_bankaccount_get_transaction_code($entity->guid, $user->guid);
$ban = elgg_get_config('ban');
$amount = $vars['amount'];

if (!$ban) 
{
   system_message(elgg_echo('fundraising:bankaccount:contributeNoBAN', array($vars['entity']->name)));
}

echo "<div class='fundraising-bankaccount-contribute-form fundraising-hidden'>";
echo "<hr>";
	echo '<div>';
	echo elgg_echo("fundraising:bankaccount:contributeToBAN", array($amount, $ban, $code));
	echo '</div>';

	if ($is_anonymous) {	
		echo '<div>';
		echo elgg_echo("fundraising:bankaccount:message_anonymous_donation");
		echo '</div>';
		
	} else {
		echo '<div>';
		echo elgg_echo("fundraising:bankaccount:message");
		echo '</div>';
		echo elgg_view('input/submit', array(
			'name' => 'method',
		        'value' => elgg_echo('fundraising:contribute:button:method', array('bankaccount')),
		));
	}

	
echo "</div>";







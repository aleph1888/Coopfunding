<?php
$user = elgg_get_logged_in_user_entity();
if (!$user) {
	$is_anonymous = true;
	elgg_load_library("coopfunding:fundraising");
	$user = fundraising_get_anonymous_usr();
}
elgg_load_library("coopfunding:fundraising:bankaccount");
$code = fundraising_bankaccount_get_transaction_code($vars['entity']->guid, $user->guid);
$ban = elgg_get_config('ban');

echo "<br>";
echo "<div class='fundraising-bankaccount-contribute-form fundraising-hidden'>";
echo "<hr>";
	echo '<div>';
		echo elgg_echo("fundraising:bankaccount:contributeToBAN", array($ban, $code));
	echo '</div>';

	if ($is_anonymous) {
		echo '<div>';
			echo elgg_echo("fundraising:bankaccount:message_anonymous_donation");
		echo '</div>';
		
	} elseif (!$vars['reward_guid']){
		echo '<div>';
			echo elgg_echo("fundraising:bankaccount:message_donation");
		echo '</div>';	
	} else {
		echo '<div>';
			echo elgg_echo("fundraising:bankaccount:message_donation_rewards", array(elgg_get_config('bankaccount_book_days')));
		echo '</div>';
		echo elgg_view('input/submit', array(
			'name' => 'method',
			'value' => elgg_echo('fundraising:contribute:button:book', array('bankaccount')), #change this literal will disconfigure in fundraising/actions/contribute.php
		));
	}	
echo "</div>";
echo "<br>";

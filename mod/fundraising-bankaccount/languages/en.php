<?php
/**
* Fundraising-bankaccount language file
*/

$language = array(

	/**
	* Content
	*/

	'fundraising:bankaccount:title' => "Contribute with Bank Account deposit to %s",
	'fundraising:bankaccount:contributeToBAN' => "Make deposit to this BAN: %s indicating this reference number: %s",
	'fundraising:bankaccount:contributeNoBAN' => "This entity is not configured to recieve Bank Account Transfers.",
	'fundraising:contributions:bankaccountEUReur' => '%.4f €',
	'fundraising:bankaccount' => 'Bank account transfers',
	'fundraising:bankaccount:newdeposit' => 'New deposit',
	'fundraising:bankaccount:editdeposit' => 'Edit deposit',
	'fundraising:bankaccount:manage' => 'Manage transfers',
	'fundraising:bankaccount:notransactions' => 'There is no transactions.',
	'fundraising:bankaccount:message:error:delete_item' => 'Deleted item',
	'fundraising:bankaccount:message:error:cannot_delete_item' => 'Cannot delete item',
	'fundraising:contributor' => 'Contributor',
	
	'fundraising:bankaccount:message_anonymous_donation' => 'Thanks for contribute. Log in to get a reward.',
	'fundraising:bankaccount:message_donation' => 'Thanks for contribute. Choose a reward before contributing if you want any.',
	'fundraising:bankaccount:message_donation_rewards' => 'By clicking the button below, you can book the reward during %s day(s) while you do the transfer.',

	'fundraising:date' => 'Date',

	'fundraising:bankaccount:verified' => 'Transaction is verified?',

);

add_translation(basename(__FILE__, '.php'), $language);




<?php
/**
* Fundraising language file
*/

$language = array(

	/**
	* Menu items and titles
	*/

	'fundraising' => "Contributions",
	'fundraising:contribute' => "Contribute",
	'fundraising:contribute:desc' => "Voluntary contribution to %s",

	'fundraising:contribute:project' => "Contribute to %s",
	'fundraising:contribute:fundcampaign' => "Contribute to %s",
	'fundraising:contribute:method' => "Contribute using %s",
	'fundraising:contribute:with' => "Contribute with %s €",
	'fundraising:contribute:button' => "Contribute",
	'fundraising:contribute:button:method' => "Contribute using %s",
	'fundraising:contribute:button:book' => "Book reward while you pay using %s",
	'fundraising:amount' => "Amount",
	'fundraising:contributors:count' => "%s contributors",
	'fundraising:contributors:project' => "%s's contributors",
	
	'fundraising:contributions:of' => ' of ',
	'fundraising:contributions:amount' => "%.2f € raised",
	
	'fundraising:contributions:eur' => "%.2f €",
	'fundraising:error:invalidmethod' => "It is not possible contribute using this method right now.",
	'fundraising:contribute:nomethod' => "There aren't any method to contribute right now.",
	'fundraising:contributors' => "Contributors",
	'fundraising:contribute:success' => "Thanks for contribute to this project",
	'fundraising:contributions' => "%s's contributions",
	'fundraising:allcontributions' => "View all contributions",
	'fundraising:message' => "Here you choose a payment method. If it is bitcoin or bankaccount you will get credentials for the contribution and you will be able to book the reward (if any) for some days while make effective payment. By choosing Paypal you will be redirected to paypal payment website.",
);
add_translation(basename(__FILE__, '.php'), $language);

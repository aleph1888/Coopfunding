<?php
/**
 * Elgg Fundcampaigns plugin language pack
 *
 * @package Coopfunding
 * @subpackage fundcampaign
 */

$language = array(

	/**
	 * Menu items and titles
	 */
	'fundcampaigns' => "Campaigns",
	'fundcampaigns:campaigns' => "Campaigns",
	'fundcampaigns:fundcampaign' => "Campaigns",
	'fundcampaigns:add' => "New campaign",
	'fundcampaigns:edit' => 'Edit campaign',
	'fundcampaigns:delete' => "Delete campaign",
	'fundcampaigns:deletewarning' => "Are you sure you want to delete this campaign?",
	'fundcampaigns:notitle'=> "No title for campaign",

	'fundcampaigns:start_date' => 'Start date',
	'fundcampaigns:end_date' => 'End date',
    	'fundcampaigns:total_amount' => 'Optimal amount (€)',
    	'fundcampaigns:period_one_duration' => 'Days of first period (if any).',
    	'fundcampaigns:period_one_amount' => 'Minimum amount (€) in first period',

    	'fundcampaigns:is_active' => 'Is this the only one active campaign of this project?',
    	'fundcampaigns:active' => 'Active',
    	'fundcampaigns:inactive' => 'Inactive',

	'fundcampaign:deleted' => 'Campaign deleted',
	'fundcampaign:notdeleted' => 'Campaign not deleted',
	'fundcampaigns:icon' => 'Campaign icon (leave blank to leave unchanged)',
	'fundcampaigns:name' => 'Campaign name',
	'fundcampaigns:alias' => 'Campaign short name (displayed in URLs, alphanumeric characters only)',
	'fundcampaigns:description' => 'Description',
	'fundcampaigns:briefdescription' => 'Brief description',
	'fundcampaigns:interests' => 'Tags',
	'fundcampaigns:paymethodBAN' => 'Bank Account Number',
	'fundcampaigns:paymethodCES' => 'Integral CES code',

	'fundcampaigns:when_closed' => 'Liquidation date',
	'fundcampaigns:who_closed' => 'Who did the liquidation',
	'fundcampaigns:info_closed' => 'Info about liquidation process',

	'fundcampaigns:members' => 'Campaigns members',
	'fundcampaings:cantedit' => 'You can not edit this campaign',
	'fundcampaigns:saved' => 'Campaign saved',
	'fundcampaigns:search:tags' => "tag",
	'fundcampaigns:search_in_fundcampaign' => "Search in this campaign",

	'fundcampaigns:notfound' => "Campaigns not found",
	'fundcampaigns:member' => "members",
	'fundcampaigns:searchtag' => "Search for campaigns by tag",

	'fundcampaigns:none' => 'No campaigns',

	'fundcampaigns:access:private' => 'Closed - Users must be invited',
	'fundcampaigns:access:public' => 'Open - Any user may join',
	'fundcampaigns:access:fundcampaign' => 'Campaign members only',
	'fundcampaigns:closed:project' => 'This campaign has a closed membership.',
	'fundcampaigns:visibility' => 'Who can see this project?',
	
	'fundraising:contributors:fundcampaigns' => "%s's contributors",
	

);

add_translation(basename(__FILE__, '.php'), $language);

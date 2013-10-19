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
    'fundcampaigns:activate_second_period' => 'Two periods?',
    'fundcampaigns:total_amount' => 'Optimal amount (€)',
    'fundcampaigns:periods_duration' => 'Days of each period',
    'fundcampaigns:minimum_amount' => 'Minimum amount (€)',
    
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

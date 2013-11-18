<?php
/**
 * campaign_collective English language file.
 *
 */

 $language = array(
	
	'campaign_collective:rewards' => 'Collective rewards',
	'campaign_collective:items' => 'Collective rewards',
	'campaign_collective:view all' => 'View all collective rewards',
	'campaign_collective:addreward' => 'Add new collective reward',
	'campaign_collective' => 'Collective Rewards',
	'campaign_collective:title' => "%s 's Collective rewards",
	'campaign_collective:body' => 'Body',
	'campaign_collective:edit' => 'Edit reward',
	'campaign_collective:add' => 'Add reward',
	'campaign_collective:saved' => 'Reward saved',
	'campaign_collective:deleted' => 'Reward deleted',

	'campaign_collective:error:item_not_found' => 'Reward not found',
	'campaign_collective:error:cannot_write_to_container' => 'Can not write the reward in the project',
	'campaign_collective:error:cannot_delete_item' => 'Reward not deleted. Please, try again.',
	'campaign_collective:error:cannot_save' => 'Reward not saved. Please, try again.',

	// messages
	'campaign_collective:error:cannot_save' => 'Cannot save campaign reward.',

	'campaign_collective:none' => 'No rewards',
	'campaign_collective:error:missing:title' => 'Please enter a title!',
	'campaign_collective:error:missing:description' => 'Please enter a description!',
	'campaign_collective:error:cannot_edit_campaign_collective' => 'This reward may not exist or you may not have permissions to edit it.',
	'campaign_collective:error:campaign_collective_not_found' => 'Cannot find specified reward.',

);

add_translation(basename(__FILE__, '.php'), $language);

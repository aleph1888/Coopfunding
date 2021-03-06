<?php
/**
 * campaign_reward English language file.
 *
 */

 $language = array(
	'campaign_reward:items' => 'Individual reward',
	'campaign_reward:view all' => 'View all rewards',
	'campaign_reward:addreward' => 'Add new reward',
	'campaign_reward' => 'Rewards',
	'campaign_reward:amount' => 'Amount',
	'campaign_reward:stock' => 'Stock',

	'campaign_reward:body' => 'Body',

	'campaign_reward:edit' => 'Edit reward',
	'campaign_reward:add' => 'Add reward',

	'campaign_reward:error:item_not_found' => 'Reward not found',
	'campaign_reward:error:cannot_write_to_container' => 'Cannto write the reward in the project',
	'campaign_reward:error:cannot_delete_item' => 'Reward not deleted. Please, try again.',
	'campaign_reward:error:cannot_save' => 'Reward not saved. Please, try again.',

	// messages
	'campaign_reward:error:cannot_save' => 'Cannot save campaign reward.',

	'campaign_reward:none' => 'No rewards',
	'campaign_reward:error:missing:title' => 'Please enter a title!',
	'campaign_reward:error:missing:description' => 'Please enter a description!',
	'campaign_reward:error:cannot_edit_campaign_reward' => 'This reward may not exist or you may not have permissions to edit it.',
	'campaign_reward:error:campaign_reward_not_found' => 'Cannot find specified reward.',

);

add_translation(basename(__FILE__, '.php'), $language);

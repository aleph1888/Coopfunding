<?php
/**
 * Edit campaign_reward form
 *
 * @package campaign_reward
 */

$campaign_reward = get_entity($vars['guid']);
$vars['entity'] = $campaign_reward;

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/campaign_reward/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete float-alt'
	));
}


$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button . $delete_link;

$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'id' => 'campaign_reward_title',
	'value' => $vars['title']
));

$body_label = elgg_echo('campaign_reward:body');
$body_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'id' => 'campaign_reward_description',
	'value' => $vars['description']
));

$amount_label = elgg_echo('campaign_reward:amount');
$amount_input = elgg_view('input/text', array(
	'name' => 'amount',
	'id' => 'campaign_reward_amount',
	'value' => _elgg_html_decode($vars['amount'])
));

$stock_label = elgg_echo('campaign_reward:stock');
$stock_input = elgg_view('input/text', array(
	'name' => 'stock',
	'id' => 'campaign_reward_stock',
	'value' => _elgg_html_decode($vars['stock'])
));

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'campaign_reward_access_id',
	'value' => $vars['access_id']
));

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));


echo <<<___HTML

<div>
	<label for="campaign_reward_title">$title_label</label>
	$title_input
</div>

<div>
	<label for="campaign_reward_excerpt">$excerpt_label</label>
	$excerpt_input
</div>

<div>
	<label for="campaign_reward_amount">$amount_label</label>
	$amount_input
</div>

<div>
	<label for="campaign_reward_stock">$stock_label</label>
	$stock_input
</div>

<div>
	<label for="campaign_reward_description">$body_label</label>
	$body_input
</div>

<div>
	<label for="campaign_reward_access_id">$access_label</label>
	$access_input
</div>

<div class="elgg-foot">
	$guid_input
	$container_guid_input

	$action_buttons
</div>

___HTML;

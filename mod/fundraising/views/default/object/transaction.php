<?php
/**
 * View transaction objects
 *
 * @package transaction
 */

$transaction = elgg_extract('entity', $vars, FALSE);

if (!$transaction) {
	return TRUE;
}

$owner = get_entity($transaction->contributor);

if ($transaction->method =="bankaccount") {
	$metadata = elgg_view_menu('entity', array(
		'entity' => $transaction,
		'handler' => 'fundraising/bankaccount',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	));
}

elgg_load_library('coopfunding:fundraising');
$container = $transaction->container_guid;

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "profile/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time ($transaction->commit_date);

if (elgg_is_active_plugin("campaign_reward")) {	
	elgg_load_library('elgg:campaign_reward');
	$reward_guid = campaign_reward_get_reward_or_transaction($transaction->guid);

	$reward = get_entity($reward_guid);

	if ($reward) {
		$reward_title = $reward->title;	
	} else {
		$reward_title = elgg_echo("campaign_reward:noreward");
	}
}

$subtitle = "$author_text $date / $transaction->method / $reward_title";
$content = "$transaction->eur_amount" . "€";

$params = array(
		'entity' => $transaction,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $content,
);

$params = $params + $vars;

$list_body = elgg_view('object/summary', $params);
echo elgg_view_image_block($owner_icon, $list_body);


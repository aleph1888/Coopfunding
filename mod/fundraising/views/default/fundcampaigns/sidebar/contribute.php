<?php
/**
 * Contribute sidebar
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 * @uses $vars['entity'] Campaign entity
 */

elgg_load_library('coopfunding:fundraising');

$guid = $vars['entity']->guid;
$entity = $vars['entity'];

$contributors_count = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'contributions_set',
	'container_guid' => $guid,
	'count' => true,
));

$contributions_amount = fundraising_sum_amount($entity);

if (!$contributors_count) {
	$contributors_count = "0";
}

$body = elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:contribute:button'),
	'href' => "fundraising/contribute/{$entity->guid}",
	'class' => 'elgg-button elgg-button-action',
));


$body .= "<br>" . elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:contributors:count', array($contributors_count)),
	'href' => "fundraising/contributors/{$guid}",
));

$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
$amount .= elgg_echo('fundraising:contributions:of');
$amount .= elgg_echo('fundraising:contributions:eur', array($entity->total_amount));
if ($entity->total_amount) {
	$amount .= "\n" . round($contributions_amount / $entity->total_amount * 100, 2) . '%';
}

$body .= "<br><br>" . elgg_view('output/text', array(
	'value' => $amount,
));


$body .= "<br><br>" . elgg_view('output/text', array('value' => "From: " . $entity->start_date));
$period_one_date = date('Y-m-d', strtotime($entity->start_date . " + {$entity->period_one_duration} days"));
if ($entity->period_one_duration) {
	$time = "Period 1: " . $period_one_date;
	$body .= "<br>" . elgg_view('output/text', array('value' => $time));	
	if (time() < strtotime($period_one_date)) {
		$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
		$amount .= elgg_echo('fundraising:contributions:of');
		$amount .= elgg_echo('fundraising:contributions:eur', array($entity->period_one_amount));
		$amount .= "\n" . round($contributions_amount / $entity->period_one_amount * 100, 2) . '%';
		$body .= "<br>" . elgg_view('output/text', array('value' => $amount));	
	}
}
$body .= "<br>" . elgg_view('output/text', array('value' => "To: " . $entity->end_date));


echo elgg_view_module('aside', elgg_echo('fundraising:contribute'), $body);

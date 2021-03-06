<?php
/**
 * Contribute sidebar
 *
 * @package Coopfunding
 * @subpackage Fundraising
 *
 * @uses $vars['entity'] Project entity
 */

elgg_load_library('coopfunding:fundraising');

$project_alias = $vars['entity']->alias;

$contributors_count = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'contributions_set',
	'container_guid' => $vars['entity']->guid,
	'count' => true,
));

$contributions_amount = fundraising_sum_amount($vars['entity']);

$body = elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:contribute:button'),
	'href' => "fundraising/contribute/{$vars['entity']->guid}",
	'class' => 'elgg-button elgg-button-action',
));


if (!$contributors_count) {
	$contributors_count = "0";
}

$body .= "<br>" . elgg_view('output/url', array(
	'text' => elgg_echo('fundraising:contributors:count', array($contributors_count)),
	'href' => "fundraising/contributors/{$vars['entity']->guid}",
));

$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
$amount .= elgg_echo('fundraising:contributions:of');
$amount .= elgg_echo('fundraising:contributions:eur', array($vars['entity']->total_amount));
if ($vars['entity']->total_amount > 0) {
	$amount .= "\n" . elgg_echo($contributions_amount / $vars['entity']->total_amount * 100) . '%';
}

$body .= "<br>" . elgg_view('output/text', array(
	'value' => $amount,
));
	
echo elgg_view_module('aside', elgg_echo('fundraising:contribute'), $body);	

<?php

$project = $vars['entity'];

if (!$project) {
	return true;
}

elgg_load_library('elgg:fundcampaigns');

$fundcampaign = fundcampaigns_get_active_campaign ($project->guid);

if (!$fundcampaign) {
	$content = '<p>' . elgg_echo('contribution:none') . '</p>';
} else {	

	$contributors_count = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'contributions_set',
		'container_guid' => $fundcampaign->guid,
		'count' => true,
	));

	$all_link = elgg_view('output/url', array(
		'href' => "fundraising/contributors/{$fundcampaign->guid}",
		'text' =>  elgg_echo('fundraising:contributors:count', array($contributors_count)),
		'is_trusted' => true,
	));

	elgg_load_library('coopfunding:fundraising');

	$contributions_amount = fundraising_sum_amount($fundcampaign);

	if (!$contributors_count) {
		$contributors_count = "0";
	}

	$body = elgg_view('output/url', array(
		'text' => elgg_echo('fundraising:contribute:button'),
		'href' => "fundraising/contribute/{$entity->guid}",
		'class' => 'elgg-button elgg-button-action',
	));	

	$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
	$amount .= elgg_echo('fundraising:contributions:of');
	$amount .= elgg_echo('fundraising:contributions:eur', array($fundcampaign->total_amount));
	if ($fundcampaign->total_amount) {
		$amount .= "\n" . round($contributions_amount / $fundcampaign->total_amount * 100, 2) . '%';
	}

	$body .= "<br><br>" . elgg_view('output/text', array(
		'value' => $amount,
	));


	$body .= "<br><br>" . elgg_view('output/text', array('value' => "From: " . $fundcampaign->start_date));
	$period_one_date = date('Y-m-d', strtotime($fundcampaign->start_date . " + {$fundcampaign->period_one_duration} days"));
	if ($fundcampaign->period_one_duration) {
		$time = "Period 1: " . $period_one_date;
		$body .= "<br>" . elgg_view('output/text', array('value' => $time));	
		if (time() < strtotime($period_one_date)) {
			$amount = elgg_echo('fundraising:contributions:amount', array($contributions_amount));
			$amount .= elgg_echo('fundraising:contributions:of');
			$amount .= elgg_echo('fundraising:contributions:eur', array($fundcampaign->period_one_amount));
			$amount .= "\n" . round($contributions_amount / $fundcampaign->period_one_amount * 100, 2) . '%';
			$body .= "<br>" . elgg_view('output/text', array('value' => $amount));	
		}
	}
	$body .= "<br>" . elgg_view('output/text', array('value' => "To: " . $fundcampaign->end_date));
}

echo elgg_view('projects/profile/module', array(
	'title' => elgg_echo('fundraising:contributions', array($fundcampaign->name)),
	'content' => $body,
	'all_link' => $all_link,
));



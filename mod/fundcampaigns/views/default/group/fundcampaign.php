<?php 
/**
 * Campaign entity view
 * 
 * @package Coopfunding
 * @subpackage fndcampaigns
 */

$fundcampaign = $vars['entity'];

$icon = elgg_view_entity_icon($fundcampaign, 'tiny');

$metadata = elgg_view_menu('entity', array(
	'entity' => $fundcampaign,
	'handler' => 'fundcampaigns',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if (elgg_in_context('owner_block') || elgg_in_context('widgets')) {
	$metadata = '';
}

if ($vars['full_view'] && !elgg_in_context('gallery')) {
	echo elgg_view('fundcampaigns/profile/summary', $vars);
} elseif (elgg_in_context('gallery')) {
	echo elgg_view('fundcampaigns/profile/gallery', $vars);
} elseif (elgg_in_context('owner_block')) {
	echo elgg_view('fundcampaigns/owner_block', $vars);
} else {
	// brief view
	$params = array(
		'entity' => $fundcampaign,
		'metadata' => $metadata,
		'subtitle' => $fundcampaign->briefdescription,
	);
	$params = $params + $vars;
	$list_body = elgg_view('group/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body, $vars);
}

<?php
/**
 * View for campaign_collective objects
 *
 * @package campaign_collective
 */
$campaign_collective = elgg_extract('entity', $vars, FALSE);
if (!$campaign_collective) {
	return TRUE;
}

$owner = $campaign_collective->getOwnerEntity();
$container = $campaign_collective->getContainerEntity();

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'campaign_collective',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$content = "$campaign_collective->description";
$subtitle = "";
$params = array(
		'entity' => $campaign_collective,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $content,
);

$params = $params + $vars;
$list_body = elgg_view('object/summary', $params);

echo elgg_view_image_block($owner_icon, $list_body);


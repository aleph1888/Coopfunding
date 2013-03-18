<?php
/**
 * Project profile RSS view
 *
 * Displays a list of the latest content in the project
 * 
 * @package Coopfunding
 * @subpackage Projects
 *
 * @uses $vars['entity'] ElggGroup object
 */

$entities = elgg_get_config('registered_entities');

if (!empty($entities['object'])) {
	echo elgg_list_entities(array(
		'type' => 'object',
		'subtypes' => $entities['object'],
		'container_guid' => $vars['entity']->getGUID(),
	));
}

<?php
/**
 * Layout of the projects profile page
 *
 * @package Coopfunding
 * @subpackage Projects
 * 
 * @uses $vars['entity']
 */

elgg_load_library ('elgg:fundcampaigns');
if (fundcampaigns_get_active_campaign ($vars['entity']->guid)) {
	echo elgg_view('projects/profile/widgets', $vars);
}
echo elgg_view('projects/profile/summary', $vars);


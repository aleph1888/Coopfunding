<?php
/**
 * Profile widgets/tools
 * 
 * @package Coopfunding
 * @subpackage Projects
 */ 

echo '<ul id="projects-tools" class="elgg-gallery elgg-gallery-fluid mtl clearfix">';

echo elgg_view("projects/campaigns", $vars);
if (elgg_is_active_plugin("blog")){ echo elgg_view("projects/blogs", $vars);}
if (elgg_is_active_plugin("campaign_reward")){ echo elgg_view("projects/campaign_rewards", $vars);}
if (elgg_is_active_plugin("campaign_collective")){ echo elgg_view("projects/campaign_collective", $vars);}
if (elgg_is_active_plugin("fundraising")) {echo elgg_view("projects/contribute", $vars);}

echo "</ul>";




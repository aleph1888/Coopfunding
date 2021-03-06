<?php
/**
 * Campaigns tag-based search form body
 * 
 * @package Coopfunding
 * @subpackage fundcampaigns
 */

$tag_string = elgg_echo('fundcampaigns:search:tags');

$params = array(
	'name' => 'tag',
	'class' => 'elgg-input-search mbm',
	'value' => $tag_string,
	'onclick' => "if (this.value=='$tag_string') { this.value='' }",
);
echo elgg_view('input/text', $params);

echo elgg_view('input/submit', array('value' => elgg_echo('search:go')));

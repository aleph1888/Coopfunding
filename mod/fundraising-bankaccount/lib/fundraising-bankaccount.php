<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.bankaccount
 */

function fundraising_contribute_bankaccount($guid, $amount, $reward_guid) {
	forward(elgg_get_site_url() . "fundraising/bankaccount/contribute/{$guid}&amount={$amount}&reward_guid={$reward_guid}");

}

function fundraising_bankaccount_managedeposits_get_page_content_list($guid = NULL) {
	$return = array();
	$return['filter_context'] = 'mine';
	$return['filter'] = false;

	$options = array(
		'type' => 'object',
		'subtype' => 'transaction',
		'container_guid' => $guid,
		'full_view' => false,
		'metadata_name' => 'method',
		'metadata_value' => 'bankaccount',
		'no_results' => elgg_echo('fundraising:bankaccount:notransactions'),
	);

	$container = get_entity($guid);
	$return['title'] = elgg_echo('fundraising:bankaccount', array($container->name));

 	$content = elgg_list_entities_from_metadata($options);
	$return['content'] = $content;

	return $return;

}

function fundraising_bankaccount_get_page_content_edit($page, $guid = NULL) {
	$return = array(
		'filter' => '',
	);

	$vars = array();
	$vars['id'] = 'fundraising_bankaccount-post-edit';
	$vars['class'] = 'elgg-form-alt';

	$sidebar = '';
	if ($page == 'edit') {
		$transaction = get_entity($guid);
        	$container = get_entity($transaction->container_guid);

		$title = elgg_echo('fundraising:bankaccount:editdeposit');

		if (elgg_instanceof($transaction, 'object', 'transaction') && $transaction->canEdit()) {
			$vars['entity'] = $transaction;

			$body_vars = fundraising_bankaccount_prepare_form_vars($transaction, $guid);

			elgg_push_breadcrumb(elgg_echo("{$container->alias}"), $container->getURL());
			elgg_push_breadcrumb(elgg_echo('fundraising:bankaccount:editdeposit'));

			$content = elgg_view_form('transaction/save', $vars, $body_vars);
		} else {
			$content = elgg_echo('transaction:error:cannot_edit_item');
		}
	} else {
		elgg_push_breadcrumb(elgg_echo('fundraising:bankaccount:newdeposit'));

		$body_vars = fundraising_bankaccount_prepare_form_vars(null, $guid);

		$title = elgg_echo('fundraising:bankaccount:newdeposit');
		$content = elgg_view_form('transaction/save', $vars, $body_vars);
	}

	$return['title'] = $title;
	$return['content'] = $content;
	$return['sidebar'] = $sidebar;

	return $return;
}

function fundraising_bankaccount_contribute_page ($entity) {
	$user = elgg_get_logged_in_user_entity();
	if (!$user) {
		$is_anonymous = true;
		elgg_load_library("coopfunding:fundraising");
		$user = fundraising_get_anonymous_usr();
	}

	$title = elgg_echo('fundraising:bankaccount:title', array($entity->name));
	$content = elgg_view('fundraising/bankaccount/contribute', array(
		'entity' => $entity,
		'user' => $user_guid,
		'is_anonymous' => $is_anonymous,
		'code' => fundraising_bankaccount_get_transaction_code($entity->guid, $user->guid),
		'ban' => elgg_get_config('ban'),
		'amount' => get_input('amount'),
		'reward_guid' => get_input('reward_guid'),
	));
	
	$body = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => '',
	));
	return elgg_view_page($title, $body);	

}
function fundraising_bankaccount_get_transaction_code ($entity_guid, $user_guid) {
	return "{$entity_guid}-$user_guid";

}

function fundraising_bankaccount_prepare_form_vars($transaction = NULL, $container_guid = NULL) {
	// input names => defaults
	$values = array(
		'eur_amount' => '0',
		'commit_date' => NULL,
		'contributor'=> NULL,
		'container_guid' => $container_guid,
		'guid' => NULL,
	);

	if ($transaction) {
		foreach (array_keys($values) as $field) {
			if (isset($transaction->$field)) {
				$values[$field] = $transaction->$field;
			}
		}
	}

	if (elgg_is_sticky_form('transaction')) {
		$sticky_values = elgg_get_sticky_values('transaction');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('transaction');

	if (!$transaction) {
		return $values;
	}
	return $values;

}

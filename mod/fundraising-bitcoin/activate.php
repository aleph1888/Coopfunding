<?php

$dbprefix = elgg_get_config('dbprefix');

try {
	get_data("CREATE TABLE IF NOT EXISTS `{$dbprefix}fundraising_bitcoin` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`address` varchar(255) NOT NULL,
		`entity_guid` bigint(20) unsigned NOT NULL,
		`user_guid` bigint(20) unsigned NOT NULL,
		PRIMARY KEY (`address`),
		KEY `id` (`id`),
		KEY `entity_user_key` (`entity_guid`, `user_guid`)
	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

} catch(Exception $e) {
	return false;
}

return true;


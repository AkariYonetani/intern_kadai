<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'type'        => 'mysqli',
		'connection'  => array(
			'hostname'   => 'db',
			'database'   => 'wishlist',
			'username'   => 'root',
			'password'   => 'root',
			'persistent' => false,
		),
		'identifier'   => '`',
		'table_prefix' => '',
		// PHP と DB の間の文字コード。テーブルと同じ utf8mb4（1文字4バイトまで）にする
		// utf8（3バイトまで）だと、絵文字などの4バイトの文字を保存しようとしたときに 500 エラーになる
		'charset'      => 'utf8mb4',
		'collation'    => 'utf8mb4_unicode_ci',
		'enable_cache' => true,
		'profiling'    => false,
	),
);

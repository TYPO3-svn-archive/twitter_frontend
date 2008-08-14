<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_twitterfrontend_cache"] = array (
	"ctrl" => $TCA["tx_twitterfrontend_cache"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "twitterid,view,lastupdate,content"
	),
	"feInterface" => $TCA["tx_twitterfrontend_cache"]["feInterface"],
	"columns" => array (
		"twitterid" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:twitter_frontend/locallang_db.xml:tx_twitterfrontend_cache.twitterid",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "30",	
				"eval" => "required,trim",
			)
		),
		"view" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:twitter_frontend/locallang_db.xml:tx_twitterfrontend_cache.view",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"max" => "10",	
				"eval" => "required,trim",
			)
		),
		"lastupdate" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:twitter_frontend/locallang_db.xml:tx_twitterfrontend_cache.lastupdate",		
			"config" => Array (
				"type"     => "input",
				"size"     => "12",
				"max"      => "20",
				"eval"     => "datetime",
				"checkbox" => "0",
				"default"  => "0"
			)
		),
		"content" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:twitter_frontend/locallang_db.xml:tx_twitterfrontend_cache.content",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "twitterid;;;;1-1-1, view, lastupdate, content")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>
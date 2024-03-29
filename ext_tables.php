<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:twitter_frontend/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Twitter: Show status");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_twitterfrontend_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_twitterfrontend_pi1_wizicon.php';

$TCA["tx_twitterfrontend_cache"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:twitter_frontend/locallang_db.xml:tx_twitterfrontend_cache',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_twitterfrontend_cache.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "twitterid, view, lastupdate, content",
	)
);

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] ='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY . '/flexform.xml');
?>
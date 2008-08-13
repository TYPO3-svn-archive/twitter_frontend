<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Stephan Lämmer <kaffdaddy@insomniaonline.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Twitter: Show status' for the 'twitter_frontend' extension.
 *
 * @author	Stephan Lämmer <kaffdaddy@insomniaonline.de>
 * @package	TYPO3
 * @subpackage	tx_twitterfrontend
 */
class tx_twitterfrontend_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_twitterfrontend_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_twitterfrontend_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'twitter_frontend';	// The extension key.
	var $pi_checkCHash = TRUE;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$cache =1;
		$this->pi_USER_INT_obj=0;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();
	
		// Read Twitter ID or username from flexform
		$id = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'username', 'sDEF');
		
		switch($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'selectView', 'sDEF'))	{
			case "showFeed":
				$content = $this->showFeed($id);
				break;
				 
			case "showSingle":
			default:
				$content = $this->showSingle($id);
				break;
		}
	
		return $this->pi_wrapInBaseClass($content);
	}
	

	/**
	* Shows the last 20 status updates from the defined user.
	* 
	* @param 	string 	$id: required. Specifies the ID or twitter username for whom to returns the updates.
	* @return	The Content
	* */
	function showFeed($id=false)	{
		// Load CSS file and template
		if($this->conf["cssFile"] != "")	{
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" type="text/css" href="' . $this->conf["cssFile"] . '" />';
		}

		$this->templateCode = $this->cObj->fileResource($this->conf["templateFile"]);
		if($this->templateCode == "")	{
			return $this->pi_getLL("missingTemplate");
		}
		$template = array();
		$template['total'] = $this->cObj->getSubpart($this->templateCode, "###SHOWFEED###");
		$template['singlestatus'] = $this->cObj->getSubpart($template['total'] , "###SHOWSINGLESTATUS###");

		// Prepare template markers
		$markerArray = array();
		$wrappedSubpartArray = array();
		
		if($id)	{
			$status = t3lib_div::xml2tree( t3lib_div::getUrl( 'http://twitter.com/statuses/user_timeline/' . $id . '.xml' ) );
			$content_singlestatus ='';
			
			for($x=0; $x<20; $x++)	{
				// Timestamp of status update
				$createdat_strip = strptime($status['statuses'][0]['ch']['status'][$x]['ch']['created_at'][0]['values'][0], "%a %b %d %H:%M:%S +0000 %Y");
				$createdat = date($this->conf["createdAt"], mktime($createdat_strip['tm_hour']+$this->conf["timezone"], $createdat_strip['tm_min'], $createdat_strip['tm_sec'], $createdat_strip['tm_mon'], $createdat_strip['tm_mday'], $createdat_strip['tm_year'] + 1900)); 		
				$wrappedSubpartArray["###CREATEDAT###"] = $createdat;
				
				$wrappedSubpartArray["###ID###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['id'][0]['values'][0];
				
				// Reply to URL
				if($this->conf["replyToUrl"] && $status['statuses'][0]['ch']['status'][$x]['ch']['in_reply_to_status_id'][0]['values'][0]!='')	{
					$replyStatus = $status['statuses'][0]['ch']['status'][$x]['ch']['text'][0]['values'][0];
					$replyId = $status['statuses'][0]['ch']['status'][$x]['ch']['in_reply_to_user_id'][0]['values'][0];
					$replyUser = t3lib_div::xml2tree( t3lib_div::getUrl( 'http://twitter.com/users/show/' . $replyId . '.xml' ) );
					$replyStatus = str_replace("@" . $replyUser['user'][0]['ch']['screen_name'][0]['values'][0], '<a href="http://www.twitter.com/' . $replyUser['user'][0]['ch']['screen_name'][0]['values'][0] . '>@' . $replyUser['user'][0]['ch']['screen_name'][0]['values'][0] . '</a>', $replyStatus);
					$wrappedSubpartArray["###SINGLESTATUS###"] = $replyStatus;
				}
				else {
					$wrappedSubpartArray["###SINGLESTATUS###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['text'][0]['values'][0];
				}
				
				$wrappedSubpartArray["###SOURCE###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['source'][0]['values'][0];
				$wrappedSubpartArray["###INREPLYTOSTATUSID###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['in_reply_to_status_id'][0]['values'][0];
				$wrappedSubpartArray["###INREPLYTOUSERID###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['in_reply_to_user_id'][0]['values'][0];
				$wrappedSubpartArray["###FAVORITED###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['favorited'][0]['values'][0];
				$wrappedSubpartArray["###USERID###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['id'][0]['values'][0];
				$wrappedSubpartArray["###USERNAME###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['name'][0]['values'][0];
				$wrappedSubpartArray["###USERSCREENNAME###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['screen_name'][0]['values'][0];
				$wrappedSubpartArray["###USERLOCATION###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['location'][0]['values'][0];
				$wrappedSubpartArray["###USERDESCRIPTION###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['description'][0]['values'][0];
				$wrappedSubpartArray["###USERPROFILEIMAGEURL###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['profile_image_url'][0]['values'][0];
				$wrappedSubpartArray["###USERURL###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['url'][0]['values'][0];
				$wrappedSubpartArray["###USERPROTECTED###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['protected'][0]['values'][0];
				$wrappedSubpartArray["###USERFOLLOWERSCOUNT###"] = $status['statuses'][0]['ch']['status'][$x]['ch']['user'][0]['ch']['followers_count'][0]['values'][0];
				
				$content_singlestatus .= $this->cObj->substituteMarkerArrayCached($template['singlestatus'], $wrappedSubpartArray, array(), array());
			}
			
			$markerArray["###SHOWSINGLESTATUS###"] = $content_singlestatus;
			
			$content = $this->cObj->substituteMarkerArrayCached($template['total'] , array(), $markerArray, array());
		}
		else	{
			$content = $this->pi_getLL("missingTwitterId");
		}
		return $content;
	}
	
	
	/**
	* Shows the last status update from the defined user.
	* 
	* @param 	string 	$id: required. Specifies the ID or twitter username for whom to returns the updates.
	* @return	The Content
	* */
	function showSingle($id=false)	{
		// Load CSS file and template
		if($this->conf["cssFile"] != "")	{
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" type="text/css" href="' . $this->conf["cssFile"] . '" />';
		}

		$this->templateCode = $this->cObj->fileResource($this->conf["templateFile"]);
		if($this->templateCode == "")	{
			return $this->pi_getLL("missingTemplate");
		}
		$template = $this->cObj->getSubpart($this->templateCode, "###SHOWSINGLE###");

		// Prepare template markers
		$markerArray = array();

		
		// Retrieve status updates from Twitter
		if($id)	{
			$status = t3lib_div::xml2tree( t3lib_div::getUrl( 'http://twitter.com/statuses/user_timeline/' . $id . '.xml' ) );
			
			// Timestamp of status update
			$createdat_strip = strptime($status['statuses'][0]['ch']['status'][0]['ch']['created_at'][0]['values'][0], "%a %b %d %H:%M:%S +0000 %Y");
			$createdat = date("H:i:s d.m.Y", mktime($createdat_strip['tm_hour']+2, $createdat_strip['tm_min'], $createdat_strip['tm_sec'], $createdat_strip['tm_mon'], $createdat_strip['tm_mday'], $createdat_strip['tm_year'] + 1900)); 		
			$markerArray["###CREATEDAT###"] = $createdat;
			
			$markerArray["###ID###"] = $status['statuses'][0]['ch']['status'][0]['ch']['id'][0]['values'][0];
			$markerArray["###SINGLESTATUS###"] = $status['statuses'][0]['ch']['status'][0]['ch']['text'][0]['values'][0];
			$markerArray["###SOURCE###"] = $status['statuses'][0]['ch']['status'][0]['ch']['source'][0]['values'][0];
			$markerArray["###INREPLYTOSTATUSID###"] = $status['statuses'][0]['ch']['status'][0]['ch']['in_reply_to_status_id'][0]['values'][0];
			$markerArray["###INREPLYTOUSERID###"] = $status['statuses'][0]['ch']['status'][0]['ch']['in_reply_to_user_id'][0]['values'][0];
			$markerArray["###FAVORITED###"] = $status['statuses'][0]['ch']['status'][0]['ch']['favorited'][0]['values'][0];
			$markerArray["###USERID###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['id'][0]['values'][0];
			$markerArray["###USERNAME###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['name'][0]['values'][0];
			$markerArray["###USERSCREENNAME###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['screen_name'][0]['values'][0];
			$markerArray["###USERLOCATION###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['location'][0]['values'][0];
			$markerArray["###USERDESCRIPTION###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['description'][0]['values'][0];
			$markerArray["###USERPROFILEIMAGEURL###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['profile_image_url'][0]['values'][0];
			$markerArray["###USERURL###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['url'][0]['values'][0];
			$markerArray["###USERPROTECTED###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['protected'][0]['values'][0];
			$markerArray["###USERFOLLOWERSCOUNT###"] = $status['statuses'][0]['ch']['status'][0]['ch']['user'][0]['ch']['followers_count'][0]['values'][0];

			$content = $this->cObj->substituteMarkerArrayCached($template, $markerArray, array(), array());
		}
		else	{
			$content = $this->pi_getLL("missingTwitterId");
		}

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_frontend/pi1/class.tx_twitterfrontend_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_frontend/pi1/class.tx_twitterfrontend_pi1.php']);
}

?>
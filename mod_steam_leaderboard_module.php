<?php
/**
 * @copyright	Copyright © 2020 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @generator	http://xdsoft/joomla-module-generator/
 */
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
/* Available fields:"app_id","publisher_key", */
// Include assets
$doc->addStyleSheet(JURI::root()."modules/mod_steam_leaderboard_module/assets/css/style.css");
$doc->addScript(JURI::root()."modules/mod_steam_leaderboard_module/assets/js/script.js");
// $width 			= $params->get("width");

/**
	$db = JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__mod_steam_leaderboard_module where del=0 and module_id=".$module->id);
	$objects = $db->loadAssocList();
*/
require JModuleHelper::getLayoutPath('mod_steam_leaderboard_module', $params->get('layout', 'default'));
<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_sitemapgenerator')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once(JPATH_COMPONENT . '/controller.php');


$languages = JLanguageHelper::getLanguages();
$app = JApplication::getInstance('site');
$menu = $app->getMenu();
$config = JFactory::getConfig();

$sef = $config->get('sef', 0);
$sefRewrite = $config->get('sef_rewrite', 0);

// TODO Add expirmental option
// TODO communicate number of currently checked site in status message

$defaultLangCode = JFactory::getLanguage()->getDefault();

$languageFilterEnabled = JPluginHelper::isEnabled('system', 'languagefilter');
if (!$languageFilterEnabled || $sef != '1') { // TODO check also if sef is enabled
	// TODO skip multilang support
}

$oldLanguageFilterValue = $app->setLanguageFilter(true); // necessary that $menu->getDefault() works

$sitemaps = array();
//$sitemaps['*'] = $menu->getDefault('*'); // TODO add?

foreach ($languages as $language) {
	$langCode = $language->lang_code;
	$default = $menu->getDefault($langCode);

	if ($default && $default->language == $langCode) {
		$sitemap = new stdClass();

		$sitemap->link = JURI::root() . 'index.php/' . $language->sef . '/';
		if ($sefRewrite) {
			$sitemap->link = JURI::root() . $language->sef . '/';
		}
		$sitemap->default = $langCode == $defaultLangCode;
		$sitemap->identifier = $language->sef;

		$sitemaps[$langCode] = $sitemap;
	}

	// TODO use $language->sef for identifier in filename if not default.
}

$app->setLanguageFilter($oldLanguageFilterValue);
exit;

$controller = JControllerLegacy::getInstance('SitemapGenerator');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
?>

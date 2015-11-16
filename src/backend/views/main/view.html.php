<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class SitemapGeneratorViewMain extends JViewLegacy {

	function display($tmpl = null) {

		JToolbarHelper::title(JText::_('COM_SITEMAPGENERATOR'));

		if (JFactory::getUser()->authorise('core.admin', 'com_sitemapgenerator')) {
			JToolbarHelper::preferences('com_sitemapgenerator');
		}

		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/angular.min.js', 'text/javascript', true);
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/sitemap-vars.js?v=1', 'text/javascript', true);
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/sitemap.js?v=3', 'text/javascript', true);

		$this->curlInstalled = function_exists('curl_version');

		$curlVersion = curl_version(); // temp var necessary for PHP 5.3
		$this->curlVersionOk = version_compare($curlVersion['version'], '7.18.1', '>=');

		$this->onLocalhost = preg_match('/^https?:\/\/(?:localhost|127\.0\.0\.1)/i', JURI::root()) === 1; // TODO improve localhost detection

		$this->hasToken = JComponentHelper::getParams('com_sitemapgenerator')->get('token') != '';

		parent::display();
	}
}

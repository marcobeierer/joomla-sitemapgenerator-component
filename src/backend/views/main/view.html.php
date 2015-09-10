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
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/sitemap.js?v=1', 'text/javascript', true);

		parent::display();
	}
}

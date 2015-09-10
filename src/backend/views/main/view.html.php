<?php
defined('_JEXEC') or die;

class SitemapGeneratorViewMain extends JViewLegacy {

	function display($tmpl = null) {

		JToolbarHelper::title(JText::_('COM_SITEMAPGENERATOR'));
		JToolbarHelper::preferences('com_sitemapgenerator');

		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/angular.min.js', 'text/javascript', true);
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/sitemap.js?v=1', 'text/javascript', true);

		parent::display();
	}
}

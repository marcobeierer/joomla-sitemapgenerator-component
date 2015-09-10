<?php
class SitemapGeneratorViewMain extends JViewLegacy {

	function display($tmpl = null) {

		JToolbarHelper::title(JText::_('COM_SITEMAPGENERATOR'));
		JToolbarHelper::preferences('com_sitemapgenerator');

		parent::display();
	}
}

<?php
defined('_JEXEC') or die('Restricted access');
//jimport('joomla.application.component.controller');

class SitemapGeneratorController extends JControllerLegacy {

	function display($cacheable = false, $urlparams = array()) {

		JRequest::setVar('view', 'main');
		parent::display($cacheable, $urlparams);
	}
}

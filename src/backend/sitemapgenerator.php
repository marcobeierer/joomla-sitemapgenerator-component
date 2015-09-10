<?php
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT . '/controller.php');

$controller = JControllerLegacy::getInstance('SitemapGenerator');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
?>

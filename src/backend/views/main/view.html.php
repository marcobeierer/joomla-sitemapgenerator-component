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
		$doc->addScript(JURI::root() . '/media/com_sitemapgenerator/js/sitemap.js?v=5', 'text/javascript', true);

		$this->curlInstalled = function_exists('curl_version');

		$curlVersion = curl_version(); // temp var necessary for PHP 5.3
		$this->curlVersionOk = version_compare($curlVersion['version'], '7.18.1', '>=');

		$this->onLocalhost = preg_match('/^https?:\/\/(?:localhost|127\.0\.0\.1)/i', JURI::root()) === 1; // TODO improve localhost detection
		
		$params = JComponentHelper::getParams('com_sitemapgenerator');
		$sef = JFactory::getConfig()->get('sef', 0);

		$languageFilterEnabled = JPluginHelper::isEnabled('system', 'languagefilter');
		if ($languageFilterEnabled) {
			$languageFilterPlugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$languageFilterParams = new JRegistry($languageFilterPlugin->params);
			$removeDefaultPrefix = $languageFilterParams->get('remove_default_prefix', 0) == '1';
		} else {
			$removeDefaultPrefix = false;
		}

		$this->hasToken = $params->get('token') != '';

		$this->multilangSupportEnabled = $params->get('multilang_support') == '1';
		$this->multilangSupportNecessary = $languageFilterEnabled && $sef == '1' && !$removeDefaultPrefix;
		$this->isSEFMultilangSiteWithoutMultilangSupportEnabled = $this->multilangSupportNecessary && !$this->multilangSupportEnabled;

		if ($this->multilangSupportEnabled && $this->multilangSupportNecessary) {
			$this->sitemapsData = $this->loadSitemapsData();
		} else {
			$this->sitemapsData = $this->loadDefaultSitemapData();
		}

		if (count($this->sitemapsData) == 0) {
			$this->sitemapsData = $this->loadDefaultSitemapData();
		}
		
		$ajaxPlugin = JPluginHelper::getPlugin('ajax', 'sitemapgenerator'); // returns an empty array if not found; and an object if found
		$module = JModuleHelper::getModule('mod_sitemapgenerator'); // returns an dummy object with id = 0 if not found
		$this->discontinuedExtensionsInstalled = !is_array($ajaxPlugin) || $module->id != 0;

		$doc->addScriptDeclaration($this->getAngularBootstrapJS($this->sitemapsData));

		parent::display();
	}

	function getAngularBootstrapJS($sitemapsData) {
		$script = "jQuery(document).ready(function() {\n";
		foreach ($sitemapsData as $data) {
			$script .= "angular.bootstrap(document.getElementById('" . $data->identifier . "SitemapGenerator'), ['sitemapGeneratorApp']);\n";
		}
		$script .= "});";

		return $script;
	}

	function loadDefaultSitemapData() {
		$sitemaps = array();

		$sitemap = new stdClass();

		if (JFactory::getApplication()->input->getInt('dev', 0) === 1) {
			$sitemap->link = 'https://www.marcobeierer.com/';
		} else {
			$sitemap->link = JURI::root();
		}

		$sitemap->base64URL = $this->base64URL($sitemap->link);
		$sitemap->identifier = '';
		$sitemap->filename = 'sitemap.xml';

		$sitemaps[] = $sitemap;
		return $sitemaps;
	}

	function base64URL($url) {
		return urlencode(strtr(base64_encode($url), '+/', '-_')); // urlencode for =
	}

	function loadSitemapsData() {
		$languages = JLanguageHelper::getLanguages();
		$app = JApplication::getInstance('site');
		$menu = $app->getMenu();
		$config = JFactory::getConfig();

		$sef = $config->get('sef', 0);
		$sefRewrite = $config->get('sef_rewrite', 0);

		$defaultLangCode = JFactory::getLanguage()->getDefault();

		$sitemaps = array();
		//$sitemaps['*'] = $menu->getDefault('*'); // TODO add?

		$languageFilterEnabled = JPluginHelper::isEnabled('system', 'languagefilter');
		if (!$languageFilterEnabled || $sef != '1') { // TODO check also if sef is enabled
			return $sitemaps;
		}

		$oldLanguageFilterValue = $app->setLanguageFilter(true); // necessary that $menu->getDefault() works

		foreach ($languages as $language) {
			$langCode = $language->lang_code;
			$default = $menu->getDefault($langCode);

			if ($default && $default->language == $langCode) {
				$sitemap = new stdClass();

				$sitemap->link = JURI::root() . 'index.php/' . $language->sef . '/';
				if ($sefRewrite) {
					$sitemap->link = JURI::root() . $language->sef . '/';
				}

				$sitemap->base64URL = $this->base64URL($sitemap->link);

				$sitemap->identifier = '';
				$sitemap->filename = 'sitemap.xml';

				if ($langCode != $defaultLangCode) {
					$sitemap->identifier = substr($language->sef, 0, 3);
					$sitemap->filename = 'sitemap.' . $language->sef . '.xml';
				}

				$sitemaps[$langCode] = $sitemap;
			}
		}

		$app->setLanguageFilter($oldLanguageFilterValue);

		return $sitemaps;
	}
}

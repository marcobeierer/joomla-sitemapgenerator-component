<?php
/*
 * @copyright  Copyright (C) 2015 - 2026 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace MarcoBeierer\Component\SitemapGenerator\Administrator\View\Main;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use MarcoBeierer\Component\SitemapGenerator\Administrator\Helper\SitemapGeneratorHelper;

class HtmlView extends BaseHtmlView
{
	public $curlInstalled = false;
	public $curlVersionOk = false;
	public $onLocalhost = false;
	public $token = '';
	public $hasToken = false;
	public $maxFetchers = 3;
	public $ignoreEmbeddedContent = 0;
	public $referenceCountThreshold = 5;
	public $queryParamsToRemove = '';
	public $disableCookies = 0;
	public $multilangSupportEnabled = false;
	public $multilangSupportNecessary = false;
	public $isSEFMultilangSiteWithoutMultilangSupportEnabled = false;
	public $sitemapsData = [];
	public $discontinuedExtensionsInstalled = false;
	public $dev = false;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->getInput();
		$this->dev = $input->getBool('dev', false);

		ToolbarHelper::title(Text::_('COM_SITEMAPGENERATOR'));

		if ($app->getIdentity()->authorise('core.admin', 'com_sitemapgenerator')) {
			ToolbarHelper::preferences('com_sitemapgenerator');
		}

		$document = $app->getDocument();
		$wa = $document->getWebAssetManager();

		$wa->useScript('jquery');
		$wa->registerAndUseScript(
			'com_sitemapgenerator.app',
			'media/com_sitemapgenerator/js/sitemap-generator-1.1.1.min.js',
			['version' => '2.0.1']
		);
		$wa->registerAndUseStyle(
			'com_sitemapgenerator.wrapped',
			'media/com_sitemapgenerator/css/wrapped.min.css',
			['version' => '2']
		);
		$wa->addInlineScript(
			"jQuery(function() { riot.mount('*', {}); });",
			[],
			[],
			[]
		);

		$this->curlInstalled = function_exists('curl_version');

		if ($this->curlInstalled) {
			$curlVersion = curl_version();
			$this->curlVersionOk = version_compare($curlVersion['version'], '7.18.1', '>=');
		}

		$this->onLocalhost = !$this->dev && preg_match('/^https?:\/\/(?:localhost|127\.0\.0\.1)/i', Uri::root()) === 1;

		$params = ComponentHelper::getParams('com_sitemapgenerator');

		$this->token = $params->get('token');
		$this->hasToken = $this->token != '';
		$this->maxFetchers = (int) $params->get('max_fetchers', 3);
		$this->ignoreEmbeddedContent = (int) $params->get('ignore_embedded_content', 0);
		$this->referenceCountThreshold = (int) $params->get('reference_count_threshold', 5);
		$this->queryParamsToRemove = $params->get('query_params_to_remove', '');
		$this->disableCookies = (int) $params->get('disable_cookies', 0);
		$this->multilangSupportEnabled = $params->get('multilang_support') == '1';
		$this->multilangSupportNecessary = SitemapGeneratorHelper::isMultilangSupportNecessary();
		$this->isSEFMultilangSiteWithoutMultilangSupportEnabled = $this->multilangSupportNecessary && !$this->multilangSupportEnabled;

		if ($this->multilangSupportEnabled && $this->multilangSupportNecessary) {
			$this->sitemapsData = $this->loadSitemapsData();
		} else {
			$this->sitemapsData = $this->loadDefaultSitemapData();
		}

		if (count($this->sitemapsData) == 0) {
			$this->sitemapsData = $this->loadDefaultSitemapData();
		}

		$ajaxPlugin = PluginHelper::getPlugin('ajax', 'sitemapgenerator');
		$module = ModuleHelper::getModule('mod_sitemapgenerator');
		$this->discontinuedExtensionsInstalled = !empty($ajaxPlugin) || !empty($module->id);

		parent::display($tpl);
	}

	private function loadDefaultSitemapData(): array
	{
		$sitemaps = [];
		$sitemap = new \stdClass();

		if ($this->dev) {
			$sitemap->link = 'https://www.marcobeierer.com/';
		} else {
			$sitemap->link = Uri::root();
		}

		$sitemap->identifier = '';
		$sitemap->filename = 'sitemap.xml';

		$sitemaps[] = $sitemap;

		return $sitemaps;
	}

	private function loadSitemapsData(): array
	{
		if ($this->dev) {
			$sitemap1 = new \stdClass();
			$sitemap1->link = 'https://www.marcobeierer.com/';
			$sitemap1->identifier = '';
			$sitemap1->filename = 'sitemap.xml';

			$sitemap2 = new \stdClass();
			$sitemap2->link = 'https://www.marcobeierer.ch/';
			$sitemap2->identifier = 'ch';
			$sitemap2->filename = 'sitemap.ch.xml';

			return [$sitemap1, $sitemap2];
		}

		return SitemapGeneratorHelper::loadMultilangData(function ($language, $langCode, $defaultLangCode, $sefRewrite) {
			$sitemap = new \stdClass();
			$sitemap->link = Uri::root() . 'index.php/' . $language->sef . '/';

			if ($sefRewrite) {
				$sitemap->link = Uri::root() . $language->sef . '/';
			}

			$sitemap->identifier = '';
			$sitemap->filename = 'sitemap.xml';

			if ($langCode != $defaultLangCode) {
				$sitemap->identifier = substr($language->sef, 0, 3);
				$sitemap->filename = 'sitemap.' . $language->sef . '.xml';
			}

			return $sitemap;
		});
	}
}

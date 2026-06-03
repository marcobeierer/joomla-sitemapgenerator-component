<?php
/*
 * @copyright  Copyright (C) 2016 - 2026 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/agpl-3.0.html GNU/AGPL
 */

namespace MarcoBeierer\Component\SitemapGenerator\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

abstract class SitemapGeneratorHelper
{
	public static function isLanguageFilterEnabled(): bool
	{
		return PluginHelper::isEnabled('system', 'languagefilter');
	}

	public static function doRemoveDefaultPrefix(): bool
	{
		if (!self::isLanguageFilterEnabled()) {
			return false;
		}

		$languageFilterPlugin = PluginHelper::getPlugin('system', 'languagefilter');
		$languageFilterParams = new Registry($languageFilterPlugin->params ?? '');

		return $languageFilterParams->get('remove_default_prefix', 0) == '1';
	}

	public static function isMultilangSupportNecessary(): bool
	{
		$sef = Factory::getApplication()->getConfig()->get('sef', 0);

		return self::isLanguageFilterEnabled() && $sef == '1' && !self::doRemoveDefaultPrefix();
	}

	public static function loadMultilangData(callable $prepareElementCallback): array
	{
		$languages = LanguageHelper::getLanguages();
		$config = Factory::getApplication()->getConfig();
		$sef = $config->get('sef', 0);
		$sefRewrite = $config->get('sef_rewrite', 0);
		$defaultLangCode = $config->get('language', Factory::getApplication()->getLanguage()->getDefault());
		$websites = [];

		if (!self::isLanguageFilterEnabled() || $sef != '1') {
			return $websites;
		}

		$defaultMenuLanguages = self::getDefaultMenuLanguages();

		foreach ($languages as $language) {
			$langCode = $language->lang_code;

			if (isset($defaultMenuLanguages[$langCode])) {
				$websites[$langCode] = $prepareElementCallback($language, $langCode, $defaultLangCode, $sefRewrite);
			}
		}

		return $websites;
	}

	private static function getDefaultMenuLanguages(): array
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select($db->quoteName('language'))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('home') . ' = 1')
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('client_id') . ' = 0');

		$db->setQuery($query);

		return array_flip($db->loadColumn());
	}
}

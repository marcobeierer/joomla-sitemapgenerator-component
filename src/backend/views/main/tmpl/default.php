<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="bootstrap3" style="margin-top:10px;">
	<h2>Sitemap Generator</h2>

	<?php if ($this->discontinuedExtensionsInstalled): ?>
		<div class="alert alert-error">
			The Sitemap Generator Ajax Plugin and the Sitemap Generator Module are no longer necessary and thus the development has discontinued. Please uninstall them in the Extension Manager.
		</div>
	<?php endif; ?>

	<?php if (!$this->curlInstalled): ?>
		<div class="alert alert-error">
			cURL is not activated on your webspace. Please activate it in your web hosting control panel. This plugin will not work without cURL activated.
		</div>
	<?php elseif (!$this->curlVersionOk): ?>
		<div class="alert alert-error">
			You have an outdated version of cURL installed. Please update to cURL 7.18.1 or higher in your web hosting control panel. A compatible version should be provided by default with PHP 5.4 or higher. This plugin will not work with the currently installed cURL version.
		</div>
	<?php endif; ?>

	<?php if ($this->onLocalhost): ?>
		<div class="alert alert-error">
			It is not possible to use this plugin in a local development environment. The backend service needs to crawl your website and this is just possible if your site is reachable from the internet.
		</div>
	<?php endif; ?>

	<?php if ($this->isSEFMultilangSiteWithoutMultilangSupportEnabled): ?>
		<div class="alert alert-error">
			You are using the Sitemap Generator with a multilanguage site and you have SEF urls enabled. The Sitemap Generator will by default only generate a sitemap for one language version of your site. To generate a sitemap for each language version of your site, you have to enable the multilanguage support in the component options.
		</div>
	<?php endif; ?>

	<div class="card" id="sitemap-widget">
		<?php if (count($this->sitemapsData) > 1): ?>
			<h3>Generate XML sitemaps for your site</h3>
		<?php else: ?>
			<h3>Generate a XML sitemap for your site</h3>
		<?php endif; ?>
		<hr />
		<?php foreach($this->sitemapsData as $data): ?>
			<p>Generate a sitemap for <strong><?php echo $data->link; ?></strong>. The sitemap will be saved with the filename <strong><?php echo $data->filename; ?></strong> in the root folder of your Joomla instance. Any existing file with the same filename will get overwritten.</p>
			<sitemap-generator
				proxy-url="index.php?option=com_sitemapgenerator&task=proxy&format=raw"
				website-url="<?php echo $data->link; ?>"
				identifier="<?php echo $data->identifier; ?>"
				sitemap-filename="<?php echo $data->filename; ?>"
				token="<?php echo $this->token; ?>"
				system-name="Joomla"
				max-fetchers="<?php echo $this->maxFetchers; ?>"
				ignore-embedded-content="<?php echo $this->ignoreEmbeddedContent; ?>"
				reference-count-threshold="<?php echo $this->referenceCountThreshold; ?>"
				query-params-to-remove="<?php echo $this->queryParamsToRemove; ?>"
				disable-cookies="<?php echo $this->disableCookies; ?>"
				enable-index-file="0"
				professional-url="https://www.marcobeierer.com/joomla-extensions/sitemap-generator-professional"
				btn-primary-class="btn-primary"
				btn-default-class="btn-default"
				dev="<?php echo $this->useLocalAPIServer; ?>"
			>
			<!-- token needs also to be set in proxy -->
			</sitemap-generator>
			<hr />
		<?php endforeach; ?>
	</div>

	<div class="card">
		<h4>Sitemap Generator Professional</h4>
		<p>Your site has <strong>more than 500 URLs</strong> or you like to integrate an <strong>image sitemap</strong> or a <strong>video sitemap</strong>? Then have a look at the <a href="https://www.marcobeierer.com/joomla-extensions/sitemap-generator-professional">Sitemap Generator Professional</a>.
	</div>
	<div class="card">
		<h4>You like the Sitemap Generator?</h4>
		<p>I would be happy if you could write a review or vote for it in the <a target="_blank" href="http://extensions.joomla.org/extensions/extension/structure-a-navigation/site-map/sitemap-generator#reviews">Joomla Extensions Directory</a>!</p>
	</div>
	<div class="card">
		<h4>Any questions?</h4>
		<p>Please have a look at the <a target="_blank" href="https://www.marcobeierer.com/tools/sitemap-generator-faq">FAQ</a> page on my website or ask your question in the <a target="_blank" href="https://groups.google.com/forum/#!forum/marcobeierer">support area on Google Groups</a>. I would be pleased to help you out!</p>
	</div>
</div>

<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="bootstrap3" style="margin-top:10px;">
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

	<?php if (count($this->sitemapsData) > 1): ?>
		<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
			<?php $firstWebsite = true; ?>
			<?php foreach ($this->sitemapsData as $sitemapData): ?>
				<li role="presentation" class="<?php if ($firstWebsite) { echo 'active'; } ?>">
					<a href="#<?php echo md5($sitemapData->link); ?>" aria-controls="<?php echo md5($sitemapData->link); ?>" role="tab" data-toggle="tab"><?php echo $sitemapData->link; ?></a>
				</li>
				<?php $firstWebsite = false; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="card" id="sitemap-widget">
		<div class="tab-content">
			<?php $firstWebsite = true; ?>
			<?php foreach($this->sitemapsData as $data): ?>
				<div role="tabpanel" class="tab-pane <?php if ($firstWebsite) { echo 'active'; } ?>" id="<?php echo md5($data->link); ?>">
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
					>
					<!-- token needs also to be set in proxy -->
					</sitemap-generator>
				</div>
				<?php $firstWebsite = false; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<hr />

	<div class="card">
		<h3>Sitemap Generator Professional</h3>
		<p>Your site has <strong>more than 500 URLs</strong> or you like to integrate an <strong>image sitemap</strong> or a <strong>video sitemap</strong>? Then have a look at the <a href="https://www.marcobeierer.com/joomla-extensions/sitemap-generator-professional">Sitemap Generator Professional</a>.
	</div>
	<div class="card">
		<h3>Please help me with a Review for the Sitemap Generator in the JED</h3>
		<p>Do you like the Sitemap Generator for Joomla and like to help out? I would be happy if you could write a short review or vote for it in the <a target="_blank" href="http://extensions.joomla.org/extensions/extension/structure-a-navigation/site-map/sitemap-generator#reviews">Joomla Extensions Directory</a>. This would help a lot to make the Sitemap Generator more popular and allows me to invest more time in the improvement of the Sitemap Generator.</p>
	</div>
	<div class="card">
		<h3>Sitemap Generator for other Platforms</h3>
		<p>The Sitemap Generator is also available as:</p>
		<ul>
			<li><a target="_blank" href="https://www.marcobeierer.com/tools/sitemap-generator">online tool</a>, </li>
			<li><a target="_blank" href="https://www.marcobeierer.com/wordpress-plugins/sitemap-generator">WordPress plugin</a>,</li>
			<li><a target="_blank" href="https://github.com/marcobeierer/sitemapgenerator-cli">command-line application</a> (direct link to GitHub) and</li>
			<li>app in the <a target="_blank" href="https://www.marcobeierer.com/tools/website-tools">Website Tools</a>.</li>
		</ul>
	</div>
	<div class="card">
		<h3>Any questions?</h3>
		<p>Please have a look at the <a target="_blank" href="https://www.marcobeierer.com/tools/sitemap-generator-faq">FAQ</a> page on my website or ask your question in the <a target="_blank" href="https://groups.google.com/forum/#!forum/marcobeierer">support area on Google Groups</a>. I would be pleased to help you out!</p>
	</div>
</div>

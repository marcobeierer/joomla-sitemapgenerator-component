<?php
/*
 * @copyright  Copyright (C) 2015 - 2026 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$proxyUrl = \Joomla\CMS\Router\Route::_('index.php?option=com_sitemapgenerator&task=proxy&format=raw', false);
?>

<div class="bootstrap3" style="margin-top:10px;">
	<?php if ($this->token == ''): ?>
		<div class="alert alert-info">
			<p>You are using the free version of the Sitemap Generator. It works for websites with up to 500 URLs and doesn't include advanced features such as image and video support. Learn more about <a href="https://www.marcobeierer.com/tools/sitemap-generator/pro">Sitemap Generator Pro</a>.</p>
		</div>
	<?php endif; ?>

	<?php if ($this->discontinuedExtensionsInstalled): ?>
		<div class="alert alert-danger">
			The Sitemap Generator Ajax Plugin and the Sitemap Generator Module are no longer necessary and thus the development has discontinued. Please uninstall them in the Extension Manager.
		</div>
	<?php endif; ?>

	<?php if (!$this->curlInstalled): ?>
		<div class="alert alert-danger">
			cURL is not activated on your webspace. Please activate it in your web hosting control panel. This plugin will not work without cURL activated.
		</div>
	<?php elseif (!$this->curlVersionOk): ?>
		<div class="alert alert-danger">
			You have an outdated version of cURL installed. Please update to cURL 7.18.1 or higher in your web hosting control panel. A compatible version should be provided by default with PHP 5.4 or higher. This plugin will not work with the currently installed cURL version.
		</div>
	<?php endif; ?>

	<?php if ($this->onLocalhost): ?>
		<div class="alert alert-danger">
			It is not possible to use this plugin in a local development environment. The backend service needs to crawl your website and this is just possible if your site is reachable from the internet.
		</div>
	<?php endif; ?>

	<?php if ($this->isSEFMultilangSiteWithoutMultilangSupportEnabled): ?>
		<div class="alert alert-danger">
			You are using the Sitemap Generator with a multilanguage site and you have SEF urls enabled. The Sitemap Generator will by default only generate a sitemap for one language version of your site. To generate a sitemap for each language version of your site, you have to enable the multilanguage support in the component options.
		</div>
	<?php endif; ?>

	<?php if (count($this->sitemapsData) > 1): ?>
		<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
			<?php $firstWebsite = true; ?>
			<?php foreach ($this->sitemapsData as $sitemapData): ?>
				<?php $tabId = md5($sitemapData->link); ?>
				<li role="presentation" class="<?php echo $firstWebsite ? 'active' : ''; ?>">
					<a href="#<?php echo $tabId; ?>" aria-controls="<?php echo $tabId; ?>" role="tab" data-toggle="tab"><?php echo $this->escape($sitemapData->link); ?></a>
				</li>
				<?php $firstWebsite = false; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="tab-content" id="sitemap-widget">
		<?php $firstWebsite = true; ?>
		<?php foreach ($this->sitemapsData as $data): ?>
			<?php $tabId = md5($data->link); ?>
			<div role="tabpanel" class="tab-pane <?php echo $firstWebsite ? 'active' : ''; ?>" id="<?php echo $tabId; ?>">
				<p>Generate a sitemap for <strong><?php echo $this->escape($data->link); ?></strong>. The sitemap will be saved with the filename <strong><?php echo $this->escape($data->filename); ?></strong> in the root folder of your Joomla instance. Any existing file with the same filename will get overwritten.</p>
				<sitemap-generator
					proxy-url="<?php echo $this->escape($proxyUrl); ?>"
					website-url="<?php echo $this->escape($data->link); ?>"
					identifier="<?php echo $this->escape($data->identifier); ?>"
					sitemap-filename="<?php echo $this->escape($data->filename); ?>"
					token="<?php echo $this->escape($this->token); ?>"
					system-name="Joomla"
					max-fetchers="<?php echo $this->maxFetchers; ?>"
					ignore-embedded-content="<?php echo $this->ignoreEmbeddedContent; ?>"
					reference-count-threshold="<?php echo $this->referenceCountThreshold; ?>"
					query-params-to-remove="<?php echo $this->escape($this->queryParamsToRemove); ?>"
					disable-cookies="<?php echo $this->disableCookies; ?>"
					enable-index-file="0"
					professional-url="https://www.marcobeierer.com/tools/sitemap-generator/pro"
					btn-primary-class="btn-primary"
					btn-default-class="btn-default"
				>
				<!-- token needs also to be set in proxy -->
				</sitemap-generator>
			</div>
			<?php $firstWebsite = false; ?>
		<?php endforeach; ?>
	</div>

	<hr />

	<div>
		<h3>Sitemap Generator Pro</h3>
		<p>Your site has <strong>more than 500 URLs</strong> or you like to integrate an <strong>image sitemap</strong> or a <strong>video sitemap</strong>? Then have a look at <a href="https://www.marcobeierer.com/tools/sitemap-generator/pro">Sitemap Generator Pro</a>.</p>
	</div>
	<div>
		<h3>Please help me with a Review for the Sitemap Generator in the JED</h3>
		<p>Do you like the Sitemap Generator for Joomla and like to help out? I would be happy if you could write a short review or vote for it in the <a target="_blank" rel="noopener noreferrer" href="http://extensions.joomla.org/extensions/extension/structure-a-navigation/site-map/sitemap-generator#reviews">Joomla Extensions Directory</a>.</p>
		<p>This would help a lot to make the Sitemap Generator more popular and allows me to invest more time in the improvement of the Sitemap Generator.</p>
	</div>
	<div>
		<h3>Sitemap Generator for other Platforms</h3>
		<p>The Sitemap Generator is also available as:</p>
		<ul>
			<li><a target="_blank" rel="noopener noreferrer" href="https://www.marcobeierer.com/tools/sitemap-generator">online tool</a>,</li>
			<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/mb-sitemap-generator/">WordPress plugin</a> and</li>
			<li><a target="_blank" rel="noopener noreferrer" href="https://github.com/marcobeierer/sitemapgenerator-cli">command-line application</a> (direct link to GitHub).</li>
		</ul>
	</div>
	<div>
		<h3>Any questions?</h3>
		<p>Please have a look at the <a target="_blank" rel="noopener noreferrer" href="https://www.marcobeierer.com/tools/sitemap-generator-faq">FAQ</a> page on my website or ask your question in the <a target="_blank" rel="noopener noreferrer" href="https://groups.google.com/forum/#!forum/marcobeierer">support area on Google Groups</a>. I would be pleased to help you out!</p>
	</div>
</div>

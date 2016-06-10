<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>

<div >
	<div >
		<div class="wrap">
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
					<div id="<?php echo $data->identifier; ?>SitemapGenerator">
						<div ng-controller="SitemapController">
							<div>
								<p>Generate a sitemap for <strong><?php echo $data->link; ?></strong>. The sitemap will be saved with the filename <strong><?php echo $data->filename; ?></strong> in the root directory of your Joomla installation. Any existing file with the same filename will get overwritten.</p>
								<form name="sitemapForm">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="glyphicon glyphicon-globe"></i>
										</span>
										<span class="input-group-btn">
											<button 
												type="submit" 
												class="btn {{ generateClass }}" 
												ng-click="generate('<?php echo $data->base64URL; ?>', '<?php echo $data->identifier; ?>')" 
												ng-disabled="generateDisabled">Generate your sitemap
											</button>
											<a class="btn {{ downloadClass }}" ng-href="<?php echo $data->link . $data->filename; ?>" ng-disabled="downloadDisabled">Show the sitemap</a>
										</span>
									</div>
								</form>
								<p class="alert well-sm {{ messageClass }}"><span ng-bind-html="message | sanitize"></span> <span ng-if="pageCount > 0 && downloadDisabled">{{ pageCount }} URLs already processed.</span></p>
							</div>

							<div class="card" ng-if="stats">
								<h4>Sitemap Stats</h4>
								<table>
									<tr>
										<td>Sitemap URL count:</td>
										<td>{{ stats.SitemapURLCount }}</td>
									</tr>
									<?php if ($this->hasToken): ?>
									<tr>
										<td>Sitemap image count:</td>
										<td>{{ stats.SitemapImageCount }}</td>
									</tr>
									<tr>
										<td>Sitemap video count:</td>
										<td>{{ stats.SitemapVideoCount }}</td>
									</tr>
									<?php endif; ?>
								</table>
								<h4>Crawl Stats</h4>
								<table>
									<tr>
										<td>Crawled URLs count:</td>
										<td>{{ stats.CrawledResourcesCount }}</td>
									</tr>
									<tr>
										<td>Dead URLs count:</td>
										<td>{{ stats.DeadResourcesCount }}</td>
									</tr>
									<tr>
										<td>Timed out URLs count:</td>
										<td>{{ stats.TimedOutResourcesCount }}</td>
									</tr>
								</table>

								<p></p>
								<p><i>Want to find out more about the dead and timed out URLs? Have a look at my <a href="https://www.marcobeierer.com/joomla-extensions/link-checker">Link Checker</a> for Joomla.</i></p>
							</div>
						</div>
					</div>
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
	</div>
</div>

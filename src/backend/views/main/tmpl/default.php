<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>

<div ng-app="sitemapGeneratorApp" ng-strict-di>
	<div ng-controller="SitemapController">
		<div class="wrap">
			<h2>Sitemap Generator</h2>

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

			<div class="card" id="sitemap-widget">
				<?php if ($this->multilangSupportEnabled): ?>
					<h3>Generate XML sitemaps for your site</h3>
					<hr />
					<?php foreach($this->sitemapsData as $data): ?>
						<div>
							<p>Generate a sitemap for <strong><?php echo $data->link; ?></strong>. The sitemap will be saved with the filename <strong><?php echo $data->filename; ?></strong>.</p>
							<form name="sitemapForm">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="glyphicon glyphicon-globe"></i>
									</span>
									<span class="input-group-btn">
										<button type="submit" class="btn {{ generateClass }}" ng-click="generate()" ng-disabled="generateDisabled">Generate your sitemap</button>
										<a class="btn {{ downloadClass }}" ng-click="download()" ng-disabled="downloadDisabled" download="sitemap.xml" ng-href="{{ href }}">Show the sitemap</a>
									</span>
								</div>
							</form>
							<p class="alert well-sm {{ messageClass }}"><span ng-bind-html="message | sanitize"></span> <span ng-if="pageCount > 0 && downloadDisabled">{{ pageCount }} URLs already processed.</span></p>
						</div>
						<hr />
					<?php endforeach; ?>
				<?php else: ?>
					<h3>Generate a XML sitemap of your site</h3>
					<div>
						<form name="sitemapForm">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="glyphicon glyphicon-globe"></i>
								</span>
								<span class="input-group-btn">
									<button type="submit" class="btn {{ generateClass }}" ng-click="generate()" ng-disabled="generateDisabled">Generate your sitemap</button>
									<a class="btn {{ downloadClass }}" ng-click="download()" ng-disabled="downloadDisabled" download="sitemap.xml" ng-href="{{ href }}">Show the sitemap</a>
								</span>
							</div>
						</form>
						<p class="alert well-sm {{ messageClass }}"><span ng-bind-html="message | sanitize"></span> <span ng-if="pageCount > 0 && downloadDisabled">{{ pageCount }} URLs already processed.</span></p>
					</div>
				<?php endif; ?>
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

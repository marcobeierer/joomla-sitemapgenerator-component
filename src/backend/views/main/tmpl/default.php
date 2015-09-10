<div ng-app="sitemapGeneratorApp" ng-strict-di>
	<div ng-controller="SitemapController">
		<div class="wrap">
			<h2>Sitemap Generator</h2>
			<div class="card" id="sitemap-widget">
				<h3>Generate a XML sitemap of your site</h3>
				<div>
					<form name="sitemapForm">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="glyphicon glyphicon-globe"></i>
							</span>
							<span class="input-group-btn">
								<button type="submit" class="button btn {{ generateClass }}" ng-click="generate()" ng-disabled="generateDisabled">Generate your sitemap</button>
								<a class="button btn {{ downloadClass }}" ng-click="download()" ng-disabled="downloadDisabled" download="sitemap.xml" ng-href="{{ href }}">Show the sitemap</a>
							</span>
						</div>
					</form>
					<div ng-show="limitReached" class="notice notice-error is-dismissible below-h2 ng-hide">
						<p class="alert alert-danger">The Sitemap Generator reached the URL limit and the generated sitemap probably isn't complete. You may buy a token for the <a href="https://www.marcobeierer.com/joomla-extensions/sitemap-generator-professional">Sitemap Generator Professional</a> to crawl up to 50000 URLs and create a complete sitemap.</p>
					</div>
					<p class="alert well-sm {{ messageClass }}">{{ message }} <span ng-if="pageCount > 0 && downloadDisabled">{{ pageCount }} pages already crawled.</span></p>
				</div>
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

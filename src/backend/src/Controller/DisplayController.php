<?php
/*
 * @copyright  Copyright (C) 2015 - 2026 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace MarcoBeierer\Component\SitemapGenerator\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	protected $default_view = 'main';

	public function display($cachable = false, $urlparams = [])
	{
		$this->assertCanManage();

		return parent::display($cachable, $urlparams);
	}

	public function proxy(): void
	{
		$this->assertCanManage();

		$params = ComponentHelper::getParams('com_sitemapgenerator');
		$input = $this->input;

		$queryParamsArr = $input->getArray();

		// Unset Joomla vars so that only Sitemap Generator params are passed through.
		unset($queryParamsArr['option'], $queryParamsArr['task'], $queryParamsArr['format'], $queryParamsArr['dev']);

		$baseURL64 = urldecode($input->getString('baseurl64', ''));
		$identifier = $input->getWord('identifier', '');
		unset($queryParamsArr['baseurl64'], $queryParamsArr['identifier']);

		$queryParams = http_build_query($queryParamsArr, '', '&', PHP_QUERY_RFC3986);

		if (strlen($identifier) > 3) {
			$this->sendRawResponse('', 'text/plain', 400);

			return;
		}

		$ch = curl_init();
		$requestURL = sprintf('https://api.marcobeierer.com/sitemap/v2/%s?%s', $baseURL64, $queryParams);

		curl_setopt($ch, CURLOPT_URL, $requestURL);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

		$token = $params->get('token');

		if ($token != '') {
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: BEARER ' . $token]);
		}

		$response = curl_exec($ch);

		if ($response === false) {
			$errorMessage = curl_error($ch);
			$responseHeader = '';
			$responseBody = $errorMessage;
			$contentType = 'text/plain';
			$statusCode = 504;

			$this->app->setHeader('X-CURL-Error', '1', true);
		} else {
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$responseHeader = substr($response, 0, $headerSize);
			$responseBody = substr($response, $headerSize);
			$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'application/octet-stream';
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}

		curl_close($ch);

		if ($statusCode == 200 && stripos($contentType, 'application/xml') === 0) {
			$matches = [];

			if (preg_match('/(?:^|\r\n)X-Stats:\s*(.*?)\r\n/i', $responseHeader, $matches)) {
				$this->app->setHeader('X-Stats', $matches[1], true);
			}

			$reader = new \XMLReader();
			$reader->xml($responseBody, 'UTF-8');
			$reader->setParserProperty(\XMLReader::VALIDATE, true);

			if ($reader->isValid()) {
				$rootPath = JPATH_ROOT;

				if ($rootPath != '') {
					$filename = 'sitemap.xml';

					if ($identifier != '') {
						$filename = 'sitemap.' . $identifier . '.xml';
					}

					$success = file_put_contents($rootPath . DIRECTORY_SEPARATOR . $filename, $responseBody);

					if ($success === false) {
						$statusCode = 500;
						$this->app->setHeader('X-Write-Error', '1', true);
					}
				}
			}
		}

		$this->sendRawResponse($responseBody, $contentType, (int) $statusCode);
	}

	private function assertCanManage(): void
	{
		if (!$this->app->getIdentity()->authorise('core.manage', 'com_sitemapgenerator')) {
			throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	private function sendRawResponse(string $body, string $contentType, int $statusCode): void
	{
		http_response_code($statusCode);
		$this->app->setHeader('Status', (string) $statusCode, true);
		$this->app->setHeader('Cache-Control', 'no-store', true);
		$this->app->setHeader('Content-Type', $contentType, true);
		$this->app->getDocument()->setMimeEncoding($contentType);
		$this->app->setBody($body);
		$this->app->sendHeaders();

		echo $body;

		$this->app->close();
	}
}

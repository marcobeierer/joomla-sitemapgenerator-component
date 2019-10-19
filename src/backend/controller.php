<?php
/*
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class SitemapGeneratorController extends JControllerLegacy {

	function display($cacheable = false, $urlparams = array()) {
		$this->input->set('view', 'main');
		parent::display($cacheable, $urlparams);
	}

	function proxy() {
		$params = JComponentHelper::getParams('com_sitemapgenerator');

		$input = JFactory::getApplication()->input;

		$queryParamsArr = $input->getArray();

		// unset joomla vars so that we just have passed sitemap generator params:
		unset($queryParamsArr['option']);
		unset($queryParamsArr['task']);
		unset($queryParamsArr['format']);

		$baseURL64 = urldecode($input->getString('baseurl64', '')); // string filter necessary that percent encoded = is not stripped
		$identifier = $input->getWord('identifier', '');
		unset($queryParamsArr['baseurl64']);
		unset($queryParamsArr['identifier']);

		// '&' is required because null is handled as empty string, also some systems seem to have an invalid default value (&amp;)
		// according to comments in docs: https://www.php.net/manual/en/function.http-build-query.php
		// RFC3986 is the same as urlencode() uses and it required for query_params_to_remove
		$queryParamms = http_build_query($queryParamsArr, null, '&', PHP_QUERY_RFC3986); 

		if (strlen($identifier) > 3) { // prevent security issues with tampered identifiers
			$this->setStatusCode(400); // bad request
			//JFactory::getApplication()->close();
			return;
		}

		$ch = curl_init();

		/*
		$maxFetchers = (int) $params->get('max_fetchers', 10);
		$ignoreEmbeddedContent = (int) $params->get('ignore_embedded_content', 0);
		$referenceCountThreshold = (int) $params->get('reference_count_threshold', -1);
		$queryParamsToRemove = urlencode($params->get('query_params_to_remove', ''));
		$disableCookies = (int) $params->get('disable_cookies', 0);

		$requestURL = sprintf('https://api.marcobeierer.com/sitemap/v2/%s?pdfs=1&origin_system=joomla&max_fetchers=%d&ignore_embedded_content=%d&reference_count_threshold=%d&query_params_to_remove=%s&disable_cookies=%d', 
			$base64URL, $maxFetchers, $ignoreEmbeddedContent, $referenceCountThreshold, $queryParamsToRemove, $disableCookies);
		 */

		$requestURL = sprintf('https://api.marcobeierer.com/sitemap/v2/%s?%s', $baseURL64, $queryParamms);

		curl_setopt($ch, CURLOPT_URL, $requestURL);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		
		$token = $params->get('token');
		if ($token != '') {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: BEARER ' . $token));
		}

		$response = curl_exec($ch);

		if ($response === false) {
			$errorMessage = curl_error($ch);

			$responseHeader = '';
			$responseBody = json_encode($errorMessage);

			$contentType = 'application/json';
			$statusCode = 504; // gateway timeout

			header('X-CURL-Error: 1');
		} else {
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

			$responseHeader = substr($response, 0, $headerSize);
			$responseBody = substr($response, $headerSize);

			$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}

		curl_close($ch);

		if ($statusCode == 200 && $contentType == 'application/xml') {
			$matches = array();

			// case insensitive (/i) because Joomla converts header names to lower case
			preg_match('/\r\nX-Stats: (.*)\r\n/i', $responseHeader, $matches);
			if (isset($matches[1])) {
				header("X-Stats: $matches[1]");
				// TODO use JResponse::setHeader instead?
			}

			$reader = new XMLReader();
			$reader->xml($responseBody, 'UTF-8');
			$reader->setParserProperty(XMLReader::VALIDATE, true);

			if ($reader->isValid()) { // TODO check if empty?
				$rootPath = JPATH_ROOT;
				if ($rootPath != '') {
					$filename = 'sitemap.xml';
					if ($identifier != '') {
						$filename = 'sitemap.' . $identifier . '.xml';
					}

					$success = file_put_contents($rootPath . DIRECTORY_SEPARATOR . $filename, $responseBody); // TODO handle and report error
					if ($success === false) {
						$statusCode = 500;
						header('X-Write-Error: 1');
					}
				}
			}
		} 

		// always echo, also xml for use with download btn
		echo $responseBody;

		$this->setStatusCode($statusCode);

		header('Cache-Control: no-store');
		//header("Content-Type: $contentType");

		// necessary if application is not closed, then content-type gets overwritten
		JFactory::getDocument()->setMimeEncoding($contentType);
		JResponse::setHeader('Content-Type', $contentType, true);

		//JFactory::getApplication()->close(); // was necessary for Content-Type header to take effect, otherwise it was overwritten // NOTE: prevents display of error messages
	}

	function setStatusCode($statusCode) {
		if (function_exists('http_response_code')) {
			http_response_code($statusCode);
		}
		else { // fix for PHP version older than 5.4.0
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' ' . $statusCode . ' ');
		}
	}
}

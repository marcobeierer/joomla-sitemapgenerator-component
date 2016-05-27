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

		$base64URL = urldecode($input->getString('base64url', '')); // string filter necessary that percent encoded = is not stripped
		$identifier = $input->getWord('identifier', '');

		if (strlen($identifier) > 3) { // prevent security issues with tampered identifiers
			$this->setStatusCode(400); // bad request
			//JFactory::getApplication()->close();
			return;
		}

		$ch = curl_init();

		$ignoreEmbeddedContent = $params->get('ignore_embedded_content', 0);
		$maxFetchers = $params->get('max_fetchers', 10);

		curl_setopt($ch, CURLOPT_URL, 'https://api.marcobeierer.com/sitemap/v2/' . $base64URL . '?pdfs=1&origin_system=joomla&ignore_embedded_content=' . (int) $ignoreEmbeddedContent . '&max_fetchers=' . (int) $maxFetchers);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if ($params->get('ca_fallback', 0) === '1') {
			curl_setopt($ch, CURLOPT_CAINFO, JPATH_ROOT . '/media/com_sitemapgenerator/ca-bundle.crt');
		}

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
			preg_match('/\r\nX-Limit-Reached: (.*)\r\n/', $responseHeader, $matches);
			if (isset($matches[1])) {
				header("X-Limit-Reached: $matches[1]");
			}

			$matches = array();
			preg_match('/\r\nX-Stats: (.*)\r\n/', $responseHeader, $matches);
			if (isset($matches[1])) {
				header("X-Stats: $matches[1]");
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
					file_put_contents($rootPath . DIRECTORY_SEPARATOR . $filename, $responseBody); // TODO handle and report error
				}
			}
		} else {
			echo $responseBody;
		}

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

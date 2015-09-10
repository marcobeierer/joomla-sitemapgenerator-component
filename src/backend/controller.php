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

		$baseurl = JURI::root();
		$baseurl64 = strtr(base64_encode($baseurl), '+/', '-_');

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.marcobeierer.com/sitemap/v2/' . $baseurl64 . '?pdfs=1&origin_system=joomla');
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$token = JComponentHelper::getParams('com_sitemapgenerator')->get('token');
		if ($token != '') {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: BEARER ' . $token));
		}

		$response = curl_exec($ch);

		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$responseHeader = substr($response, 0, $headerSize);
		$responseBody = substr($response, $headerSize);

		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		curl_close($ch);

		if ($statusCode == 200 && $contentType == 'application/xml') {

			$matches = array();
			preg_match('/\r\nX-Limit-Reached: (.*)\r\n/', $responseHeader, $matches);
			if (isset($matches[1])) {
				header("X-Limit-Reached: $matches[1]");
			}

			$reader = new XMLReader();
			$reader->xml($responseBody, 'UTF-8');
			$reader->setParserProperty(XMLReader::VALIDATE, true);

			if ($reader->isValid()) { // TODO check if empty?

				$rootPath = JPATH_ROOT;
				if ($rootPath != '') {
					file_put_contents($rootPath . DIRECTORY_SEPARATOR . 'sitemap.xml', $responseBody); // TODO handle and report error
				}
			}
		}

		if (function_exists('http_response_code')) {
			http_response_code($statusCode);
		}
		else { // fix for PHP version older than 5.4.0
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' ' . $statusCode . ' ');
		}

		header("Content-Type: $contentType");

		echo $responseBody;
		JFactory::getApplication()->close();
	}
}

<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();

/**
 * Openstreetmap API object class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */

abstract class JOpenstreetmapObject
{

	/**
	 * @var    JRegistry  Options for the Openstreetmap object.
	 * @since  12.3
	 */
	protected $options;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  12.3
	 */
	protected $client;

	/**
	 * @var JOpenstreetmapOauth The OAuth client.
	 * @since 12.3
	 */
	protected $oauth;

	/**
	 * Constructor.
	 *
	 * @param   JRegistry  &$options  Openstreetmap options object.
	 * @param   JHttp      $client    The HTTP client object.
	 *
	 * @since   12.3
	 */
	public function __construct(JRegistry &$options = null, JHttp $client = null, $oauth = null)
	{
		$this->options = isset($options) ? $options : new JRegistry;
		$this->client = isset($client) ? $client : new JHttp($this->options);
		$this->oauth = $oauth;
	}

	/**
	 * Method to convert boolean to string.
	 *
	 * @param   boolean  $bool  The boolean value to convert.
	 *
	 * @return  string  String with the converted boolean.
	 *
	 * @since 12.3
	 */
	public function booleanToString($bool)
	{
		if ($bool)
		{
			return 'true';
		}
		else
		{
			return 'false';
		}
	}

	/**
	 * Get an option from the JOpenstreetmapObject instance.
	 *
	 * @param   string  $key  The name of the option to get.
	 *
	 * @return  mixed  The option value.
	 *
	 * @since   12.3
	 */
	public function getOption($key)
	{
		return $this->options->get($key);
	}

	/**
	 * Set an option for the JOpenstreetmapObject instance.
	 *
	 * @param   string  $key    The name of the option to set.
	 * @param   mixed   $value  The option value to set.
	 *
	 * @return  JOpenstreetmapObject  This object for method chaining.
	 *
	 * @since   12.3
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);

		return $this;
	}

	/**
	 * Method to send the request which does not require authentication.
	 *
	 * @param   string  $path     The path of the request to make
	 * @param   string  $method   The request method.
	 * @param   array   $headers  The headers passed in the request.
	 * @param   mixed   $data     Either an associative array or a string to be sent with the post request.
	 *
	 * @return  SimpleXMLElement  The XML response
	 *
	 * @since   12.3
	 * @throws  DomainException
	 */
	public function sendRequest($path, $method='GET', $headers = array(), $data='')
	{
		// Send the request.
		switch ($method)
		{
			case 'GET':
				$response = $this->client->get($path, $headers);
				break;
			case 'POST':
				$response = $this->client->post($path, $data, $headers);
				break;
		}

		if (strpos($response->body, 'redirected') !== false)
		{
			return $response->headers['Location'];
		}

		// Validate the response code.
		if ($response->code != 200)
		{
			$error = htmlspecialchars($response->body);

			throw new DomainException($error, $response->code);
		}

		return simplexml_load_string($response->body);
	}
}

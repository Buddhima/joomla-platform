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
 * Joomla Platform class for generating Openstreetmap API access token.
 *
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */
// TODO: MORE CAREFUL EDITING NEEDED
class JOpenstreetmap
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
	 * @var    JOpenstreetmapChangesets  Openstreetmap API object for changesets.
	 * @since  12.3
	 */
	protected $changesets;
	
	/**
	 * @var    JOpenstreetmapElements  Openstreetmap API object for elements.
	 * @since  12.3
	 */
	protected $elements;
		
	/**
	 * Constructor.
	 *
	 * @param   JRegistry      $options  Openstreetmap options object.
	 * @param   JOpenstreetmapHttp  $client   The HTTP client object.
	 *
	 * @since   12.3
	 */
	public function __construct(JRegistry $options = null, JHttp $client = null)
	{
		$this->options = isset($options) ? $options : new JRegistry;
		$this->client  = isset($client) ? $client : new JHttp($this->options);
	
		// Setup the default API url if not already set.
		$this->options->def('api.url', 'http://api.openstreetmap.org');
		//$this->options->def('api.url', 'http://api06.dev.openstreetmap.org');
	}
	
	/**	
	 *
	 * @param   string  $name  Name of property to retrieve
	 *
	 * @return  JOpenstreetmapObject  Openstreetmap API object .
	 *
	 * @since   12.3
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'changesets':
				if ($this->changesets == null)
				{
					$this->changesets = new JOpenstreetmapChangesets($this->options, $this->client);
				}
				return $this->changesets;
	
			case 'elements':
				if ($this->elements == null)
				{
					$this->elements = new JOpenstreetmapElements($this->options, $this->client);
				}
				return $this->elements;				
		}
	}
	
	/**
	 * Get an option from the JOpenstreetmap instance.
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
	 * Set an option for the Openstreetmap instance.
	 *
	 * @param   string  $key    The name of the option to set.
	 * @param   mixed   $value  The option value to set.
	 *
	 * @return  JOpenstreetmap  This object for method chaining.
	 *
	 * @since   12.3
	 */
	public function setOption($key, $value)
	{
		$this->options->set($key, $value);
	
		return $this;
	}	
}

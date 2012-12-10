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

class JOpenstreetmapGps extends JOpenstreetmapObject
{
	public function retrieveGps($left,$bottom,$right,$top,$page=0)
	{
		// Set the API base
		$base = 'trackpoints?bbox='.$left.','.$bottom.','.$right.','.$top.'&page='.$pageNumber;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string;
	}
	
	public function uploadTrace($file, $description, $tags, $public, $visibility)
	{
		
		// Set parameters.
		$parameters = array(
				'file' => $file,
				'description' => $description,
				'tags' => $tags,
				'public' => $public,
				'visibility' => $visibility
		);
		
		// Set the API base
		$base = 'gpx/create';
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		$header['Content-Type'] = 'multipart/form-data';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'POST', $parameters, $xml, $header);
		
		
		
		return $response;
	}
	
	public function downloadTraceDetails($id)
	{
		
		// Set the API base
		$base = 'gpx/'.$id.'/details';
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string;
	}
	
	public function downloadTraceData($id)
	{
	
		// Set the API base
		$base = 'gpx/'.$id.'/data';
	
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
	
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());
	
		$xml_string= simplexml_load_string ( $response->body );
	
		return $xml_string;
	}
}
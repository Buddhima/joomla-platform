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
 * Openstreetmap API Gps class for the Joomla Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */
class JOpenstreetmapGps extends JOpenstreetmapObject
{
	/**
	 * Method to retrieve GPS points
	 * 
	 * @param	float		$left		left boundary
	 * @param	float		$bottom		bottom boundary
	 * @param	float		$right		right boundary
	 * @param	float		$top		top boundary
	 * @param	int			$page		page number
	 * 
	 * @return	array	The xml response containing GPS points
	 * 
	 * @since	12.3
	 */
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
	
	/**
	 * Method to upload GPS Traces
	 * 
	 * @param	string		$file				file name that contains trace points
	 * @param	string		$description		description on trace points
	 * @param	string		$tags				tags for trace
	 * @param	int			$public				1 for public, 0 for private
	 * @param	string		$visibility			One of the following: private, public, trackable, identifiable
	 * 
	 * @return	JHttpResponse the response
	 * 
	 * @since	12.3
	 */
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
	
	/**
	 * Method to download Trace details
	 * 
	 * @param	int 		$id					trace identifier
	 * 
	 * @return	array	The xml response
	 * 
	 * @since	12.3
	 */
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
	
	/**
	 * Method to download Trace data
	 * 
	 * @param	int			$id					trace identifier
	 * 
	 * @return	array	The xml response
	 * 
	 * @since	12.3
	 */
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
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

class JOpenstreetmapUser extends JOpenstreetmapObject
{
	public function getDetails($oauth)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'user/details';
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', $parameters);
		
		return $response->body;
	}
	
	public function getPreferences($oauth)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'user/preferences';
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', $parameters);
		
		return $response->body;
	}
	
	public function replacePreferences($oauth, $preferences)
	{
		$token = $oauth->getToken();
	
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
	
		// Set the API base
		$base = 'user/preferences';
	
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
	
		// Create a list of preferences
		$preference_list='';
		if(!empty($preferences))
		{
			foreach ($preferences as $key=>$value){
				
				$preference_list.='<preference k="'.$key.'" v="'.$value.'"/>';
								
			}
		}
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
			<osm version="0.6" generator="JOpenstreetmap">
				<preferences>'
				.$preference_list.
				'</preferences>
			</osm>';		
		
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);
	
		return $response->body;
	}
	
	public function changePreference($oauth, $key, $preference)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'user/preferences/'.$key;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $preference);
		
		return $response->body;
	}
}
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
class JOpenstreetmapChangesets extends JOpenstreetmapObject
{
	/**
	 * Creates a changeset
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $xml
	 */
	public function createChangeset($oauth, $changesets=array())
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key'],
				'oauth_token_secret'=>$token['secret']
		);
		
		
		// Set the API base
		$base = 'changeset/create' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">';
		if(!empty($changesets))
		{	
			// Create Changeset element for every changeset		
			foreach($changesets as $tags)
			{
				$xml.='<changeset>';
				$tag_list='';
				if(!empty($tags))
				{
					// Create a list of tags for each changeset
					foreach ($tags as $key=>$value){
						
						$xml.='<tag k="'.$key.'" v="'.$value.'"/>';
						
					}
					
				}				
				$xml.='</changeset>';
			}
		}
		
		$xml.='</osm>';
		
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);
		
		return $response->body;		
		
	}
	
	/**
	 * Read a changeset
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $id
	 * @return associated array with details about a changeset
	 * 
	 * $changeset=$osm->changesets;	$result=$changeset->readChangeset($oauth, '20');
	 */
	public function readChangeset($oauth, $id)
	{

		// Set the API base
		$base = 'changeset/'.$id ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->changeset;
		
	}
	
	/**
	 * Update a changeset
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $xml
	 * @param unknown_type $id
	 */
	public function updateChangeset($oauth, $id, $tags=array() )
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'changeset/'.$id ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Create a list of tags to update changeset
		$tag_list='';
		if(!empty($tags))
		{
			foreach ($tags as $key=>$value){
				
				$tag_list.='<tag k="'.$key.'" v="'.$value.'"/>';
								
			}
		}
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">
  				<changeset>'
   				 .$tag_list.
  				'</changeset>  
			</osm>'; 
		
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->changeset;
	}
	
	/**
	 * Close a changeset
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $id
	 * No value will return
	 */
	public function closeChangeset($oauth, $id)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'changeset/'.$id.'/close' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		$header['format'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $header);		
		
	}
	
	/**
	 * Download a changeset
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $id
	 * @return associated array contain all modifications done to changeset
	 * $result=$changeset->downloadChangeset($oauth, '20');		print_r($result[2]);
	 */
	public function downloadChangeset($oauth, $id)
	{
				
		// Set the API base
		$base = 'changeset/'.$id.'/download' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
				
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET',  array());
		
		$xml_string= simplexml_load_string ( $response->body );
						
		return $xml_string->create;
	}
	
	/**
	 * Expand the bounding box of a changeset
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $xml
	 * @param unknown_type $id
	 * @return mixed
	 */
	public function expandBBoxChangeset($oauth, $id, $nodes)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'changeset/'.$id.'/expand_bbox' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Create a list of tags to update changeset
		$node_list='';
		if(!empty($nodes))
		{
			foreach ($nodes as $node){
		
				$node_list.='<node lat="'.$node[0].'" lon="'.$node[1].'"/>';
		
			}
		}
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">
				<changeset>'
				.$node_list.
				'</changeset>
			</osm>';
		 
		
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'POST', $parameters, $xml, $header);
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->changeset;
	}
	
	/**
	 * Query on changesets -to be implemented
	 *  
	 * @param unknown_type $oauth
	 * @param unknown_type $bbox
	 * @param unknown_type $userId
	 * @param unknown_type $displayName
	 * @param unknown_type $time
	 * @param unknown_type $state
	 */
	public function queryChangeset($oauth, $param)
	{
		// Set the API base
		$base = 'changesets/'.$param ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET',  array());
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->osm;
	}
	
	/**
	 * Upload a diff to a changeset - to be implemented
	 * 
	 * @param unknown_type $oauth
	 * @param unknown_type $xml
	 * @param unknown_type $id
	 */
	public function diffUploadChangeset($oauth, $xml, $id)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'changeset/'.$id.'/upload' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'POST', $parameters, $xml, $header);
		
		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->diffResult;
	}
}
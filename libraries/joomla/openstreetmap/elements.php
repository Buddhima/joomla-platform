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
class JOpenstreetmapElements extends JOpenstreetmapObject
{

	
	
	// expecting a associated array for $tags eg:	$tags=array("A"=>"Apple","B"=>"Ball");
	public function createNode($oauth,$changeset,$latitude,$longitude,$tags)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'node/create' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		$tag_list="";
		
		// Create XML node
		if(!empty($tags))
		{
			
			foreach ($tags as $key=>$value){
				
				$tag_list.='<tag k="'.$key.'" v="'.$value.'"/>';
								
			}
			
		}
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">
				<node changeset="'.$changeset.'" lat="'.$latitude.'" lon="'.$longitude.'">'
					.$tag_list.
				'</node>
				</osm>';
		
				
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);
		
		return $response->body;	
			
	}
	
	
	
	
	
	// tags and nds expects associated arrays ;for $tags eg:	$tags=array("A"=>"Apple","B"=>"Ball"); & for $nds eg:$nds=array("A","Apple","B","Ball");
	public function createWay($oauth,$changeset,$tags,$nds)
	{
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
		
		// Set the API base
		$base = 'node/create' ;
		
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		$tag_list="";
		
		// Create XML node
		if(!empty($tags))
		{				
			foreach ($tags as $key=>$value){		
				$tag_list.='<tag k="'.$key.'" v="'.$value.'"/>';		
			}				
		}
		
		$nd_list="";
		
		
		if(!empty($nds))
		{		
			foreach ($nds as $value){		
				$nd_list.='<nd ref="'.$value.'"/>';		
			}		
		}
		
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">
				<way changeset="'.$changeset.'">'
					.$tag_list
					.$nd_list.
				'</way>
			</osm>';
		
		
		$header['Content-Type'] = 'text/xml';
		
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);
		
		return $response->body;
		
	}
	
	// create ways
	// expecting a associated array for $tags eg:	$tags=array("A"=>"Apple","B"=>"Ball"); $members=array(array("type"=>"node","role"=>"stop","ref"=>"123"),array("type"=>"way","ref"=>"123"))
	public function createRelation($oauth,$changeset,$tags,$members)
	{
		$token = $oauth->getToken();
	
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
	
		// Set the API base
		$base = 'node/create' ;
	
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
	
		$tag_list="";
	
		// Create XML node
		if(!empty($tags))
		{				
			foreach ($tags as $key=>$value){	
				$tag_list.='<tag k="'.$key.'" v="'.$value.'"/>';	
			}				
		}
		
		// members 
		$member_list="";
		if(!empty($members))
		{
			foreach ($tags as $member){
				if($member['type']=="node")
				{
					$member_list.='<member type="'.$member['type'].'" role="'.$member['role'].'" ref="'.$member['ref'].'"/>';
				}
				else if($member['type']=="way")
				{
					$member_list.='<member type="'.$member['type'].'" ref="'.$member['ref'].'"/>';
				}			
				
			}
		}
		
	
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">
				<relation relation="'.$changeset.'" >'
					.$tag_list
					.$member_list.
				'</relation>
			</osm>';
	
	
		$header['Content-Type'] = 'text/xml';
	
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);
	
		return $response->body;
		
	}
	
	

	/**
	 * Read an Element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $id
	 * @return - array of details on given element
	 * $result=$element->readElement($oauth,'relation', '56688');		print_r($result);
	 */
	public function readElement($oauth, $element, $id)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}

		// Set the API base
		$base = $element.'/'.$id ;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->$element;
	}

	/**
	 * Update an Element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $xml
	 * @param unknown_type $id
	 */
	public function updateElement($oauth, $element, $xml, $id)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}

		$token = $oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = $element.'/'.$id ;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters, $xml, $header);

		return $response->body;

	}

	/**
	 * Delete an element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $xml
	 * @param unknown_type $id
	 * @return void|unknown
	 */
	public function deleteElement($oauth, $element, $id, $version, $changeset, $latitude=null, $longitude=null)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}

		$token = $oauth->getToken();

		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);

		// Set the API base
		$base = $element.'/'.$id ;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;
		
		//create xml
		$xml='<?xml version="1.0" encoding="UTF-8"?>
<osm version="0.6" generator="JOpenstreetmap">
				<'.$element.' id="'.$id.'" version="'.$version.'" changeset="'.$changeset.'"';
		
		if(!empty($latitude)&&!empty($longitude))
		{
			$xml.=' lat="'.$latitude.'" lon="'.$longitude.'"';
		}
		$xml.='/></osm>';

		$header['Content-Type'] = 'text/xml';

		// Send the request.
		$response = $oauth->oauthRequest($path, 'DELETE', $parameters, $xml, $header);

		return $response->body;
	}

	/**
	 * Get history of an element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $id
	 * Array consists of history of an element (with different versions)
	 * $result=$element->historyOfElement($oauth,'relation', '56688');	print_r($result[1]);
	 */
	public function historyOfElement($oauth, $element, $id)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}

		// Set the API base
		$base = $element.'/'.$id.'/history' ;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;
				
		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->$element;
	}

	/**
	 * Get details about a version of an element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $id
	 * @param unknown_type $version
	 */
	public function versionOfElement($oauth, $element, $id ,$version)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}

		// Set the API base
		$base = $element.'/'.$id.'/'.$version ;

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->$element;
	}

	/**
	 * Get data about multiple ids of an element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element - use plural word
	 * @param unknown_type $id
	 * @param unknown_type $params - Comma separated list ids belongto type $element
	 * @return 
	 * Returns an array of required elements 
	 * $result=$element->multiFetchElements($oauth,'nodes', '123,456,789');		print_r($result[2]);
	 */
	public function multiFetchElements($oauth, $element, $params)
	{
		if($element!='nodes' && $element!='ways' && $element!='relations'){
			return;
		}

		//get singular word
		$single_element=substr($element, 0,strlen($element)-1);
		
		// Set the API base
		$base = $element.'?'.$element."=".$params ; //$params is a string with comma seperated values

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->$single_element;
	}

	/**
	 * Get relations for an element [node|way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $id
	 * @return void|mixed
	 * get relatons for $element
	 * $result=$element->relationsForElement($oauth,'node', '123');		print_r($result);
	 */
	public function relationsForElement($oauth, $element, $id)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}

		// Set the API base
		$base = $element.'/'.$id.'/relations';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->$element;
	}

	/**
	 * Get ways for a Node element
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $id
	 * @return mixed
	 * $result=$element->waysForNode($oauth, '123');	print_r($result);
	 */
	public function waysForNode($oauth, $id)
	{		
		// Set the API base
		$base = 'node/'.$id.'/ways';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->way;
	}

	/**
	 * Get full information about an element [way|relation]
	 *
	 * @param unknown_type $oauth
	 * @param unknown_type $element
	 * @param unknown_type $id
	 * @return void|mixed
	 * $result=$element->fullElement($oauth, 'way','123');	print_r($result[3]);
	 */
	public function fullElement($oauth, $element, $id)
	{
		if($element!='way' && $element!='relation'){
			return;
		}

		// Set the API base
		$base = $element.'/'.$id.'/full';

		// Build the request path.
		$path = $this->getOption('api.url') . $base;

		// Send the request.
		$response = $oauth->oauthRequest($path, 'GET', array());

		$xml_string= simplexml_load_string ( $response->body );
		
		return $xml_string->node;
	}
	
	public function redaction($oauth, $element, $id, $version, $redaction_id)
	{
		if($element!='node' && $element!='way' && $element!='relation'){
			return;
		}
		
		$token = $oauth->getToken();
		
		// Set parameters.
		$parameters = array(
				'oauth_token' => $token['key']
		);
	
		// Set the API base
		$base = $element.'/'.$id.'/'.$version.'/redact?redaction='.$redaction_id;
	
		// Build the request path.
		$path = $this->getOption('api.url') . $base;
	
		// Send the request.
		$response = $oauth->oauthRequest($path, 'PUT', $parameters);
	
		$xml_string= simplexml_load_string ( $response->body );
	
		return $xml_string->node;
	}
}
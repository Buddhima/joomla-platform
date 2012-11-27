<?php
// We are a valid Joomla entry point.
define('_JEXEC', 1);

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));

// Maximise error reporting.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap the application.
require dirname(__FILE__).'/tests/bootstrap.php';

class OsmApp extends JApplicationWeb
{
	/**
	 * Display the application.
	 */
	function doExecute(){
		$key = "8DVVqjUxRiruXsMYZegClYbWODIMoKZxLl8w9pXR";
		$secret = "HvZkpFOsyq9oD7GEHYASyRgzKasRAMsvQ53Qb483";
		

		$option = new JRegistry;
		$option->set('consumer_key', $key);
		$option->set('consumer_secret', $secret);
		$option->set('sendheaders', true);

		$oauth = new JOpenstreetmapOauth($option);

		
		//$access_token = array('key' => '617544537-uMhDHjkCPGbgsb8NASkyWOfQj6wkIGWNjtZOIxDX', 'secret' => 'x9VpWp0tGK7q7lIlTyij7c0kfpRKWEWNJo2daPqHU8');
		//$oauth->setToken($access_token);
		$new_token = $oauth->authenticate();
		
		$oauth->setToken($new_token);
		

		$osm=new JOpenstreetmap();
		$changeset= $osm ->changesets;
// 		$result = $changeset -> readChangeset($oauth, '1');
		
// 		print_r($result);
// 		echo '<br />';
		
		//$element=$osm->elements;
		//$result= $element->createNode($oauth, '1', '34', '54', array("A"=>"Apple","B"=>"Ball"));
		
		
		$changesets = array
		(
// 				array
// 				(
// 						"A"=>"Apple",
// 						"B"=>"Ball",
// 						"c"=>"Call"
// 				),
// 				array
// 				(
// 						"B"=>"Ball"
// 				),
				array
				(
						"comment"=>"my changeset comment",
						"created_by"=>"JOSM/1.5 (5581 en)"
				),
				array
				(
						"A"=>"Apple",
						"F"=>"Apple",
						"B"=>"Ball"
				)
		);
		
		$result = $changeset ->createChangeset($oauth,$changesets);
		
		print_r($result);
		echo '<br />';
		
		//$result = $changeset ->updateChangeset($oauth, '1',$tags);
		
// 		print_r($result);
// 		echo '<br />';
		
// 		$node_list=array(array(4,5),array(6,7));
// 		$result = $changeset ->expandBBoxChangeset($oauth,'1',$node_list);
// 		print_r('DDDDPPPPP');
		//print_r($new_token);

	}
}

$web = JApplicationWeb::getInstance('OsmApp');
JFactory::$application = $web;

$session = JFactory::getSession();
if($session->isActive() == false){
	$session->initialise(JFactory::getApplication()->input);
	$session->start();
}

// Run the application
$web->execute();
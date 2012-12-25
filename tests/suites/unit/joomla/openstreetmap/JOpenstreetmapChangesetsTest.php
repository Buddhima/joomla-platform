<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JOpenstreetmapChangesets.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @since       12.3
 */
class JOpenstreetmapChangesetsTest extends TestCase
{
	/**
	 * @var    JRegistry  Options for the Openstreetmap object.
	 * @since  12.3
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 * @since  12.3
	 */
	protected $client;

	/**
	 * @var    JInput The input object to use in retrieving GET/POST data.
	 * @since  12.3
	 */
	protected $input;

	/**
	 * @var    JOpenstreetmapChangesets Object under test.
	 * @since  12.3
	 */
	protected $object;

	/**
	 * @var    JOpenstreetmapOauth  Authentication object for the Openstreetmap object.
	 * @since  12.3
	 */
	protected $oauth;

	/**
	 * @var    string  Sample JSON string.
	 * @since  12.3
	 */
	protected $sampleString = '<osm><changeset></changeset></osm>';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  12.3
	 */
	protected $errorString = '{"error":"Generic error"}';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$key = "app_key";
		$secret = "app_secret";
			
		$access_token = array('key' => 'token_key', 'secret' => 'token_secret');

		$this->options = new JRegistry;
		$this->input = new JInput;
		$this->client = $this->getMock('JHttp', array('get', 'post', 'delete', 'put'));
		$this->oauth = new JOpenstreetmapOauth($this->options, $this->client, $this->input);
		$this->oauth->setToken($access_token);

		$this->object = new JOpenstreetmapChangesets($this->options, $this->client, $this->oauth);

		$this->options->set('consumer_key', $key);
		$this->options->set('consumer_secret', $secret);
		$this->options->set('sendheaders', true);
	}

	/**
	 * Tests the createChangeset method
	 *
	 * @return  array
	 *
	 * @since   12.3
	 */
	public function testCreateChangeset()
	{
		$changeset = array
		(
				array
				(
						"comment"=>"my changeset comment",
						"created_by"=>"JOSM/1.0 (5581 en)"
				),
				array
				(
						"A"=>"Apple",
						"F"=>"Apple",
						"B"=>"Ball"
				)
		);

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

// 		$path = 'http://api.openstreetmap.org/api/0.6/changeset/create';
		$path = 'changeset/create';

		$this->client->expects($this->once())
		->method('put')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->createChangeset($changeset),
				$this->equalTo($this->sampleString)
		);
	}

	/**
	 * Tests the createChangeset method - failure
	 *
	 * @return  void
	 *
	 *
	 * @since   12.3
	 * @expectedException DomainException
	 */
	public function testCreateChangesetFailure()
	{
		$changeset = array
		(
				array
				(
						"comment"=>"my changeset comment",
						"created_by"=>"JOSM/1.0 (5581 en)"
				),
				array
				(
						"A"=>"Apple",
						"F"=>"Apple",
						"B"=>"Ball"
				)
		);

		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'changeset/create';

		$this->client->expects($this->once())
		->method('put')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->createChangeset($changeset);
	}

	/**
	 * Tests the readChangeset method
	 *
	 * @return  void
	 *
	 * @since   12.3
	 */
	public function testReadChangeset()
	{
		$id = '14153708';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		$path = 'changeset/'.$id;

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->readChangeset($id),
				$this->equalTo(new SimpleXMLElement(''))
		);
	}
}
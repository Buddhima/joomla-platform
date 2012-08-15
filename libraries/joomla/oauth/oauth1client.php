<?php
/**
 * @package     Joomla.Platform
 * @subpackage  OAuth
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die();
jimport('joomla.environment.response');

/**
 * Joomla Platform class for interacting with an OAuth 1.0a server.
 *
 * @package     Joomla.Platform
 * @subpackage  OAuth
 *
 * @since       12.2
 */
abstract class JOauth1Client extends JOauth1aClient
{
	

	/**
	 * Method to for the oauth flow.
	 *
	 * @return void
	 *
	 * @since  12.2
	 *
	 * @throws DomainException
	 */
	public function auth()
	{
		// Already got some credentials stored?
		if ($this->token)
		{
			$response = $this->verifyCredentials();

			if ($response)
			{
				return $this->token;
			}
			else
			{
				$this->token = null;
			}
		}

		
			// Generate a request token.
			$this->_generateRequestToken();

			// Authenticate the user and authorise the app.
			$this->_authorise();
		
		// Callback
		
			$session = JFactory::getSession();

			// Get token form session.
			$this->token = array('key' => $session->get('key', null, 'oauth_token'), 'secret' => $session->get('secret', null, 'oauth_token'));

			// Verify the returned request token.
			if (strcmp($this->token['key'], $this->input->get('oauth_token')) !== 0)
			{
				throw new DomainException('Bad session!');
			}

		
			// Generate access token.
			$this->_generateAccessToken();

			// Return the access token.
			return $this->token;		
	}

	/**
	 * Method used to get a request token.
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	private function _generateRequestToken()
	{
		// Set the callback URL. tTODO: no call back should be setted - how request for Requst Token change?
		if(isset($this->getOption('callback')))	
		{	
			$parameters = array(
				'oauth_callback' => $this->getOption('callback')
			);
		}

		// Make an OAuth request for the Request Token.
		$response = $this->oauthRequest($this->getOption('requestTokenURL'), 'POST', $parameters); // TODO: parameter only has 'callback' why?

		parse_str($response->body, $params);
		
		// Save the request token.
		$this->token = array('key' => $params['oauth_token'], 'secret' => $params['oauth_token_secret']);

		// Save the request token in session
		$session = JFactory::getSession();
		$session->set('key', $this->token['key'], 'oauth_token');
		$session->set('secret', $this->token['secret'], 'oauth_token');		
	}

	

	/**
	 * Method used to get an access token.
	 *
	 * @return void
	 *
	 * @since  12.2
	 */
	private function _generateAccessToken()
	{
		// Set the parameters.
		$parameters = array(
			'oauth_token' => $this->token['key']
		);

		// Make an OAuth request for the Access Token.
		$response = $this->oauthRequest($this->getOption('accessTokenURL'), 'POST', $parameters);

		parse_str($response->body, $params);

		// Save the access token.
		$this->token = array('key' => $params['oauth_token'], 'secret' => $params['oauth_token_secret']);
	}

	

	
}

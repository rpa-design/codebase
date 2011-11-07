<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase Core - Codebase factory class that is responsible for providing
 * instances of all Codebase Model classes.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
abstract class Codebase_Core
{

	/**
	 * Constant for the Codebase API URI, shouldn't be changing often so
	 * hardcoded here
	 */
	const API_URI = 'api3.codebasehq.com';

	/**
	 * An instance of Codebase_Request, used to make all requests to the
	 * Codebase API
	 *
	 * @var		Codebase_Request
	 * @access	private
	 */
	private $request = NULL;

	/**
	 * Constructor
	 *
	 * @param	string	$username	The Codebase username in the format of account/username
	 * @param	string	$api_key	The Codebase API key
	 * @access	public
	 */
	public function __construct($username, $api_key, $secure = TRUE)
	{
		if($secure)
		{
			$protocol = 'https';
		}
		else
		{
			$protocol = 'http';
		}
		$api_uri = $protocol.'://'.self::API_URI;

		$request = new Codebase_Request($api_uri, $username, $api_key);
		$this->set_request($request);
	}

	public function get_all_projects()
	{
		return Codebase_Model_Project::get_all_projects($this->get_request());
	}

	/**
	 * Getter for the $request property
	 *
	 * @access	public
	 * @return	Codebase_Request
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * Setter for the $request property
	 *
	 * @access	public
	 * @param	Codebase_Request	$request	An instance of Codebase_Request
	 */
	public function set_request(Codebase_Request $request) {
		$this->request = $request;
	}

}
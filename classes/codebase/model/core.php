<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase Model Core - Base class for all Codebase model classes, this layer
 * is responsible for taking care of making HTTP requests to the Codebase API
 * and handling the responses. Also implements automatic handling of
 * getters/setters for all properties.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @uses		Kohana_XML (https://github.com/ccazette/Kohana_XML)
 * @uses		Request_Client_External
 * @uses		Request
 * @uses		Response
 * @abstract
 */
abstract class Codebase_Model_Core
{
	/**
	 * Constructor
	 *
	 * @param	array	$inflate_data	The data to inflate the object with
	 * @access	public
	 */
	public function __construct(Codebase_Request $request, Array $inflate_data = NULL)
	{
		if($inflate_data !== NULL)
		{
			$this->inflate($inflate_data);
		}
	}

	/**
	 * Parses a response from the Codebase API and returns the results as an array,
	 * if the response contains any errors an exception is thrown
	 *
	 * @param	Response			$response
	 * @return	array
	 * @throws	Codebase_Exception
	 * @static
	 */
	protected static function parse_response(Response $response)
	{
		if ($response->status() >= 400)
		{
			throw new Codebase_Exception('HTTP '.$response->status().' error');
		}

		$parsed_result = new SimpleXMLElement($response->body());

		// check for errors?

		return $parsed_result;
	}

	/**
	 * Static getter for the $Codebase_uri static var
	 *
	 * @static
	 * @access	public
	 */
	public static function get_uri()
	{
		return self::$codebase_uri;
	}

	/**
	 * Static setter for the $Codebase_uri static var
	 *
	 * @param	string	$uri
	 * @static
	 * @access	public
	 */
	public static function set_uri($uri)
	{
		self::$codebase_uri = $uri;
	}

	/**
	 * Adds all data from the input array to the object via setters, designed
	 * as a quick way to build the object from data returned via the API. The
	 * keys of the input array must match the property names of the class.
	 *
	 * @param	array	$data
	 * @access	public
	 */
	public function inflate(SimpleXMLElement $data)
	{
		foreach($data as $property_name => $value)
		{
			// Codebase XML tags contain hyphens, replace them with underscores
			$property_name = str_replace('-', '_', $property_name);
			$method_name = 'set_'.$property_name;
			$this->{$method_name}($value);
		}
	}

	/**
	 * Magic __call method to catch all calls to undefined/non-visible
	 * functions.  Used to automatically create getters and setters for all
	 * properties.  If this method catches a call for a method name that does
	 * not being with get_ or set_, or attempts to get or set a property that
	 * does not exist, an Exception will be thrown.
	 *
	 * @param	string	$name
	 * @param	array	$arguments
	 * @return	mixed
	 * @access	public
	 * @throws	Exception
	 */
	public function __call($name, $arguments)
	{
		$getter_regex_pattern = '/get_([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*.)/';
		$setter_regex_pattern = '/set_([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*.)/';
		$matches = array();

		if(preg_match($getter_regex_pattern, $name, $matches))
		{
			$property_name = $matches[1];

			if(!(property_exists($this, $property_name)))
			{
				throw new Exception('Attempted to get undefined property: '.$property_name);
			}

			return $this->$property_name;
		}
		elseif(preg_match($setter_regex_pattern, $name, $matches))
		{
			$property_name = $matches[1];
			$value = $arguments[0];

			if(!(property_exists($this, $property_name)))
			{
				throw new Exception('Attempted to set undefined property: '.$property_name);
			}

			$this->$property_name = $value;
		}
		else
		{
			throw new Exception('Call to undefined method: '.get_class($this).'::'.$name);
		}
	}

}
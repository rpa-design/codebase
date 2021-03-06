<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Codebase_Model_Milestone_Core class, an instance of this class represents a
 * Codebase status, this class also contains static methods used to make a
 * request to the Codebase API, parse the result and return an instance of
 * Codebase_Model_Milestone with the data returned from the API.
 *
 * @author		Jon Cotton <jon@rpacode.co.uk>
 * @copyright	(c) 2011 RPA Code
 * @version		1.0
 * @abstract
 */
class Codebase_Model_Milestone_Core extends Codebase_Model
{

	/**
	 * The properties of the milestone object as sepcified in the Codebase API
	 */
	protected $id = NULL;
	protected $name = NULL;
	protected $start_at = NULL;
	protected $deadline = NULL;
	protected $parent_id = NULL;
	protected $status = NULL;
	protected $responsible_user_id = NULL;

	/**
	 * The project that this milestone belongs to
	 *
	 * @var		Codebase_Model_Project
	 */
	protected $project = NULL;

	/**
	 * static function to return all milestones belonging to the specified project
	 *
	 * @param	Codebase_Request	$request
	 * @param	string				$project_permalink
	 * @return	array				A collection Codebase_Model_Milestone objects
	 * @static
	 * @access	public
	 */
	public static function get_milestones_for_project(Codebase_Request $request, $project_permalink)
	{
		$path = '/'.$project_permalink.'/milestones';

		// TODO: Shouldn't have to specify the child class here, should just be 'self' but need PHP 5.3 and Late Static Binding to achieve this
		return self::get_objects_for_path($request, 'Codebase_Model_Milestone', $path);
	}

	/**
	 * getter for the project property
	 *
	 * @return	Codebase_Model_Project
	 */
	public function get_project() {
		return $this->project;
	}

	/**
	 * setter for the project property
	 *
	 * @param	Codebase_Model_Project	$project
	 */
	public function set_project(Codebase_Model_Project $project) {
		$this->project = $project;
	}

	/**
	 * a function that is passed to PHP's usort function in order to determine
	 * the correct order of an array of milestone objects
	 *
	 * @param	Codebase_Model_Milestone	$a
	 * @param	Codebase_Model_Milestone	$b
	 * @return	type
	 * @static
	 */
	public static function sort(Codebase_Model_Milestone $a, Codebase_Model_Milestone $b)
	{
		$return_value = 0;

		$a_deadline = strtotime($a->get_deadline());
		$b_deadline = strtotime($b->get_deadline());

		if($a_deadline != $b_deadline)
		{
			$return_value = ($a_deadline < $b_deadline) ? -1 : 1;
		}

		return $return_value;
	}

}
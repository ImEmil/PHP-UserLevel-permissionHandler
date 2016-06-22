<?php

class vGroups
{
	protected $groups        = [];
	protected $specialGroups = [];
	private $defaultGroup    = 1;

	public function __construct()
	{
	}

	public function getGroups()
	{
		return $this->groups;
	}

	public function getSpecialGroups()
	{
		return $this->specialGroups;
	}

	public function addGroups($groups)
	{
		try
		{
			if(!is_array($groups))
				throw new Exception("Parameter \$groups must be a type of an array");

			foreach($groups as $groupKey => $group)
			{
				if(strpos($group, "#") !== false)
				{
					list($group, $description) = explode("#", $group);

					$this->groups[trim($group)] =
					[
						"group"       => $groupKey,
						"description" => trim($description)
					];
				}
				else
					$this->groups[trim($group)] = ["group" => $groupKey, "description" => "No description for this group"];
			}
		}
		catch(Exception $e)
		{
			die($e->getMessage() . "<hr><b>Trace:</b> " . $e->getTraceAsString());
		}
	}

	public function addSpecialGroups($specialGroups)
	{
		try
		{
			if(!is_array($specialGroups))
				throw new Exception("Parameter \$specialGroups must be a type of an array");

			foreach($specialGroups as $groupKey => $group)
			{
				if(strpos($group, "#") !== false)
				{
					list($group, $description) = explode("#", $group);

					$this->specialGroups[trim($group)] =
					[
						"special_group" => $groupKey,
						"description"   => trim($description)
					];
				}
				else
					$this->specialGroups[trim($group)] = ["special_group" => $groupKey, "description" => "No description for this group"];
			}
		}
		catch(Exception $e)
		{
			die($e->getMessage() . "<hr><b>Trace:</b> " . $e->getTraceAsString());
		}
	}
}

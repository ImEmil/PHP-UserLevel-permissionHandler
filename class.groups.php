<?php

class vGroups
{
	protected $groups = [];
	private $defaultGroup = 1;

	public function __construct($groups)
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

	public function getGroups()
	{
		return $this->groups;
	}
}

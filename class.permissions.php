<?php

class vPermissions
{
	private $groups; // Not in use. kek?
	private $specialGroups; // Not in use. kek?

	private $userRank			 = 1;
	private $specialRank         = 0;

	private $defaultGroup        = 1;
	private $defaultGroupSpecial = 1;

	private $permissions         = [];
	private $groups_permissions  = [];

	private $groupsKeys          = [];
	private $specialGroupKeys    = [];

	public function __construct($userRank, $specialRank = 0)
	{
		$this->userRank    = $userRank;
		$this->specialRank = $specialRank;
	}

	public function register(vGroups $group)
	{	
		foreach( $group->getGroups() as $groupName => $groupArray )
			$this->groupsKeys[$groupArray["group"]] = $groupName;

		foreach( $group->getSpecialGroups() as $groupName => $groupArray )
			$this->specialGroupKeys[$groupArray["special_group"]] = $groupName;
	}

	public function addPermissions(array $permissions)
	{
		foreach($permissions as $permission)
		{
			if(strpos($permission, "#") !== false)
			{
				list($name, $description) = explode("#", $permission);
				$this->permissions[trim($name)] =
				[
					"description"   => trim($description),
					"group"         => $this->defaultGroup,
					"group_special" => $this->defaultGroupSpecial
				];
			}
			else
			{
				$this->permissions[trim($permission)] =
				[
					"description"   => "No description for this permission",
					"group"         => $this->defaultGroup,
					"group_special" => $this->defaultGroupSpecial
				];
			}
		}
	}

	public function has($rule = null)
	{
		if(empty($rule))
			throw new Permissions_Exception("Missing rule content");

		if(!in_array($rule, array_keys($this->permissions)))
			throw new Permissions_Exception("Permission ({$rule}) is missing from the permissions list (" . join(", ", array_keys($this->permissions)) . ")");

		foreach($this->groups_permissions as $key => $data)
		{
			if($rule != $data["permission"])
				continue;

			if(in_array($this->userRank, array_values($data["group"])) || in_array($this->specialRank, array_values($data["group_special"])))
				return true;
		}

		return false;
	}

	public function setPermissionGroups(array $rules)
	{
		foreach($rules as $permission => $groupData)
		{
			if(!in_array($permission, array_keys($this->permissions)))
				throw new Permissions_Exception("Permission ({$permission}) is missing from the added permissions list (" . join(", ", array_keys($this->permissions)) . ")");

			if(is_array($groupData)) // has to be an array O_o
			{
				$permissionData = [];
				
				if(is_array($groupData["groups"]))
				{
					foreach($groupData["groups"] as $groupKey) // Loop through given groups and check if they exist
						if(!in_array($groupKey, array_keys($this->groupsKeys)))
							throw new Permissions_Exception("Group ({$groupKey}) couldn't be found within the defined groups (" . join(", ", array_keys($this->groupsKeys)) . ") [" . join(", ", $this->groupsKeys) . "]");
					
					$permissionData["group"] = $groupData["groups"];
				}
				else
					$permissionData["group"] = [$groupData["groups"]]; // Todo: Validate if exist like in the loop ^
				
				if(is_array($groupData["special_groups"]))
				{
					foreach($groupData["special_groups"] as $groupKey) // Loop through given groups and check if they're existing
						if(!in_array($groupKey, array_keys($this->specialGroupKeys)))
							throw new Permissions_Exception("SPECIAL Group ({$groupKey}) couldn't be found within the defined groups (" . join(", ", array_keys($this->specialGroupKeys)) . ") [" . join(", ", $this->specialGroupKeys) . "]");
					
					$permissionData["group_special"] = $groupData["special_groups"];
				}
				else
					$permissionData["group_special"] = [$groupData["special_groups"]]; // Todo: Validate if exist like in the loop ^

				$this->groups_permissions[] =
				[
					"permission"    => $permission,
					"group"         => $permissionData["group"],
					"group_special" => $permissionData["group_special"]
				];
			}
			else // Else throw error, must be array!
			{

			}
		}
	}

	public function printAll()
	{
		echo "<pre>";
		print_r(get_object_vars($this));
		echo "</pre>";
	}
}

class Permissions_Exception extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        if(!$message)
            throw new $this("Unknown: " . get_class($this));

        parent::__construct($message, $code, $previous);

        $debug = array_shift($this->getTrace());

        $error = <<<Err
        <!DOCTYPE html>
        <html>
        <head>
            <title>Permissions engine ERROR</title>
        </head>
        <body style="background:#fff;color:red;font-weight:bolder;">
            <h1>Permissions engine ERROR</h1>
            <p>
                <ul>
                    <li>File: <span style="color:coral;">{$this->getFile()}</span></i>
                    <li>Error thrown on line: <span style="color:coral;">{$this->getLine()}</span></i>
                    <li>Function found at line: <span style="color:coral;">{$debug['line']} in file {$debug['file']}</span></li>
                    <li>Function: (class)Permissions -> <span style="color:coral;">{$debug['function']}</span></li>
                </ul>
                <h3>
                    Error: <span style="color:black;">{$this->getMessage()}</span>
                    <br>
                    <span style="font-size:14px;font-weight:light;">
                    Trace string &raquo; <span style="color:coral;">{$this->getTraceAsString()}</span>
                    </span>
                </h3>
            </p>
        </body>
        </html>
Err;
        exit($error);
    }

}

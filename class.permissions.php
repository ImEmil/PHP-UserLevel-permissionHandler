<?php

class vPermissions
{
	private $groups;
	private $userRank;
	private $defaultGroup       = 1;
	private $permissions        = [];
	private $groups_permissions = [];
	private $groupsKeys			= [];

	public function __construct($userRank)
	{
		$this->userRank = $userRank;
	}

	public function registerGroups(vGroups $group)
	{
		$this->groups = $group;
	}

	public function addPermissions($permissions = array())
	{
		foreach($this->groups->getGroups() as $groupName => $groupArray)
			$this->groupsKeys[$groupArray["group"]] = $groupName;

		foreach($permissions as $rule)
		{
			if(strpos($rule, "#") !== false)
			{
				list($permission, $description) = explode("#", $rule);
				$this->permissions[trim($permission)] =
				[
					"description" => trim($description),
					"group"       => $this->defaultGroup
				];
			}
			else
				$this->permissions[trim($rule)] = ["description" => "No description for this permission", "group" => $this->defaultGroup];
		}
	}

	public function has($rule = null)
	{
		if(empty($rule))
			throw new Permissions_Exception("Missing rule content");

		if(!in_array($rule, array_keys($this->permissions)))
			throw new Permissions_Exception("Permission ({$rule}) is missing from the permissions list (" . join(", ", array_keys($this->permissions)) . ")");

		foreach($this->groups_permissions as $key => $permission) // Loop registered permissions and their groups
			foreach($this->groups->getGroups() as $groupName => $groupArray) // Loop thro all groups
				if($permission["group"] == $groupArray["group"]) // Is group defined within the groups class with the same name?
					if($rule == $permission["permission"] && $this->userRank == $permission["group"]) // Does the rule exist and does the current users uid/rank exist with the current group aliases?
						return true;

		return false;
	}

	public function setPermissionGroups($rules)
	{
		foreach($rules as $permission => $groupKey)
		{
			if(is_array($groupKey))
			{
				foreach($groupKey as $key)
				{
					if(!in_array($key, array_keys($this->groupsKeys)))
						throw new Permissions_Exception("Group ({$key}) couldn't be found within the defined groups (" . join(",", array_keys($this->groupsKeys)) . ") [" . join(",", $this->groupsKeys) . "]");

					$this->groups_permissions[] = ["permission" => $permission, "group" => $key];
				}
			}
			else
			{
				if(!in_array($groupKey, array_keys($this->groupsKeys)))
					throw new Permissions_Exception("Group ({$groupKey}) couldn't be found within the defined groups (" . join(",", array_keys($this->groupsKeys)) . ") [" . join(",", $this->groupsKeys) . "]");

				$this->groups_permissions[] = ["permission" => $permission, "group" => $groupKey];
			}
		}
	}

	public function printAll()
	{
		echo "<pre>";
		print_r($this->permissions);
		print_r($this->groups->getGroups());
		print_r($this->groups_permissions);
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

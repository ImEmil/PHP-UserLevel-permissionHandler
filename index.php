<?php
require_once("class.permissions.php");
require_once("class.groups.php");

$user_rank = 3;

$permissions = new vPermissions($user_rank); // Initialize with user rank
$groups = new vGroups([	// (rank_number) => "Rank_name # description"
	1 => "User # Given a default set of permissions",
	2 => "Expert # Given a minimal set of moderation permissions",
	3 => "Admin # Given full permissions"
]);

// TODO: Check if permissions exists, check if x exists
$permissions->registerGroups($groups);

$permissions->addPermissions([ // "Permission_name # description"
	"hk.login # Allows user to login",
	"ban.add # Can ban the user",
	"users.delete # Can delete the user",
	"users.edit",
	"admin.lel"
]);

$permissions->setPermissionGroups([	// "Permission_name" => [group_id/rank_number]
	"hk.login"     => [1, 2, 3],
	"ban.add"      => [1, 3],
	"users.delete" => [2, 3],
	"users.edit"   => 3,
	"admin.lel"    => 2
]);

#$permissions->printAll();

if($permissions->has("hk.login"))
	echo "has";
else
	echo "hasn't!";

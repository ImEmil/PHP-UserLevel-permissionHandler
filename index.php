<?php
require_once("class.permissions.php");
require_once("class.groups.php");

$user_rank = 3;
$vip_rank  = 7;

$permissions = new vPermissions($user_rank, $vip_rank); // Initialize with user rank and special rank (vip?)
$groups      = new vGroups;


$groups->addGroups([	// (rank_number) => "Rank_name # description"
	1 => "User # Given a default set of permissions",
	2 => "Expert # Given a minimal set of moderation permissions",
	3 => "Admin # Given full permissions"
]);

$groups->addSpecialGroups([
	6 => "Bronze VIP",
	7 => "Silver VIP",
	8 => "Gold VIP # Given a ***",
]);

// TODO: Add support for special groups permissions | !#!CHECK!#!
// TODO: Check if permissions exists, check if x exists | !#!CHECK!#!

$permissions->register($groups);

$permissions->addPermissions([ // "Permission_name # description"
	"hk.login # Allows user to login",
	"ban.add # Can ban the user",
	"users.delete # Can delete the user",
	"users.edit",
	"admin.lel"
]);

$permissions->setPermissionGroups([
	"hk.login" => [
		"groups"         => [1, 2, 3],
		"special_groups" => [6, 7]
	],
	"ban.add" => [
		"groups"         => [1, 2, 3],
		"special_groups" => 0
	],
	"users.delete" => [
		"groups"         => [1, 2, 3],
		"special_groups" => 0
	]
]);

#$permissions->printAll();

if($permissions->has("hk.login"))
	echo "has";
else
	echo "hasn't!";

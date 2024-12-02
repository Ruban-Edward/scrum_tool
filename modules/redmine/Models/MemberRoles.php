<?php
namespace Redmine\Models;

class MemberRoles extends RedmineBaseModel
{
    protected $table = "member_roles";
    protected $allowedFields = [
        "id",
        "member_id",
        "role_id",
        "inherited_from"
    ];
}

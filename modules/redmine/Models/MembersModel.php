<?php
namespace Redmine\Models;

class MembersModel
{
    protected $table = "members";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "user_id",
        "project_id",
        "created_on",
        "mail_notification"
    ];
}
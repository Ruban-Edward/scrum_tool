<?php
namespace Redmine\Models;

class IssueStatus extends RedmineBaseModel
{
    protected $table = "issue_statuses";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "name",
        "is_closed",
        "position",
        "default_done_ratio",
    ];
}
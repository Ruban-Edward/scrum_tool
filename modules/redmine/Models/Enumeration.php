<?php
namespace Redmine\Models;

class Enumeration extends RedmineBaseModel
{
    protected $table = 'enumerations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        "name", 
        "position", 
        "is_default", 
        "type",
        "active",
        "project_id",
        "parent_id",
        "position_name"
    ];
}
<?php
namespace Redmine\Models;

class CustomFieldRole extends RedmineBaseModel
{
    protected $table = "custom_fields_projects";

    protected $useAutoIncrement = false;
    
    protected $allowedFields = [	
        'custom_field_id',	
        'role_id'
    ];
}
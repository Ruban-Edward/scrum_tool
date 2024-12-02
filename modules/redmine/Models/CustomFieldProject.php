<?php
namespace Redmine\Models;

class CustomFieldProject extends RedmineBaseModel
{
    protected $table = "custom_fields_projects";

    protected $useAutoIncrement = false;
    
    protected $allowedFields = [	
        'custom_field_id',	
        'project_id'
    ];
}
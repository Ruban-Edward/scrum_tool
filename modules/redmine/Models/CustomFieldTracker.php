<?php
namespace Redmine\Models;

class CustomFieldTracker extends RedmineBaseModel
{
    protected $table = "custom_fields_trackers";

    protected $useAutoIncrement = false;
    
    protected $allowedFields = [	
        'custom_field_id',	
        'tracker_id'
    ];
}
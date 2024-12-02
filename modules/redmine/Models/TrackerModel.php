<?php
namespace Redmine\Models;

class TrackerModel extends RedmineBaseModel
{
    protected $table = "trackers";

    protected $primaryKey = "id";
    
    protected $allowedFields = [	
        'name',
        'description',
        'is_in_chlog',
        'position',
        'is_in_roadmap',
        'fields_bits',
        'default_status_id',
    ];
}
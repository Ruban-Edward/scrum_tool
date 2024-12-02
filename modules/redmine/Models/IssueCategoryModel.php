<?php
namespace Redmine\Models;

class IssueCategoryModel extends RedmineBaseModel
{
    protected $table = "issue_categories";

    protected $primaryKey = "id";
    
    protected $allowedFields = [	
        'project_id',
        'name',
        'assigned_to_id'
    ];
}
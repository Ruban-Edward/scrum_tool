<?php
namespace Redmine\Models;

class JournalDetailModel extends RedmineBaseModel
{
    protected $table = "journal_details";

    protected $primaryKey = "id";
    
    protected $allowedFields = [	
        'journal_id',
        'property',
        'prop_key',
        'old_value',
        'value'
    ];
}
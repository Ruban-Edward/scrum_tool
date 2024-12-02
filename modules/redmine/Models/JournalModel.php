<?php
namespace Redmine\Models;

class JournalModel extends RedmineBaseModel
{
    protected $table = "journals";

    protected $primaryKey = "id";
    
    protected $allowedFields = [	
        'journalized_id',
        'journalized_type',
        'user_id',
        'notes',
        'created_on',
        'private_notes'
    ];

    protected $validationRules = [
        'journalized_id'   => 'required|integer',
        'journalized_type' => 'required|max_length[255]',
        'user_id'          => 'required|integer',
        'created_on'       => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'journalized_id' => [
            'required' => 'The journalized ID is required.',
            'integer'  => 'The journalized ID must be an integer.'
        ],
        'journalized_type' => [
            'required'   => 'The journalized type is required.',
            'max_length' => 'The journalized type cannot exceed 255 characters.'
        ],
        'user_id' => [
            'required' => 'The user ID is required.',
            'integer'  => 'The user ID must be an integer.'
        ],
        'created_on' => [
            'required'  => 'The creation date is required.',
            'valid_date' => 'The creation date must be a valid date.'
        ]
    ];
    
}
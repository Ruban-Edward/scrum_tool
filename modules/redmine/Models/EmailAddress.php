<?php
namespace Redmine\Models;

class EmailAddress extends RedmineBaseModel
{ 
    protected $table = 'email_addresses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        "user_id",
        "address",
        "is_default",
        "notify",
        "created_on",
        "updated_on"
    ];

    protected $validationRules = [
        'user_id'   => 'required|integer',
        'address'   => 'required|valid_email|max_length[255]',
        'is_default' => 'required|boolean',
        'notify'    => 'required|boolean'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'The user ID is required.',
            'integer'  => 'The user ID must be an integer.'
        ],
        'address' => [
            'required'   => 'The email address is required.',
            'valid_email' => 'The email address must be a valid email.',
            'max_length' => 'The email address cannot exceed 255 characters.'
        ],
        'is_default' => [
            'required' => 'The default status is required.',
            'boolean'  => 'The default status must be a boolean value.'
        ],
        'notify' => [
            'required' => 'The notify status is required.',
            'boolean'  => 'The notify status must be a boolean value.'
        ]
    ];
    
}   
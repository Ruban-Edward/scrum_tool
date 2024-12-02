<?php

namespace Redmine\Models;

class Roles extends RedmineBaseModel
{
    protected $table = "roles";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "name",
        "position",
        "assignable",
        "builtin",
        "permissions",
        "issues_visibility",
        "users_visibility",
        "time_entries_visibility",
        "all_roles_managed",
        "settings",
    ];

    protected $validationRules = [
        'name'        => 'required|max_length[255]',
        'position'    => 'required|integer',
        'assignable'  => 'required|boolean',
        'builtin'     => 'required|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'The name is required.',
            'max_length' => 'The name cannot exceed 255 characters.'
        ],
        'position' => [
            'required' => 'The position is required.',
            'integer'  => 'The position must be an integer.'
        ],
        'assignable' => [
            'required' => 'The assignable field is required.',
            'boolean'  => 'The assignable field must be a boolean value.'
        ],
        'builtin' => [
            'required' => 'The builtin field is required.',
            'integer'  => 'The builtin field must be an integer.'
        ]
    ];
}

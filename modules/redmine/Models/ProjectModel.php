<?php

namespace Redmine\Models;

class ProjectModel extends RedmineBaseModel
{
    protected $table = "projects";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "name",
        "description",
        "homepage",
        "is_public",
        "parent_id",
        "created_on",
        "updated_on",
        "identifier",
        "status",
        "lft",
        "rgt",
        "inherit_members",
        "default_version_id",
        "default_assigned_to_id",
        "activity_report_settings",
        "indicator_left_top",
        "indicator_left_bottom",
        "indicator_right",
        "product_backlog_id"
    ];

    protected $validationRules = [
        'name'              => 'required|max_length[255]',
        'identifier'        => 'required|max_length[255]|alpha_numeric',
        'status'            => 'required|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'The name is required.',
            'max_length' => 'The name cannot exceed 255 characters.'
        ],
        'identifier' => [
            'required'    => 'The identifier is required.',
            'max_length'  => 'The identifier cannot exceed 255 characters.',
            'alpha_numeric' => 'The identifier can only contain alphanumeric characters.'
        ],
        'status' => [
            'required' => 'The status is required.',
            'integer'  => 'The status must be an integer.'
        ]
    ];
   

  
    //tasks sync


    public function getAllProductUsersFromRedmine()
    {
        // Define the SQL query to retrieve user and project mappings
        $query = "
        SELECT user_id as external_user_id, project_id as external_project_id
        FROM members";
        
        // Execute the query and retrieve results
        $result = $this->query($query);

        // Return the result as an array
        return $result->getResultArray();
    }

    /**
     * Retrieve all projects from the Redmine database.
     *
     * This method executes a SQL query to fetch project details including
     * project ID, name, created date, and updated date.
     *
     * @return array An array of project details.
     */
    public function getAllProductsFromRedmine()
    {
        // Define the SQL query to retrieve project details
        $sql = "SELECT 
                    id AS external_project_id,
                    name AS product_name,
                    parent_id as parent_id,
                    created_on as created_date,
                    updated_on as updated_date 
                FROM 
                    projects";
          
        // Execute the query
        $query = $this->query($sql);

        // Check if any rows were returned and return the result as an array
        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        }
    }

}
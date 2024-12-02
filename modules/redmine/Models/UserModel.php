<?php
namespace Redmine\Models;

class UserModel extends RedmineBaseModel
{
    protected $table = "users";
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'login',
        'hashed_password',
        'firstname',
        'lastname',
        'admin',
        'status',
        'last_login_on',
        'language',
        'auth_source_id',
        'created_on',
        'updated_on',
        'type',
        'identity_url',
        'mail_notification',
        'salt',
        'must_change_passwd',
        'passwd_changed_on',
        'parent_id',
        'lft',
        'rgt',
        'created_by_omniauth_saml'
    ];

    protected $validationRules = [
        'login' => 'required|max_length[255]',
        'hashed_password' => 'required|max_length[255]',
        'firstname' => 'required|max_length[255]',
        'lastname' => 'required|max_length[255]',
        'status' => 'required|integer'
    ];


    protected $validationMessages = [
        'login' => [
            'required' => 'The login is required.',
            'max_length' => 'The login cannot exceed 255 characters.'
        ],
        'hashed_password' => [
            'required' => 'The password is required.',
            'max_length' => 'The password cannot exceed 255 characters.'
        ],
        'firstname' => [
            'required' => 'The first name is required.',
            'max_length' => 'The first name cannot exceed 255 characters.'
        ],
        'lastname' => [
            'required' => 'The last name is required.',
            'max_length' => 'The last name cannot exceed 255 characters.'
        ],
        'status' => [
            'required' => 'The status is required.',
            'integer' => 'The status must be an integer.'
        ]
    ];

    /**
     * User::getUser()
     *
     * created by Stervin Richard
     * Home Controller user profile
     */

    public function getUser($username)
    {
        $sql = "SELECT 
                    u.id, 
                    u.login, 
                    u.firstname, 
                    u.lastname, 
                    u.hashed_password,
                    u.salt, 
                    u.admin,
                    e.address,
                    t.action,
                    t.value,
                    cv.value AS role 
                FROM 
                    users AS u     
                INNER JOIN 
                    email_addresses AS e 
                ON 
                    u.id = e.user_id 
                LEFT JOIN
                    tokens AS t 
                ON 
                    t.user_id = u.id and t.action = :api:
                INNER JOIN 
                    custom_values AS cv
                ON 
                    cv.customized_id = u.id
                WHERE 
                   # t.action = 'api'
                #AND
                    cv.custom_field_id = :designation:
                AND 
                    u.login = :username:
            ";
        $query = $this->query($sql, [
            'username' => $username,
            'api' => USER_API,
            'designation' => CUSTOM_FIELD['designation']
        ]);
        if ($query->getNumRows() > 0) {
            $user = $query->getResult();
            return $user;
        }
        return [];
    }

    /**
     * User::getUsers()
     *
     * created by Vishva
     * Home Controller user profile
     */

    public function getUsers()
        {
        $sql = "SELECT DISTINCT 
                m.user_id,
                concat(u.firstname,' ',u.lastname) as name,
                COALESCE(p2.id, p.id) AS project_id,
                COALESCE(p2.name, p.name) AS product
                FROM members m
                JOIN users u ON u.id = m.user_id
                JOIN projects p ON m.project_id = p.id
                LEFT JOIN projects p2 ON p.parent_id = p2.id";
        $query = $this->query($sql);
        if ($query->getNumRows() > 0) {
            $user = $query->getResultArray();
            return $user;
        }
    }

    public function syncAllUser(){
        $sql = "SELECT 
                    u.id, 
                    u.login, 
                    u.firstname, 
                    u.lastname, 
                    u.hashed_password,
                    u.salt, 
                    u.admin,
                    e.address,
                    t.action,
                    t.value,
                    cv.value AS role 
                FROM 
                    users AS u     
                INNER JOIN 
                    email_addresses AS e 
                ON 
                    u.id = e.user_id 
                LEFT JOIN
                    tokens AS t 
                ON 
                    t.user_id = u.id and t.action = :api:
                INNER JOIN 
                    custom_values AS cv
                ON 
                    cv.customized_id = u.id
                WHERE 
                    cv.custom_field_id = :designation:";
        $query = $this->query($sql,[
            'api' => USER_API,
            'designation' => CUSTOM_FIELD['designation']
        ]);
        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        }
        return [];
    }
}
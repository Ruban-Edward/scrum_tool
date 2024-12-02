<?php
namespace Redmine\Models;

class TimeEntry extends RedmineBaseModel
{
    protected $DBGroup = 'redmine';
    protected $table = "time_entries";
    protected $primaryKey = "id";
    protected $allowedFields = [
        "project_id",
        "author_id",
        "user_id",
        "issue_id",
        "hours",
        "comments",
        "activity_id",
        "spent_on",
        "tyear",
        "tmonth",
        "tweek",
    ];

    protected $validationRules = [
        'project_id' => 'required|integer',
        'author_id' => 'required|integer',
        'user_id' => 'required|integer',
        'issue_id' => 'required|integer',
        'hours' => 'required|numeric|greater_than[0]',
        'comments' => 'permit_empty|string|max_length[255]',
        'activity_id' => 'required|integer',
        'spent_on' => 'required|valid_date',
        'tyear' => 'required|integer|exact_length[4]',
        'tmonth' => 'required|integer|greater_than[0]|less_than_equal_to[12]',
        'tweek' => 'required|integer|greater_than[0]|less_than_equal_to[53]',
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'The project ID is required.',
            'integer' => 'The project ID must be an integer.',
        ],
        'author_id' => [
            'required' => 'The author ID is required.',
            'integer' => 'The author ID must be an integer.',
        ],
        'user_id' => [
            'required' => 'The user ID is required.',
            'integer' => 'The user ID must be an integer.',
        ],
        'issue_id' => [
            'required' => 'The issue ID is required.',
            'integer' => 'The issue ID must be an integer.',
        ],
        'hours' => [
            'required' => 'The number of hours is required.',
            'numeric' => 'The number of hours must be a number.',
            'greater_than' => 'The number of hours must be greater than 0.',
        ],
        'comments' => [
            'string' => 'The comments must be a string.',
            'max_length' => 'The comments cannot exceed 255 characters.',
        ],
        'activity_id' => [
            'required' => 'The activity ID is required.',
            'integer' => 'The activity ID must be an integer.',
        ],
        'spent_on' => [
            'required' => 'The date spent on is required.',
            'valid_date' => 'The date spent on must be a valid date.',
        ],
        'tyear' => [
            'required' => 'The year is required.',
            'integer' => 'The year must be an integer.',
            'exact_length' => 'The year must be exactly 4 digits long.',
        ],
        'tmonth' => [
            'required' => 'The month is required.',
            'integer' => 'The month must be an integer.',
            'greater_than' => 'The month must be greater than 0.',
            'less_than_equal_to' => 'The month must be 12 or less.',
        ],
        'tweek' => [
            'required' => 'The week is required.',
            'integer' => 'The week must be an integer.',
            'greater_than' => 'The week must be greater than 0.',
            'less_than_equal_to' => 'The week must be 53 or less.',
        ],
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_on';
    protected $updatedField  = 'updated_on';

    public function getSumTaskHoursPerSprint($sprintData)
    {
        $db = \Config\Database::connect('redmine');

        // SQL query to get total task hours per sprint 
        $result = [];
        foreach ($sprintData as $sprint) {
            $sprintVersion = $sprint['sprint_version'];
            $userStoryIds = explode(',', $sprint['user_story_ids']);
            
            $userStoryIdsStr = implode(",", $userStoryIds);
            $sql = "SELECT 
                        cv.customized_id as issue_id, 
                        ROUND(SUM(te.hours),2) as total_hours,
                        count(DISTINCT user_id) as resource_count
                    FROM
                        custom_values cv
                    JOIN 
                        time_entries te ON cv.customized_id = te.issue_id
                    WHERE 
                        cv.custom_field_id = 52
                        AND cv.value IN ($userStoryIdsStr)
                    GROUP BY
                         cv.value";
            
            $query = $db->query($sql);
            $taskHours = $query->getResultArray();
        
            $totalHours = array_sum(array_column($taskHours, 'total_hours'));
            $resource_count = array_sum(array_column($taskHours, 'resource_count'));
            
            if($resource_count == 0){
                $resource_count = 1;
            }
            $result[$sprintVersion] =round($totalHours / $resource_count, 1);
        }
        
        return $result;
    }


    public function getSprintHours(array $issueIds): array
    {
        if (empty($issueIds)) {
            return [];
        }

        // Connect to the Redmine database
        $db = \Config\Database::connect('redmine');
        $placeholders = implode(',', array_fill(0, count($issueIds), '?'));
        
        // SQL query to get total hours per day of a sprint for given issue IDs
        $query = "SELECT
                    DATE(te.spent_on) AS spent_date,
                    ROUND(SUM(te.hours),1) AS daily_actual_hours,
                    count(DISTINCT user_id) as resource_count
                FROM
                    time_entries te
                WHERE
                    te.issue_id IN ($placeholders)
                GROUP BY
                    DATE(te.spent_on)
                ORDER BY
                    DATE(te.spent_on)"; 
        try {
            $result = $db->query($query, $issueIds);
            return $result->getResultArray();
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Database query failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserStoryTaskHours(array $params): array
    {
        if (empty($params)) {
            return [];
        }
        
        $userStoryIds = $params['userStoryIds'];
        if(empty($userStoryIds)){
            return [];
        }
        
        // SQL query to get total hours for userstories of a sprint for given user story IDs
        $query = "SELECT 
                        cv.value as user_story, 
                        ROUND(SUM(te.hours),1) as total_hours
                    FROM
                        custom_values cv
                    JOIN 
                        time_entries te ON cv.customized_id = te.issue_id
                    WHERE 
                        cv.custom_field_id = 52 AND 
                        cv.value IN ($userStoryIds) AND
                        te.project_id = ?
                    GROUP BY
                        cv.value";

            $result = $this->query($query,$params['productId']);
            return $result->getResultArray();
    }

    /** 
    * @author Rama Selvan
    */
    public function getActivityId($name)
    {
        $sql = "SELECT 
                    id 
                FROM 
                    enumerations 
                WHERE 
                    name = :name:
                    AND active = 1
                    AND project_id IS NULL";
    
        $result = $this->db->query($sql, ['name' => $name])->getResult();
    
        if (!empty($result)) {
            return $result[0]->id;
        }
    
        return null;
    }
    
    /** 
    * @author Rama Selvan
    */
    public function entryTimeLog($args)
    {
        $sql = "INSERT INTO time_entries (
                    project_id, author_id, user_id, issue_id, 
                    hours, comments, activity_id, spent_on, 
                    tyear, tmonth, tweek, created_on, 
                    updated_on
                ) 
                VALUES 
                    (
                        :project_id:, :author_id:, :user_id:, 
                        :issue_id:, :hours:, :comments:, 
                        :activity_id:, :spent_on:, :tyear:, 
                        :tmonth:, :tweek:, :created_on:, 
                        :updated_on:
                    );";
        
        $result = $this->query($sql, [
            'project_id' => $args['project_id'],
            'author_id' => $args['author_id'],
            'user_id' => $args['user_id'],
            'issue_id' => $args['issue_id'],
            'hours' => $args['hours'],
            'comments' => $args['comments'],
            'activity_id' => $args['activity_id'],
            'spent_on' => $args['spent_on'],
            'tyear' => $args['tyear'],
            'tmonth' => $args['tmonth'],
            'tweek' => $args['tweek'],
            'created_on' => $args['created_on'],
            'updated_on' => $args['updated_on']
        ]);
        
        return $result;
    }
    
}
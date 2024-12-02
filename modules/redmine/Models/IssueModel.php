<?php

namespace Redmine\Models;

class IssueModel extends RedmineBaseModel
{
    protected $table = "issues";

    protected $primaryKey = "id";

    protected $allowedFields = [
        "tracker_id",
        "project_id",
        "subject",
        "description",
        "due_date",
        "category_id",
        "status_id",
        "assigned_to_id",
        "priority_id",
        "fixed_version_id",
        "author_id",
        "lock_version",
        "created_on",
        "updated_on",
        "start_date",
        "done_ratio",
        "estimated_hours",
        "parent_id",
        "root_id",
        "lft",
        "rgt",
        "is_private",
        "closed_on",
        "sprint_id",
        "position"
    ];

    protected $validationRules = [
        'tracker_id' => 'required|integer',
        'project_id' => 'required|integer',
        'subject' => 'required|max_length[255]',
        'status_id' => 'required|integer',
        'priority_id' => 'required|integer',
        'author_id' => 'required|integer',
    ];

    protected $validationMessages = [
        'tracker_id' => [
            'required' => 'The tracker ID is required.',
            'integer' => 'The tracker ID must be an integer.'
        ],
        'project_id' => [
            'required' => 'The project ID is required.',
            'integer' => 'The project ID must be an integer.'
        ],
        'subject' => [
            'required' => 'The subject is required.',
            'max_length' => 'The subject cannot exceed 255 characters.'
        ],
        'status_id' => [
            'required' => 'The status ID is required.',
            'integer' => 'The status ID must be an integer.'
        ],
        'priority_id' => [
            'required' => 'The priority ID is required.',
            'integer' => 'The priority ID must be an integer.'
        ],
        'author_id' => [
            'required' => 'The author ID is required.',
            'integer' => 'The author ID must be an integer.'
        ]
    ];
   
    public function getTasks($id)
    {
        $sql = "SELECT 
                    i.id,
                    i.subject,
                    i.description,
                    i.start_date,
                    i.due_date,
                    concat(u.firstname,' ',u.lastname) AS name,
                    i.priority_id,
                    s.name,
                    i.done_ratio,
                    i.estimated_hours,
                    cv.value   
                FROM 
                    issues i
                INNER JOIN 
                    custom_values cv ON cv.customized_id = i.id 
                INNER JOIN 
                    users u ON u.id = i.assigned_to_id
                INNER JOIN 
                    issue_statuses s ON s.id = i.status_id
                WHERE 
                    cv.custom_field_id = 52;
        ";
        $query = $this->query($sql);
        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        }
    }


    public function insertTasks($args)
    {
        $sql = "INSERT INTO issues (
                    tracker_id,
                    project_id,
                    subject,
                    description,
                    assigned_to_id,
                    due_date,
                    start_date,
                    estimated_hours,
                    done_ratio, 
                    author_id,
                    priority_id,
                    status_id,
                    lft,
                    rgt,
                    created_on,
                    updated_on)
                VALUES (
                    :tracker_id:,
                    :project_id:,
                    :title:,
                    :description:,
                    :assignee:,
                    :due_date:,
                    :start_date:,
                    :estimated_hours:,
                    :completed_percentage:,
                    :author_id:,
                    :priority:,
                    :status:,
                    :lft:,
                    :rgt:,
                    :created_on:,
                    :updated_on:)";

        $result = $this->query($sql, [
            'project_id' => $args['project_id'],
            'title' => $args['task_title'],
            'description' => $args['task_description'],
            'assignee' => $args['task_assignee'],
            'priority' => $args['task_priority'],
            'due_date' => $args['end_date'],
            'start_date' => $args['start_date'],
            'estimated_hours' => $args['estimated_time'],
            'completed_percentage' => $args['completed_percentage'],
            'status' => $args['task_statuses'],
            'tracker_id' => $args['task_tracker'],
            'author_id' => $args['author_id'],
            'created_on' => $args['created_on'],
            'updated_on' => $args['updated_on'],
            'lft' => 1,
            'rgt' => 2
        ]);

        if ($result) {
            return 1;
        }
        return 0;
    }
    public function updateTaskById($args)
    {
        $sql = "UPDATE 
                    issues 
                SET 
                    subject=:task_title:,
                    description=:description:,
                    assigned_to_id=:assignee:,
                    priority_id=:priority:,
                    status_id=:status:,
                    tracker_id=:tracker_id:,
                    done_ratio=:completed_percentage:,
                    estimated_hours=:estimated_time:,
                    start_date=:start_date:,
                    due_date=:end_date:,
                    updated_on = :updated_on: 
                WHERE 
                    id=:task_id:";

        $this->query($sql, [
            'task_id' => $args['task_id'],
            'task_title' => $args['task_title'],
            'description' => $args['task_description'],
            'reference_task_id' => $args['task_id'],
            'assignee' => $args['task_assignee'],
            'priority' => $args['task_priority'],
            'status' => $args['task_statuses'],
            'tracker_id' => $args['task_tracker'],
            'start_date' => (empty($args['start_date']) ? NULL : $args['start_date']),
            'end_date' => (empty($args['end_date']) ? NULL : $args['end_date']),
            'author_id' => $args['author_id'],
            'completed_percentage' => $args['completed_percentage'],
            'estimated_time' => $args['estimated_time'],
            'updated_on' => $args['updated_on']
        ]);

        if ($sql) {
            return 1;
        }
    }

    public function getTaskId($time)
    {
        $sql = "SELECT 
                    id 
                FROM 
                    issues 
                WHERE 
                    created_on = :time:";
        $query = $this->query($sql, [
            'time' => $time
        ]);
        return $query->getResultArray()[0];
    }

    public function mapCustomValues($args)
    {
        $sql = "INSERT INTO custom_values(
                    customized_type,
                    customized_id,
                    custom_field_id,
                    value)
                VALUES (
                    :customize_type:,
                    :issue_id:,
                    52,
                    :user_story_id:);  
                ";
        $this->query($sql, [
            'issue_id' => $args['external_task_id'],
            'user_story_id' => $args['user_story_id'],
            'customize_type' => 'issue'
        ]);
        return $sql;
    }

    public function updateSprintForIssues(array $issueIds, int $sprintId)
    {
        $issueIdsString = implode(',', $issueIds);
        $sql = "UPDATE issues SET sprint_id = ? WHERE id IN ($issueIdsString)";
        return $this->query($sql, [$sprintId]);
    }

    public function getPendingTask($sprintData): array
    {
        $pendingStatuses = ['New', 'In Progress', 'Resolved', 'Feedback', 'Assigned', 'OnHold'];
        $statusesArray = array_fill(0, count($pendingStatuses), '?');
        $statuses = implode(',', $statusesArray);
        $query = "  SELECT 
                        e.name AS priority, COUNT(i.id) AS pending_tasks
                    FROM 
                        issues i
                    JOIN 
                        enumerations e ON i.priority_id = e.id
                    JOIN 
                        issue_statuses s ON i.status_id = s.id
                    WHERE e.type = 'IssuePriority'
                        AND s.name IN ($statuses)
                        AND i.project_id = ?
                        AND i.sprint_id = ?
                        GROUP BY e.name
                        ORDER BY e.position DESC";
        // Merge the statuses array with the project ID into one parameters array
        $params = array_merge($pendingStatuses, [$sprintData['productId']], [$sprintData['sprintId']]);

        // Execute the query with the provided parameters
        $result = $this->query($query, $params);
        return $result->getResultArray();
    }
    public function getAllTasksFromRedmine($priority, $custom_field_id,$lastupdate)
    {
        $priorities = implode(',', array_map(fn($item) => $this->db->escape($item), $priority));
        // Define the SQL query to retrieve tasks
        $query = "SELECT
                    custom_values.value AS r_user_story_id,
                    issue.subject AS task_title,
                    issue.description AS task_description,
                    issue.id AS external_reference_task_id,
                    enumerations.id AS priority,
                    issue.assigned_to_id AS assignee_id,
                    issue.author_id AS r_user_id_created,
                    issue.assigned_to_id AS r_user_id_updated,
                    issue.created_on AS created_date,
                    issue.updated_on AS updated_date,
                    issue_statuses.id AS task_status,
                    issue.done_ratio AS completed_percentage,
                    issue.start_date AS start_date,
                    issue.due_date AS end_date,
                    issue.estimated_hours AS estimated_hours,
                    issue.tracker_id AS tracker_id
                FROM 
                    issues AS issue
                INNER JOIN 
                    trackers ON trackers.id = issue.tracker_id
                INNER JOIN 
                    enumerations ON enumerations.id = issue.priority_id
                INNER JOIN 
                    users AS author ON author.id = issue.author_id
                LEFT JOIN 
                    users AS assignee ON assignee.id = issue.assigned_to_id
                INNER JOIN 
                    issue_statuses ON issue_statuses.id = issue.status_id
                INNER JOIN 
                    custom_values ON custom_values.customized_id = issue.id
                WHERE 
                    custom_values.custom_field_id = :custom_field_id:
                    AND enumerations.id IN ($priorities)
                    AND issue.updated_on > :lastupdateddate:";
         $timestamp = strtotime($lastupdate);
         $lastupdate=date('Y-m-d H:i:s', $timestamp);

        // Execute the query and retrieve results
        $result = $this->query($query, ['custom_field_id' => $custom_field_id,'lastupdateddate'=>$lastupdate]);

        // Return the result as an array
        return $result->getResultArray();
    }

    //ram
    public function insertTasksIssue($args)
    {
        $sql = "INSERT INTO issues (tracker_id,project_id,subject,description,assigned_to_id,author_id,priority_id,status_id,lft,rgt,created_on,updated_on)
                VALUES (:tracker_id:,:project_id:,:subject:,:description:,:assignee:,:author_id:,:priority:,:status:,:lft:,:rgt:,:created_on:,:created_on:);  
                ";
        $this->db->query($sql, [
            'project_id' => $args['project_id'],
            'title' => $args['task_title'],
            'subject' => $args['task_subject'],
            'description' => $args['task_description'],
            'assignee' => $args['task_assignee'],
            'priority' => $args['task_priority'],
            'status' => $args['task_statuses'],
            'tracker_id' => $args['task_tracker'] ,
            'author_id' => $args['author_id'],
            'created_on' => $args['created_on'],
            'updated_on' => $args['updated_on'],
            'lft' => 1,
            'rgt' => 2
        ]);
        // Get the ID of the inserted issue
        return $this->db->insertID();
    }
}

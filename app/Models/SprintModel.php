<?php

/**
 * SprintModel.php
 * 
 * @category   Model
 * @author     Jeril,Jeeva,Vishva,Sivabalan
 * @created    04 July 2024
 * @purpose This Model is for handling database for overall sprint module 
 */

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Stmt\Return_;

class SprintModel extends BaseModel
{
     /**
      * @author Jeril
      * Method to update sprint to running automatically when start date is reached
      */
     public function updateSprintRunning($today)
     {
          $query = "UPDATE scrum_sprint
                    SET r_module_status_id = :id:
                    WHERE r_module_status_id = :exist_id:
                    AND start_date <= :today:";
          $this->query($query, [
               "id" => 20,
               "exist_id" => 19,
               "today" => $today
          ]);
     }

     /**
      * @author Sivabalan
      * @param array $products
      * @param int $limit
      * @param int $offset
      * @return array
      * Method to fetch data from multiple tables for sprint list page and return to the controller
      */
     public function getSprintList($products, $uId, $limit = null, $offset = null, $filter = null, $columns = null)
     {


          $placeholders = implode(',', array_fill(0, count($products), '?'));

          if ($filter) {
               $productNameFilter = $filter . '%';
               $products[] = $productNameFilter;
          }
          $query = "SELECT SQL_CALC_FOUND_ROWS 
                     scrum_product.product_name AS product,
                     scrum_customer.customer_name AS customer,
                     scrum_sprint.sprint_name,
                     scrum_sprint.start_date,
                     scrum_sprint.end_date,
                     CONCAT(ROUND(AVG(IFNULL(scrum_task.completed_percentage, 0)), 0), ' %') AS sprint_completed,
                     scrum_status.status_name AS sprint_status,
                     scrum_sprint.estimated_hrs,
                     CONCAT(uc.first_name,' ',uc.last_name) AS created_by,
                     scrum_sprint.sprint_version,
                     scrum_sprint_duration.sprint_duration_value AS duration,
                     scrum_sprint.created_date,
                     uc.first_name AS user_created,
                     scrum_sprint.updated_date,
                     uc.first_name AS user_updated,
                     scrum_sprint.sprint_id,
                     scrum_sprint.r_product_id AS product_id
                     FROM scrum_sprint
                     INNER JOIN scrum_sprint_duration ON
                     scrum_sprint.r_sprint_duration_id = scrum_sprint_duration.sprint_duration_id
                     INNER JOIN scrum_customer ON
                     scrum_sprint.r_customer_id = scrum_customer.customer_id
                     INNER JOIN scrum_module_status ON
                     scrum_sprint.r_module_status_id = scrum_module_status.module_status_id
                     INNER JOIN scrum_status ON
                     scrum_module_status.r_status_id = scrum_status.status_id
                     INNER JOIN scrum_user uc ON
                     scrum_sprint.r_user_id_created = uc.external_employee_id
                     and
                     scrum_sprint.r_user_id_updated = uc.external_employee_id
                     INNER JOIN scrum_sprint_task ON
                     scrum_sprint.sprint_id = scrum_sprint_task.r_sprint_id
                     INNER JOIN scrum_task ON
                     scrum_task.task_id = scrum_sprint_task.r_task_id
                     INNER JOIN scrum_product ON
                     scrum_sprint.r_product_id = scrum_product.external_project_id
                     WHERE scrum_product.external_project_id IN ({$placeholders})";
          if ($filter) {
               $query .= " AND scrum_product.product_name LIKE ?";
          }
          if (!empty($columns['product_name'])) {
               $productname = $columns['product_name'];
               $query .= " AND scrum_product.product_name IN ($productname) ";
          }
          if (!empty($columns['sprint_name'])) {
               $sprintname = $columns['sprint_name'];
               $query .= " AND scrum_sprint.sprint_name IN ($sprintname)";
          }
          if (!empty($columns['customer'])) {
               $sprintcustomer = $columns['customer'];
               $query .= " AND scrum_customer.customer_name IN ($sprintcustomer)";
          }
          if (!empty($columns['status_name'])) {
               $sprintstatus = $columns['status_name'];
               $query .= " AND  scrum_status.status_name IN ($sprintstatus)";
          }
          if (!empty($columns['sprint_duration_value'])) {
               $sprintstatus = $columns['sprint_duration_value'];
               $query .= " AND   scrum_sprint_duration.sprint_duration_value IN ($sprintstatus)";
          }
          if (!empty($columns['start_date']) && !empty($columns['end_date'])) {
               $sprintdate = $columns['start_date'];
               $sprintEnddate = $columns['end_date'];
               $query .= " AND (scrum_sprint.start_date >= $sprintdate AND scrum_sprint.start_date <= $sprintEnddate) OR (scrum_sprint.end_date <= $sprintEnddate AND scrum_sprint.end_date >= $sprintEnddate)";
          }
          // if (!empty($columns['end_date'])) {
          //      $sprintEnddate = $columns['end_date'];
          //      $query .= "";
          // }
          $query .= " AND scrum_sprint_task.is_deleted = 'N' AND scrum_sprint.is_deleted = 'N'
                     GROUP BY scrum_sprint_task.r_sprint_id
                     ORDER BY scrum_sprint.created_date DESC, scrum_sprint.start_date, scrum_sprint.end_date";


          if (isset($limit) && isset($offset)) {
               $query .= " LIMIT ?, ?";
          }
          // Bind limit and offset values to the parameters array
          if (isset($limit) && isset($offset)) {
               $products[] = (int) $offset; // Ensure offset is cast to integer
               $products[] = (int) $limit;
          }
          // Ensure limit is cast to integer
          $result = $this->query($query, $products);
          // Check if query executed successfully
          if (!$result) {
               // Handle error, e.g., log it, return an error response, etc.
               return [];
          }
          $totalRowsQuery = "SELECT FOUND_ROWS() AS total_rows";
          $totalRowsResult = $this->query($totalRowsQuery);
          $totalRows = $totalRowsResult->getRowArray();
          return [$result->getResultArray(), $totalRows['total_rows']];
     }

     /**
      * @author Jeril
      * @return array
      * Method to fetch sprint duration values from the t_sprint_duration table
      */
     public function getSprintDuration(): array
     {
          $query = "SELECT *
                    FROM scrum_sprint_duration
                    WHERE scrum_sprint_duration.is_deleted = 'N'";
          $result = $this->query($query);

          return $result->getResultArray();
     }

     /**
      * @author Jeril
      * @return array
      * Method to fetch sprint activity from the t_sprint_activity table
      */
     public function getSprintActivity(): array
     {
          $query = "SELECT *
                    FROM scrum_sprint_activity
                    WHERE scrum_sprint_activity.is_deleted = 'N'";
          $result = $this->query($query);

          return $result->getResultArray();
     }

     /**
      * @author Jeril
      * @return array
      * Method to fetch sthe customer details
      */
     public function getCustomer(): array
     {
          $sql = "SELECT *
                    FROM scrum_customer
                    WHERE scrum_customer.is_deleted = 'N'";
          $query = $this->query($sql);
          if ($query->getNumRows() > 0) {
               return $query->getResultArray();
          }
          return [];
     }

     /**
      * @author Vishva
      * @param array $param
      * @return array
      * Method to fetch task ready for sprint by product wise
      */
     public function getReadyForSprintByProduct($param): array
     {
          $sql = "SELECT scrum_product.external_project_id AS prodoct_id,
				scrum_product.product_name, 
				scrum_backlog_item.backlog_item_id, 
				scrum_backlog_item.backlog_item_name,
                    scrum_backlog_item.priority, 
				scrum_epic.epic_id, 
				scrum_epic.epic_description AS epic_name, 
				scrum_user_story.user_story_id,
				CONCAT(scrum_user_story.as_a_an,' ',
				scrum_user_story.i_want,' ',
				scrum_user_story.so_that) AS user_story,                     
				scrum_task.task_id,
				scrum_task.task_title,
                    CONCAT(scrum_user.first_name,' ',scrum_user.last_name) AS assignee_name,
                    scrum_task_status.name 
				FROM scrum_task
				INNER JOIN scrum_user_story ON 
				scrum_task.r_user_story_id = scrum_user_story.user_story_id 
				INNER JOIN scrum_epic ON 
				scrum_user_story.r_epic_id = scrum_epic.epic_id 
				INNER JOIN scrum_backlog_item ON 
				scrum_epic.r_backlog_item_id = scrum_backlog_item.backlog_item_id 
				INNER JOIN scrum_product ON
				scrum_backlog_item.r_product_id = scrum_product.external_project_id
                    INNER JOIN scrum_task_status ON
                    scrum_task.task_status = scrum_task_status.id
                    INNER JOIN scrum_user ON
                    scrum_task.assignee_id = scrum_user.external_employee_id
				WHERE scrum_user_story.r_module_status_id IN :user_story:
                    AND scrum_backlog_item.r_module_status_id IN :backlog_status:
                    AND scrum_task.is_deleted = 'N'
                    AND scrum_task.task_status IN :task_status_id:
                    AND scrum_product.external_project_id = :product_id:
                    ORDER BY scrum_backlog_item.backlog_order ASC";
          $query = $this->query($sql, [
               "user_story" => $param["userStory"],
               "backlog_status" => $param["backlog"],
               "task_status_id" => $param["task"],
               "product_id" => $param["productId"]
          ]);
          if ($query->getNumRows() > 0) {
               return $query->getResultArray();
          }
          return [];
     }

     /**
      * @author Vishva
      * @param int $productId
      * @return array
      * Method to fetch task in the sprint by product wise
      */

     public function getTaskSprint($productId)
     {
          $sql = "SELECT scrum_product.external_project_id AS prodoct_id,
     			scrum_product.product_name, 
     			scrum_backlog_item.backlog_item_id, 
     			scrum_backlog_item.backlog_item_name, 
     			scrum_epic.epic_id, 
     			scrum_epic.epic_description AS epic_name,  
     			scrum_user_story.user_story_id,
     			CONCAT(scrum_user_story.as_a_an,' ',
     			scrum_user_story.i_want,' ',
     			scrum_user_story.so_that) AS user_story,                     
     			scrum_task.task_id,
     			scrum_task.task_description,
                    scrum_task_status.name 
     			FROM scrum_task
     			INNER JOIN scrum_user_story ON 
     			scrum_task.r_user_story_id = scrum_user_story.user_story_id 
     			INNER JOIN scrum_epic ON 
     			scrum_user_story.r_epic_id = scrum_epic.epic_id 
     			INNER JOIN scrum_backlog_item ON 
     			scrum_epic.r_backlog_item_id = scrum_backlog_item.backlog_item_id 
     			INNER JOIN scrum_product ON
     			scrum_backlog_item.r_product_id = scrum_product.external_project_id 
     			WHERE scrum_user_story.r_module_status_id IN (:status_id:, :status_id2:)
     			AND scrum_task.task_status IN (:task_status_id:, :task_status_id2:)
     			AND scrum_product.external_project_id = :product_id:
                    AND scrum_task.is_deleted = 'N'";
          $query = $this->query($sql, [
               "task_status_id" => 1,
               "task_status_id2" => 2,
               "status_id" => 16,
               "status_id2" => 17,
               "product_id" => $productId
          ]);
          if ($query->getNumRows() > 0) {
               return $query->getResultArray();
          }
          return [];
     }

     /**
      * @author Vishva
      * @param int $productId
      * @return array
      * Method to fetch users by product wise
      */

     public function getMembersByProduct($productId)
     {
          $query = "SELECT scrum_user.external_employee_id AS id,
                  CONCAT(scrum_user.first_name,' ',scrum_user.last_name) AS name,
                  scrum_role.role_name
                  FROM scrum_product_user
                  INNER JOIN scrum_user
                  ON scrum_user.external_employee_id = scrum_product_user.r_user_id
                  INNER JOIN scrum_role
                  ON scrum_role.role_id=scrum_user.r_role_id
                  WHERE scrum_product_user.r_product_id = :r_product_id:
                  AND scrum_product_user.is_deleted = 'N'";
          $result = $this->query($query, ["r_product_id" => $productId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeril
      * @param array $data
      * @return array
      * Method to fetch user stories for task inserted in sprint
      */
     public function getUserStories($data): array
     {
          $placeholders = implode(',', array_fill(0, count($data), '?'));
          $query = "SELECT DISTINCT
                    r_user_story_id AS id
                    FROM scrum_task
                    WHERE task_id IN ({$placeholders})
                    AND scrum_task.is_deleted = 'N'";
          $result = $this->query($query, $data);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeril
      * @param array $data
      * @param int $status_id
      * Method to update user story status from id fetched from getUserStories()
      */

     public function updateUserStory($data, $status_id)
     {
          $placeholders = implode(',', array_fill(0, count($data), '?'));
          $query = "UPDATE scrum_user_story
                    SET r_module_status_id = ?
                    WHERE user_story_id in ({$placeholders})
                    AND scrum_user_story.is_deleted = 'N'";
          $status[] = $status_id;
          $param = array_merge($status, $data);
          $this->query($query, $param);
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * @return array
      * Method to fetch sprint data by sprint id
      */
     public function getSprint($sprintId): array
     {
          $query = "SELECT 
				scrum_product.product_name,
				scrum_sprint.sprint_name,
				scrum_sprint.start_date,
				scrum_sprint.end_date,
                    scrum_sprint.r_module_status_id as sprint_status_id,
				scrum_status.status_name as sprint_status_name,
				scrum_sprint.sprint_version,
				scrum_sprint_duration.sprint_duration_value as sprint_duration,
				scrum_customer.customer_name,
                    scrum_sprint.sprint_goal,
                    CONCAT(uc.first_name,' ',uc.last_name) AS created_by,
                    scrum_sprint.r_product_id
				FROM scrum_sprint
				INNER JOIN scrum_product ON
				scrum_sprint.r_product_id = scrum_product.external_project_id
				INNER JOIN scrum_sprint_duration ON
				scrum_sprint.r_sprint_duration_id = scrum_sprint_duration.sprint_duration_id
				INNER JOIN scrum_customer ON
				scrum_sprint.r_customer_id = scrum_customer.customer_id
				INNER JOIN scrum_module_status ON
				scrum_sprint.r_module_status_id = scrum_module_status.module_status_id
				INNER JOIN scrum_status ON
				scrum_module_status.r_status_id = scrum_status.status_id
				INNER JOIN scrum_user uc ON
				scrum_sprint.r_user_id_created = uc.external_employee_id
				INNER JOIN scrum_user uu ON
				scrum_sprint.r_user_id_updated = uu.external_employee_id
				WHERE scrum_sprint.sprint_id = :id:
                    AND scrum_sprint.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeril
      * @param int $sprintId
      * @return array
      * Method to fetch sprint data for edit
      */
     public function getEditSprint($sprintId): array
     {
          $query = "SELECT * FROM scrum_sprint
                    WHERE sprint_id = :id:
                    AND scrum_sprint.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * @return array
      * Method to fetch data of sprint planning by sprint id
      */
     public function getSprintPlanning($sprintId): array
     {
          $query = "SELECT
                    scrum_sprint_planning.r_sprint_activity_id,
				scrum_sprint_activity.activity,
				scrum_sprint_planning.start_date as startDate,
				scrum_sprint_planning.end_date as endDate,
                    scrum_sprint_planning.r_module_status_id,
                    scrum_notes.notes,
                    scrum_status.status_name
				FROM scrum_sprint_planning
				INNER JOIN scrum_sprint_activity
				ON scrum_sprint_planning.r_sprint_activity_id = scrum_sprint_activity.sprint_activity_id
                    INNER JOIN scrum_module_status ON
                    scrum_sprint_planning.r_module_status_id = scrum_module_status.module_status_id
                    INNER JOIN scrum_status ON
                    scrum_module_status.r_status_id = scrum_status.status_id
                    INNER JOIN scrum_notes ON
                    scrum_sprint_planning.r_notes_id = scrum_notes.notes_id
				WHERE r_sprint_id = :id:
                    AND scrum_sprint_planning.is_deleted = 'N'
				ORDER BY scrum_sprint_planning.start_date ASC";
          $result = $this->query($query, ["id" => $sprintId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * @return array
      * Method to fetch task associated with particular sprint
      */
     public function getSprintTask($sprintId)
     {
          $query = "SELECT DISTINCT
				scrum_backlog_item.backlog_item_id, 
				scrum_backlog_item.backlog_item_name, 
				scrum_epic.epic_id, 
				scrum_epic.epic_description AS epic_name, 
				scrum_user_story.user_story_id AS userstory_id,
				CONCAT('As a an ',scrum_user_story.as_a_an,' i want ',scrum_user_story.i_want,' so that ',scrum_user_story.so_that) AS user_story,                     
				scrum_task.task_id,
                    scrum_task.task_title,
                    IFNULL(scrum_task.completed_percentage, 0) AS completed_percentage,
				scrum_task.task_description,
				scrum_task_status.name as task_status 
				FROM scrum_sprint_task
				INNER JOIN scrum_sprint ON
				scrum_sprint_task.r_sprint_id = scrum_sprint.sprint_id
				INNER JOIN scrum_task ON
				scrum_sprint_task.r_task_id = scrum_task.task_id
                    INNER JOIN scrum_task_status ON
                    scrum_task_status.id = scrum_task.task_status
				INNER JOIN scrum_user_story ON 
				scrum_task.r_user_story_id = scrum_user_story.user_story_id 
				INNER JOIN scrum_epic ON 
				scrum_user_story.r_epic_id = scrum_epic.epic_id 
				INNER JOIN scrum_backlog_item ON 
				scrum_epic.r_backlog_item_id = scrum_backlog_item.backlog_item_id 
				INNER JOIN scrum_product ON
				scrum_backlog_item.r_product_id = scrum_product.external_project_id 
				WHERE scrum_sprint_task.r_sprint_id = :id:
                    AND scrum_sprint_task.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];

     }

     /**
      * @author Sivabalan
      * @param int $sprintId
      * @return array
      * Method to fetch users associated with a sprint
      */
     public function getSprintMember($sprintId): array
     {
          $query = "SELECT DISTINCT
				scrum_sprint_user.r_user_id AS id,
                    scrum_user.external_employee_id AS emp_id,
                    CONCAT(scrum_user.first_name,' ',scrum_user.last_name) AS name,
                    scrum_user.external_username as email_id,
                    scrum_role.role_name
				FROM scrum_sprint_user
				INNER JOIN scrum_user ON
                    scrum_sprint_user.r_user_id = scrum_user.external_employee_id
                    INNER JOIN scrum_role ON scrum_role.role_id=scrum_user.r_role_id
				WHERE scrum_sprint_user.r_sprint_id = :id:
                    AND scrum_sprint_user.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * @return array
      * Method to fetch data of daily scrum
      */

     public function getDailyScrum($sprintId): array
     {
          $query = "SELECT
				scrum_daily_scrum.added_date,
				scrum_daily_scrum.challenges,
				scrum_notes.notes,
                    scrum_task.task_title
				FROM scrum_daily_scrum
				INNER JOIN scrum_notes
				ON scrum_daily_scrum.r_notes_id = scrum_notes.notes_id
                    INNER JOIN scrum_task
                    ON scrum_daily_scrum.r_task_id = scrum_task.task_id
				WHERE scrum_daily_scrum.r_sprint_id = :id:
                    AND scrum_daily_scrum.is_deleted = 'N'
				ORDER by added_date DESC";
          $result = $this->query($query, ["id" => $sprintId]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Sivabalan
      * @param int $sprintId
      * @return array
      * Method to fetch date of sprint review
      */

     public function getSprintReviewDate($sprintId): array
     {
          $query = "SELECT
				scrum_sprint_planning.start_date AS added_date
				FROM scrum_sprint_planning
				INNER JOIN scrum_sprint_activity
				ON scrum_sprint_planning.r_sprint_activity_id = scrum_sprint_activity.sprint_activity_id
				WHERE scrum_sprint_planning.r_sprint_id = :id:
				AND scrum_sprint_planning.r_sprint_activity_id = :r_sprint_activity_id:
                    AND scrum_sprint_planning.is_deleted = 'N'
                    AND scrum_sprint_activity.is_deleted = 'N'";
          $result = $this->query($query, [
               "id" => $sprintId,
               "r_sprint_activity_id" => 12
          ]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return array(0 => array("added_date" => "Not yet planned"));
     }

     /**
      * @author Jeril
      * @param int $sprintId
      * @return array
      * Method to fetch data of sprint review
      */
     public function getSprintReview($sprintId): array
     {
          $query = "SELECT  
                    sn1.notes AS General,
                    ssr.code_review_status AS CodeReviewStatus,
                    sn2.notes AS CodeReview,
                    ssr.challenges_status AS ChallengesStatus,
                    sn3.notes AS ChallengesFaced,
                    ssr.sprint_goal_status AS SprintGoalStatus,
                    sn4.notes AS SprintGoal,
                    CONCAT(su.first_name,' ',su.last_name) as code_reviewers
                    FROM scrum_sprint_review ssr
                    INNER JOIN scrum_notes sn1 ON ssr.r_scrum_notes_id = sn1.notes_id
                    INNER JOIN scrum_notes sn2 ON ssr.r_scrum_notes_id_cr = sn2.notes_id
                    INNER JOIN scrum_notes sn3 ON ssr.r_scrum_notes_id_challenges = sn3.notes_id
                    INNER JOIN scrum_notes sn4 ON ssr.r_scrum_notes_id_sg = sn4.notes_id
                    LEFT JOIN scrum_code_review_users scru ON ssr.r_sprint_id = scru.r_sprint_id
                    LEFT JOIN scrum_user su ON scru.r_user_id = su.external_employee_id
                    WHERE ssr.r_sprint_id = :id:
                    AND ssr.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          return $result->getResultArray();
     }

     /**
      * @author Vishva
      * @param int $sprintId
      * @return array
      * Method to fetch data of sprint review
      */
     public function fetchCodeReviewers($sprintId): array
     {
          $query = "SELECT  
                     CONCAT(su.first_name,' ',su.last_name) as code_reviewers
                     FROM scrum_code_review_users scru
                     INNER JOIN scrum_user su ON scru.r_user_id = su.external_employee_id
                     WHERE scru.r_sprint_id = :id:
                     AND scru.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          return $result->getResultArray();
     }

     /**
      * @author Vishva
      * @param int $sprintId
      * @return array
      * Method to fetch date of sprint retrospective
      */

     public function getSprintRetrospectiveDate($sprintId)
     {
          $query = "SELECT
				scrum_sprint_planning.start_date AS added_date
				FROM scrum_sprint_planning
				INNER JOIN scrum_sprint_activity
				ON scrum_sprint_planning.r_sprint_activity_id = scrum_sprint_activity.sprint_activity_id
				WHERE scrum_sprint_planning.r_sprint_id = :id:
				AND scrum_sprint_planning.r_sprint_activity_id = :r_sprint_activity_id:
                    AND scrum_sprint_planning.is_deleted = 'N'";
          $result = $this->query($query, [
               "id" => $sprintId,
               "r_sprint_activity_id" => 13
          ]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return array(0 => array("added_date" => "Not yet planned"));
     }

     /**
      * @author Vishva
      * @param int $sprintId
      * @return array
      * Method to fetch data of sprint review
      */
     public function getSprintRetrospective($sprintId)
     {
          $query = "SELECT
				scrum_sprint_retrospective.challenge,
				scrum_notes.notes
				FROM scrum_sprint_retrospective
				INNER JOIN scrum_notes
				ON scrum_sprint_retrospective.r_notes_id = scrum_notes.notes_id
				WHERE scrum_sprint_retrospective.r_sprint_id = :id:
                    AND scrum_sprint_retrospective.is_deleted = 'N'";
          $result = $this->query($query, ["id" => $sprintId]);
          return $result->getResultArray();
     }


     /**
      * @author Jeeva
      * @return array
      * Method to fetch status of sprint
      */
     public function getSprintStatus(): array
     {
          $query = "SELECT scrum_module_status.module_status_id,
                     scrum_status.status_name
                     FROM scrum_module_status
                     INNER JOIN scrum_status
                     ON scrum_module_status.r_status_id = scrum_status.status_id
                     WHERE scrum_module_status.r_module_id = :r_module_id:
                     AND scrum_module_status.is_deleted = 'N'";
          $result = $this->db->query($query, ['r_module_id' => 8]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * Method to update task status during sprint review
      */
     public function updateTaskReview($tasks, $status)
     {
          $query = "UPDATE scrum_task
                     SET task_status = :task_status:
                     WHERE task_id = :task_id:";
          $result = $this->query($query, [
               "task_status" => $status,
               "task_id" => $tasks
          ]);
          return $result;
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * Method to remove users from a sprint
      */
     public function removeSprintUsers($sprintId)
     {
          $query = "UPDATE scrum_sprint_user
                SET is_deleted = :is_deleted:
                WHERE r_sprint_id = :r_sprint_id:";
          $this->db->query($query, [
               "is_deleted" => "Y",
               "r_sprint_id" => $sprintId
          ]);
     }

     /**
      * @author Jeeva
      * @param int $sprintId
      * Method to remove tasks from a sprint
      */
     public function removeSprintTasks($sprintId)
     {
          $query = "UPDATE scrum_sprint_task
                SET is_deleted = :is_deleted:
                WHERE r_sprint_id = :r_sprint_id:";
          $this->db->query($query, [
               "is_deleted" => "Y",
               "r_sprint_id" => $sprintId
          ]);
     }

     /**
      * @author Vishva
      * @param array $param
      * @return array
      * Method to fetch tasks for edit purpose
      */
     public function getTaskForEdit($param): array
     {
          $query = "SELECT scrum_product.external_project_id AS prodoct_id,
				scrum_product.product_name, 
				scrum_backlog_item.backlog_item_id, 
				scrum_backlog_item.backlog_item_name,
                    scrum_backlog_item.priority,  
				scrum_epic.epic_id, 
				scrum_epic.epic_description AS epic_name, 
				scrum_user_story.user_story_id,
				CONCAT(scrum_user_story.as_a_an,' ',
				scrum_user_story.i_want,' ',
				scrum_user_story.so_that) AS user_story,                     
				scrum_task.task_id,
				scrum_task.task_title,
                    CONCAT(scrum_user.first_name,' ',scrum_user.last_name) AS assignee_name,
                    scrum_task_status.name 
				FROM scrum_task
				INNER JOIN scrum_user_story ON 
				scrum_task.r_user_story_id = scrum_user_story.user_story_id 
				INNER JOIN scrum_epic ON 
				scrum_user_story.r_epic_id = scrum_epic.epic_id 
				INNER JOIN scrum_backlog_item ON 
				scrum_epic.r_backlog_item_id = scrum_backlog_item.backlog_item_id 
				INNER JOIN scrum_product ON
				scrum_backlog_item.r_product_id = scrum_product.external_project_id 
                    INNER JOIN scrum_task_status ON
                    scrum_task.task_status = scrum_task_status.id
                    INNER JOIN scrum_user ON
                    scrum_task.assignee_id = scrum_user.external_employee_id
				WHERE scrum_user_story.r_module_status_id IN :user_story:
                    AND scrum_task.is_deleted = 'N'
                    AND scrum_product.external_project_id = :r_project_id:
                    ORDER BY scrum_backlog_item.backlog_order ASC";
          $query = $this->query($query, [
               "user_story" => $param["userStory"],
               "r_project_id" => $param["r_product_id"]
          ]);
          if ($query->getNumRows() > 0) {
               return $query->getResultArray();
          }
          return [];
     }

     /**
      * @author Vishva
      * @return array
      * Method to fetch sprint planning status
      */
     public function getSprintPlanningStatus()
     {
          $query = "SELECT scrum_module_status.module_status_id,
                    scrum_status.status_name
                    FROM scrum_module_status
                    INNER JOIN scrum_status ON
                    scrum_module_status.r_status_id = scrum_status.status_id
                    WHERE r_module_id = :r_module_id:";
          $result = $this->query($query, [
               "r_module_id" => 19
          ]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Vishva
      * @param $data
      * Method to update sprint planning status
      */
     public function updateSprintPlan($data)
     {
          $query = "UPDATE scrum_sprint_planning
                    SET r_module_status_id = :r_module_status_id:
                    WHERE r_sprint_id = :r_sprint_id:
                    AND r_sprint_activity_id = :r_activity_id:";
          return $this->query($query, [
               "r_module_status_id" => $data["r_status_id"],
               "r_sprint_id" => $data["sprint_id"],
               "r_activity_id" => $data["activity_id"]
          ]);
     }

     /**
      * @author Vishva
      * @param $data
      * Method to update sprint status
      */
     public function updateSprintStatus($data)
     {
          $query = "UPDATE scrum_sprint
                     SET r_module_status_id = :r_module_status_id:
                     WHERE sprint_id = :r_sprint_id:";
          return $this->query($query, [
               "r_module_status_id" => $data["r_status_id"],
               "r_sprint_id" => $data["sprint_id"]
          ]);
     }

     /**
      * @author Vishva
      * @param array $param
      * @return array
      * Method to fetch data for sprint history
      */
     public function getSprintHistory($param): array
     {
          $query = "SELECT 
                    CONCAT(u.first_name,' ',u.last_name) AS name,
                    scrum_module.module_name,
                    scrum_sprint.sprint_name,
                    scrum_action_type.action_type_name,
                    scrum_user_action.action_data,
                    scrum_user_action.action_date
                    FROM scrum_user_action
                    INNER JOIN scrum_user u
                    ON scrum_user_action.r_user_id = u.external_employee_id
                    INNER JOIN scrum_action_type
                    ON scrum_user_action.r_action_type_id = scrum_action_type.action_type_id
                    INNER JOIN scrum_module
                    ON scrum_user_action.r_module_id = scrum_module.module_id
                    INNER JOIN scrum_sprint
                    ON scrum_user_action.reference_id = scrum_sprint.sprint_id
                    WHERE scrum_user_action.r_module_id IN :module:
                    AND scrum_user_action.reference_id = :r_sprint_id:
                    ORDER BY scrum_user_action.action_date DESC";
          $result = $this->query($query, [
               "module" => $param["module"],
               "r_sprint_id" => $param["sprint_id"]
          ]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Gokul
      * @param $args
      * @return int|bool
      * Method to alter sprint date from the meeting module
      */
     public function alterSprintDate($args): int|bool
     {
          $query = "UPDATE scrum_sprint
                  SET r_sprint_duration_id = :duration_id:,
                    start_date = :start:,
                    end_date = :end:,
                    r_user_id_updated = :uId:,
                    updated_date = now()
                    WHERE sprint_id = :id:";
          $result = $this->query($query, [
               'id' => $args['sprintId'],
               'duration_id' => $args['duration'],
               'start' => $args['startDate'],
               'end' => $args['endDate'],
               'uId' => $args['userId']
          ]);
          return $result;
     }


     /**
      * @author Gokul
      * @param $args
      * @return int|bool
      * Method to alter sprint status from the meeting module
      */
     public function alterSprintStatusById($args): int|bool
     {
          $query = "UPDATE scrum_sprint
                  SET r_module_status_id = :statusId:,
                    r_user_id_updated = :uId:,
                    updated_date = now()
                    WHERE sprint_id = :id:";
          $result = $this->query($query, [
               'id' => $args['sprintId'],
               'statusId' => $args['status'],
               'uId' => $args['userId']
          ]);
          return $result;
     }

     /**
      * @author Sivabalan
      * @return array
      * Method to alter sprint status from the meeting module
      */
     public function fetchRunningSprints(): array
     {
          $query = "SELECT sprint_id
                    FROM scrum_sprint
                    WHERE r_module_status_id = :id:";
          $result = $this->query($query, ['id' => 20]);
          if ($result->getNumRows() > 0) {
               return $result->getResultArray();
          }
          return [];
     }

     /**
      * @author Sivabalan
      * @param array $sprintIds
      * Method to alter sprint status from the meeting module
      */
     public function updateSprintTaskRunning($sprintIds)
     {
          $placeholders = implode(',', array_fill(0, count($sprintIds), '?'));
          $status[] = 2;
          $param = array_merge($status, $sprintIds, [8]);
          $query = "UPDATE scrum_task
                    JOIN scrum_sprint_task
                    ON scrum_task.task_id = scrum_sprint_task.r_task_id
                    SET scrum_task.task_status = ?
                    WHERE scrum_sprint_task.r_sprint_id IN ({$placeholders})
                    AND scrum_task.task_status = ?";
          $this->query($query, $param);
     }

     /**
      * @author Sivabalan
      * @param int $taskId
      * Method to fetch sprint id from scrum_sprint_task
      */
     public function getSprintId($taskId)
     {
          $query = "select scrum_sprint_task.r_sprint_id from scrum_sprint_task inner join scrum_task on scrum_sprint_task.r_task_id=scrum_task.task_id where 
           external_reference_task_id=:task_id:";
          $result = $this->query($query, ["task_id" => $taskId]);
          if ($result->getNumRows() > 0) {
               $sprintId = $result->getResultArray();
               $sprintId = $sprintId[0]['r_sprint_id'];
               return $sprintId;
          }
          return 0;
     }

     /**
      * @author Sivabalan
      * @param int $sprintId
      * Method to update estimated hours in scrum_sprint
      */
     public function updatesprintEstimationTime($sprintId)
     {
          $query = "select SUM(IFNULL(scrum_task.estimated_hours, 0)) AS total_estd_hours
     from scrum_task inner join scrum_sprint_task on scrum_task.task_id=scrum_sprint_task.r_task_id
     where scrum_sprint_task.r_sprint_id=:sprint_id: and scrum_sprint_task.is_deleted ='N' ";
          $resultTemp = $this->query($query, ["sprint_id" => $sprintId]);
          if ($resultTemp) {
               $result = $resultTemp->getResultArray();
               $estimated_hours = $result[0]['total_estd_hours'];
          }
          $query = "UPDATE scrum_sprint set estimated_hrs = :estimated_hours: where sprint_id=:sprint_id:";
          $result = $this->query($query, ["estimated_hours" => $estimated_hours, "sprint_id" => $sprintId]);
          return 0;
     }
}
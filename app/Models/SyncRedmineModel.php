<?php

namespace App\Models;

/**
 * SyncRedmineModel
 *
 * This model handles synchronization activities between Redmine and local database.
 * It includes methods for logging sync activities, managing tasks, products, and product users.
 *
 * Author: T siva Teja 
 * Email: thotasivateja57@gmail.com
 * Created Date: 2024-07-25
 * Updated Date: 2024-07-25
 */
class SyncRedmineModel extends BaseModel
{
    /**
     * Log synchronization activity to the database.
     *
     * @param string $synctype Type of synchronization activity.
     * @param bool $syncstatus Status of synchronization (true for success, false for failure).
     * @return mixed Result of the query execution.
     */



    public function logSyncActivity($synctype, $syncstatus, $r_employee_id)
    {
         if(is_null($r_employee_id)){
            $r_employee_id=0;
         }
        // Determine the sync status string based on the boolean value
        $syncStatus = $syncstatus ? "success" : "failed";

        // Prepare SQL query to insert sync activity into the database
        $query = "INSERT INTO scrum_sync_activities (sync_type, r_external_employee_id, sync_status, sync_datetime) 
                  VALUES (:sync_type:, :r_external_employee_id:, :sync_status:, :sync_datetime:)";

        // Bind data to the query
        $data = [
            'sync_type' => $synctype,
            'r_external_employee_id' => $r_employee_id,
            'sync_status' => $syncStatus,
            'sync_datetime' => date('Y-m-d H:i:s') // Current timestamp
        ];

        // Execute the query with the data
        $result = $this->query($query, $data);
        return $result;
    }


    /**
     * Retrieve all tasks from the local database.
     *
     * @return array List of tasks.
     */
    public function getAllTasksFromLocal($status)
    {
        // SQL query to select all tasks
        $status = implode(',', array_map(fn($item) => $this->db->escape($item), $status));
        $query = "
        SELECT 
            st.r_user_story_id,
            st.task_title,
            st.task_description,
            st.external_reference_task_id,
            st.priority,
            st.assignee_id,
            st.r_user_id_created,
            st.r_user_id_updated,
            st.created_date,
            st.updated_date,
            st.task_status,
            st.completed_percentage,
            st.start_date,
            st.end_date,
            st.estimated_hours,
            st.tracker_id
        FROM scrum_task AS st
        INNER JOIN scrum_sprint_task AS sst ON sst.r_task_id = st.task_id
        INNER JOIN scrum_sprint AS sp ON sp.sprint_id = sst.r_sprint_id
        WHERE sp.r_module_status_id IN ($status)
        ";

        // Execute the query and return result as an array
        $result = $this->query($query);
        return $result->getResultArray();
    }

    /**
     * Retrieve all product users from the local database.
     *
     * @return array List of product users.
     */
    public function getAllProductUsersFromLocal()
    {
        // SQL query to select all product users
        $query = "
        SELECT 
            r_user_id AS external_user_id,
            r_product_id AS external_project_id
        FROM scrum_product_user
        ";

        // Execute the query and return result as an array
        $result = $this->query($query);
        return $result->getResultArray();
    }

    /**
     * Insert or update a product user in the local database.
     *
     * @param array $data Product user data with external project and user IDs.
     * @return int Status of the operation (1 for success).
     */
    public function updateProductUserSync($data)
    {
        // SQL query to insert or update multiple product users
        $query = "
     INSERT INTO scrum_product_user (r_product_id, r_user_id)
     VALUES ";

        $values = [];
        $params = [];

        // Loop through the data and prepare the values and parameters
        foreach ($data as $index => $item) {
            $values[] = "(:r_product_id_{$index}:, :r_user_id_{$index}:)";

            $params["r_product_id_{$index}"] = $item["external_project_id"];
            $params["r_user_id_{$index}"] = $item["external_user_id"];
        }

        // Combine the query with the prepared values
        $query .= implode(", ", $values);

        // Add the ON DUPLICATE KEY UPDATE part
        $query .= "
         ON DUPLICATE KEY UPDATE
         r_product_id = VALUES(r_product_id),
         r_user_id = VALUES(r_user_id)
        ";

        // Execute the query with the provided data
        $result = $this->db->query($query, $params);
        $sql = $this->db->getLastQuery();
        return $result;

    }

    /**
     * Insert or update a product in the local database.
     *
     * @param array $data Product data with external project ID, name, and dates.
     * @return int Status of the operation (1 for success).
     */
    public function updateProductSync($data)
    {
        $query = "
        INSERT INTO scrum_product (external_project_id, r_owner_id, product_name, parent_id,created_date, updated_date)
        VALUES ";

        $values = [];
        $params = [];

        // Loop through the data and prepare the values and parameters
        foreach ($data as $index => $item) {
            $values[] = "(:external_project_id_{$index}:, :r_owner_id_{$index}:, :product_name_{$index}:,:parent_id_{$index}:,:created_date_{$index}:, :updated_date_{$index}:)";

            $params["external_project_id_{$index}"] = $item["external_project_id"];
            $params["r_owner_id_{$index}"] = R_OWNER_ID;
            $params["product_name_{$index}"] = $item["product_name"];
            $params["parent_id_{$index}"] = $item["parent_id"];
            $params["created_date_{$index}"] = $item["created_date"];
            $params["updated_date_{$index}"] = $item["updated_date"];
        }

        // Combine the query with the prepared values
        $query .= implode(", ", $values);

        // Add the ON DUPLICATE KEY UPDATE part
        $query .= "
        ON DUPLICATE KEY UPDATE
            product_name = VALUES(product_name),
            parent_id = VALUES(parent_id),
            created_date = VALUES(created_date),
            updated_date = VALUES(updated_date),
            r_owner_id = VALUES(r_owner_id)
    ";
        // Execute the query with the provided data
        $result = $this->db->query($query, $params);

        return $result;



    }

    /**
     * Retrieve all products from the local database.
     *
     * @return array List of products.
     */
    public function getLocalProduct()
    {
        // SQL query to select all products
        $sql = "SELECT 
                   external_project_id,
                   product_name,
                   created_date,
                   updated_date
                FROM scrum_product";

        // Execute the query and return result as an array
        $result = $this->query($sql);
        return $result->getNumRows() > 0 ? $result->getResultArray() : [];
    }

    /**
     * Insert or update a task in the local database.
     *
     * @param array $args Task data with all required fields.
     * @return int Status of the operation (1 for success, 0 for failure).
     */
    public function updateTaskSync($data)
    {
        // Base SQL query
        $query = "
        INSERT INTO scrum_task (
            r_user_story_id,
            task_title,
            task_description,
            external_reference_task_id,
            priority,
            assignee_id,
            r_user_id_created,
            r_user_id_updated,
            created_date,
            updated_date,
            task_status,
            completed_percentage,
            start_date,
            end_date,
            estimated_hours,
            tracker_id
        )
        VALUES ";

        $values = [];
        $params = [];

        // Loop through the data and prepare the values and parameters
        foreach ($data as $index => $item) {
            $values[] = "(
                :r_user_story_id_{$index}:,
                :task_title_{$index}:,
                :task_description_{$index}:,
                :external_reference_task_id_{$index}:,
                :priority_{$index}:,
                :assignee_id_{$index}:,
                :r_user_id_created_{$index}:,
                :r_user_id_updated_{$index}:,
                :created_date_{$index}:,
                :updated_date_{$index}:,
                :task_status_{$index}:,
                :completed_percentage_{$index}:,
                :start_date_{$index}:,
                :end_date_{$index}:,
                :estimated_hours_{$index}:,
                :tracker_id_{$index}:
            )";

            $params["r_user_story_id_{$index}"] = $item["r_user_story_id"];
            $params["task_title_{$index}"] = $item["task_title"];
            $params["task_description_{$index}"] = strip_tags($item["task_description"]);
            $params["external_reference_task_id_{$index}"] = $item["external_reference_task_id"];
            $params["priority_{$index}"] = $item["priority"];
            $params["assignee_id_{$index}"] = $item["assignee_id"];
            $params["r_user_id_created_{$index}"] = $item["r_user_id_created"];
            $params["r_user_id_updated_{$index}"] = $item["r_user_id_updated"];
            $params["created_date_{$index}"] = $item["created_date"];
            $params["updated_date_{$index}"] = $item["updated_date"];
            $params["task_status_{$index}"] = $item["task_status"];
            $params["completed_percentage_{$index}"] = $item["completed_percentage"];
            $params["start_date_{$index}"] = $item["start_date"];
            $params["end_date_{$index}"] = $item["end_date"];
            $params["estimated_hours_{$index}"] = $item["estimated_hours"];
            $params["tracker_id_{$index}"] = $item["tracker_id"];
        }

        // Combine the query with the prepared values
        $query .= implode(", ", $values);

        // Add the ON DUPLICATE KEY UPDATE part
        $query .= "
        ON DUPLICATE KEY UPDATE
            task_title = VALUES(task_title),
            task_description = VALUES(task_description),
            external_reference_task_id = VALUES(external_reference_task_id),
            priority = VALUES(priority),
            assignee_id = VALUES(assignee_id),
            r_user_id_created = VALUES(r_user_id_created),
            r_user_id_updated = VALUES(r_user_id_updated),
            updated_date = VALUES(updated_date),
            task_status = VALUES(task_status),
            completed_percentage = VALUES(completed_percentage),
            start_date = VALUES(start_date),
            end_date = VALUES(end_date),
            estimated_hours = VALUES(estimated_hours),
            tracker_id = VALUES(tracker_id)
        ";

    

        // Execute the query with the provided data
        $result = $this->db->query($query, $params);

        return $result;
    }

    public function getLastUpdates()
    {
        $query = "
        SELECT
        sync_type,
        DATE_FORMAT(MAX(sync_datetime), '%Y-%m-%d %h:%i:%s %p') AS last_updated_datetime
        FROM
        scrum_sync_activities
    WHERE
        sync_status='success'
    GROUP BY
    sync_type

    ";
        $result = $this->db->query($query);
        return $result->getResultArray();
    }
    public function updateCustomerSync($data)
    {
        $query = "
        INSERT INTO scrum_customer (customer_name, created_date, updated_date, is_deleted)
        VALUES ";

        $values = [];
        $params = [];

        // Loop through the data and prepare the values and parameters
        foreach ($data as $index => $customer_name) {
            $values[] = "(:customer_name_{$index}:, NOW(), NOW(), 'N')";

            // Since $data contains strings, use the string directly as the customer_name
            $params["customer_name_{$index}"] = $customer_name;
        }

        // Combine the query with the prepared values
        $query .= implode(", ", $values);

        // Add the ON DUPLICATE KEY UPDATE part
        $query .= "
        ON DUPLICATE KEY UPDATE
            customer_name = VALUES(customer_name),
            updated_date = NOW(),
            is_deleted = 'N'
    ";
        // Execute the query with the provided data
        $result = $this->db->query($query, $params);
        $sql = $this->db->getLastQuery();

        return $result;
    }



}

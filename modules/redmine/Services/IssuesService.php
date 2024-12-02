<?php

namespace Redmine\Services;

use PHPUnit\Util\Json;


class IssuesService extends RedmineBaseService
{
    protected $issues;
    protected $issueTimeEntries ;

    public function __construct()
    {
        $this->issues = model(\Redmine\Models\IssueModel::class);
        $this->issueTimeEntries = model(\Redmine\Models\TimeEntry::class);
    }

    public function all()
    {
        $data = $this->issues->find(101);
        return json_encode($data);
    }

    // Get total task hours for a product per sprint
    public function getTotalTaskHours($userStoryIds)
    {
        $data = $this->issueTimeEntries->getSumTaskHoursPerSprint($userStoryIds);
        return json_encode($data);
    }

    // // Get burndown chart data for a sprint and product
    // public function getBurndownChartData($issueIds)
    // {
    //     $data = $this->issueTimeEntries->getSprintHours($issueIds);
    //     return json_encode($data);
    // }

    public function getDailySpentHours($issueIds){
        $data = $this->issueTimeEntries->getSprintHours($issueIds);
        return json_encode($data);
    }

    // Get the total task hours spent on each user story of a sprint for a product
    public function getUserStoryTaskHours($userStoryIds){
        $data = $this->issueTimeEntries->getUserStoryTaskHours($userStoryIds);
        return json_encode($data);
    }

    public function getTasks($id)
    {
        $data = $this->issues->getTasks($id);
        return $data;
    }

    public function mapCustomValues($id)
    {
        $data = $this->issues->mapCustomValues($id);
        return $data;
    }
    public function insertTasks($args)
    {
        $data = $this->issues->insertTasks($args);
        if ($data) {
            $time = $args['created_on'];
            $result = $this->issues->getTaskId($time);
        }
        return $result;
    }

    public function updateTasks($args){
        $data = $this->issues->updateTaskById($args);
        return $data;
    }

    public function updateSprintForIssues(array $issueIds, int $sprintId){
        $data = $this->issues->updateSprintForIssues($issueIds,$sprintId);
        return 1;
    }
    public function syncTasks(){
        $data=$this->issues->syncTasks();
        return $data;
    }
    public function getAllTasksFromRedmine($prority,$custom_field_id,$lastupdate){
        $data=$this->issues->getAllTasksFromRedmine($prority,$custom_field_id,$lastupdate);
        return $data;
    }
    public function insertTasksId($args){
        $data = $this->issues->insertTasksIssue($args);
        return $data;
    }
}
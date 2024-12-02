<?php
namespace Redmine\Services;

use Redmine\Services\RedmineBaseService;

class TimeEntryService extends RedmineBaseService
{

    protected $timeEntryModelObj;
    public function __construct()
    {
        $this->timeEntryModelObj = model(\Redmine\Models\TimeEntry::class);
    }
    public function timeEntryLog($data)
    {
        // the $data should be sent to timeentrymodel to insert in time entry table in pmt_redmine db
        // return 1 if query executed
        $data = $this->timeEntryModelObj->entryTimeLog($data);
        return $data;
    }

    public function activityId($type)
    {

        $data = $this->timeEntryModelObj->getActivityId($type['name']);
        return $data;
    }
}
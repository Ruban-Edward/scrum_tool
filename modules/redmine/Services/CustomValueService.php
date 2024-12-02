<?php

namespace Redmine\Services;

use PHPUnit\Util\Json;

class CustomValueService extends RedmineBaseService
{
    protected $customValue;
    protected $issueTimeEntries ;

    public function __construct()
    {
        $this->customValue = model(\Redmine\Models\CustomValueModel::class);
    }

    public function insertCustomValue($id,$customId,$values){
        return $this->customValue->insertCustomValue($id,$customId,$values);
    }
}
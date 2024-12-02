<?php

namespace Redmine\Services;

use PHPUnit\Util\Json;

class CustomFieldService extends RedmineBaseService
{
    protected $customField;
    protected $issueTimeEntries ;

    public function __construct()
    {
        $this->customField = model(\Redmine\Models\CustomFieldModel::class);
    }

    public function getCustomField($type,$name){
        $data = $this->customField->getCustomField($type,$name);
        $values = $data['possible_values'];   
        $values = explode("\n",$values);
        foreach($values as $val){
            $val = trim($val,' -');
            if(!empty($val)){
                $ans[]=$val;
            }
        }
        $data['possible_values'] = $ans;
        return $data;
    }

    public function insertCustomField($id,$customId,$values){
        return $this->customField->getCustomField($id,$customId,$values);
    }
}
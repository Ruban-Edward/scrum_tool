<?php
namespace Redmine\Models;

class CustomValueModel extends RedmineBaseModel
{
    protected $table = "custom_values";

    protected $primaryKey = "id";
    
    protected $allowedFields = [	
        'customized_type',	
        'customized_id',	
        'custom_field_id',	
        'value'
    ];

    public function insertCustomValue($id,$customId,$values){
        $sql = "INSERT INTO custom_values (customized_type,customized_id,custom_field_id,value)
                VALUES (:customize_type:,:issue_id:,:custom_field_id:,:values:);";
        $this->query($sql, [
            'issue_id' => $id,
            'values' => $values,
            'customize_type' => 'issue',
            'custom_field_id' => $customId
        ]);
        return $sql;
    }
}
<?php
namespace Redmine\Models;

class CustomFieldModel extends RedmineBaseModel
{
    protected $table = "custom_fields";

    protected $primaryKey = "id";
    
    protected $allowedFields = [	
        'type',	
        'name',	
        'field_format',	
        'possible_values',
        'regexp',	
        'min_length',	
        'max_length',	
        'is_required',	
        'is_for_all',	
        'is_filter',
        'position',	
        'searchable',	
        'default_value',	
        'editable',	
        'visible',	
        'multiple',	
        'format_store',	
        'description'
    ];

    public function getCustomField($type,$name){
        $sql = "SELECT 
                    possible_values
                FROM 
                    custom_fields
                WHERE 
                    type = :type: AND
                    name = :name:";
        $query = $this->query($sql,[
            'type' => $type,
            'name' => $name
        ]);
        if ($query->getNumRows() > 0) {
            return $query->getResultArray()[0];
        }
    }

    public function insertCustomField($id,$customId,$values){
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
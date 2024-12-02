<?php
namespace Redmine\Models;
use App\Models\BaseModel;

class RedmineBaseModel extends BaseModel
{
    protected $DBGroup = 'redmine';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;

    public function __construct()
    {
        parent::__construct();
    }
}
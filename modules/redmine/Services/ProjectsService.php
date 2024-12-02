<?php
namespace Redmine\Services;

class ProjectsService extends RedmineBaseService
{
    protected $projectModel;

    public function __construct()
    {
        $this->projectModel = model(\Redmine\Models\ProjectModel::class);
    }
    public function getProducts(){
        $data = $this->projectModel->getProducts();
        return $data;
    }

    public function getProductUser(){
        $data = $this->projectModel->getProductUser();
        return $data;
    }
    public function syncProducts(){
        $data = $this->projectModel->syncProducts();
        return $data;
    }

    public function productUserSync(){
        $data = $this->projectModel->productUserSync();
        return $data;
    }
    public function getAllProductUsersFromRedmine(){
        $data=$this->projectModel->getAllProductUsersFromRedmine();
        return $data;
    }
    public function getAllProductsFromRedmine(){
        $data=$this->projectModel->getAllProductsFromRedmine();
        return $data;
    }

}
<?php
namespace Redmine\Services;
use Redmine\Services\RedmineBaseService;

class UsersService extends RedmineBaseService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = model(\Redmine\Models\UserModel::class);
    }
    public function all()
    {
        $result = $this->userModel->findAll();
        return json_encode($result);
    }

    /**
     * $id -  int| array
     * where,
     * whereIn
     */
    public function read($id, $field)
    {
        if (! isset($id)) {
            return [];
        }
        if(is_array($id))
            $user = $this->userModel->whereIn($field, $id);
        else 
            $user = $this->userModel->where($field, $id);

        $data = $user->get()->getResult();
        return $data;
    }

    // created by Stervin Richard
    // return redmine user data
    public function getUser($username){
        $data = $this->userModel->getUser($username);
        return $data;
    }

    // created by Vishva
    // return all redmine user data
    public function getUsers(){
        $data = $this->userModel->getUsers();
        return $data;
    }

    
    public function isValidPassword($ipassword, $dpassword, $salt)
    {
        $isValid = false;
        if(isset($ipassword)) {
            $ipassword = sha1($salt.sha1($ipassword));
            $isValid = ($dpassword == $ipassword)?true:false;
        }
        return $isValid;
    }

    public function userSync(){
        $result = $this->userModel->syncAllUser();
        return $result;
    }
}
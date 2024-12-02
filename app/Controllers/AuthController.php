<?php
namespace App\Controllers;
use App\Models\ProjectModel;
use Config\SprintModelConfig;
use CodeIgniter\Events\Events;

/**
 * @author Infiniti <infiniti@infinitisoftware.net>
 * 
 * @modified-by Infiniti <infiniti@infinitisoftware.net>
 * @created-date 13-06-2024
 * @modified-date 13-06-2024
 * 
 */
class AuthController extends BaseController
{
    /**
     * AuthController::index()
     * returns default page (login screen)
     * 
     * @return string
     */ 
    protected $backlogModel;
    protected $session;
    protected $userModel;

    public function __construct(){
        // object of the BacklogModel
        $this->backlogModel = model(\App\Models\Backlog\BacklogModel::class);
        $this->session=session();
        // object of the UserModel
        $this->userModel = model(\App\Models\User\UserModel::class);    
    }

    /**
     * @author Stervin Richard 
     * AuthController::index()
     *
     * to view the login page 
     */ 
    public function index(): string
    {   
        return view('user/login');
    }

    /**
     * @author Stervin Richard 
     * AuthController::loginValidate()
     *
     * Check user login authentication with scrum DB and the redmine DB
     * if the user is valid then allowed to scrum master portal
     */ 
    public function loginValidate()
    {
        // validation rules of input fields with custom messages
        $rules = [
            "username" => [
                "label" => "Username",
                "rules" => "required|min_length[3]",
                "errors" => [
                    "required" => "The {field} field is required",
                    "min_length" => "The {field} field must be at least 3 characters in length"
                ]
            ],
            "password" => [
                "label" => "Password",
                "rules" => "required|min_length[8]",
                "errors" => [
                    "required" => "The {field} field is required",
                    "min_length" => "The {field} field must be at least 8 characters in length"
                ]
            ]
        ];
        // check validation and send error message
        if (!$this->validate($rules)) {
            $errorMessage = $this->validator->getErrors();
            session()->setFlashdata('validation', $errorMessage);
            return redirect()->to(ASSERT_PATH.'/login');
        }
        $userdata = $this->request->getPost();
        $username = trim($userdata['username']);
        $password = $userdata['password'];            
        $userData = [
            'username' => $username,
            'password' => $password
        ];
        //Check user login details with scrum database 
        $user = $this->userModel->getUser($userData); 
        //check valid user
        if(! empty($user) && isset($user[0]->first_name) && isset($user[0]->external_employee_id) && isset( $user[0]->external_api_key)) { 
            $firstName = $user[0]->first_name;
            $employeeId = $user[0]->external_employee_id;
            $roleId = $user[0]->r_role_id;
            // $redmineApiKey = $user[0]->external_api_key;
        }
        else{
            // check login details with redmine database
            $userService = service("users");
            $user = $userService->getUser($username);
            //check invalid user
            if (empty($user)) {
                $errorMessage = "Invalid username or password";
                session()->setFlashdata('error', $errorMessage);
                return redirect()->to(ASSERT_PATH.'/login');
                        
            }
            // for checking valid password or not
            if (! $userService->isValidPassword(
                $password, 
                $user[0]->hashed_password, 
                $user[0]->salt
            )) {
                $errorMessage = "Invalid username or password";
                session()->setFlashdata('error', $errorMessage);
                return redirect()->to(ASSERT_PATH.'/login');
            }
            // for user role setting 
            $roleConfig = new SprintModelConfig();
            $userRoles = $roleConfig->userRoles;
            $userRoleId = $roleConfig->userRoleId;            
            // Determine role based on admin status
            if ($user[0]->admin > 0) {
                $roleId = $userRoleId['scrum_admin'];
            } else {
                $roleId = $this->userRoleSet($user[0]->role,$userRoles,$userRoleId);
            }
            //for valid users           
            $userData = [
                'username' => $username,
                'password' => $password,
                'employee_id' => $user[0]->id,
                'api_key' => $user[0]->value,
                'first_name' => $user[0]->firstname,
                'last_name' => $user[0]->lastname,
                'email_id' => $user[0]->address,
                'role_id' => $roleId,
            ];
            // insert or update user data into scrum database
            $this->userModel->insertOrUpdatetUser([$userData]);
        }
        $url = $this->session->get('url');
        //Set User details in session
        $user_session = [
            'first_name' => $userData['first_name'] ?? $firstName ,
            'employee_id' => $userData['employee_id'] ?? $employeeId,
            'role_id' => $roleId,
            'is_user_logged' => true
            // 'redmine_api_key' => $userData['api_key'] ??  $redmineApiKey
        ];
        $this->session->set($user_session);
        // set the user login action in scrum db
        $this->addLogin(LOGIN_ACTION);
        // redirect directly to the user entered url
        if(isset($url)){
            return redirect()->to(ASSERT_PATH.$url);
        }
        //Redirect to user dashboard page                    
        return redirect()->to(ASSERT_PATH.'dashboard/dashboardView');
    }
    
    /**
    * AuthController::logout()
    *
    * Logout user and destroy the session details
    */ 
    public function logout()
    {
        // set the user logout action in db
        $this->addLogin(LOGOUT_ACTION);
        $this->session->destroy();
        return redirect()->to(ASSERT_PATH);
    }

    /**
     * @author Stervin Richard 
     * AuthController::addLogin()
     *
     * set the user action in scrum db 
     */ 
    public function addLogin($actionData){
        $action = formActionData(__FUNCTION__, $this->session->get('employee_id'), 0, $actionData);
        Events::trigger('log_actions', $action);
    }

    /**
    * AuthController::noAccess()
    *
    * Logout user
    */ 

    /**
     * @author Ruban Edward 
     * AuthController::noAccess()
     *
     * If the user does not have the permission for the page it redirects to this page
     */ 
    public function noAccess()
    {
        return view('unauthorized');
    }

}
<?php
namespace App\Controllers;

use App\Controllers\ApiController;
use App\Repositories\UserRepository;
use App\Library\MyLibrary;

class UserController extends ApiController
{
    //------- start : Define variable ----------//
    private $getUserDetail = [
        [
            'type'   => 'required',
            'fields' => ['id'],
        ]
    ];

    private $insertUserRule = [
        [
            'type'   => 'required',
            'fields' => ['username', 'password', 'name', 'last_name', 'email', 'role_id'],
        ],[
            'type'   => 'within',
            'fields' => ['status' => ['active', 'inactive']],
        ]
    ];

    private $updateUserRule = [
        [
            'type'   => 'required',
            'fields' => ['id', 'username', 'password', 'name', 'last_name', 'email', 'role_id'],
        ],[
            'type'   => 'within',
            'fields' => ['status' => ['active', 'inactive']],
        ]
    ];

    private $deleteUserRule = [
        [
            'type'   => 'required',
            'fields' => ['id'],
        ],
    ];

    private $userLogin = [
        [
            'type'   => 'required',
            'fields' => ['username', 'password'],
        ]
    ];

    private $userChangePass = [
        [
            'type'   => 'required',
            'fields' => ['id', 'old', 'new'],
        ]
    ];
    //------- end : Define variable ----------//


    //------- start: protected method ------------//
    //method for get user repo
    protected function getUserRepository()
    {
        return $this->repository->getRepository('UserRepository');
    }
    //------- end: protected method ------------//

    //------- start: Main method ---------------//
    //Method for get user data
    public function getUserAction()
    {
        //get input
        $inputs   = $this->getAllUrlParam();
        
        //get user repository
        $userRepo = $this->getUserRepository();
        
        //get user data by input
        $result   = $userRepo->getUser($inputs);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

         if (isset($result['totalRecord'])) {
            $inputs['totalRecord'] = $result['totalRecord'];
        }

        return $this->output($result['data'], $inputs);
    }

    //Method for get user detail data
    public function getUserDetailAction()
    {
        //get input
        $inputs       = $this->getAllUrlParam();

        //define default
        $default      = [];
        
        // Validate input
        $params       = $this->validateApi($this->getUserDetail, $default, $inputs);
        
        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }
        
        //get user repository
        $userRepo = $this->getUserRepository();
        
        //get user data by id
        $result   = $userRepo->getUserDetail($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    //Method for add new user
    public function postCreateAction()
    {
        //get input
        $inputs = $this->getPostInput();

        //define default
        $default = [];

        // Validate input
        $params = $this->validateApi($this->insertUserRule, $default, $inputs);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get user repository
        $userRepo = $this->getUserRepository();

        //add user data by input
        $result = $userRepo->addUser($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    //Method for update user
    public function putUpdateAction($id)
    {
        //get input
        $inputs       = $this->getPostInput();
        $inputs['id'] = $id;

        //define default
        $default = [];

        // Validate input
        $params = $this->validateApi($this->updateUserRule, $default, $inputs);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get user repository
        $userRepo = $this->getUserRepository();

        //update user data
        $result = $userRepo->editUser($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    //Method for delete user
    public function deleteUserAction($id)
    {
        //get input
        $inputs       = $this->getAllUrlParam();
        $inputs['id'] = $id;

        //define default
        $default = [];

        // Validate input
        $params = $this->validateApi($this->deleteUserRule, $default, $inputs);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get user repository
        $userRepo = $this->getUserRepository();

        //update user data
        $result = $userRepo->deleteUser($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    //Method for user login
    public function postLoginAction()
    {
        //get input
        $inputs = $this->getPostInput();

        //define default
        $default = [
            'status' => 'active'
        ];

        // Validate input
        $params = $this->validateApi($this->userLogin, $default, $inputs);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get user repository
        $userRepo = $this->getUserRepository();

        //process user login
        $result = $userRepo->checkLogin($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    //Method for change password
    public function putChangepasswordAction($id)
    {
        //get input
        $inputs       = $this->getPostInput();
        $inputs['id'] = $id;

        //define default
        $default = [];

        // Validate input
        $params = $this->validateApi($this->userChangePass, $default, $inputs);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get user repository
        $userRepo = $this->getUserRepository();
        
        //update user data
        $result   = $userRepo->changePassword($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }
    //------- end: Main method ---------------//
}
<?php
namespace App\Repositories;

use App\Models\Users;

class FieldRepository extends \Phalcon\Mvc\Micro {

    //------- start : Define variable ----------//
    protected $allowFilter = ['username', 'name', 'last_name', 'email', 'role_id', 'created_at', 'updated_at', 'status'];
    //------- end : Define variable ----------//

    //------- start: protected method ------------//
    //Method for get user model
    protected function getUserModel()
    {
        return $this->model->getModel('Users');
    }

    //Method for get data by filter
    protected function getDataByParams($params)
    {
        //Create conditions
        $conditions  = $this->mongoService->createConditionFilter($params, $this->allowFilter);
        //create model
        $model       = $this->getUserModel();
        
        $filterCon   = [$conditions];
        //Manage order
        $filterCon   = $this->mongoService->manageOrderInParams($params, $filterCon, $this->allowFilter);

        //query data
        $total = 0;
        if (!isset($params['limit'])) {
            $users = $model->find($filterCon);
            $total = count($users);
        } else {
            $total     = $model->count([$conditions]);
            $filterCon = $this->mongoService->manageLimitOffsetInParams($params, $filterCon);
            $users     = $model->find($filterCon);
            
        }

        return [$users, $total];
    }

    //Method for get category detail
    protected function getDetailDataById($id)
    {
        //create model
        $model = $this->getUserModel();
        $users = $this->mongoService->getDetailDataById($model, $id, $this->allowFilter);

        return $users;
    }

    // //Method for get type by name
    // protected function getTypeById($id)
    // {
    //     //create type repository
    //     $typeRepo = $this->repository->getRepository('TypeRepository');
    //     $type     = $typeRepo->getTypeById($id);

    //     return $type;
    // }

    //Method for check duplicate (insert)
    protected function checkDuplicate($model, $params)
    {
        $filter = [
            'username' => $params['username']
        ];

        $user = $model->find([$filter]);
        
        if (!empty($user))
        {
            return true;
        }

        return false;
    }

    //Method for check duplicate (update)
    protected function checkDuplicateForUpdate($model, $id, $params)
    {
        $filter = [
            'username' => $params['username']
        ];

        $datas = $model->find([$filter]);

        if (!empty($datas) && ((string)$datas[0]->_id != $id))
        {
            return true;
        }

        return false;
    }

    //Method for encrypt password
    protected function encryptPassword($password)
    {
        if (!empty($password)) {
            return $this->security->hash($password);
        }
        return "";
    }

    //Method for insert data to db
    protected function insertData($model, $params)
    {
        $currentDate = date("Y-m-d H:i:s");
        //get password
        $password    = (isset($params['password']))?$params['password']:"";

        //create content
        $model->username   = $params['username'];
        $model->password   = $this->encryptPassword($password);
        $model->name       = $params['name'];
        $model->last_name  = $params['last_name'];
        $model->email      = $params['email'];
        $model->role_id    = $params['role_id'];
        $model->status     = $params['status'];
        $model->created_at = $currentDate;
        $model->updated_at = $currentDate;

        if (!$model->save())
        {
            return null;
        }

        return $model;
    }

    //Method for update data to db
    protected function updateData($user, $params)
    {
        unset($params['password']);
        //add deal data to model
        foreach ($params as $key => $value) {
            if ( property_exists($user, $key) ) {
                $user->{$key} = $value;
            }
        }

        //add created date
        $user->updated_at = date("Y-m-d H:i:s");

        if (!$user->save())
        {
            return null;
        }

        return $user;
    }

    //Method for update password
    protected function updatePassword($user, $password)
    {
        $user->password   = $this->encryptPassword($password);
        $user->updated_at = date("Y-m-d H:i:s");

        if (!$user->save())
        {
            return null;
        }

        return $user;
    }

    //Method for delete data from db
    protected function deleteData($user)
    {
        if (!$user->delete())
        {
            return null;
        }

        return (string)$user->_id;
    }

    //Method for check password
    protected function checkPassword($passInDb, $passCheck)
    {
        return $this->security->checkHash($passCheck, $passInDb);
    }

    //Method for manage login by type
    protected function manageLogin($users, $password)
    {
        //Define output
        $outputs      = [false];
        //get first
        $user         = $users[0];
        //get type by id
        $type         = $user['content_type'];

        switch(strtolower($type)) {
            case "admin":
                //login with AD
                //TODO
                $outputs = [true, $user];
                break;
            case "merchant":
                $outputs = [$this->checkPassword($user['password'], $password), $user];
                break;
            case "sale":
                $outputs = [$this->checkPassword($user['password'], $password), $user];
                break;
        }

        return $outputs;
    }
    //------- end: protected method ------------//


    //------- start: Main method ---------------//
    //Method for get user by id
    public function getUserById($id)
    {
        //get model
        $model = $this->getUserModel();
        //get data
        $user  = $model->findById($id);
        return $user;
    }

    //Method for get user by filter
    public function getUser($params)
    {
        //Define output
        $outputs = [
            'success' => true,
            'message' => '',
        ];

        try {
            //create filter
            $users           = $this->getDataByParams($params);
            $outputs['data'] = $users[0];
            
            if (isset($params['limit'])) {
                //get total record
                $outputs['totalRecord'] = $users[1];
            }

        } catch (\Exception $e) {
            $outputs['success'] = false;
            $outputs['message'] = 'missionFail';
        }
        

        return $outputs;
    }

    //Method for get user detail by id (list)
    public function getUserDetail($params)
    {
        //Define output
        $outputs = [
            'success' => true,
            'message' => '',
        ];

        try { 
            //get data
            $users           = $this->getDetailDataById($params['id']);
            
            $users           = $this->mongoService->addIdTodata($users);
            
            $users           = $this->mongoService->manageSortDataByIdList($users, $params['id']);
            
            $outputs['data'] = $users;

        } catch (\Exception $e) {
            $outputs['success'] = false;
            $outputs['message'] = 'missionFail';
        }
        

        return $outputs;    
    }

    //Method for insert data
    public function addUser($params)
    {
        //Define output
        $output = [
            'success' => true,
            'message' => '',
            'data'    => '',
        ];

        //get model
        $userModel = $this->getUserModel();
            
        //Check Duplicate
        if (!$this->checkDuplicate($userModel, $params))
        {
            //insert
            $res = $this->insertData($userModel, $params);

            if (!$res)
            {
                //Cannot insert
                $output['success'] = false;
                $output['message'] = 'insertError';
                return $output;
            }

            //add user data
            $output['data'] = $this->mongoService->addIdTodata($res, false);
        }
        else
        {
            //Duplicate
            $output['success'] = false;
            $output['message'] = 'dataDuplicate';
        }

        return $output;
    }

    //Method for edit data
    public function editUser($params)
    {
        //Define output
        $output = [
            'success' => true,
            'message' => '',
            'data'    => '',
        ];

        //get model
        $userModel = $this->getUserModel();

        //get old content
        $user = $userModel->findById($params['id']);

        if (empty($user))
        {
            //Cannot insert
            $output['success'] = false;
            $output['message'] = 'dataNotFound';
            return $output;
        }

        //check duplicate
        if (!$this->checkDuplicateForUpdate($userModel, $params['id'], $params))
        {
            //update
            $res = $this->updateData($user, $params);

            if (!$res)
            {
                //Cannot insert
                $output['success'] = false;
                $output['message'] = 'updateError';
                return $output;
            }

            //add update user data
            $output['data'] = $this->mongoService->addIdTodata($res, false);
        }
        else
        {
            //Duplicate
            $output['success'] = false;
            $output['message'] = 'dataDuplicate';
        }

        return $output;
    }

    //Method for delete data
    public function deleteUser($params)
    {
        //Define output
        $output = [
            'success' => true,
            'message' => '',
            'data'    => '',
        ];

        //get model
        $userModel = $this->getUserModel();
        $user      = $userModel->findById($params['id']);

        if (empty($user))
        {
            //No Data
            $output['success'] = false;
            $output['message'] = 'dataNotFound';
            return $output;
        }

        //delete
        $res = $this->deleteData($user);

        if (!$res)
        {
            //Cannot insert
            $output['success'] = false;
            $output['message'] = 'deleteError';
            return $output;
        }

        //get insert id
        $output['data'] = $res;

        return $output;
    }

    //Method for check login
    public function checkLogin($params)
    {
        //Define output
        $output = [
            'success' => true,
            'message' => '',
            'data'    => '',
        ];

        //get user 
        $users = $this->getDataByParams($params);

        if (empty($users[0])) {
            //No Data
            $output['success'] = false;
            $output['message'] = 'dataNotFound';
            return $output;
        }

        //fotmat data
        $users = $this->mongoService->addIdTodata($users[0]);

        //validate login
        $res = $this->manageLogin($users, $params['password']);

        if (!$res[0])
        {
            //Cannot insert
            $output['success'] = false;
            $output['message'] = 'loginFail';
            return $output;
        }

        //get insert id
        $output['data'] = $res[1];

        return $output;
    }

    //Method for change password
    public function changePassword($params)
    {
        //Define output
        $output = [
            'success' => true,
            'message' => '',
            'data'    => '',
        ];

        //get user data
        $user = $this->getUserById($params['id']);

        if (empty($user)) {
            //No Data
            $output['success'] = false;
            $output['message'] = 'dataNotFound';
            return $output;
        }

        //check old password
        if (!$this->checkPassword($user->password, $params['old'])) {
            //old password not match
            $output['success'] = false;
            $output['message'] = 'oldPasswordWrong';
            return $output;
        }

        //update password
        $res = $this->updatePassword($user, $params['new']);

        if (!$res)
        {
            //Cannot insert
            $output['success'] = false;
            $output['message'] = 'updateError';
            return $output;
        }

        //get insert id
        $output['data'] = $this->mongoService->addIdTodata($res, false);

        return $output;
    }

    //Method for check duplicate user
    public function checkDuplicateUsername($username)
    {
        //get model
        $userModel = $this->getUserModel();

        return $this->checkDuplicate($userModel, ['username' => $username]);
    }
    //------- end: Main method ---------------//
}
<?php
namespace App\Controllers;

use App\Controllers\ApiController;
use App\Repositories\SchemaRepository;
use App\Library\MyLibrary;

class SchemaController extends ApiController
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
    protected function getSchemaRepository()
    {
        return $this->repository->getRepository('SchemaRepository');
    }
    //------- end: protected method ------------//

    //------- start: Main method ---------------//
    //Method for get user data
    public function getSchemaAction()
    {
        //get input
        $inputs   = $this->getAllUrlParam();
        //get field repository
        $schemaRepo = $this->getSchemaRepository();
        $inputs['limit'] = 10;
        //get schema data by input
        $result   = $schemaRepo->getSchema($inputs);

        // dd($result['success']);

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

}
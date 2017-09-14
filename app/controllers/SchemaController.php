<?php
namespace App\Controllers;

use App\Controllers\ApiController;
use App\Repositories\SchemaRepository;
use App\Library\MyLibrary;

class SchemaController extends ApiController
{
    //------- start : Define variable ----------//
    // private $getSchema = [
    //     [
    //         'type'   => 'required',
    //         'fields' => ['id'],
    //     ]
    // ];
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
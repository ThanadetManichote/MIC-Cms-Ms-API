<?php
namespace App\Controllers;

use App\Controllers\ApiController;
use App\Repositories\ContentRepository;
use App\Library\MyLibrary;

class ContentController extends ApiController
{
    //------- start : Define variable ----------//
    private $getContentDetail = [
        [
            'type'   => 'required',
            'fields' => ['id'],
        ]
    ];

    private $insertContentRule = [
        [
            'type'   => 'required',
            'fields' => ['first_name'],
        ]
    ];


    //------- end : Define variable ----------//


    //------- start: protected method ------------//
    //method for get content repo
    protected function getContentRepository()
    {
        return $this->repository->getRepository('ContentRepository');
    }
    //------- end: protected method ------------//

    //------- start: Main method ---------------//
    //Method for get content data
    public function getContentAction()
    {
        //get input
        $inputs   = $this->getAllUrlParam();

        $headers = $this->getHeaders();

        //get field repository
        $contentRepo = $this->getContentRepository();

        //get content data by input
        $result   = $contentRepo->getContent($inputs,$headers);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

         if (isset($result['totalRecord'])) {
            $inputs['totalRecord'] = $result['totalRecord'];
        }

        return $this->outputgraph($result, $inputs);
    }

    //Method for get user detail data
    public function getContentDetailAction()
    {
        //get input
        $inputs       = $this->getAllUrlParam();

        $headers = $this->getHeaders();

        //define default
        $default      = [];
        
        // Validate input
        $params       = $this->validateApi($this->getContentDetail, $default, $inputs);
        
        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }
        
        //get content repository
        $contentRepo = $this->getContentRepository();

        //get user data by id
        $result   = $contentRepo->getContentDetail($params,$headers);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    public function postContentCreateAction()
    {
        //get input
        $inputs = $this->postInput();

        //define default
        $default = [];

        // Validate input
        $params = $this->validateApi($this->insertContentRule, $default, $inputs);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get content repository
        $contentRepo = $this->getContentRepository();

        //add user data by input
        $result = $contentRepo->addContent($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }



}
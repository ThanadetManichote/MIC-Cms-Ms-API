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

        //get field repository
        $contentRepo = $this->getContentRepository();

        //get content data by input
        $result   = $contentRepo->getContent($inputs);

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

}